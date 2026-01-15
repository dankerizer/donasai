<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Donation Form Template (Modern Redesign)
 */

$campaign_id = isset($campaign_id) ? $campaign_id : get_the_ID();
$title = get_the_title($campaign_id);
$thumbnail = get_the_post_thumbnail_url($campaign_id, 'medium');
$type = get_post_meta($campaign_id, '_wpd_type', true);
$packages = get_post_meta($campaign_id, '_wpd_packages', true);
$packages = json_decode($packages, true);

// Get User Data
$current_user = wp_get_current_user();
$default_name = $current_user->ID ? $current_user->display_name : '';
$default_email = $current_user->ID ? $current_user->user_email : '';

// Settings
$settings = get_option('wpd_settings_donation', []);
$min_amount = $settings['min_amount'] ?? 10000;
$presets = explode(',', $settings['presets'] ?? '50000,100000,200000,500000');
$presets = array_map('intval', $presets);
$preset_emoji = $settings['preset_emoji'] ?? '💖';

// Layout Settings
$settings_app = get_option('wpd_settings_appearance', []);
// Pro Check for Dark Mode
$is_pro = defined('WPD_PRO_VERSION'); 
$dark_mode = ($settings_app['dark_mode'] ?? false) && $is_pro;

$donation_layout = $settings_app['donation_layout'] ?? 'default';
if (!$is_pro) {
    $donation_layout = 'default';
}

$body_classes = 'wpd-payment-page';
if ($dark_mode) $body_classes .= ' dark';
if ($donation_layout === 'split') $body_classes .= ' wpd-layout-split';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class($body_classes); ?>>

    <div class="wpd-layout-wrapper">

        <!-- Header (Mobile Sticky) -->
        <div class="wpd-header-mobile">
            <a href="<?php echo esc_url(get_permalink($campaign_id)); ?>" class="wpd-back-btn">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="wpd-header-title">Konfirmasi Donasi</div>
        </div>

        <!-- Main Card -->
        <div class="wpd-card">

            <!-- Campaign Summary -->
            <div class="wpd-campaign-summary">
                <?php if ($thumbnail): ?>
                    <img src="<?php echo esc_url($thumbnail); ?>" alt="Campaign" class="wpd-campaign-thumb">
                <?php endif; ?>
                <div class="wpd-campaign-info">
                    <div class="wpd-campaign-label">Anda akan berdonasi untuk program:</div>
                    <h3 class="wpd-campaign-title"><?php echo esc_html($title); ?></h3>
                </div>
            </div>

            <form method="post" id="donationForm">
                <!-- Correct Nonce & Action for Service Handler -->
                <?php wp_nonce_field('wpd_donate_action', 'wpd_donate_nonce'); ?>
                <input type="hidden" name="wpd_action" value="submit_donation">
                <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign_id); ?>">

                <!-- Alert Success -->
                <?php if (isset($_GET['donation_success']) && '1' === sanitize_text_field(wp_unslash($_GET['donation_success']))): // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                    <div class="wpd-alert-success">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Donasi berhasil dicatat! Silakan selesaikan pembayaran.</span>
                    </div>
                <?php endif; ?>

                <!-- SECTION: NOMINAL -->
                <div class="wpd-section ">
                    <?php do_action('wpd_form_before_amount', $campaign_id); ?>
                    <label class="wpd-label-heading">Masukkan Nominal Donasi</label>

                    <?php if ($type === 'zakat'): ?>
                        <!-- Zakat Logic Placeholder (Simplified for Redesign) -->
                        <div class="wpd-callout-blue ">
                            <div class="wpd-form-row">
                                <label>Pilih Jenis Zakat</label>
                                <select id="zakat_type" class="wpd-input" onchange="toggleZakatType(this.value)">
                                    <option value="maal">Zakat Maal</option>
                                    <option value="income">Zakat Penghasilan</option>
                                </select>
                            </div>
                            <div class="wpd-form-row">
                                <!-- Inputs added via JS for simplicity in this view -->
                                <input type="text" id="zakat_calc_input_display" class="wpd-input mt-2"
                                    placeholder="Masukkan jumlah harta / penghasilan" autocomplete="off">
                                <input type="hidden" id="zakat_calc_input">
                            </div>
                        </div>
                    <?php elseif ($type === 'qurban' && !empty($packages)): ?>
                        <!-- Qurban List -->
                        <div class="wpd-qurban-list wpd-form-row">
                            <?php foreach ($packages as $pkg): ?>
                                <label class="wpd-radio-card">
                                    <input type="radio" name="qurban_package" value="<?php echo esc_attr($pkg['price']); ?>"
                                        onclick="selectQurbanPackage(this.value)">
                                    <div class="wpd-radio-check"></div>
                                    <div class="wpd-radio-content">
                                        <div class="wpd-pkg-name"><?php echo esc_html($pkg['name']); ?></div>
                                        <div class="wpd-pkg-price">Rp <?php echo esc_html(number_format($pkg['price'], 0, ',', '.')); ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <!-- Qty Input hidden by default -->
                        <div id="qurban_qty_wrapper" class="wpd-form-row" style="display:none; margin-top:15px;">
                            <label class="wpd-label-sm">Jumlah Hewan Kurban</label>
                            <div class="wpd-qty-control">
                                <button type="button" onclick="changeQty(-1)">-</button>
                                <input type="number" name="qurban_qty" id="qurban_qty" value="1" readonly>
                                <button type="button" onclick="changeQty(1)">+</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Standard Donation Presets -->
                        <div class="wpd-grid-presets">
                            <?php foreach ($presets as $val): ?>
                                <div class="wpd-preset-card" onclick="selectAmount(this, <?php echo esc_attr($val); ?>)">
                                    <div class="wpd-preset-emoji"><?php echo esc_html($preset_emoji); ?></div>
                                    <div class="wpd-preset-val">Rp <?php echo esc_html(number_format($val / 1000, 0)); ?>rb
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="wpd-input-money-wrapper">
                        <span class="wpd-currency">Rp</span>
                        <input type="text" id="amount_display" class="wpd-input-money" placeholder="0"
                            autocomplete="off">
                        <!-- Hidden input for actual submission -->
                        <input type="hidden" name="amount" id="amount" min="<?php echo esc_attr($min_amount); ?>">
                    </div>
                    <div class="wpd-helper-text">Minimal donasi sebesar Rp
                        <?php echo esc_html(number_format($min_amount, 0, ',', '.')); ?>
                    </div>
                </div>

                <div class="wpd-divider"></div>

                <!-- SECTION: IDENTITY -->
                <div class="wpd-section">
                    <div class="wpd-user-option">
                        <?php if (is_user_logged_in()): ?>
                            <div class="wpd-user-profile">
                                <img src="<?php echo esc_url(get_avatar_url($current_user->ID)); ?>" alt="">
                                <div>
                                    <div class="name"><?php echo esc_html($default_name); ?></div>
                                    <div class="email"><?php echo esc_html($default_email); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="wpd-login-prompt">
                                Sudah memiliki akun? <a
                                    href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">Masuk</a>
                                untuk
                                kemudahan berdonasi.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="wpd-form-row">
                        <input type="text" name="donor_name" class="wpd-input" placeholder="Nama Lengkap"
                            value="<?php echo esc_attr($default_name); ?>" required>
                    </div>

                    <div class="wpd-form-row">
                        <input type="text" name="donor_email" class="wpd-input" placeholder="Nomor WhatsApp atau Email"
                            value="<?php echo esc_attr($default_email); ?>" required>
                    </div>

                    <div class="wpd-switch-wrapper">
                        <label class="wpd-switch">
                            <input type="checkbox" name="is_anonymous" value="1">
                            <span class="slider round"></span>
                        </label>
                        <span class="wpd-switch-label">Sembunyikan nama saya (Hamba Allah)</span>
                    </div>

                    <div class="wpd-form-row mt-3">
                        <textarea name="donor_note" class="wpd-textarea"
                            placeholder="Tuliskan doa atau dukungan (opsional)..." rows="2"></textarea>
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

                        if (defined('WPD_PRO_VERSION') && !empty($active_banks)) {
                            $first = true;
                            foreach ($active_banks as $bank) {
                                // If ID exists (Pro), use manual_ID. If not (Legacy), use manual.
                                $p_val = isset($bank['id']) ? 'manual_' . $bank['id'] : 'manual';
                                ?>
                                <label class="wpd-payment-item">
                                    <input type="radio" name="payment_method" value="<?php echo esc_attr($p_val); ?>" <?php echo $first ? 'checked' : ''; ?>>
                                    <div class="wpd-payment-box">
                                        <div class="wpd-payment-icon">🏦</div>
                                        <div class="wpd-payment-details">
                                            <div class="title">Transfer <?php echo esc_html($bank['bank_name']); ?></div>
                                            <div class="desc"><?php echo esc_html($bank['account_number']); ?> a.n
                                                <?php echo esc_html($bank['account_name']); ?>
                                            </div>
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
                        <?php if (wpd_is_gateway_active('midtrans')): ?>
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

                <?php
                // Fee Coverage (Pro Only)
                if (defined('WPD_PRO_VERSION')) {
                    $fee_settings = get_option('wpd_pro_fee_coverage', []);
                    $fee_enabled = !empty($fee_settings['enabled']);
                    $fee_default_checked = !empty($fee_settings['default_checked']);

                    if ($fee_enabled):
                        ?>
                        <div class="wpd-section wpd-fee-coverage-section" id="fee-coverage-section" style="display:none;">
                            <div class="wpd-fee-wrapper">
                                <label class="wpd-fee-checkbox">
                                    <input type="checkbox" name="cover_fee" value="1" id="cover_fee_checkbox" <?php echo $fee_default_checked ? 'checked' : ''; ?>>
                                    <span class="wpd-fee-check-icon"></span>
                                    <div class="wpd-fee-text">
                                        <span class="wpd-fee-label">Saya ingin menanggung biaya admin</span>
                                        <span class="wpd-fee-amount" id="fee_amount_display">Rp 0</span>
                                    </div>
                                </label>
                                <input type="hidden" name="fee_amount" id="fee_amount" value="0">

                                <div class="wpd-fee-summary" id="fee_summary" style="display:none;">
                                    <div class="wpd-fee-row">
                                        <span>Donasi</span>
                                        <span id="base_amount_display">Rp 0</span>
                                    </div>
                                    <div class="wpd-fee-row">
                                        <span>Biaya Admin</span>
                                        <span id="fee_display">Rp 0</span>
                                    </div>
                                    <div class="wpd-fee-row wpd-fee-total">
                                        <span>Total</span>
                                        <span id="total_display">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    endif;
                }
                ?>

                <!-- FOOTER ACTION -->
                <div class="wpd-footer-action">
                    <button type="submit" class="wpd-btn-primary">Lanjutkan Pembayaran</button>
                    <div class="wpd-secure-badge">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Pembayaran Aman & Terenkripsi
                    </div>
                </div>

            </form>

            <?php
            $gen_settings = get_option('wpd_settings_general', []);
            $is_branding_removed = !empty($gen_settings['remove_branding']);

            if (!$is_branding_removed): ?>
                <div class="wpd-powered-by">
                    Powered by <a href="https://donasai.com" target="_blank">Donasai</a>
                </div>
            <?php endif; ?>
            <div id="wpd-toast"></div>
        </div>
    </div>

    <?php if (is_admin_bar_showing()): ?>
        <style>
            .wpd-header-mobile {
                top: 32px;
            }
        </style>
    <?php endif; ?>
    <?php wp_footer(); ?>
</body>

</html>