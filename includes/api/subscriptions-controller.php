<?php
/**
 * Subscriptions API Controller
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    // GET /donasai/v1/subscriptions (My Subscriptions)
    register_rest_route('donasai/v1', '/subscriptions', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_my_subscriptions',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));

    // POST /donasai/v1/subscriptions/{id}/cancel
    register_rest_route('donasai/v1', '/subscriptions/(?P<id>\d+)/cancel', array(
        'methods' => 'POST',
        'callback' => 'donasai_api_cancel_subscription',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ));
});

function donasai_api_get_my_subscriptions($request)
{
    $service = new DONASAI_Subscription_Service();
    $subs = $service->get_user_subscriptions(get_current_user_id());

    return rest_ensure_response($subs);
}

function donasai_api_cancel_subscription($request)
{
    $id = isset($request['id']) ? intval($request['id']) : 0;
    $service = new DONASAI_Subscription_Service();
    $result = $service->cancel_subscription($id, get_current_user_id());

    if ($result === false) {
        return new WP_Error('db_error', 'Failed to cancel subscription', array('status' => 500));
    }

    return rest_ensure_response(array('success' => true, 'id' => $id, 'status' => 'cancelled'));
}
