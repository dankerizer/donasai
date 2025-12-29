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
    
    <style>
        .wpd-form-title { margin-top:0; margin-bottom:20px; font-size:22px; font-weight:700; color:#1f2937; text-align:center; }
        .wpd-input, .wpd-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 5px;
            background: #fff;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .wpd-input:focus, .wpd-textarea:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .wpd-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .wpd-form-group {
            margin-bottom: 20px;
        }
        .wpd-amount-presets {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .wpd-btn-preset {
            background: white;
            border: 1px solid #d1d5db;
            color: #4b5563;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 14px;
        }
        .wpd-btn-preset:hover {
            border-color: #3b82f6;
            color: #2563eb;
            background: #eff6ff;
        }
        .wpd-btn-submit {
            display: block;
            width: 100%;
            padding: 16px;
            background: #ec4899; /* Pink to match reference or Brand Color */
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 30px;
            box-shadow: 0 4px 6px -1px rgba(236, 72, 153, 0.3);
        }
        .wpd-btn-submit:hover {
            background: #db2777;
        }
        .wpd-checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .wpd-checkbox-group input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .wpd-checkbox-group label {
            cursor: pointer;
            font-size: 14px;
            color: #4b5563;
        }
        .wpd-alert-success {
            background: #ecfdf5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #a7f3d0;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>

	<h3 class="wpd-form-title"><?php _e( 'Donasi Sekarang', 'wp-donasi' ); ?></h3>

	<?php if ( isset( $_GET['donation_success'] ) && $_GET['donation_success'] == 1 ) : ?>
		<div class="wpd-alert-success">
			<?php _e( 'Terima kasih! Donasi Anda telah tercatat. Silakan lakukan pembayaran (Instruksi Transfer akan muncul di atas).', 'wp-donasi' ); ?>
		</div>
	<?php endif; ?>

	<?php
        $settings = get_option('wpd_settings_donation', []);
        $gen_settings = get_option('wpd_settings_general', []);
        
        $min_amount = $settings['min_amount'] ?? 10000;
        $anon_label = $settings['anonymous_label'] ?? 'Hamba Allah';
        $presets_str = $settings['presets'] ?? '50000,100000,200000,500000';
        $presets = array_map('intval', explode(',', $presets_str));
        $remove_branding = ! empty( $gen_settings['remove_branding'] );
    ?>
    
    <form method="post" action="" class="wpd-form">
		<?php wp_nonce_field( 'wpd_donate_action', 'wpd_donate_nonce' ); ?>
		<input type="hidden" name="wpd_action" value="submit_donation">
		<input type="hidden" name="campaign_id" value="<?php echo esc_attr( $campaign_id ); ?>">

		<!-- Amount Selection -->
		<div class="wpd-form-group">
			<label class="wpd-label"><?php _e( 'Nominal Donasi', 'wp-donasi' ); ?></label>
			
			<?php if ( $type === 'zakat' ) : ?>
                <!-- Zakat Calculator -->
                <div style="background:#f9fafb; padding:20px; border-radius:10px; border:1px solid #e5e7eb; margin-bottom:15px;">
                    <label class="wpd-label" style="margin-top:0; color:#2563eb;">Kalkulator Zakat</label>
                    <select id="zakat_type" class="wpd-input" onchange="toggleZakatType(this.value)">
                        <option value="maal">Zakat Maal (Harta)</option>
                        <option value="income">Zakat Penghasilan</option>
                    </select>
                    
                    <div id="zakat_wealth_row" style="margin-top:15px;">
                        <label style="font-size:13px; color:#6b7280; display:block; margin-bottom:5px;">Total Harta (Rp)</label>
                        <input type="number" id="zakat_wealth" class="wpd-input" oninput="calculateZakat()" placeholder="0">
                    </div>
                    
                    <div id="zakat_income_row" style="display:none; margin-top:15px;">
                        <label style="font-size:13px; color:#6b7280; display:block; margin-bottom:5px;">Penghasilan Bulanan (Rp)</label>
                        <input type="number" id="zakat_income" class="wpd-input" oninput="calculateZakat()" placeholder="0">
                    </div>
                </div>
            <?php elseif ( $type === 'qurban' && ! empty( $packages ) ) : ?>
                <!-- Qurban Packages -->
                 <div class="wpd-packages-list" style="margin-bottom:15px;">
                    <p style="font-size:13px; color:#6b7280; margin-bottom:10px;">Pilih Hewan Qurban:</p>
                    <?php foreach ( $packages as $pkg ) : ?>
                        <label style="display:block; padding:15px; border:1px solid #ddd; margin-bottom:10px; border-radius:8px; cursor:pointer; background:white; transition:border 0.2s;">
                            <input type="radio" name="qurban_package" draggable="false" value="<?php echo esc_attr( $pkg['price'] ); ?>" onclick="selectQurbanPackage(this.value, '<?php echo esc_js($pkg['name']); ?>')"> 
                            <strong style="color:#111827;"><?php echo esc_html( $pkg['name'] ); ?></strong>
                            <div style="color:#059669; font-weight:600;">Rp <?php echo number_format( (float)$pkg['price'], 0, ',', '.' ); ?></div>
                        </label>
                    <?php endforeach; ?>
                    
                    <div id="qurban_qty_wrapper" style="display:none; margin-top:15px; padding-top:15px; border-top:1px dashed #ddd;">
                        <label class="wpd-label">Jumlah Qurban</label>
                        <input type="number" name="qurban_qty" id="qurban_qty" class="wpd-input" value="1" min="1" onchange="updateQurbanTotal()" oninput="updateQurbanTotal()">
                        
                        <div id="qurban_names_wrapper" style="margin-top:15px;">
                            <label class="wpd-label">Nama Pekurban (Opsional)</label>
                            <p style="font-size:12px; color:#9ca3af; margin-top:-3px; margin-bottom:10px;">* Jika dikosongkan, akan menggunakan nama donatur utama.</p>
                            <div id="qurban_names_container">
                                <!-- Dynamic Inputs -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Standard Presets -->
                <div class="wpd-amount-presets">
                    <?php foreach($presets as $p): ?>
                        <button type="button" class="wpd-btn-preset" onclick="setWpdAmount(<?php echo $p; ?>)">Rp <?php echo number_format($p, 0, ',', '.'); ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

			<div style="position:relative;">
                <span style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#9ca3af; font-weight:600;">Rp</span>
			    <input type="number" name="amount" id="wpd-amount-input" class="wpd-input" style="padding-left:45px; font-weight:bold; color:#111827;" placeholder="<?php echo $type === 'zakat' ? '0' : 'Masukkan nominal lainnya'; ?>" required min="<?php echo esc_attr($min_amount); ?>" <?php echo $type === 'zakat' ? 'readonly' : ''; ?>>
            </div>
            <?php if($type !== 'zakat'): ?>
                <p style="font-size:12px; color:#6b7280; margin-top:5px;">Minimal donasi: Rp <?php echo number_format($min_amount, 0, ',', '.'); ?></p>
            <?php endif; ?>
		</div>

		<!-- Donor Info -->
		<div style="margin-top:30px;">
            <div class="wpd-form-group">
                <label class="wpd-label"><?php _e( 'Nama Lengkap', 'wp-donasi' ); ?></label>
                <input type="text" name="donor_name" class="wpd-input" required placeholder="Nama Anda">
            </div>

            <div class="wpd-form-group">
                <label class="wpd-label"><?php _e( 'Email / WhatsApp', 'wp-donasi' ); ?></label>
                <input type="text" name="donor_email" class="wpd-input" required placeholder="nomor wa atau email">
            </div>

            <div class="wpd-form-group">
                <label class="wpd-label"><?php _e( 'Pesan / Doa (Opsional)', 'wp-donasi' ); ?></label>
                <textarea name="donor_note" class="wpd-textarea" rows="3" placeholder="Tulis doa atau pesan dukungan..."></textarea>
            </div>
        </div>

		<div class="wpd-form-group wpd-checkbox-group">
			<input type="checkbox" name="is_anonymous" id="is_anonymous" value="1">
			<label for="is_anonymous"><?php printf( __( 'Sembunyikan nama saya (%s)', 'wp-donasi' ), esc_html($anon_label) ); ?></label>
		</div>

		<button type="submit" class="wpd-btn-submit">
			<?php _e( 'Lanjut Pembayaran', 'wp-donasi' ); ?>
		</button>
        
        <?php if(!$remove_branding): ?>
            <div style="text-align:center; margin-top:20px; font-size:12px; color:#9ca3af;">
                Secure payment powered by wp-donasi
            </div>
        <?php endif; ?>
	</form>

	<script>
		var currentQurbanPrice = 0;

		function setWpdAmount(amount) {
			document.getElementById('wpd-amount-input').value = amount;
            // Prevent default form submission if button is inside form
            // (Buttons are type=button so safe)
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
		    var existingInputs = container.querySelectorAll('input');
		    var values = [];
		    existingInputs.forEach(function(input) {
		        values.push(input.value);
		    });
		    
		    container.innerHTML = '';
		    
		    for (var i = 0; i < qty; i++) {
		        var val = values[i] || '';
		        var div = document.createElement('div');
		        div.style.marginBottom = '8px';
		        div.innerHTML = '<input type="text" name="qurban_names[]" class="wpd-input" placeholder="Nama Pekurban ' + (i+1) + '" value="' + val + '" style="font-size:14px; padding:10px;">';
		        container.appendChild(div);
		    }
		}
	</script>
</div>
