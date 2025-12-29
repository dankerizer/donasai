<?php
/**
 * Email Service
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Email {
	/**
	 * Send Donation Confirmation Email
	 *
	 * @param int $donation_id
	 * @return bool
	 */
	public static function send_confirmation( $donation_id ) {
		global $wpdb;
		$table_donations = $wpdb->prefix . 'wpd_donations';
		$donation        = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_donations WHERE id = %d", $donation_id ) );

		if ( ! $donation ) {
			return false;
		}

		$to      = $donation->email;
		$subject = sprintf( __( 'Donation Receipt #%d - %s', 'wp-donasi' ), $donation->id, get_bloginfo( 'name' ) );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$message = self::get_email_template( $donation );

		return wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Get Email Template
	 *
	 * @param object $donation
	 * @return string
	 */
	private static function get_email_template( $donation ) {
		$campaign_title = get_the_title( $donation->campaign_id );
		
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<style>
				body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
				.container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
				.header { background: #f4f4f4; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
				.content { padding: 20px; }
				.footer { font-size: 0.8em; text-align: center; color: #777; margin-top: 20px; }
				.amount { font-size: 1.2em; font-weight: bold; color: #065f46; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h2><?php _e( 'Terima Kasih atas Donasi Anda', 'wp-donasi' ); ?></h2>
				</div>
				<div class="content">
					<p><?php printf( __( 'Halo %s,', 'wp-donasi' ), esc_html( $donation->name ) ); ?></p>
					<p><?php _e( 'Terima kasih telah berdonasi untuk campaign:', 'wp-donasi' ); ?></p>
					<p><strong><?php echo esc_html( $campaign_title ); ?></strong></p>
					
					<p><?php _e( 'Detail Donasi:', 'wp-donasi' ); ?></p>
					<ul>
						<li><?php _e( 'Nominal:', 'wp-donasi' ); ?> <span class="amount">Rp <?php echo number_format( $donation->amount, 0, ',', '.' ); ?></span></li>
						<li><?php _e( 'Tanggal:', 'wp-donasi' ); ?> <?php echo date_i18n( get_option( 'date_format' ), strtotime( $donation->created_at ) ); ?></li>
						<li><?php _e( 'Status:', 'wp-donasi' ); ?> <?php echo ucfirst( $donation->status ); ?></li>
					</ul>

					<?php if ( 'pending' === $donation->status && 'manual' === $donation->payment_method ) : 
                        $bank_settings = get_option( 'wpd_settings_bank', array() );
                    ?>
						<div style="background:#fef3c7; padding:15px; border-radius:5px; margin-top:15px;">
							<strong><?php _e( 'Instruksi Pembayaran:', 'wp-donasi' ); ?></strong><br>
							<?php echo esc_html( $bank_settings['bank_name'] ?? '' ); ?> <?php echo esc_html( $bank_settings['account_number'] ?? '' ); ?><br>
                            a.n <?php echo esc_html( $bank_settings['account_name'] ?? '' ); ?>
						</div>
					<?php endif; ?>

				</div>
				<div class="footer">
					<p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
				</div>
			</div>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}
}
