<?php
/**
 * Donation Form Template
 * 
 * Variables available:
 * $campaign_id (int)
 */

$campaign_id = isset( $campaign_id ) ? $campaign_id : get_the_ID();
$type = get_post_meta( $campaign_id, '_wpd_type', true );
$packages = get_post_meta( $campaign_id, '_wpd_packages', true );
$packages = json_decode( $packages, true );
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
			
			<?php if ( $type === 'zakat' ) : ?>
                <!-- Zakat Calculator -->
                <div style="background:#f9fafb; padding:15px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:15px;">
                    <label class="wpd-label" style="margin-top:0;"><strong>Kalkulator Zakat</strong></label>
                    <select id="zakat_type" class="wpd-input" onchange="toggleZakatType(this.value)">
                        <option value="maal">Zakat Maal (Harta)</option>
                        <option value="income">Zakat Penghasilan</option>
                    </select>
                    
                    <div id="zakat_wealth_row" style="margin-top:10px;">
                        <label style="font-size:12px;">Total Harta (Rp)</label>
                        <input type="number" id="zakat_wealth" class="wpd-input" oninput="calculateZakat()">
                    </div>
                    
                    <div id="zakat_income_row" style="display:none; margin-top:10px;">
                        <label style="font-size:12px;">Penghasilan Bulanan (Rp)</label>
                        <input type="number" id="zakat_income" class="wpd-input" oninput="calculateZakat()">
                    </div>
                </div>
            <?php elseif ( $type === 'qurban' && ! empty( $packages ) ) : ?>
                <!-- Qurban Packages -->
                 <div class="wpd-packages-list" style="margin-bottom:15px;">
                    <p style="font-size:12px; margin-bottom:5px;">Pilih Hewan Qurban:</p>
                    <?php foreach ( $packages as $pkg ) : ?>
                        <label style="display:block; padding:10px; border:1px solid #ddd; margin-bottom:5px; border-radius:6px; cursor:pointer;">
                            <input type="radio" name="qurban_package" value="<?php echo esc_attr( $pkg['price'] ); ?>" onclick="setWpdAmount(this.value)"> 
                            <strong><?php echo esc_html( $pkg['name'] ); ?></strong> - Rp <?php echo number_format( (float)$pkg['price'], 0, ',', '.' ); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <!-- Standard Presets -->
                <div class="wpd-amount-presets">
                    <button type="button" class="wpd-btn-preset" onclick="setWpdAmount(50000)">Rp 50.000</button>
                    <button type="button" class="wpd-btn-preset" onclick="setWpdAmount(100000)">Rp 100.000</button>
                    <button type="button" class="wpd-btn-preset" onclick="setWpdAmount(200000)">Rp 200.000</button>
                    <button type="button" class="wpd-btn-preset" onclick="setWpdAmount(500000)">Rp 500.000</button>
                </div>
            <?php endif; ?>

			<input type="number" name="amount" id="wpd-amount-input" class="wpd-input" placeholder="<?php echo $type === 'zakat' ? 'Hasil Perhitungan Zakat' : 'Masukkan nominal lain (Rp)'; ?>" required min="10000" <?php echo $type === 'zakat' ? 'readonly' : ''; ?>>
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

        function toggleZakatType(type) {
             document.getElementById('zakat_wealth_row').style.display = type === 'maal' ? 'block' : 'none';
             document.getElementById('zakat_income_row').style.display = type === 'income' ? 'block' : 'none';
             calculateZakat();
        }

		function calculateZakat() {
		    var type = document.getElementById('zakat_type').value;
		    var amount = 0;
		    
		    if (type === 'maal') {
		        var wealth = parseFloat(document.getElementById('zakat_wealth').value) || 0;
		        amount = wealth * 0.025;
		    } else if (type === 'income') {
		        var income = parseFloat(document.getElementById('zakat_income').value) || 0;
		        amount = income * 0.025;
		    }
		    
		    document.getElementById('wpd-amount-input').value = Math.ceil(amount);
		}
	</script>
</div>
