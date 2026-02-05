<?php
/**
 * REST API for Donations
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    // GET /donations
    register_rest_route('donasai/v1', '/donations', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_donations',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // POST /donations/{id} (Update Status)
    register_rest_route('donasai/v1', '/donations/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'donasai_api_update_donation',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /export/donations
    register_rest_route('donasai/v1', '/export/donations', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_export_donations',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /stats
    register_rest_route('donasai/v1', '/stats', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_stats',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /stats/chart
    register_rest_route('donasai/v1', '/stats/chart', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_chart_stats',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));
});

function donasai_api_get_chart_stats()
{
    $chart_data = donasai_get_chart_data();
    return rest_ensure_response($chart_data);
}

function donasai_api_get_stats()
{
    $overview = donasai_get_stats_overview();
    $advanced = donasai_get_advanced_analytics();

    return rest_ensure_response(array_merge($overview, $advanced, array(
        'total_donations' => $overview['total_collected']
    )));
}



function donasai_api_export_donations($request)
{
    // Check Nonce
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        wp_die('Invalid nonce');
    }

    $filters = array(
        'campaign_id' => isset($_GET['campaign_id']) ? sanitize_text_field(wp_unslash($_GET['campaign_id'])) : '',
        'status' => isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '',
        'start_date' => isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : '',
        'end_date' => isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : '',
        'payment_method' => isset($_GET['payment_method']) ? sanitize_text_field(wp_unslash($_GET['payment_method'])) : '',
        'is_recurring' => isset($_GET['is_recurring']) ? sanitize_text_field(wp_unslash($_GET['is_recurring'])) : '',
    );
    
    // Use service to get list
    $results = donasai_get_donations_list(array(
        'filters' => $filters,
        'per_page' => 10000 // Large limit for export
    ));

    $filename = 'donations-export-' . wp_date('Y-m-d') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Campaign ID', 'Date', 'Name', 'Email', 'Amount', 'Status', 'Payment Method'));

    foreach ($results as $row) {
        $name = $row->name;
        if (preg_match('/^[\=\+\-\@]/', $name)) {
            $name = "'" . $name;
        }

        fputcsv($output, array(
            $row->id,
            $row->campaign_id,
            $row->created_at,
            $name,
            $row->email,
            $row->amount,
            $row->status,
            $row->payment_method
        ));
    }

    fclose($output);
    exit;
}

function donasai_api_update_donation($request)
{
    $id = isset($request['id']) ? intval($request['id']) : 0;
    $params = $request->get_json_params();

    $data_to_update = array();
    $format = array();
    $allowed_fields = array('status', 'name', 'email', 'phone', 'amount', 'note');

    foreach ($allowed_fields as $field) {
        if (isset($params[$field])) {
            $value = $params[$field];
            if ('amount' === $field) {
                $data_to_update[$field] = (float) $value;
                $format[] = '%f';
            } else {
                if ('note' === $field) {
                    $data_to_update[$field] = sanitize_textarea_field($value);
                } else {
                    $data_to_update[$field] = sanitize_text_field($value);
                }
                $format[] = '%s';
            }
        }
    }

    if (empty($data_to_update)) {
        return new WP_Error('no_data', 'No data to update', array('status' => 400));
    }

    $updated = donasai_update_donation($id, $data_to_update, $format);

    if ($updated === false) {
        return new WP_Error('db_error', 'Could not update donation', array('status' => 500));
    }

    if (isset($data_to_update['status']) && 'complete' === $data_to_update['status']) {
        do_action('donasai_donation_completed', $id);
        
        $donation = donasai_get_donation($id);
        if ($donation && $donation->campaign_id) {
            donasai_update_campaign_stats($donation->campaign_id);
        }
    }

    $updated_row = donasai_get_donation($id);

    return rest_ensure_response(array(
        'success' => true,
        'id' => $id,
        'message' => 'Donation updated',
        'data' => array(
            'id' => $updated_row->id,
            'name' => $updated_row->name,
            'email' => $updated_row->email,
            'phone' => $updated_row->phone,
            'amount' => (float) $updated_row->amount,
            'status' => $updated_row->status,
            'payment_method' => $updated_row->payment_method,
            'gateway_txn_id' => $updated_row->gateway_txn_id,
            'note' => $updated_row->note,
            'date' => $updated_row->created_at,
        )
    ));
}

function donasai_api_get_donations($request)
{
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 20;

    if ($page < 1) $page = 1;
    if ($per_page < 1) $per_page = 20;
    if ($per_page > 100) $per_page = 100;

    $offset = ($page - 1) * $per_page;

    $filters = array(
        'campaign_id' => $request->get_param('campaign_id'),
        'status' => $request->get_param('status'),
        'start_date' => $request->get_param('start_date'),
        'end_date' => $request->get_param('end_date'),
        'payment_method' => $request->get_param('payment_method'),
        'is_recurring' => $request->get_param('is_recurring'),
    );
    
    $total_items = donasai_get_donations_count($filters);
    $total_pages = ceil($total_items / $per_page);

    $results = donasai_get_donations_list(array(
        'filters' => $filters,
        'per_page' => $per_page,
        'offset' => $offset
    ));

    $data = array_map(function ($row) {
        return array(
            'id' => (int) $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'phone' => $row->phone,
            'amount' => (float) $row->amount,
            'status' => $row->status,
            'payment_method' => $row->payment_method,
            'gateway_txn_id' => $row->gateway_txn_id,
            'note' => $row->note,
            'metadata' => json_decode($row->metadata, true),
            'date' => $row->created_at,
        );
    }, $results);

    return rest_ensure_response(array(
        'data' => $data,
        'meta' => array(
            'current_page' => $page,
            'per_page' => $per_page,
            'total' => $total_items,
            'total_pages' => $total_pages
        )
    ));
}
