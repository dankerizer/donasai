<?php
/**
 * Donation Form Template
 * 
 * Variables available:
 * $campaign_id (int)
 */

$campaign_id = isset( $campaign_id ) ? $campaign_id : get_the_ID();
?>

<div class="wpd-donation-form-wrapper" id="wpd-form">
	<h3 class="wpd-form-title"><?php _e( 'Donasi Sekarang', 'wp-donasi' ); ?></h3>

	<?php if ( isset( $_GET['donation_success'] ) && $_GET['donation_success'] == 1 ) : ?>
		<div class="wpd-alert wpd-alert-success">
			<?php _e( 'Terima kasih! Donasi Anda telah tercatat. Silakan lakukan pembayaran (Instruksi Transfer akan muncul di sini).', 'wp-donasi' ); ?>
		</div>
	<?php endif; ?>

	<form method="post" action="" class="wpd-form">
		<?php wp_nonce_field( 'wpd_donate_action', 'wpd_donate_nonce' ); ?>
		<input type="hidden" name="wpd_action" value="submit_donation">
		<input type="hidden" name="campaign_id" value="<?php echo esc_attr( $campaign_id ); ?>">

		<!-- Amount Selection -->
		<div class="wpd-form-group">
			<label class="wpd-label"><?php _e( 'Nominal Donasi', 'wp-donasi' ); ?></label>
			<div class="wpd-amount-presets">
				<button type="button" class="wpd-btn-preset" onclick="setWpdAmount(50000)">Rp 50.000</button>
				<button type="button" class="wpd-btn-preset" onclick="setWpdAmount(100000)">Rp 100.000</button>
				<button type="button" class="wpd-btn-preset" onclick="setWpdAmount(200000)">Rp 200.000</button>
				<button type="button" class="wpd-btn-preset" onclick="setWpdAmount(500000)">Rp 500.000</button>
			</div>
			<input type="number" name="amount" id="wpd-amount-input" class="wpd-input" placeholder="Masukkan nominal lain (Rp)" required min="10000">
		</div>

		<!-- Donor Info -->
		<div class="wpd-form-group">
			<label class="wpd-label"><?php _e( 'Nama Lengkap', 'wp-donasi' ); ?></label>
			<input type="text" name="donor_name" class="wpd-input" required>
		</div>

		<div class="wpd-form-group">
			<label class="wpd-label"><?php _e( 'Email / WhatsApp', 'wp-donasi' ); ?></label>
			<input type="text" name="donor_email" class="wpd-input" required placeholder="email@contoh.com atau 0812...">
		</div>

		<div class="wpd-form-group">
			<label class="wpd-label"><?php _e( 'Pesan / Doa (Opsional)', 'wp-donasi' ); ?></label>
			<textarea name="donor_note" class="wpd-textarea" rows="2"></textarea>
		</div>

		<div class="wpd-form-group wpd-checkbox-group">
			<input type="checkbox" name="is_anonymous" id="is_anonymous" value="1">
			<label for="is_anonymous"><?php _e( 'Sembunyikan nama saya (Hamba Allah)', 'wp-donasi' ); ?></label>
		</div>

		<button type="submit" class="wpd-btn-submit">
			<?php _e( 'Lanjut Pembayaran', 'wp-donasi' ); ?>
		</button>
	</form>

	<script>
		function setWpdAmount(amount) {
			document.getElementById('wpd-amount-input').value = amount;
		}
	</script>
</div>
