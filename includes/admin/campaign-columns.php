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
function donasai_campaign_columns($columns)
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
    $new_columns['donasai_actions'] = __('Actions', 'donasai');

    // Optional: Keep date, author if needed, or remove to simplify
    // $new_columns['date'] = $columns['date']; 

    return $new_columns;
}
add_filter('manage_donasai_campaign_posts_columns', 'donasai_campaign_columns');

/**
 * Render Custom Columns
 */
function donasai_campaign_custom_column($column, $post_id)
{
    switch ($column) {
        case 'thumb':
            if (has_post_thumbnail($post_id)) {
                echo wp_kses_post(get_the_post_thumbnail($post_id, array(50, 50), array('class' => 'donasai-admin-thumb')));
            } else {
                echo '<span class="donasai-admin-thumb-placeholder"></span>';
            }
            break;

        case 'stats':
            $target = get_post_meta($post_id, '_donasai_target_amount', true);
            $collected = get_post_meta($post_id, '_donasai_collected_amount', true);
            $donors = get_post_meta($post_id, '_donasai_donor_count', true);

            if (!$target)
                $target = 0;
            if (!$collected)
                $collected = 0;
            if (!$donors)
                $donors = 0;

            $percent = $target > 0 ? ($collected / $target) * 100 : 0;
            $text_class = $percent >= 100 ? 'donasai-text-success' : 'donasai-text-primary';
            $bg_class = $percent >= 100 ? 'donasai-bg-success' : 'donasai-bg-primary';

            echo '<div class="donasai-stat-row"><strong>Target:</strong> Rp ' . esc_html(number_format((float) $target, 0, ',', '.')) . '</div>';
            echo '<div class="donasai-stat-row"><strong>Collected:</strong> <span class="' . esc_attr($text_class) . '">' . 'Rp ' . esc_html(number_format((float) $collected, 0, ',', '.')) . '</span></div>';
            echo '<div><strong>Donors:</strong> ' . intval($donors) . '</div>';

            // Progress bar
            echo '<div class="donasai-progress-bar">';
            echo '<div class="donasai-progress-fill ' . esc_attr($bg_class) . '" style="width:' . esc_attr(min(100, $percent)) . '%;"></div>';
            echo '</div>';
            break;

        case 'dates':
            $deadline = get_post_meta($post_id, '_donasai_deadline', true);
            $published = get_the_date('d M Y', $post_id);

            echo '<div><strong>Start:</strong> ' . esc_html($published) . '</div>';
            if ($deadline) {
                $days_left = ceil((strtotime($deadline) - time()) / 86400);
                if ($days_left < 0) {
                    echo '<div class="donasai-text-danger"><strong>End:</strong> ' . esc_html(date_i18n('d M Y', strtotime($deadline))) . ' (Expired)</div>';
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

        case 'donasai_actions':
            // Link to Donation Detail (Filtered by Campaign)
            // Just linking to main donations page with ?campaign_id query (needs implementation in React or standard WC-like filtering)
            // For now, let's link to the standard edit page or a custom export link.

            // Download CSV
            $nonce = wp_create_nonce('wp_rest');
            $export_url = home_url('/wp-json/donasai/v1/export/donations');
            $export_url = add_query_arg(array('campaign_id' => $post_id, '_wpnonce' => $nonce), $export_url);

            // Recalculate Stats Action
            $recalc_nonce = wp_create_nonce('donasai_recalc_stats_' . $post_id);
            $recalc_url = admin_url('admin-post.php');
            $recalc_url = add_query_arg(array(
                'action' => 'donasai_recalc_stats',
                'post_id' => $post_id,
                '_wpnonce' => $recalc_nonce
            ), $recalc_url);

            echo '<details class="donasai-actions-details">';
            echo '<summary class="button button-small">Actions <span class="dashicons dashicons-arrow-down-alt2"></span></summary>';
            echo '<div class="donasai-actions-dropdown">';

            // Edit
            echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '" class="donasai-action-link">Edit Campaign</a>';

            // Download Donors
            echo '<a href="' . esc_url($export_url) . '" target="_blank" class="donasai-action-link">Download Donors</a>';

            // Recalc Stats
            echo '<a href="' . esc_url($recalc_url) . '" class="donasai-action-link is-danger">Recalculate Stats</a>';

            echo '</div>';
            echo '</details>';

            // Add a small script to close other details when one is opened (optional but good for UX)
            // But for MVP, native behavior + outside click handling via JS is better. 
            // Simplified: we rely on users closing it or clicking another.
            break;
    }
}
add_action('manage_donasai_campaign_posts_custom_column', 'donasai_campaign_custom_column', 10, 2);

/**
 * Handle Recalculate Stats Action
 */
function donasai_handle_recalc_stats()
{
    if (!isset($_GET['post_id']) || !isset($_GET['_wpnonce'])) {
        return;
    }

    $post_id = intval($_GET['post_id']);
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'donasai_recalc_stats_' . $post_id)) {
        wp_die('Invalid nonce');
    }

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    // Force recalculate
    donasai_update_campaign_stats($post_id);

    // Redirect back
    wp_safe_redirect(admin_url('edit.php?post_type=donasai_campaign&updated=true'));
    exit;
}
add_action('admin_post_donasai_recalc_stats', 'donasai_handle_recalc_stats');

/**
 * Make Columns Sortable
 */
function donasai_campaign_sortable_columns($columns)
{
    $columns['stats'] = 'collected_amount';
    $columns['dates'] = 'date';
    return $columns;
}
add_filter('manage_edit-donasai_campaign_sortable_columns', 'donasai_campaign_sortable_columns');

/**
 * Handle Sorting
 */
function donasai_campaign_column_orderby($vars)
{
    if (isset($vars['orderby']) && 'collected_amount' === $vars['orderby']) {
        $vars = array_merge($vars, array(
            'meta_key' => '_donasai_collected_amount', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required for sorting campaigns by revenue in admin.
            'orderby' => 'meta_value_num'
        ));
    }
    return $vars;
}
add_filter('request', 'donasai_campaign_column_orderby');

/**
 * Enqueue Admin Assets
 */
function donasai_campaign_admin_enqueue($hook) {
    global $typenow;
    if ('edit.php' === $hook && 'donasai_campaign' === $typenow) {
        wp_enqueue_style('donasai-admin-columns', DONASAI_PLUGIN_URL . 'includes/admin/assets/admin-columns.css', array(), DONASAI_VERSION);
    }
}
add_action('admin_enqueue_scripts', 'donasai_campaign_admin_enqueue');
