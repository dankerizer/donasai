<?php
/**
 * Donation Form Template (Modern Redesign)
 */

$campaign_id = isset( $campaign_id ) ? $campaign_id : get_the_ID();
$title = get_the_title($campaign_id);
$thumbnail = get_the_post_thumbnail_url($campaign_id, 'medium');
$type = get_post_meta( $campaign_id, '_wpd_type', true );
$packages = get_post_meta( $campaign_id, '_wpd_packages', true );
$packages = json_decode( $packages, true );

// Get User Data
$current_user = wp_get_current_user();
$default_name = $current_user->ID ? $current_user->display_name : '';
$default_email = $current_user->ID ? $current_user->user_email : '';

// Settings
$settings = get_option('wpd_settings_donation', []);
$min_amount = $settings['min_amount'] ?? 10000;
$presets = explode(',', $settings['presets'] ?? '50000,100000,200000,500000');
$presets = array_map('intval', $presets);

// Layout Settings
$settings_app = get_option('wpd_settings_appearance', []);
$container_width = $settings_app['container_width'] ?? '1100px'; // For donation form, we usually use smaller width, but let's respect the radius.
// Actually, donation form is "standalone" usually, constrained by .wpd-layout-wrapper max-width: 480px.
// But we should respect the radius.
$border_radius = $settings_app['border_radius'] ?? '12px';
$primary_color = $settings_app['brand_color'] ?? '#059669';
$button_color = $settings_app['button_color'] ?? '#ec4899';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        :root {
            --wpd-radius: <?php echo esc_attr($border_radius); ?>;
            --wpd-primary: <?php echo esc_attr($primary_color); ?>;
            --wpd-btn: <?php echo esc_attr($button_color); ?>;
        }
        .wpd-card, .btn, .wpd-radio-card, .wpd-input {
            border-radius: var(--wpd-radius) !important;
        }
    </style>
</head>
<body <?php body_class('wpd-payment-page'); ?>>

<div class="wpd-layout-wrapper">
    
    <!-- Header (Mobile Sticky) -->
    <div class="wpd-header-mobile">
        <a href="<?php echo get_permalink($campaign_id); ?>" class="wpd-back-btn">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="wpd-header-title">Konfirmasi Donasi</div>
    </div>

    <!-- Main Card -->
    <div class="wpd-card">
        
        <!-- Campaign Summary -->
        <div class="wpd-campaign-summary">
            <?php if($thumbnail): ?>
                <img src="<?php echo esc_url($thumbnail); ?>" alt="Campaign" class="wpd-campaign-thumb">
            <?php endif; ?>
            <div class="wpd-campaign-info">
                <div class="wpd-campaign-label">Anda akan berdonasi untuk program:</div>
                <h3 class="wpd-campaign-title"><?php echo esc_html($title); ?></h3>
            </div>
        </div>

        <form method="post" id="donationForm">
            <!-- Correct Nonce & Action for Service Handler -->
            <?php wp_nonce_field( 'wpd_donate_action', 'wpd_donate_nonce' ); ?>
            <input type="hidden" name="wpd_action" value="submit_donation">
            <input type="hidden" name="campaign_id" value="<?php echo esc_attr( $campaign_id ); ?>">

            <!-- Alert Success -->
            <?php if ( isset( $_GET['donation_success'] ) && $_GET['donation_success'] == 1 ) : ?>
                <div class="wpd-alert-success">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Donasi berhasil dicatat! Silakan selesaikan pembayaran.</span>
                </div>
            <?php endif; ?>

            <!-- SECTION: NOMINAL -->
            <div class="wpd-section">
                <?php do_action( 'wpd_form_before_amount', $campaign_id ); ?>
                <label class="wpd-label-heading">Masukkan Nominal Donasi</label>
                
                <?php if ( $type === 'zakat' ) : ?>
                    <!-- Zakat Logic Placeholder (Simplified for Redesign) -->
                     <div class="wpd-callout-blue">
                        <label>Pilih Jenis Zakat</label>
                        <select id="zakat_type" class="wpd-input" onchange="toggleZakatType(this.value)">
                            <option value="maal">Zakat Maal</option>
                            <option value="income">Zakat Penghasilan</option>
                        </select>
                        <!-- Inputs added via JS for simplicity in this view -->
                        <input type="number" id="zakat_calc_input" class="wpd-input mt-2" placeholder="Masukkan jumlah harta / penghasilan" oninput="calculateZakat()">
                    </div>
                <?php elseif ( $type === 'qurban' && !empty($packages) ) : ?>
                    <!-- Qurban List -->
                    <div class="wpd-qurban-list">
                        <?php foreach($packages as $pkg): ?>
                        <label class="wpd-radio-card">
                            <input type="radio" name="qurban_package" value="<?php echo esc_attr($pkg['price']); ?>" onclick="selectQurbanPackage(this.value)">
                            <div class="wpd-radio-check"></div>
                            <div class="wpd-radio-content">
                                <div class="wpd-pkg-name"><?php echo esc_html($pkg['name']); ?></div>
                                <div class="wpd-pkg-price">Rp <?php echo number_format($pkg['price'],0,',','.'); ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <!-- Qty Input hidden by default -->
                    <div id="qurban_qty_wrapper" style="display:none; margin-top:15px;">
                        <label class="wpd-label-sm">Jumlah Hewan Kurban</label>
                        <div class="wpd-qty-control">
                            <button type="button" onclick="changeQty(-1)">-</button>
                            <input type="number" name="qurban_qty" id="qurban_qty" value="1" readonly>
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Standard Donation Presets -->
                    <div class="wpd-grid-presets">
                        <?php foreach($presets as $val): ?>
                        <div class="wpd-preset-card" onclick="selectAmount(this, <?php echo $val; ?>)">
                            <div class="wpd-preset-emoji">💖</div>
                            <div class="wpd-preset-val">Rp <?php echo number_format($val/1000, 0); ?>rb</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="wpd-input-money-wrapper">
                    <span class="wpd-currency">Rp</span>
                    <input type="number" name="amount" id="amount" class="wpd-input-money" placeholder="0" min="<?php echo $min_amount; ?>" required>
                </div>
                <div class="wpd-helper-text">Minimal donasi sebesar Rp <?php echo number_format($min_amount,0,',','.'); ?></div>
            </div>

            <div class="wpd-divider"></div>

            <!-- SECTION: IDENTITY -->
            <div class="wpd-section">
                <div class="wpd-user-option">
                    <?php if ( is_user_logged_in() ) : ?>
                        <div class="wpd-user-profile">
                            <img src="<?php echo get_avatar_url($current_user->ID); ?>" alt="">
                            <div>
                                <div class="name"><?php echo esc_html($default_name); ?></div>
                                <div class="email"><?php echo esc_html($default_email); ?></div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="wpd-login-prompt">
                            Sudah memiliki akun? <a href="<?php echo wp_login_url(get_permalink()); ?>">Masuk</a> untuk kemudahan berdonasi.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="wpd-form-row">
                    <input type="text" name="donor_name" class="wpd-input" placeholder="Nama Lengkap" value="<?php echo esc_attr($default_name); ?>" required>
                </div>
                
                <div class="wpd-form-row">
                    <input type="text" name="donor_email" class="wpd-input" placeholder="Nomor WhatsApp atau Email" value="<?php echo esc_attr($default_email); ?>" required>
                </div>

                <div class="wpd-switch-wrapper">
                    <label class="wpd-switch">
                        <input type="checkbox" name="is_anonymous" value="1">
                        <span class="slider round"></span>
                    </label>
                    <span class="wpd-switch-label">Sembunyikan nama saya (Hamba Allah)</span>
                </div>

                <div class="wpd-form-row mt-3">
                    <textarea name="donor_note" class="wpd-textarea" placeholder="Tuliskan doa atau dukungan (opsional)..." rows="2"></textarea>
                </div>
            </div>

            <div class="wpd-divider"></div>

            <!-- SECTION: PAYMENT METHOD -->
            <div class="wpd-section">
                <label class="wpd-label-heading">Metode Pembayaran</label>
                
                <div class="wpd-payment-list">
                    <!-- Manual Banks Loop -->
                    <?php 
                    $manual_gateway = new WPD_Gateway_Manual(); 
                    $active_banks = $manual_gateway->get_active_banks($campaign_id);
                    
                    if ( ! empty( $active_banks ) ) {
                        $first = true;
                        foreach ( $active_banks as $bank ) {
                            // If ID exists (Pro), use manual_ID. If not (Legacy), use manual.
                            $p_val = isset($bank['id']) ? 'manual_' . $bank['id'] : 'manual';
                            ?>
                            <label class="wpd-payment-item">
                                <input type="radio" name="payment_method" value="<?php echo esc_attr($p_val); ?>" <?php echo $first ? 'checked' : ''; ?>>
                                <div class="wpd-payment-box">
                                    <div class="wpd-payment-icon">🏦</div>
                                    <div class="wpd-payment-details">
                                        <div class="title">Transfer <?php echo esc_html($bank['bank_name']); ?></div>
                                        <div class="desc"><?php echo esc_html($bank['account_number']); ?> a.n <?php echo esc_html($bank['account_name']); ?></div>
                                    </div>
                                    <div class="wpd-check-icon"></div>
                                </div>
                            </label>
                            <?php
                            $first = false;
                        }
                    } else {
                        // Fallback if no banks configured (should rarely happen as active_banks defaults to legacy)
                         ?>
                        <label class="wpd-payment-item">
                            <input type="radio" name="payment_method" value="manual" checked>
                            <div class="wpd-payment-box">
                                <div class="wpd-payment-icon">🏦</div>
                                <div class="wpd-payment-details">
                                    <div class="title">Transfer Bank Manual</div>
                                    <div class="desc">Verifikasi manual dalam 1x24 jam</div>
                                </div>
                                <div class="wpd-check-icon"></div>
                            </div>
                        </label>
                        <?php
                    }
                    ?>

                    <!-- Midtrans -->
                    <?php if( wpd_is_gateway_active('midtrans') ): ?>
                    <label class="wpd-payment-item">
                        <input type="radio" name="payment_method" value="midtrans">
                        <div class="wpd-payment-box">
                            <div class="wpd-payment-icon">⚡</div>
                            <div class="wpd-payment-details">
                                <div class="title">Pembayaran Otomatis</div>
                                <div class="desc">QRIS, E-Wallet, Virtual Account</div>
                            </div>
                            <div class="wpd-badge-instant">Auto</div>
                            <div class="wpd-check-icon"></div>
                        </div>
                    </label>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FOOTER ACTION -->
            <div class="wpd-footer-action">
                <button type="submit" class="wpd-btn-primary">Lanjutkan Pembayaran</button>
                <div class="wpd-secure-badge">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    Pembayaran Aman & Terenkripsi
                </div>
            </div>

        </form>
        
        <?php 
        $gen_settings = get_option('wpd_settings_general', []);
        $is_branding_removed = !empty($gen_settings['remove_branding']);
        
        if ( ! $is_branding_removed ) : ?>
            <div class="wpd-powered-by">
                Powered by <a href="https://donasai.com" target="_blank">Donasai</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Reset & Base */
html, body {
    margin: 0;
    padding: 0;
    background-color: #f3f4f6;
}
.wpd-layout-wrapper {
    background-color: #f3f4f6;
    min-height: 100vh;
    padding-bottom: 90px; /* Space for footer */
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.wpd-card {
    background: white;
    max-width: 550px;
    margin: 0 auto;
    min-height: 100vh; /* Fill on mobile */
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
}
@media(min-width: 640px) {
    .wpd-layout-wrapper { padding: 0px 20px; }
    .wpd-card { min-height: auto; border-radius: 20px; overflow: hidden; }
}

/* Header */
.wpd-header-mobile {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: white;
    border-bottom: 1px solid #f3f4f6;
    position: fixed;
    top: 0;
    z-index: 99;
    width: 100%;
    left: 0;
}
/* Admin Bar Adjustment */
body.admin-bar .wpd-header-mobile { top: 32px; }
@media screen and (max-width: 782px) {
    body.admin-bar .wpd-header-mobile { top: 46px; }
}

.wpd-back-btn {
    color: #374151;
    margin-right: 15px;
    display: flex;
    align-items: center;
    text-decoration: none;
}
.wpd-header-title {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

/* Campaign Summary */
.wpd-campaign-summary {
    padding: 20px 24px;
    background: #fdfdfd;
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #f3f4f6;
}
.wpd-campaign-thumb {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}
.wpd-campaign-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.wpd-campaign-title {
    margin: 2px 0 0 0;
    font-size: 15px;
    line-height: 1.3;
    color: #1f2937;
    font-weight: 700;
}

/* Sections */
.wpd-section {
    padding: 20px 24px;
}
.wpd-divider {
    height: 8px;
    background: #f3f4f6;
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}
.wpd-label-heading {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 16px;
}

/* Presets Grid */
.wpd-grid-presets {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.wpd-preset-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
    position: relative;
    overflow: hidden;
}
.wpd-preset-card:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}
.wpd-preset-card.active {
    border-color: var(--wpd-primary, #2563eb);
    background: var(--wpd-bg-soft, #eff6ff); 
    box-shadow: 0 0 0 1px var(--wpd-primary, #2563eb);
}
.wpd-preset-emoji { font-size: 20px; margin-bottom: 4px; }
.wpd-preset-val { font-weight: 700; color: #374151; font-size: 15px; }

/* Money Input */
.wpd-input-money-wrapper {
    position: relative;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    overflow: hidden;
    background: white;
    transition: all 0.2s;
}
.wpd-input-money-wrapper:focus-within {
    border-color: var(--wpd-primary, #2563eb);
    box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
}
.wpd-currency {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 700;
    color: #9ca3af;
    font-size: 18px;
}
.wpd-input-money {
    width: 100%;
    border: none;
    padding: 16px 16px 16px 50px;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    outline: none;
    box-sizing: border-box;
}
.wpd-helper-text {
    font-size: 13px;
    color: #6b7280;
    margin-top: 8px;
}

/* Inputs */
.wpd-form-row { margin-bottom: 15px; }
.wpd-input, .wpd-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 15px;
    outline: none;
    transition: border-color 0.2s;
    background: #fdfdfd;
    box-sizing: border-box;
    font-family: inherit;
}
.wpd-input:focus, .wpd-textarea:focus {
    background: white;
    border-color: var(--wpd-primary, #2563eb);
}
.mt-3 { margin-top: 15px; }

/* User Profile - FIXED */
.wpd-user-option {
    margin-bottom: 20px;
}
.wpd-user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}
.wpd-user-profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.wpd-user-profile .name { font-weight: 600; font-size: 14px; color: #111827; }
.wpd-user-profile .email { font-size: 12px; color: #6b7280; }
.wpd-login-prompt { font-size: 14px; color: #4b5563; }
.wpd-login-prompt a { color: var(--wpd-primary, #2563eb); font-weight: 600; text-decoration: none; }

/* Switch */
.wpd-switch-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 10px 0 20px 0;
}
.wpd-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}
.wpd-switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #e5e7eb;
    transition: .3s;
    border-radius: 34px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
input:checked + .slider { background-color: var(--wpd-primary, #2563eb); }
input:checked + .slider:before { transform: translateX(20px); }
.wpd-switch-label { font-size: 14px; color: #4b5563; cursor: pointer; }

/* Payment Types */
.wpd-payment-list { display: grid; gap: 12px; }
.wpd-payment-item { display: block; cursor: pointer; }
.wpd-payment-item input { display: none; }
.wpd-payment-box {
    display: flex;
    align-items: center;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    transition: all 0.2s;
}
.wpd-payment-item input:checked + .wpd-payment-box {
    border-color: var(--wpd-primary, #2563eb);
    background: var(--wpd-bg-soft, #f0fdf4);
    box-shadow: 0 0 0 1px var(--wpd-primary, #2563eb);
}
.wpd-payment-icon {
    font-size: 20px;
    margin-right: 15px;
    background: #f3f4f6;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
.wpd-payment-details { flex: 1; }
.wpd-payment-details .title { font-weight: 700; color: #1f2937; font-size: 15px; margin-bottom: 2px; }
.wpd-payment-details .desc { font-size: 12px; color: #6b7280; }
.wpd-badge-instant {
    background: #eff6ff;
    color: #2563eb;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 6px;
    border-radius: 4px;
    margin-right: 10px;
    text-transform: uppercase;
}
.wpd-check-icon {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}
.wpd-payment-item input:checked + .wpd-payment-box .wpd-check-icon {
    border-color: var(--wpd-primary, #2563eb);
    background: var(--wpd-primary, #2563eb);
}
.wpd-payment-item input:checked + .wpd-payment-box .wpd-check-icon::after {
    content: '';
    position: absolute;
    top: 5px; left: 5px;
    width: 6px; height: 6px;
    background: white;
    border-radius: 50%;
}

/* Footer Action - FIXED */
.wpd-footer-action {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: white;
    padding: 16px 20px;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
    z-index: 100;
    text-align: center;
    border-top: 1px solid #f3f4f6;
}
@media(min-width: 640px) {
    .wpd-footer-action {
        position: static;
        box-shadow: none;
        border: none;
        padding: 20px 0 0 0;
    }
}
.wpd-btn-primary {
    display: block;
    width: 100%;
    background: var(--wpd-btn, #2563eb); /* Fallback to blue */
    color: white;
    font-weight: 700;
    font-size: 16px;
    padding: 14px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: transform 0.1s, opacity 0.2s;
    -webkit-appearance: none;
}
.wpd-btn-primary:hover {
    opacity: 0.95;
    background: var(--wpd-btn-hover, #1d4ed8);
}
.wpd-btn-primary:active { transform: scale(0.98); }
.wpd-secure-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 12px;
    color: #9ca3af;
    margin-top: 12px;
}
.wpd-powered-by {
    text-align: center;
    font-size: 13px;
    color: #9ca3af;
    margin-top: 10px;
    padding-bottom: 30px; /* Extra padding for bottom */
}
.wpd-powered-by a {
    color: inherit;
    font-weight: 600;
    text-decoration: none;
}
.wpd-powered-by a:hover {
    text-decoration: underline;
}
</style>

<script>
function selectAmount(card, amount) {
    document.getElementById('amount').value = amount;
    
    // Clear active classes
    document.querySelectorAll('.wpd-preset-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
}

// Qurban & Zakat js stubs for interaction
function toggleZakatType(val) {
    let output = document.getElementById('zakat_calc_input');
    if(val === 'maal') output.placeholder = 'Total Harta (Rp)';
    else output.placeholder = 'Total Penghasilan (Rp)';
}
function calculateZakat() {
    let val = document.getElementById('zakat_calc_input').value;
    document.getElementById('amount').value = Math.round(val * 0.025);
}
function selectQurbanPackage(price) {
    document.querySelector('#qurban_qty_wrapper').style.display = 'block';
    updateQurbanTotal();
}
function changeQty(delta) {
    let input = document.getElementById('qurban_qty');
    let val = parseInt(input.value) + delta;
    if(val < 1) val = 1;
    input.value = val;
    updateQurbanTotal();
}
function updateQurbanTotal() {
    let qty = document.getElementById('qurban_qty').value;
    let price = document.querySelector('input[name="qurban_package"]:checked').value;
    document.getElementById('amount').value = qty * price;
}

// --- MIDTRANS SNAP INTREGRATION ---
<?php
$midtrans = WPD_Gateway_Registry::get_gateway('midtrans');
$snap_active = $midtrans && $midtrans->is_active();
$client_key = $snap_active && method_exists($midtrans, 'get_client_key') ? $midtrans->get_client_key() : '';
$is_prod = $snap_active && method_exists($midtrans, 'is_production') ? $midtrans->is_production() : false;
$snap_url = $is_prod ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';

if( $snap_active && !empty($client_key) ): 
?>
    // Load Snap JS
    const script = document.createElement('script');
    script.src = "<?php echo $snap_url; ?>";
    script.setAttribute('data-client-key', "<?php echo esc_js($client_key); ?>");
    document.body.appendChild(script);

    document.getElementById('donationForm').addEventListener('submit', function(e) {
        // Only hijack if Midtrans is selected
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        if( method !== 'midtrans' ) return;

        e.preventDefault();
        const btn = document.querySelector('.wpd-btn-primary');
        const originalText = btn.innerText;
        btn.innerText = 'Memproses...';
        btn.disabled = true;

        const formData = new FormData(this);
        formData.append('wpd_ajax', '1');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if(res.success) {
                if(res.data.is_midtrans && res.data.snap_token) {
                    window.snap.pay(res.data.snap_token, {
                        onSuccess: function(result){ window.location.href = res.data.redirect_url; },
                        onPending: function(result){ window.location.href = res.data.redirect_url; },
                        onError: function(result){ alert("Payment Failed!"); btn.disabled = false; btn.innerText = originalText; },
                        onClose: function(){ btn.disabled = false; btn.innerText = originalText; }
                    });
                } else {
                    // Fallback redirect
                    if(res.data.redirect_url) window.location.href = res.data.redirect_url;
                }
            } else {
                alert('Error: ' + res.data.message);
                btn.disabled = false;
                btn.innerText = originalText;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });
<?php endif; ?>
</script>

<?php if ( is_admin_bar_showing() ) : ?>
<style>.wpd-header-mobile { top: 32px; } </style>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>