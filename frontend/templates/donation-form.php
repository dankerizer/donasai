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
                            <input type="radio" name="qurban_package" draggable="false" value="<?php echo esc_attr( $pkg['price'] ); ?>" onclick="selectQurbanPackage(this.value, '<?php echo esc_js($pkg['name']); ?>')"> 
                            <strong><?php echo esc_html( $pkg['name'] ); ?></strong> - Rp <?php echo number_format( (float)$pkg['price'], 0, ',', '.' ); ?>
                        </label>
                    <?php endforeach; ?>
                    
                    <div id="qurban_qty_wrapper" style="display:none; margin-top:10px; padding-top:10px; border-top:1px dashed #ddd;">
                        <label style="font-size:12px;">Jumlah Qurban</label>
                        <input type="number" name="qurban_qty" id="qurban_qty" class="wpd-input" value="1" min="1" onchange="updateQurbanTotal()" oninput="updateQurbanTotal()">
                        
                        <div id="qurban_names_wrapper" style="margin-top:10px;">
                            <label style="font-size:12px;">Nama Pekurban (Opsional)</label>
                            <p style="font-size:10px; color:#666; margin-top:0;">* Jika dikosongkan, akan menggunakan nama donatur utama.</p>
                            <div id="qurban_names_container">
                                <!-- Dynamic Inputs -->
                            </div>
                        </div>
                    </div>
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

        <!-- Recurring Option (Only if not Zakat/Qurban and logged in) -->
        <?php if ( is_user_logged_in() && $type !== 'zakat' && $type !== 'qurban' ) : ?>
		<div class="wpd-form-group wpd-checkbox-group" style="background:#f0fdf4; padding:10px; border-radius:6px; border:1px solid #bbf7d0;">
			<input type="checkbox" name="is_recurring" id="is_recurring" value="1">
			<label for="is_recurring" style="color:#166534; font-weight:600;"><?php _e( 'Jadikan Donasi Rutin (Bulanan)', 'wp-donasi' ); ?></label>
            <p style="font-size:11px; margin-left:25px; margin-top:2px; color:#15803d;">Anda akan mendapatkan notifikasi tagihan setiap bulan.</p>
		</div>
        <?php endif; ?>

		<div class="wpd-form-group wpd-checkbox-group">
			<input type="checkbox" name="is_anonymous" id="is_anonymous" value="1">
			<label for="is_anonymous"><?php _e( 'Sembunyikan nama saya (Hamba Allah)', 'wp-donasi' ); ?></label>
		</div>

		<button type="submit" class="wpd-btn-submit">
			<?php _e( 'Lanjut Pembayaran', 'wp-donasi' ); ?>
		</button>
	</form>

	<script>
		var currentQurbanPrice = 0;

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
		
		function selectQurbanPackage(price, name) {
		    currentQurbanPrice = parseFloat(price);
		    document.getElementById('qurban_qty_wrapper').style.display = 'block';
		    updateQurbanTotal();
		}
		
		function updateQurbanTotal() {
		    var qty = parseInt(document.getElementById('qurban_qty').value) || 1;
		    if(qty < 1) qty = 1;
		    
		    var total = currentQurbanPrice * qty;
		    document.getElementById('wpd-amount-input').value = total;
		    
		    renderQurbanNames(qty);
		}
		
		function renderQurbanNames(qty) {
		    var container = document.getElementById('qurban_names_container');
		    // We want to preserve existing values if user increases qty
		    // But simple approach: check current inputs, map values, re-render
		    var existingInputs = container.querySelectorAll('input');
		    var values = [];
		    existingInputs.forEach(function(input) {
		        values.push(input.value);
		    });
		    
		    container.innerHTML = '';
		    
		    for (var i = 0; i < qty; i++) {
		        var val = values[i] || '';
		        var div = document.createElement('div');
		        div.style.marginBottom = '5px';
		        div.innerHTML = '<input type="text" name="qurban_names[]" class="wpd-input" placeholder="Nama Pekurban ' + (i+1) + '" value="' + val + '" style="font-size:13px; padding:6px;">';
		        container.appendChild(div);
		    }
		}
	</script>
</div>
