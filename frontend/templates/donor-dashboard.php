<?php
/**
 * Donor Dashboard Template
 */

$user_id = get_current_user_id();

if ( ! $user_id ) {
	echo '<p>' . __( 'Please login to view your donations.', 'donasai' ) . '</p>';
	return;
}

global $wpdb;
$table_donations = $wpdb->prefix . 'wpd_donations';
$donations       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_donations WHERE user_id = %d ORDER BY created_at DESC", $user_id ) );
?>

<div class="wpd-donor-dashboard">
	<h3><?php _e( 'Riwayat Donasi Saya', 'donasai' ); ?></h3>

	<?php if ( empty( $donations ) ) : ?>
		<div style="background:#f9fafb; padding:40px; text-align:center; border-radius:8px; border:1px solid #e5e7eb;">
			<p style="color:#6b7280; font-size:16px; margin-bottom:20px;"><?php _e( 'Belum ada riwayat donasi.', 'donasai' ); ?></p>
			<a href="<?php echo home_url('/campaigns'); ?>" class="button" style="background:#2563eb; color:white; padding:10px 20px; text-decoration:none; border-radius:6px;"><?php _e( 'Mulai Berdonasi', 'donasai' ); ?></a>
		</div>
	<?php else : ?>
		<div style="overflow-x:auto;">
			<table class="wpd-table" style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
				<thead style="background:#f9fafb; color:#374151;">
					<tr>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">#ID</th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Tanggal', 'donasai' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Campaign', 'donasai' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Nominal', 'donasai' ); ?></th>
						<th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Status', 'donasai' ); ?></th>
						<th style="padding:12px 16px; text-align:right; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Aksi', 'donasai' ); ?></th>
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
									<a href="<?php echo home_url( '/?wpd_receipt=' . $donation->id ); ?>" target="_blank" class="button-small" style="font-size:12px; color:#2563eb; text-decoration:none; margin-right:5px;">Receipt</a>
                                    <a href="<?php echo get_permalink( $donation->campaign_id ); ?>" class="button-small" style="font-size:12px; color:#4b5563; text-decoration:none;">View</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

<div class="wpd-subscription-dashboard" style="margin-top:40px;">
    <h3><?php _e( 'Langganan Rutin Saya', 'donasai' ); ?></h3>

    <?php 
    $table_subs = $wpdb->prefix . 'wpd_subscriptions';
    $subscriptions = $wpdb->get_results( $wpdb->prepare( 
        "SELECT s.*, p.post_title as campaign_title 
         FROM $table_subs s
         JOIN {$wpdb->posts} p ON s.campaign_id = p.ID
         WHERE s.user_id = %d 
         ORDER BY s.created_at DESC", 
        $user_id 
    ) );
    ?>

    <?php if ( empty( $subscriptions ) ) : ?>
        <p style="color:#6b7280; font-size:14px;"><?php _e( 'Belum ada donasi rutin aktif.', 'donasai' ); ?></p>
    <?php else : ?>
        <div style="overflow-x:auto;">
            <table class="wpd-table" style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                <thead style="background:#f9fafb; color:#374151;">
                    <tr>
                        <th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">#ID</th>
                        <th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Campaign', 'donasai' ); ?></th>
                        <th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Nominal', 'donasai' ); ?></th>
                        <th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Jadwal', 'donasai' ); ?></th>
                        <th style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Status', 'donasai' ); ?></th>
                        <th style="padding:12px 16px; text-align:right; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;"><?php _e( 'Aksi', 'donasai' ); ?></th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <?php foreach ( $subscriptions as $sub ) : ?>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">#<?php echo $sub->id; ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <a href="<?php echo get_permalink( $sub->campaign_id ); ?>" style="color:#2563eb; text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html( $sub->campaign_title ); ?></a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">Rp <?php echo number_format( $sub->amount, 0, ',', '.' ); ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                                / <?php echo ucfirst( $sub->frequency ); ?><br>
                                <span style="font-size:11px; color:#6b7280;">Berikutnya: <?php echo date_i18n( 'd M Y', strtotime( $sub->next_payment_date ) ); ?></span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <span class="wpd-status-badge wpd-status-<?php echo esc_attr( $sub->status ); ?>">
                                    <?php echo ucfirst( $sub->status ); ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
                                <?php if ( 'active' === $sub->status ) : ?>
                                    <button onclick="wpdCancelSub(<?php echo $sub->id; ?>)" class="button-small" style="background:#fee2e2; color:#991b1b; border:none; padding:4px 8px; border-radius:4px; font-size:11px; cursor:pointer;"><?php _e( 'Batalkan', 'donasai' ); ?></button>
                                <?php else: ?>
                                    <span style="color:#9ca3af; font-size:11px;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <script>
    function wpdCancelSub(id) {
        if(!confirm('<?php _e("Yakin ingin membatalkan donasi rutin ini?", "donasai"); ?>')) return;
        
        var nonce = '<?php echo wp_create_nonce( 'wp_rest' ); ?>';
        fetch('/wp-json/wpd/v1/subscriptions/' + id + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Berhasil dibatalkan.');
                location.reload();
            } else {
                alert('Gagal membatalkan.');
            }
        });
    }
    </script>
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
