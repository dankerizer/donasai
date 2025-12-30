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
		__( 'Campaign Options', 'donasai' ),
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
			<label for="wpd_target_amount"><strong><?php _e( 'Target Amount (Rp)', 'donasai' ); ?></strong></label><br>
			<input type="number" name="wpd_target_amount" id="wpd_target_amount" value="<?php echo esc_attr( $target ); ?>" class="widefat" style="max-width: 300px;">
		</p>
		
		<p>
			<label for="wpd_collected_amount"><strong><?php _e( 'Collected Amount (Rp)', 'donasai' ); ?></strong></label><br>
			<input type="text" value="<?php echo esc_attr( number_format( (float) $collected, 0, ',', '.' ) ); ?>" class="widefat" style="max-width: 300px;" readonly>
			<span class="description"><?php _e( 'Auto-calculated from donations. Read-only.', 'donasai' ); ?></span>
		</p>

		<p>
			<label for="wpd_deadline"><strong><?php _e( 'Deadline', 'donasai' ); ?></strong></label><br>
			<input type="date" name="wpd_deadline" id="wpd_deadline" value="<?php echo esc_attr( $deadline ); ?>" class="widefat" style="max-width: 300px;">
		</p>

		<hr>

		<?php 
		$type = get_post_meta( $post->ID, '_wpd_type', true ); 
		$pixels = get_post_meta( $post->ID, '_wpd_pixel_ids', true );
		$whatsapp = get_post_meta( $post->ID, '_wpd_whatsapp_settings', true );

		if ( ! is_array( $pixels ) ) $pixels = [];
		if ( ! is_array( $whatsapp ) ) $whatsapp = [];
		?>

		<p>
			<label for="wpd_type"><strong><?php _e( 'Campaign Type', 'donasai' ); ?></strong></label><br>
			<select name="wpd_type" id="wpd_type" class="widefat" style="max-width: 300px;">
				<option value="donation" <?php selected( $type, 'donation' ); ?>>General Donation</option>
				<option value="zakat" <?php selected( $type, 'zakat' ); ?>>Zakat (Calculator)</option>
				<option value="qurban" <?php selected( $type, 'qurban' ); ?>>Qurban (Packages)</option>
				<option value="wakaf" <?php selected( $type, 'wakaf' ); ?>>Wakaf</option>
			</select>
		</p>

		<div id="wpd_packages_wrapper" style="<?php echo $type !== 'qurban' ? 'display:none;' : ''; ?>; margin-top:20px; background:#f0f0f1; padding:15px; border-radius:5px;">
			<h4 style="margin-top:0;">Qurban Packages</h4>
			<p class="description">Add packages for donors to choose from.</p>
			
			<div id="wpd_packages_container"></div>
			
			<button type="button" class="button" onclick="wpdAddPackage()">+ Add Package</button>
			
			<!-- Hidden input to store the JSON -->
			<?php $packages = get_post_meta( $post->ID, '_wpd_packages', true ); ?>
			<textarea name="wpd_packages" id="wpd_packages_json" style="display:none;"><?php echo esc_textarea( $packages ); ?></textarea>
		</div>

		<script>
		// Listener for Type Toggle
		document.getElementById('wpd_type').addEventListener('change', function(e) {
			var wrapper = document.getElementById('wpd_packages_wrapper');
			wrapper.style.display = e.target.value === 'qurban' ? 'block' : 'none';
		});

		// Initialize Packages
		var packagesData = <?php echo $packages ? $packages : '[]'; ?>;
		var container = document.getElementById('wpd_packages_container');

		function renderPackages() {
			container.innerHTML = '';
			packagesData.forEach(function(pkg, index) {
				var row = document.createElement('div');
				row.style.marginBottom = '10px';
				row.style.display = 'flex';
				row.style.gap = '10px';
				row.style.alignItems = 'center';

				row.innerHTML = `
					<input type="text" placeholder="Package Name (e.g. Sapi A)" value="${pkg.name}" onchange="updatePackage(${index}, 'name', this.value)" style="flex:2;">
					<input type="number" placeholder="Price (Rp)" value="${pkg.price}" onchange="updatePackage(${index}, 'price', this.value)" style="flex:1;">
					<button type="button" class="button" onclick="removePackage(${index})" style="color:#b32d2e; border-color:#b32d2e;">&times;</button>
				`;
				container.appendChild(row);
			});
			updateJson();
		}

		function wpdAddPackage() {
			packagesData.push({name: '', price: ''});
			renderPackages();
		}

		function removePackage(index) {
			packagesData.splice(index, 1);
			renderPackages();
		}

		function updatePackage(index, key, value) {
			packagesData[index][key] = value;
			updateJson();
		}

		function updateJson() {
			document.getElementById('wpd_packages_json').value = JSON.stringify(packagesData);
		}

		// Initial Render
		renderPackages();
		</script>

		<h4>Marketing Pixels</h4>
		<p>
			<label for="wpd_pixel_fb">Facebook Pixel ID</label><br>
			<input type="text" name="wpd_pixel_ids[fb]" id="wpd_pixel_fb" value="<?php echo esc_attr( $pixels['fb'] ?? '' ); ?>" class="widefat" style="max-width: 300px;">
		</p>
		<p>
			<label for="wpd_pixel_tiktok">TikTok Pixel ID</label><br>
			<input type="text" name="wpd_pixel_ids[tiktok]" id="wpd_pixel_tiktok" value="<?php echo esc_attr( $pixels['tiktok'] ?? '' ); ?>" class="widefat" style="max-width: 300px;">
		</p>

		<h4>WhatsApp Support (Flying Button)</h4>
		<p>
			<label for="wpd_wa_number">Number (e.g. 6281...)</label><br>
			<input type="text" name="wpd_whatsapp_settings[number]" id="wpd_wa_number" value="<?php echo esc_attr( $whatsapp['number'] ?? '' ); ?>" class="widefat" style="max-width: 300px;">
		</p>
		<p>
			<label for="wpd_wa_message">Default Message</label><br>
			<textarea name="wpd_whatsapp_settings[message]" id="wpd_wa_message" class="widefat" style="max-width: 300px;"><?php echo esc_textarea( $whatsapp['message'] ?? '' ); ?></textarea>
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

	if ( isset( $_POST['wpd_type'] ) ) {
		update_post_meta( $post_id, '_wpd_type', sanitize_text_field( $_POST['wpd_type'] ) );
	}

	if ( isset( $_POST['wpd_packages'] ) ) {
		// Save as raw JSON string, but maybe validate json? For MVP just sanitize textarea
		// sanitize_textarea_field sends it as string.
		update_post_meta( $post_id, '_wpd_packages', sanitize_textarea_field( $_POST['wpd_packages'] ) );
	}

	if ( isset( $_POST['wpd_pixel_ids'] ) && is_array( $_POST['wpd_pixel_ids'] ) ) {
		$pixels = array_map( 'sanitize_text_field', $_POST['wpd_pixel_ids'] );
		update_post_meta( $post_id, '_wpd_pixel_ids', $pixels );
	}

	if ( isset( $_POST['wpd_whatsapp_settings'] ) && is_array( $_POST['wpd_whatsapp_settings'] ) ) {
		$wa = array_map( 'sanitize_text_field', $_POST['wpd_whatsapp_settings'] );
		update_post_meta( $post_id, '_wpd_whatsapp_settings', $wa );
	}
}
add_action( 'save_post', 'wpd_save_campaign_options' );
