<?php
/**
 * Email Service
 */

if (!defined('ABSPATH')) {
	exit;
}

class WPD_Email
{
	public static function init()
	{
		add_action('wpd_donation_created', array(__CLASS__, 'send_pending_email'));
		add_action('wpd_donation_completed', array(__CLASS__, 'send_success_email'));
	}

	public static function send_pending_email($donation_id)
	{
		self::send_email($donation_id, 'pending');
	}

	public static function send_success_email($donation_id)
	{
		self::send_email($donation_id, 'complete');
	}

	public static function send_email($donation_id, $type = 'pending')
	{
		global $wpdb;
		if (function_exists('wpd_get_donation')) {
			$donation = wpd_get_donation($donation_id);
		} else {
			$table_donations = $wpdb->prefix . 'wpd_donations';
			$cache_key = 'wpd_donation_' . $donation_id;
			$donation = wp_cache_get($cache_key, 'wpd_donations');

			if (false === $donation) {
				$donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_donations} WHERE id = %d", $donation_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				if ($donation) {
					wp_cache_set($cache_key, $donation, 'wpd_donations', 300);
				}
			}
		}

		if (!$donation || empty($donation->email)) {
			return false;
		}

		$to = $donation->email;
		$blog_name = get_bloginfo('name');

		// Check if Pro Email Generator is available
		if (class_exists('WPD_Pro_Email_Generator')) {
			$generator = new WPD_Pro_Email_Generator();
			$email_type = ($type === 'complete') ? 'success' : 'pending';
			$message = $generator->generate($donation_id, $email_type);
			$subject = $generator->get_subject($donation, $email_type);
		} else {
			// Fallback to default template
			if ($type === 'complete') {
				/* translators: 1: Donation ID, 2: Blog Name */
				$subject = sprintf(__('Pembayaran Diterima #%1$d - %2$s', 'donasai'), $donation->id, $blog_name);
			} else {
				/* translators: 1: Donation ID, 2: Blog Name */
				$subject = sprintf(__('Menunggu Pembayaran #%1$d - %2$s', 'donasai'), $donation->id, $blog_name);
			}
			$message = self::get_email_template($donation, $type);
		}

		$headers = array('Content-Type: text/html; charset=UTF-8');

		return wp_mail($to, $subject, $message, $headers);
	}

	private static function get_email_template($donation, $type)
	{
		$campaign_title = get_the_title($donation->campaign_id);
		ob_start();
		?>
		<!DOCTYPE html>
		<html>

		<head>
			<style>
				body {
					font-family: sans-serif;
					line-height: 1.6;
					color: #333;
				}

				.container {
					max-width: 600px;
					margin: 0 auto;
					padding: 20px;
					border: 1px solid #ddd;
					border-radius: 5px;
				}

				.header {
					background: #f4f4f4;
					padding: 10px;
					text-align: center;
					border-bottom: 1px solid #ddd;
				}

				.content {
					padding: 20px;
				}

				.footer {
					font-size: 0.8em;
					text-align: center;
					color: #777;
					margin-top: 20px;
				}

				.amount {
					font-size: 1.2em;
					font-weight: bold;
					color: #065f46;
				}

				.btn {
					display: inline-block;
					padding: 10px 20px;
					background: #2563eb;
					color: white;
					text-decoration: none;
					border-radius: 4px;
					margin-top: 10px;
				}
			</style>
		</head>

		<body>
			<div class="container">
				<div class="header">
					<?php if ($type === 'complete'): ?>
						<h2 style="color:#059669;"><?php esc_html_e('Pembayaran Berhasil!', 'donasai'); ?></h2>
					<?php else: ?>
						<h2 style="color:#d97706;"><?php esc_html_e('Menunggu Pembayaran', 'donasai'); ?></h2>
					<?php endif; ?>
				</div>
				<div class="content">
					<p><?php
					/* translators: %s: Donor Name */
					printf(esc_html__('Halo %s,', 'donasai'), esc_html($donation->name));
					?></p>

					<?php if ($type === 'complete'): ?>
						<p><?php esc_html_e('Terima kasih! Donasi Anda telah kami terima.', 'donasai'); ?></p>
					<?php else: ?>
						<p><?php esc_html_e('Terima kasih telah melakukan pemesanan donasi. Mohon segera selesaikan pembayaran Anda.', 'donasai'); ?>
						</p>
					<?php endif; ?>

					<p><strong><?php echo esc_html($campaign_title); ?></strong></p>

					<ul>
						<li><?php esc_html_e('Nominal:', 'donasai'); ?> <span class="amount">Rp
								<?php echo esc_html(number_format($donation->amount, 0, ',', '.')); ?></span></li>
						<li><?php esc_html_e('ID Donasi:', 'donasai'); ?> #<?php echo esc_html($donation->id); ?></li>
					</ul>

					<?php if ($type === 'pending' && 'manual' === $donation->payment_method):
						$bank_settings = get_option('wpd_settings_bank', array());
						?>
						<div style="background:#fef3c7; padding:15px; border-radius:5px; margin-top:15px;">
							<strong><?php esc_html_e('Silakan Transfer ke:', 'donasai'); ?></strong><br>
							<?php echo esc_html($bank_settings['bank_name'] ?? ''); ?>:
							<?php echo esc_html($bank_settings['account_number'] ?? ''); ?><br>
							a.n <?php echo esc_html($bank_settings['account_name'] ?? ''); ?>
						</div>
					<?php endif; ?>

					<?php if ($type === 'complete'):
						// Generate persistent token for receipt access
						$token_seed = $donation->id . ($donation->created_at ?? '') . wp_salt('auth');
						$token = hash('sha256', $token_seed);
						$receipt_url = add_query_arg([
							'wpd_receipt' => $donation->id,
							'token' => $token
						], home_url('/'));
						?>
						<p><a href="<?php echo esc_url($receipt_url); ?>"
								class="btn"><?php esc_html_e('Download E-Receipt', 'donasai'); ?></a></p>
					<?php endif; ?>

				</div>
				<div class="footer">
					<p>&copy; <?php echo esc_html(gmdate('Y')); ?> 		<?php echo esc_html(get_bloginfo('name')); ?>.</p>
				</div>
			</div>
		</body>

		</html>
		<?php
		return ob_get_clean();
	}
}
