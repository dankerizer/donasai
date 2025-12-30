<?php
/**
 * Donation Confirmation Form template
 * Used by [wpd_confirmation_form]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wpd-confirmation-container" style="max-width:500px; margin:0 auto; padding:20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <?php if ( $success ) : ?>
        <div style="background:#ecfdf5; border:1px solid #10b981; color:#064e3b; padding:20px; border-radius:8px; text-align:center; margin-bottom:20px;">
            <svg style="width:48px; height:48px; color:#10b981; margin:0 auto 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 style="margin-top:0; font-size:20px;">Konfirmasi Berhasil</h3>
            <p>Terima kasih! Bukti pembayaran Anda telah kami terima dan akan segera diverifikasi oleh tim kami (maksimal 1x24 jam).</p>
            <a href="<?php echo home_url('/'); ?>" style="display:inline-block; margin-top:15px; background:#10b981; color:white; padding:10px 20px; border-radius:6px; text-decoration:none;">Kembali ke Beranda</a>
        </div>
    <?php else : ?>

        <div style="background:white; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); border:1px solid #e5e7eb; padding:25px;">
            
            <h2 style="margin-top:0; font-size:22px; text-align:center; color:#111827;">Konfirmasi Donasi</h2>
            <p style="text-align:center; color:#6b7280; font-size:14px; margin-bottom:25px;">Upload bukti transfer untuk memverifikasi donasi manual Anda.</p>

            <?php if ( ! empty( $error ) ) : ?>
                <div style="background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:15px; border-radius:6px; margin-bottom:20px; font-size:14px;">
                    <strong>Error:</strong> <?php echo esc_html( $error ); ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="">
                <?php wp_nonce_field( 'wpd_confirm_payment' ); ?>
                <input type="hidden" name="wpd_confirm_submit" value="1">

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:500; color:#374151;">ID Donasi</label>
                    <input type="number" name="donation_id" placeholder="Contoh: 154" value="<?php echo esc_attr( $donation_id_val ); ?>" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                    <p style="font-size:12px; color:#6b7280; margin-top:3px;">ID Donasi dapat dilihat di email instruksi pembayaran.</p>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:500; color:#374151;">Nominal Transfer (Rp)</label>
                    <input type="text" name="amount" placeholder="Contoh: 100000" value="<?php echo esc_attr( $amount_val ); ?>" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                </div>

                <div style="margin-bottom:25px;">
                    <label style="display:block; margin-bottom:5px; font-weight:500; color:#374151;">Bukti Transfer</label>
                    <input type="file" name="proof_file" accept="image/jpeg,image/png,image/jpg" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; background:#f9fafb;">
                    <p style="font-size:12px; color:#6b7280; margin-top:3px;">Format: JPG, PNG. Maksimal 2MB.</p>
                </div>

                <button type="submit" style="width:100%; background:#2563eb; color:white; font-weight:bold; padding:12px; border:none; border-radius:8px; cursor:pointer; font-size:16px;">
                    Kirim Konfirmasi
                </button>
            </form>

        </div>

    <?php endif; ?>
</div>
