<?php
/**
 * Email Service
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Email {
	public static function init() {
        add_action( 'wpd_donation_created', array( __CLASS__, 'send_pending_email' ) );
        add_action( 'wpd_donation_completed', array( __CLASS__, 'send_success_email' ) );
    }

    public static function send_pending_email( $donation_id ) {
        self::send_email( $donation_id, 'pending' );
    }

    public static function send_success_email( $donation_id ) {
        self::send_email( $donation_id, 'complete' );
    }

	public static function send_email( $donation_id, $type = 'pending' ) {
		global $wpdb;
		$table_donations = $wpdb->prefix . 'wpd_donations';
		$donation        = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_donations WHERE id = %d", $donation_id ) );

		if ( ! $donation || empty( $donation->email ) ) {
			return false;
		}

		$to      = $donation->email;
        $blog_name = get_bloginfo( 'name' );
        
        if ( $type === 'complete' ) {
            $subject = sprintf( __( 'Pembayaran Diterima #%d - %s', 'wp-donasi' ), $donation->id, $blog_name );
        } else {
            $subject = sprintf( __( 'Menunggu Pembayaran #%d - %s', 'wp-donasi' ), $donation->id, $blog_name );
        }
        
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$message = self::get_email_template( $donation, $type );

		return wp_mail( $to, $subject, $message, $headers );
	}

	private static function get_email_template( $donation, $type ) {
		$campaign_title = get_the_title( $donation->campaign_id );
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<style>
				body { font-family: sans-serif; line-height: 1.6; color: #333; }
				.container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
				.header { background: #f4f4f4; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
				.content { padding: 20px; }
				.footer { font-size: 0.8em; text-align: center; color: #777; margin-top: 20px; }
				.amount { font-size: 1.2em; font-weight: bold; color: #065f46; }
                .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
                    <?php if ( $type === 'complete' ) : ?>
					    <h2 style="color:#059669;"><?php _e( 'Pembayaran Berhasil!', 'wp-donasi' ); ?></h2>
                    <?php else : ?>
                        <h2 style="color:#d97706;"><?php _e( 'Menunggu Pembayaran', 'wp-donasi' ); ?></h2>
                    <?php endif; ?>
				</div>
				<div class="content">
					<p><?php printf( __( 'Halo %s,', 'wp-donasi' ), esc_html( $donation->name ) ); ?></p>
					
                    <?php if ( $type === 'complete' ) : ?>
                        <p><?php _e( 'Terima kasih! Donasi Anda telah kami terima.', 'wp-donasi' ); ?></p>
                    <?php else : ?>
                        <p><?php _e( 'Terima kasih telah melakukan pemesanan donasi. Mohon segera selesaikan pembayaran Anda.', 'wp-donasi' ); ?></p>
                    <?php endif; ?>

					<p><strong><?php echo esc_html( $campaign_title ); ?></strong></p>
					
					<ul>
						<li><?php _e( 'Nominal:', 'wp-donasi' ); ?> <span class="amount">Rp <?php echo number_format( $donation->amount, 0, ',', '.' ); ?></span></li>
						<li><?php _e( 'ID Donasi:', 'wp-donasi' ); ?> #<?php echo $donation->id; ?></li>
					</ul>

					<?php if ( $type === 'pending' && 'manual' === $donation->payment_method ) : 
                        $bank_settings = get_option( 'wpd_settings_bank', array() );
                    ?>
						<div style="background:#fef3c7; padding:15px; border-radius:5px; margin-top:15px;">
							<strong><?php _e( 'Silakan Transfer ke:', 'wp-donasi' ); ?></strong><br>
							<?php echo esc_html( $bank_settings['bank_name'] ?? '' ); ?>: <?php echo esc_html( $bank_settings['account_number'] ?? '' ); ?><br>
                            a.n <?php echo esc_html( $bank_settings['account_name'] ?? '' ); ?>
						</div>
					<?php endif; ?>

                    <?php if ( $type === 'complete' ) : ?>
                        <p><a href="<?php echo home_url( '/?wpd_receipt=' . $donation->id ); ?>" class="btn"><?php _e( 'Download E-Receipt', 'wp-donasi' ); ?></a></p>
                    <?php endif; ?>

				</div>
				<div class="footer">
					<p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</p>
				</div>
			</div>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}
}
