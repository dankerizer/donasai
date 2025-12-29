<?php
/**
 * Campaign Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Meta Boxes
 */
function wpd_register_meta_boxes() {
	add_meta_box(
		'wpd_campaign_options',
		__( 'Campaign Options', 'wp-donasi' ),
		'wpd_campaign_options_callback',
		'wpd_campaign',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'wpd_register_meta_boxes' );

/**
 * Meta Box Callback
 */
function wpd_campaign_options_callback( $post ) {
	wp_nonce_field( 'wpd_save_campaign_options', 'wpd_campaign_options_nonce' );

	$target    = get_post_meta( $post->ID, '_wpd_target_amount', true );
	$collected = get_post_meta( $post->ID, '_wpd_collected_amount', true );
	$deadline  = get_post_meta( $post->ID, '_wpd_deadline', true );
	?>
	<div class="wpd-meta-box">
		<p>
			<label for="wpd_target_amount"><strong><?php _e( 'Target Amount (Rp)', 'wp-donasi' ); ?></strong></label><br>
			<input type="number" name="wpd_target_amount" id="wpd_target_amount" value="<?php echo esc_attr( $target ); ?>" class="widefat" style="max-width: 300px;">
		</p>
		
		<p>
			<label for="wpd_collected_amount"><strong><?php _e( 'Collected Amount (Rp)', 'wp-donasi' ); ?></strong></label><br>
			<input type="text" value="<?php echo esc_attr( number_format( (float) $collected, 0, ',', '.' ) ); ?>" class="widefat" style="max-width: 300px;" readonly>
			<span class="description"><?php _e( 'Auto-calculated from donations. Read-only.', 'wp-donasi' ); ?></span>
		</p>

		<p>
			<label for="wpd_deadline"><strong><?php _e( 'Deadline', 'wp-donasi' ); ?></strong></label><br>
			<input type="date" name="wpd_deadline" id="wpd_deadline" value="<?php echo esc_attr( $deadline ); ?>" class="widefat" style="max-width: 300px;">
		</p>
	</div>
	<?php
}

/**
 * Save Meta Box Data
 */
function wpd_save_campaign_options( $post_id ) {
	if ( ! isset( $_POST['wpd_campaign_options_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['wpd_campaign_options_nonce'], 'wpd_save_campaign_options' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['wpd_target_amount'] ) ) {
		update_post_meta( $post_id, '_wpd_target_amount', sanitize_text_field( $_POST['wpd_target_amount'] ) );
	}

	if ( isset( $_POST['wpd_deadline'] ) ) {
		update_post_meta( $post_id, '_wpd_deadline', sanitize_text_field( $_POST['wpd_deadline'] ) );
	}
}
add_action( 'save_post', 'wpd_save_campaign_options' );
