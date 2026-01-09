<?php
/**
 * Campaign Admin Columns
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Filter Campaign Columns
 */
function wpd_campaign_columns($columns)
{
    $new_columns = array();

    // Insert Image after Checkbox
    $new_columns['cb'] = $columns['cb'];
    $new_columns['thumb'] = __('Image', 'donasai');
    $new_columns['title'] = $columns['title']; // Includes "Edit | Quick Edit | Trash"

    // Add custom columns
    $new_columns['donation_category'] = __('Category', 'donasai'); // Should be auto-added by register_taxonomy, but good to ensure order
    $new_columns['stats'] = __('Stats', 'donasai');
    $new_columns['dates'] = __('Campaign Dates', 'donasai');
    $new_columns['campaigner'] = __('Campaigner', 'donasai');
    $new_columns['wpd_actions'] = __('Actions', 'donasai');

    // Optional: Keep date, author if needed, or remove to simplify
    // $new_columns['date'] = $columns['date']; 

    return $new_columns;
}
add_filter('manage_wpd_campaign_posts_columns', 'wpd_campaign_columns');

/**
 * Render Custom Columns
 */
function wpd_campaign_custom_column($column, $post_id)
{
    switch ($column) {
        case 'thumb':
            if (has_post_thumbnail($post_id)) {
                echo wp_kses_post(get_the_post_thumbnail($post_id, array(50, 50), array('style' => 'width:50px;height:50px;object-fit:cover;border-radius:4px;')));
            } else {
                echo '<span style="width:50px;height:50px;background:#f0f0f1;display:block;border-radius:4px;"></span>';
            }
            break;

        case 'stats':
            $target = get_post_meta($post_id, '_wpd_target_amount', true);
            $collected = get_post_meta($post_id, '_wpd_collected_amount', true);
            $donors = get_post_meta($post_id, '_wpd_donor_count', true);

            if (!$target)
                $target = 0;
            if (!$collected)
                $collected = 0;
            if (!$donors)
                $donors = 0;

            $percent = $target > 0 ? ($collected / $target) * 100 : 0;
            $color = $percent >= 100 ? '#16a34a' : '#2563eb';

            echo '<div style="margin-bottom:4px;"><strong>Target:</strong> Rp ' . esc_html(number_format((float) $target, 0, ',', '.')) . '</div>';
            echo '<div style="margin-bottom:4px;"><strong>Collected:</strong> <span style="color:' . esc_attr($color) . '">Rp ' . esc_html(number_format((float) $collected, 0, ',', '.')) . '</span></div>';
            echo '<div><strong>Donors:</strong> ' . intval($donors) . '</div>';

            // Progress bar
            echo '<div style="background:#e5e7eb;height:4px;width:100%;margin-top:5px;border-radius:2px;overflow:hidden;">';
            echo '<div style="background:' . esc_attr($color) . ';height:100%;width:' . esc_attr(min(100, $percent)) . '%;"></div>';
            echo '</div>';
            break;

        case 'dates':
            $deadline = get_post_meta($post_id, '_wpd_deadline', true);
            $published = get_the_date('d M Y', $post_id);

            echo '<div><strong>Start:</strong> ' . esc_html($published) . '</div>';
            if ($deadline) {
                $days_left = ceil((strtotime($deadline) - time()) / 86400);
                if ($days_left < 0) {
                    echo '<div style="color:#dc2626;"><strong>End:</strong> ' . esc_html(date_i18n('d M Y', strtotime($deadline))) . ' (Expired)</div>';
                } else {
                    echo '<div><strong>End:</strong> ' . esc_html(date_i18n('d M Y', strtotime($deadline))) . '</div>';
                }
            } else {
                echo '<div><strong>End:</strong> -</div>';
            }
            break;

        case 'campaigner':
            $author_id = get_post_field('post_author', $post_id);
            $user = get_userdata($author_id);
            if ($user) {
                echo esc_html($user->display_name);
            } else {
                echo '-';
            }
            break;

        case 'wpd_actions':
            // Link to Donation Detail (Filtered by Campaign)
            // Just linking to main donations page with ?campaign_id query (needs implementation in React or standard WC-like filtering)
            // For now, let's link to the standard edit page or a custom export link.

            // Download CSV
            $nonce = wp_create_nonce('wp_rest');
            $export_url = home_url('/wp-json/wpd/v1/export/donations');
            $export_url = add_query_arg(array('campaign_id' => $post_id, '_wpnonce' => $nonce), $export_url);

            // Recalculate Stats Action
            $recalc_nonce = wp_create_nonce('wpd_recalc_stats_' . $post_id);
            $recalc_url = admin_url('admin-post.php');
            $recalc_url = add_query_arg(array(
                'action' => 'wpd_recalc_stats',
                'post_id' => $post_id,
                '_wpnonce' => $recalc_nonce
            ), $recalc_url);

            echo '<details style="position:relative;">';
            echo '<summary class="button button-small">Actions <span class="dashicons dashicons-arrow-down-alt2" style="font-size:12px;line-height: inherit;"></span></summary>';
            echo '<div style="position:absolute; right:0; top:30px; width:160px; background:#fff; border:1px solid #ccd0d4; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:999; border-radius:4px; overflow:hidden;">';

            // Edit
            echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '" style="display:block; padding:8px 12px; text-decoration:none; color:#1d2327; border-bottom:1px solid #f0f0f1; transition:bg 0.2s;" onmouseover="this.style.background=\'#f6f7f7\'" onmouseout="this.style.background=\'#fff\'">Edit Campaign</a>';

            // Download Donors
            echo '<a href="' . esc_url($export_url) . '" target="_blank" style="display:block; padding:8px 12px; text-decoration:none; color:#1d2327; border-bottom:1px solid #f0f0f1; transition:bg 0.2s;" onmouseover="this.style.background=\'#f6f7f7\'" onmouseout="this.style.background=\'#fff\'">Download Donors</a>';

            // Recalc Stats
            echo '<a href="' . esc_url($recalc_url) . '" style="display:block; padding:8px 12px; text-decoration:none; color:#b32d2e; transition:bg 0.2s;" onmouseover="this.style.background=\'#f6f7f7\'" onmouseout="this.style.background=\'#fff\'">Recalculate Stats</a>';

            echo '</div>';
            echo '</details>';

            // Add a small script to close other details when one is opened (optional but good for UX)
            // But for MVP, native behavior + outside click handling via JS is better. 
            // Simplified: we rely on users closing it or clicking another.
            break;
    }
}
add_action('manage_wpd_campaign_posts_custom_column', 'wpd_campaign_custom_column', 10, 2);

/**
 * Handle Recalculate Stats Action
 */
function wpd_handle_recalc_stats()
{
    if (!isset($_GET['post_id']) || !isset($_GET['_wpnonce'])) {
        return;
    }

    $post_id = intval($_GET['post_id']);
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wpd_recalc_stats_' . $post_id)) {
        wp_die('Invalid nonce');
    }

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    // Force recalculate
    wpd_update_campaign_stats($post_id);

    // Redirect back
    wp_safe_redirect(admin_url('edit.php?post_type=wpd_campaign&updated=true'));
    exit;
}
add_action('admin_post_wpd_recalc_stats', 'wpd_handle_recalc_stats');

/**
 * Make Columns Sortable
 */
function wpd_campaign_sortable_columns($columns)
{
    $columns['stats'] = 'collected_amount';
    $columns['dates'] = 'date';
    return $columns;
}
add_filter('manage_edit-wpd_campaign_sortable_columns', 'wpd_campaign_sortable_columns');

/**
 * Handle Sorting
 */
function wpd_campaign_column_orderby($vars)
{
    if (isset($vars['orderby']) && 'collected_amount' === $vars['orderby']) {
        $vars = array_merge($vars, array(
            'meta_key' => '_wpd_collected_amount',
            'orderby' => 'meta_value_num'
        ));
    }
    return $vars;
}
add_filter('request', 'wpd_campaign_column_orderby');

/**
 * Custom CSS for Columns
 */
function wpd_campaign_admin_css()
{
    global $typenow;
    if ('wpd_campaign' === $typenow) {
        echo '<style>
            .column-thumb { width: 60px; }
            .column-stats { width: 25%; }
            .column-dates { width: 15%; }
            .column-wpd_actions { width: 100px; text-align:right;}
        </style>';
    }
}
add_action('admin_head', 'wpd_campaign_admin_css');
