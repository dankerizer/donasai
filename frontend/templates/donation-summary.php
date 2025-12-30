<?php
/**
 * Donation Summary / Thank You Page
 * Endpoint: /campaign-slug/thank-you/donation_id
 */

$thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';
$donation_id = get_query_var($thankyou_slug);

if ( ! $donation_id ) {
    wp_safe_redirect( home_url() );
    exit;
}

global $wpdb;
$donation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpd_donations WHERE id = %d", $donation_id ) );

if ( ! $donation ) {
    wp_die( 'Donasi tidak ditemukan.', 'Error', array( 'response' => 404 ) );
}

$campaign_id = $donation->campaign_id;
$title = get_the_title( $campaign_id );
$amount = $donation->amount;
$status = $donation->status; // pending, complete, failed, on-hold
$gateway_id = $donation->gateway;

// Get Gateway Instance
$gateway = WPD_Gateway_Registry::get_gateway( $gateway_id );

// Colors from settings
$primary_color = get_option('wpd_appearance_brand_color', '#059669'); // Default Emerald
$button_color = get_option('wpd_appearance_button_color', '#ec4899'); // Default Pink

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        :root {
            --wpd-primary: <?php echo esc_attr($primary_color); ?>;
            --wpd-btn: <?php echo esc_attr($button_color); ?>;
        }
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .wpd-summary-card {
            background: white;
            max-width: 600px;
            margin: 40px auto;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        @media (max-width: 640px) {
            .wpd-summary-card { margin: 0; border-radius: 0; min-height: 100vh; }
        }
        .wpd-header {
            background: #ecfdf5;
            padding: 40px 30px;
            text-align: center;
            border-bottom: 1px solid #d1fae5;
        }
        .wpd-header.pending { background: #fffbeb; border-color: #fef3c7; }
        .wpd-header.failed { background: #fef2f2; border-color: #fee2e2; }

        .wpd-icon-circle {
            width: 80px; height: 80px;
            margin: 0 auto 20px;
            background: #10b981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .wpd-header.pending .wpd-icon-circle { background: #f59e0b; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); }
        .wpd-header.failed .wpd-icon-circle { background: #ef4444; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); }

        .wpd-status-title { font-size: 24px; font-weight: 800; color: #064e3b; margin: 0 0 10px; }
        .wpd-status-desc { color: #047857; margin: 0; font-size: 16px; }

        .wpd-header.pending .wpd-status-title { color: #92400e; }
        .wpd-header.pending .wpd-status-desc { color: #b45309; }
        
        .wpd-content { padding: 30px; }
        .wpd-section-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .wpd-detail-row { display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #e5e7eb; }
        .wpd-detail-row:last-child { border-bottom: none; }
        .wpd-detail-label { color: #6b7280; font-size: 14px; }
        .wpd-detail-value { color: #111827; font-weight: 600; font-size: 14px; text-align: right; }

        .wpd-total-box {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 10px;
        }
        .wpd-total-label { font-weight: 600; color: #374151; }
        .wpd-total-amount { font-size: 24px; font-weight: 800; color: var(--wpd-primary); }

        .wpd-instructions {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .wpd-btn {
            display: block; width: 100%;
            padding: 15px;
            background: var(--wpd-btn);
            color: white;
            text-align: center;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            margin-top: 15px;
            transition: opacity 0.2s;
        }
        .wpd-btn:hover { opacity: 0.9; }
        .wpd-btn-secondary { background: #e5e7eb; color: #374151; }
    </style>
</head>
<body <?php body_class('wpd-thankyou-page'); ?>>

<div class="wpd-summary-card">
    
    <!-- Status Header -->
    <?php if ( $status == 'complete' ) : ?>
        <div class="wpd-header">
            <div class="wpd-icon-circle">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="wpd-status-title">Terima Kasih!</h1>
            <p class="wpd-status-desc">Donasi Anda telah berhasil diverifikasi.</p>
        </div>
    <?php elseif ( $status == 'pending' ) : ?>
        <div class="wpd-header pending">
            <div class="wpd-icon-circle">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="wpd-status-title">Menunggu Pembayaran</h1>
            <p class="wpd-status-desc">Mohon selesaikan pembayaran Anda.</p>
        </div>
    <?php else : ?>
        <div class="wpd-header failed">
            <div class="wpd-icon-circle">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h1 class="wpd-status-title">Donasi Gagal</h1>
            <p class="wpd-status-desc">Mohon maaf, terjadi kesalahan.</p>
        </div>
    <?php endif; ?>

    <div class="wpd-content">
        
        <!-- Payment Instructions (If Pending) -->
        <?php if ( $status == 'pending' && $gateway ) : ?>
            <div class="wpd-section-title">Instruksi Pembayaran</div>
            <div class="wpd-instructions">
                <?php echo $gateway->get_payment_instructions( $donation_id ); ?>
            </div>
        <?php endif; ?>

        <!-- Donation Details -->
        <div class="wpd-section-title">Detail Donasi</div>
        
        <div class="wpd-detail-row">
            <span class="wpd-detail-label">ID Donasi</span>
            <span class="wpd-detail-value">#<?php echo $donation_id; ?></span>
        </div>
        <div class="wpd-detail-row">
            <span class="wpd-detail-label">Program</span>
            <span class="wpd-detail-value"><?php echo esc_html( $title ); ?></span>
        </div>
        <div class="wpd-detail-row">
            <span class="wpd-detail-label">Tanggal</span>
            <span class="wpd-detail-value"><?php echo date_i18n( 'd M Y, H:i', strtotime( $donation->created_at ) ); ?></span>
        </div>
        <div class="wpd-detail-row">
            <span class="wpd-detail-label">Donatur</span>
            <span class="wpd-detail-value"><?php echo $donation->is_anonymous ? 'Hamba Allah' : esc_html( $donation->name ); ?></span>
        </div>

        <div class="wpd-total-box">
            <span class="wpd-total-label">Total Donasi</span>
            <span class="wpd-total-amount">Rp <?php echo number_format( $amount, 0, ',', '.' ); ?></span>
        </div>

        <div style="margin-top: 30px;">
            <?php if ( $status == 'complete' ) : ?>
                <a href="<?php echo home_url( '/?wpd_receipt=' . $donation_id ); ?>" target="_blank" class="wpd-btn wpd-btn-secondary" style="margin-bottom: 10px;">
                    Download Kwitansi
                </a>
            <?php endif; ?>
            
            <a href="<?php echo get_permalink( $campaign_id ); ?>" class="wpd-btn">
                Kembali ke Halaman Program
            </a>
            
             <?php if ( $status == 'pending' && $gateway_id == 'manual' ) : ?>
                <div style="margin-top: 15px; text-align: center; font-size: 13px; color: #6b7280;">
                    Sudah transfer? <a href="#" onclick="alert('Silakan hubungi admin untuk konfirmasi manual atau kirim bukti via WhatsApp.')">Konfirmasi Pembayaran</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
