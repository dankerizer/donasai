<?php
/**
 * Payment Success Page Template
 * Triggered by ?donation_success=1
 */

get_header();

$campaign_id = get_the_ID();
$donation_id = isset( $_GET['donation_id'] ) ? intval( $_GET['donation_id'] ) : 0;
$method      = isset( $_GET['method'] ) ? sanitize_text_field( $_GET['method'] ) : 'manual';

$gateway = WPD_Gateway_Registry::get_gateway( $method );
?>

<div class="wpd-container" style="max-width:600px; margin:0 auto; padding:40px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <div style="background:white; border-radius:16px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); border:1px solid #e5e7eb; overflow:hidden;">
        
        <!-- Header Success -->
        <div style="background:#ecfdf5; padding:30px; text-align:center; border-bottom:1px solid #d1fae5;">
            <div style="width:60px; height:60px; background:#10b981; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 4px 6px rgba(16, 185, 129, 0.4);">
                <svg style="width:32px; height:32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 style="font-size:24px; font-weight:700; color:#064e3b; margin:0 0 5px 0;">Terima Kasih!</h1>
            <p style="color:#047857; margin:0;">Donasi Anda berhasil dicatat.</p>
        </div>

        <!-- content -->
        <div style="padding:30px;">
            
            <!-- Instructions -->
            <div style="margin-bottom:30px;">
                <h3 style="font-size:18px; font-weight:600; color:#111827; margin-bottom:15px; border-bottom:2px solid #f3f4f6; padding-bottom:10px;">Instruksi Pembayaran</h3>
                
                <?php
                if ( $gateway && $donation_id ) {
                    echo '<div class="wpd-gateway-instructions" style="background:#f9fafb; padding:20px; border-radius:8px; border:1px solid #e5e7eb;">';
                    echo $gateway->get_payment_instructions( $donation_id );
                    echo '</div>';
                } else {
                    echo '<p>Silahkan cek email Anda untuk detail pembayaran.</p>';
                }
                ?>
            </div>
            
            <!-- Actions -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="<?php echo home_url( '/?wpd_receipt=' . $donation_id ); ?>" target="_blank" style="flex:1; text-align:center; padding:12px; background:#f3f4f6; color:#374151; font-weight:600; text-decoration:none; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
                    <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Receipt
                </a>
                
                <a href="<?php echo get_permalink( $campaign_id ); ?>" style="flex:1; text-align:center; padding:12px; background:#2563eb; color:white; font-weight:600; text-decoration:none; border-radius:8px;">
                    Kembali ke Campaign
                </a>
            </div>

        </div>

    </div>

    <?php 
    $gen_settings = get_option('wpd_settings_general', []);
    $is_branding_removed = !empty($gen_settings['remove_branding']);
    
    if ( ! $is_branding_removed ) : ?>
        <div class="wpd-powered-by" style="text-align:center; padding-top: 20px; color:#9ca3af; font-size:13px;">
            Powered by <a href="https://donasai.com" target="_blank" style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
?>
