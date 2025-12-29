<?php
/**
 * Donor Dashboard Template
 */

$user_id = get_current_user_id();

if ( ! $user_id ) {
	echo '<p>' . __( 'Please login to view your donations.', 'wp-donasi' ) . '</p>';
	return;
}

global $wpdb;
$table_donations = $wpdb->prefix . 'wpd_donations';
$donations       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_donations WHERE user_id = %d ORDER BY created_at DESC", $user_id ) );
?>

<div class="wpd-donor-dashboard">
	<h3><?php _e( 'Riwayat Donasi Saya', 'wp-donasi' ); ?></h3>

	<?php if ( empty( $donations ) ) : ?>
		<p><?php _e( 'Belum ada donasi.', 'wp-donasi' ); ?></p>
	<?php else : ?>
		<table class="wp-block-table">
			<thead>
				<tr>
					<th><?php _e( 'Tanggal', 'wp-donasi' ); ?></th>
					<th><?php _e( 'Campaign', 'wp-donasi' ); ?></th>
					<th><?php _e( 'Nominal', 'wp-donasi' ); ?></th>
					<th><?php _e( 'Status', 'wp-donasi' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $donations as $donation ) : 
					$campaign_title = get_the_title( $donation->campaign_id );
					?>
					<tr>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $donation->created_at ) ); ?></td>
						<td><a href="<?php echo get_permalink( $donation->campaign_id ); ?>"><?php echo esc_html( $campaign_title ); ?></a></td>
						<td>Rp <?php echo number_format( $donation->amount, 0, ',', '.' ); ?></td>
						<td>
							<span class="wpd-status-badge wpd-status-<?php echo esc_attr( $donation->status ); ?>">
								<?php echo ucfirst( $donation->status ); ?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>

<style>
.wpd-status-badge {
	padding: 4px 8px;
	border-radius: 4px;
	font-size: 0.85em;
	font-weight: bold;
}
.wpd-status-complete { background: #d1fae5; color: #065f46; }
.wpd-status-pending { background: #fef3c7; color: #92400e; }
.wpd-status-failed { background: #fee2e2; color: #991b1b; }
</style>
