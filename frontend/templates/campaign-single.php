<?php
/**
 * Single Campaign Template
 */

get_header();

$campaign_id = get_the_ID();
$progress    = wpd_get_campaign_progress( $campaign_id );

// Check for Success Message
if ( isset( $_GET['donation_success'] ) && $_GET['donation_success'] == 1 ) {
    $bank_settings = get_option( 'wpd_settings_bank', array() );
    $bank_name = isset($bank_settings['bank_name']) ? $bank_settings['bank_name'] : '';
    $account_number = isset($bank_settings['account_number']) ? $bank_settings['account_number'] : '';
    $account_name = isset($bank_settings['account_name']) ? $bank_settings['account_name'] : '';

    echo '<div class="wpd-success-message" style="background:#d1fae5; color:#065f46; padding:20px; margin:20px 0; border-radius:8px; text-align:center;">';
    echo '<h3>' . __( 'Terima Kasih atas Donasi Anda!', 'wp-donasi' ) . '</h3>';
    echo '<p>' . __( 'Mohon selesaikan pembayaran Anda dengan transfer ke:', 'wp-donasi' ) . '</p>';
    
    if ( ! empty( $bank_name ) && ! empty( $account_number ) ) {
        echo '<div style="background:#fff; padding:15px; border-radius:8px; display:inline-block; margin-top:10px; border:1px solid #ddd;">';
        echo '<strong>' . esc_html( $bank_name ) . '</strong><br>';
        echo '<span style="font-size:1.2em; letter-spacing:1px;">' . esc_html( $account_number ) . '</span><br>';
        echo 'a.n ' . esc_html( $account_name );
        echo '</div>';
    } else {
         echo '<p><em>' . __( 'Silahkan hubungi admin untuk informasi rekening.', 'wp-donasi' ) . '</em></p>';
    }

    echo '<p style="margin-top:15px;"><a href="' . get_permalink() . '" class="button">' . __( 'Kembali ke Campaign', 'wp-donasi' ) . '</a></p>';
    echo '</div>';
}

?>

<div class="wpd-campaign-container">
	<div class="wpd-campaign-layout">
		<!-- Left: Content -->
		<div class="wpd-campaign-content">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="wpd-campaign-thumbnail">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>

			<h1 class="wpd-campaign-title"><?php the_title(); ?></h1>
			
			<div class="wpd-campaign-stats-mobile wpd-hide-desktop">
				<!-- Progress bar for mobile if needed -->
				<?php // duplicate progress bar here or styling logic ?>
			</div>

			<div class="wpd-campaign-body">
				<?php the_content(); ?>
			</div>
		</div>

		<!-- Right: Sidebar / Sticky Form -->
		<div class="wpd-campaign-sidebar">
			<div class="wpd-card">
				<div class="wpd-progress-section">
					<div class="wpd-progress-stats">
						<span class="wpd-collected">Rp <?php echo number_format( $progress['collected'], 0, ',', '.' ); ?></span>
						<span class="wpd-target">terkumpul dari Rp <?php echo number_format( $progress['target'], 0, ',', '.' ); ?></span>
					</div>
					<div class="wpd-progress-bar-bg">
						<div class="wpd-progress-bar-fill" style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%"></div>
					</div>
				</div>

				<div class="wpd-form-embed">
					<?php echo wpd_get_donation_form_html( $campaign_id ); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
?>
