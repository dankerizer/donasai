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
		<div style="background:#f9fafb; padding:40px; text-align:center; border-radius:8px; border:1px solid #e5e7eb;">
			<p style="color:#6b7280; font-size:16px; margin-bottom:20px;"><?php _e( 'Belum ada riwayat donasi.', 'wp-donasi' ); ?></p>
			<a href="<?php echo home_url('/campaigns'); ?>" class="button" style="background:#2563eb; color:white; padding:10px 20px; text-decoration:none; border-radius:6px;"><?php _e( 'Mulai Berdonasi', 'wp-donasi' ); ?></a>
		</div>
	<?php else : ?>
		<div style="overflow-x:auto;">
			<table class="wpd-table" style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
				<thead style="background:#f9fafb; color:#374151;">
					<tr>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">#ID</th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Tanggal', 'wp-donasi' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Campaign', 'wp-donasi' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Nominal', 'wp-donasi' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Status', 'wp-donasi' ); ?></th>
						<th style="padding:12px 16px; text-align:right; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Aksi', 'wp-donasi' ); ?></th>
					</tr>
				</thead>
				<tbody style="background:white;">
					<?php foreach ( $donations as $donation ) : 
						$campaign_title = get_the_title( $donation->campaign_id );
						?>
						<tr style="border-bottom:1px solid #e5e7eb;">
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">#<?php echo $donation->id; ?></td>
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827; font-size:14px;"><?php echo date_i18n( 'd M Y', strtotime( $donation->created_at ) ); ?></td>
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
								<a href="<?php echo get_permalink( $donation->campaign_id ); ?>" style="color:#2563eb; text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html( $campaign_title ); ?></a>
							</td>
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">Rp <?php echo number_format( $donation->amount, 0, ',', '.' ); ?></td>
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
								<span class="wpd-status-badge wpd-status-<?php echo esc_attr( $donation->status ); ?>">
									<?php echo ucfirst( $donation->status ); ?>
								</span>
							</td>
							<td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
								<?php if ( 'pending' === $donation->status && 'midtrans' === $donation->payment_method ) : ?>
									<!-- Ideally link to payment -->
									<button disabled class="button-small" style="font-size:11px; padding:4px 8px; background:#e5e7eb; color:#9ca3af; border:none; border-radius:4px;">Pending</button>
								<?php else : ?>
									<a href="<?php echo get_permalink( $donation->campaign_id ); ?>" class="button-small" style="font-size:12px; color:#2563eb; text-decoration:none;">View</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

<style>
.wpd-table tr:last-child td { border-bottom: none; }
.wpd-table tr:hover { background-color: #f9fafb; }
.wpd-status-badge {
	display: inline-block;
	padding: 4px 10px;
	border-radius: 9999px;
	font-size: 0.75rem;
	font-weight: 600;
	text-transform: capitalize;
}
.wpd-status-complete { background: #d1fae5; color: #065f46; }
.wpd-status-pending { background: #fef3c7; color: #92400e; }
.wpd-status-failed { background: #fee2e2; color: #991b1b; }
</style>
