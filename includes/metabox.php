<?php
/**
 * Campaign Meta Boxes
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register Meta Boxes
 */
function donasai_register_meta_boxes()
{
	add_meta_box(
		'donasai_campaign_options',
		__('Campaign Options', 'donasai'),
		'donasai_campaign_options_callback',
		'donasai_campaign',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'donasai_register_meta_boxes');

/**
 * Enqueue Admin Scripts
 */
function donasai_admin_scripts($hook) {
    global $post;
    if (($hook == 'post-new.php' || $hook == 'post.php') && $post && $post->post_type == 'donasai_campaign') {
         wp_enqueue_script('donasai-metabox', DONASAI_PLUGIN_URL . 'includes/admin/assets/campaign-metabox.js', array(), DONASAI_VERSION, true);
         
         $packages = get_post_meta($post->ID, '_donasai_packages', true);
         $packages_array = json_decode($packages, true);
         wp_localize_script('donasai-metabox', 'donasai_packages_data', is_array($packages_array) ? $packages_array : []);
    }
}
add_action('admin_enqueue_scripts', 'donasai_admin_scripts');

/**
 * Meta Box Callback
 */
function donasai_campaign_options_callback($post)
{
	wp_nonce_field('donasai_save_campaign_options', 'donasai_campaign_options_nonce');

	$target = get_post_meta($post->ID, '_donasai_target_amount', true);
	$collected = get_post_meta($post->ID, '_donasai_collected_amount', true);
	$deadline = get_post_meta($post->ID, '_donasai_deadline', true);
	?>
	<div class="donasai-meta-box">
		<p>
			<label
				for="donasai_target_amount"><strong><?php esc_html_e('Target Amount (Rp)', 'donasai'); ?></strong></label><br>
			<input type="number" name="donasai_target_amount" id="donasai_target_amount" value="<?php echo esc_attr($target); ?>"
				class="widefat" style="max-width: 300px;">
		</p>

		<p>
			<label
				for="donasai_collected_amount"><strong><?php esc_html_e('Collected Amount (Rp)', 'donasai'); ?></strong></label><br>
			<input type="text" value="<?php echo esc_attr(number_format((float) $collected, 0, ',', '.')); ?>"
				class="widefat" style="max-width: 300px;" readonly>
			<span class="description"><?php esc_html_e('Auto-calculated from donations. Read-only.', 'donasai'); ?></span>
		</p>

		<p>
			<label for="donasai_deadline"><strong><?php esc_html_e('Deadline', 'donasai'); ?></strong></label><br>
			<input type="date" name="donasai_deadline" id="donasai_deadline" value="<?php echo esc_attr($deadline); ?>"
				class="widefat" style="max-width: 300px;">
		</p>

		<hr>

		<?php
		$type = get_post_meta($post->ID, '_donasai_type', true);
		$pixels = get_post_meta($post->ID, '_donasai_pixel_ids', true);
		$whatsapp = get_post_meta($post->ID, '_donasai_whatsapp_settings', true);

		if (!is_array($pixels))
			$pixels = [];
		if (!is_array($whatsapp))
			$whatsapp = [];
		?>

		<p>
			<label for="donasai_type"><strong><?php esc_html_e('Campaign Type', 'donasai'); ?></strong></label><br>
			<select name="donasai_type" id="donasai_type" class="widefat" style="max-width: 300px;">
				<option value="donation" <?php selected($type, 'donation'); ?>>General Donation</option>
				<option value="zakat" <?php selected($type, 'zakat'); ?>>Zakat (Calculator)</option>
				<option value="qurban" <?php selected($type, 'qurban'); ?>>Qurban (Packages)</option>
				<option value="wakaf" <?php selected($type, 'wakaf'); ?>>Wakaf</option>
			</select>
		</p>

		<div id="donasai_packages_wrapper"
			style="<?php echo $type !== 'qurban' ? 'display:none;' : ''; ?>; margin-top:20px; background:#f0f0f1; padding:15px; border-radius:5px;">
			<h4 style="margin-top:0;">Qurban Packages</h4>
			<p class="description">Add packages for donors to choose from.</p>

			<div id="donasai_packages_container"></div>

			<button type="button" class="button" onclick="donasai_add_package()">+ Add Package</button>

			<!-- Hidden input to store the JSON -->
			<?php $packages = get_post_meta($post->ID, '_donasai_packages', true); ?>
			<textarea name="donasai_packages" id="donasai_packages_json"
				style="display:none;"><?php echo esc_textarea($packages); ?></textarea>
		</div>

		<?php
		$pro_accounts = get_option('donasai_pro_bank_accounts', []);
		$license_status = get_option('donasai_pro_license_status'); // Or check define DONASAI_PRO_VERSION
	
		if (defined('DONASAI_PRO_VERSION') && !empty($pro_accounts)) {
			$campaign_banks = get_post_meta($post->ID, '_donasai_campaign_banks', true);
			if (!is_array($campaign_banks))
				$campaign_banks = [];
			?>
			<hr>
			<h4><?php esc_html_e('Bank Accounts (Manual Transfer)', 'donasai'); ?></h4>
			<p class="description">
				<?php esc_html_e('Select which bank accounts to display for this campaign. Leave empty to use global defaults.', 'donasai'); ?>
			</p>
			<div style="background:#fff; border:1px solid #ddd; padding:10px; max-height:150px; overflow-y:auto;">
				<?php foreach ($pro_accounts as $acc): ?>
					<label style="display:block; margin-bottom:5px;">
						<input type="checkbox" name="donasai_campaign_banks[]" value="<?php echo esc_attr($acc['id']); ?>" <?php checked(in_array($acc['id'], $campaign_banks)); ?>>
						<strong><?php echo esc_html($acc['bank_name']); ?></strong> -
						<?php echo esc_html($acc['account_number']); ?>
						<?php if (!empty($acc['is_default']))
							echo '<span style="font-size:10px; background:#eee; padding:2px 4px; border-radius:3px; margin-left:5px;">Default</span>'; ?>
					</label>
				<?php endforeach; ?>
			</div>
			<?php
		}
		?>

		<?php if (defined('DONASAI_PRO_VERSION')): ?>


			<h4>WhatsApp Support (Flying Button)</h4>
			<p>
				<label for="donasai_wa_number">Number (e.g. 6281...)</label><br>
				<input type="text" name="donasai_whatsapp_settings[number]" id="donasai_wa_number"
					value="<?php echo esc_attr($whatsapp['number'] ?? ''); ?>" class="widefat" style="max-width: 300px;">
			</p>
			<p>
				<label for="donasai_wa_message">Default Message</label><br>
				<textarea name="donasai_whatsapp_settings[message]" id="donasai_wa_message" class="widefat"
					style="max-width: 300px;"><?php echo esc_textarea($whatsapp['message'] ?? ''); ?></textarea>
			</p>
			</p>
		<?php endif; ?>

		<?php do_action('donasai_campaign_metabox_end', $post); ?>
	</div>
	<?php
}

/**
 * Save Meta Box Data
 */
function donasai_save_campaign_options($post_id)
{
	if (!isset($_POST['donasai_campaign_options_nonce'])) {
		return;
	}

	if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['donasai_campaign_options_nonce'])), 'donasai_save_campaign_options')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (isset($_POST['donasai_target_amount'])) {
		update_post_meta($post_id, '_donasai_target_amount', sanitize_text_field(wp_unslash($_POST['donasai_target_amount'])));
	}

	if (isset($_POST['donasai_deadline'])) {
		update_post_meta($post_id, '_donasai_deadline', sanitize_text_field(wp_unslash($_POST['donasai_deadline'])));
	}

	if (isset($_POST['donasai_type'])) {
		update_post_meta($post_id, '_donasai_type', sanitize_text_field(wp_unslash($_POST['donasai_type'])));
	}

	if (isset($_POST['donasai_packages'])) {
		// Save as raw JSON string, but maybe validate json? For MVP just sanitize textarea
		// sanitize_textarea_field sends it as string.
		update_post_meta($post_id, '_donasai_packages', sanitize_textarea_field(wp_unslash($_POST['donasai_packages'])));
	}





	if (isset($_POST['donasai_whatsapp_settings']) && is_array($_POST['donasai_whatsapp_settings'])) {
		$wa_settings = wp_unslash($_POST['donasai_whatsapp_settings']);
		$wa = array();
		foreach ($wa_settings as $key => $value) {
			$clean_key = sanitize_key($key);
			// Message might need textarea sanitization
			if ($clean_key === 'message') {
				$wa[$clean_key] = sanitize_textarea_field($value);
			} else {
				$wa[$clean_key] = sanitize_text_field($value);
			}
		}
		update_post_meta($post_id, '_donasai_whatsapp_settings', $wa);
	}

	// Save Campaign Banks
	if (defined('DONASAI_PRO_VERSION')) {
		if (isset($_POST['donasai_campaign_banks']) && is_array($_POST['donasai_campaign_banks'])) {
			$banks_post = wp_unslash($_POST['donasai_campaign_banks']);
			$banks = array_map(function ($val) {
				return sanitize_text_field($val);
			}, $banks_post);
			update_post_meta($post_id, '_donasai_campaign_banks', $banks);
		} else {
			// If not set but defined DONASAI_PRO_VERSION (and nonce valid), it means user unchecked all.
			delete_post_meta($post_id, '_donasai_campaign_banks');
		}
	}
}
add_action('save_post', 'donasai_save_campaign_options');
