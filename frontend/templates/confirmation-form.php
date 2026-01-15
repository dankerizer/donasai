<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Donation Confirmation Form template
 * Used by [wpd_confirmation_form]
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wpd-confirmation-wrapper">

    <?php if ($success): ?>
        <div class="wpd-card wpd-text-center" style="border-top: 4px solid var(--wpd-primary, #10b981);">
            <div style="margin-bottom: 1.5rem;">
                <svg style="width:64px; height:64px; color:var(--wpd-primary, #10b981); margin:0 auto;" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 style="margin:0 0 10px; font-size:1.5rem; color:#111827;">Konfirmasi Berhasil</h3>
            <p style="color:#6b7280; margin-bottom:20px;">Terima kasih! Bukti pembayaran Anda telah kami terima dan akan
                segera diverifikasi (max 1x24 jam).</p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="wpd-btn-submit"
                style="display:inline-block; text-decoration:none; max-width:200px; margin:0 auto;">Kembali ke Beranda</a>
        </div>
    <?php else: ?>

        <div class="wpd-card">

            <div class="wpd-form-header">
                <h2>Konfirmasi Donasi</h2>
                <p>Upload bukti transfer untuk memverifikasi donasi Anda.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="wpd-alert wpd-alert-error">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <svg style="width:20px; height:20px; flex-shrink:0;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo esc_html($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="" class="wpd-confirmation-form">
                <?php wp_nonce_field('wpd_confirm_payment'); ?>
                <input type="hidden" name="wpd_confirm_submit" value="1">

                <div class="wpd-form-group">
                    <label class="wpd-label">ID Donasi</label>
                    <div class="wpd-input-group">
                        <span class="wpd-input-icon">#</span>
                        <input type="number" name="donation_id" class="wpd-input wpd-has-icon" placeholder="Contoh: 154"
                            value="<?php echo esc_attr($donation_id_val); ?>" required <?php echo !empty($donation_id_val) ? 'readonly style="background-color:#f9fafb;"' : ''; ?>>
                    </div>
                    <?php if (empty($donation_id_val)): ?>
                        <p class="wpd-helper-text">ID Donasi dapat dilihat di email instruksi.</p>
                    <?php endif; ?>
                </div>

                <div class="wpd-form-group">
                    <label class="wpd-label">Nominal Transfer</label>
                    <div class="wpd-input-group">
                        <span class="wpd-input-icon" style="font-size:0.8rem;">Rp</span>
                        <input type="text" name="amount" class="wpd-input wpd-has-icon" placeholder="100000"
                            value="<?php echo esc_attr($amount_val); ?>" required>
                    </div>
                </div>

                <div class="wpd-form-group">
                    <label class="wpd-label">Dari Bank</label>
                    <div class="wpd-input-group">
                        <span class="wpd-input-icon">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <input type="text" name="sender_bank" class="wpd-input wpd-has-icon"
                            placeholder="BCA / Mandiri / GoPay" required>
                    </div>
                </div>

                <div class="wpd-form-group">
                    <label class="wpd-label">Atas Nama Pengirim</label>
                    <div class="wpd-input-group">
                        <span class="wpd-input-icon">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <input type="text" name="sender_name" class="wpd-input wpd-has-icon"
                            placeholder="Nama Pemilik Rekening" required>
                    </div>
                </div>

                <div class="wpd-form-group">

                    <label class="wpd-label">Bukti Transfer</label>
                    <div class="wpd-upload-box">
                        <input type="file" name="proof_file" id="proof_file" class="wpd-file-input"
                            accept="image/jpeg,image/png,image/jpg" required onchange="wpdUpdateFileName(this)">
                        <label for="proof_file" class="wpd-upload-label">
                            <div class="wpd-upload-icon-wrapper">
                                <svg class="wpd-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <span id="wpd-file-text" style="font-weight:500; color:#4b5563;">Klik untuk upload bukti</span>
                            <span class="wpd-upload-hint">Format: JPG, PNG (Max 2MB)</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="wpd-btn-submit">
                    Kirim Konfirmasi
                </button>
            </form>

        </div>
    <?php endif; ?>
    <?php
    $gen_settings = get_option('wpd_settings_general', []);
    $is_branding_removed = !empty($gen_settings['remove_branding']);

    if (!$is_branding_removed): ?>
        <div class="wpd-powered-by" style="text-align:center; padding: 20px 0; color:#9ca3af; font-size:13px;">
            Powered by <a href="https://donasai.com" target="_blank"
                style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
        </div>
    <?php endif; ?>
</div>