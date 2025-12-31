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
// Use 'gateway' if set (e.g. midtrans), otherwise fallback to 'payment_method' (e.g. manual)
$gateway_id = !empty($donation->gateway) ? $donation->gateway : $donation->payment_method;

// Get Gateway Instance
$gateway = WPD_Gateway_Registry::get_gateway( $gateway_id );

// Colors from settings
$primary_color = get_option('wpd_appearance_brand_color', '#059669'); // Emerald
$button_color = get_option('wpd_appearance_button_color', '#ec4899'); // Pink

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - <?php echo esc_html( $title ); ?></title>
    <?php wp_head(); ?>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --wpd-primary: <?php echo esc_attr($primary_color); ?>;
            --wpd-btn: <?php echo esc_attr($button_color); ?>;
            --wpd-bg: #f8fafc;
            --wpd-card-bg: #ffffff;
            --wpd-text-main: #0f172a;
            --wpd-text-muted: #64748b;
        }

        body { 
            margin: 0; padding: 0; 
            background: var(--wpd-bg); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--wpd-text-main);
        }

        /* Ambient Background */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; height: 300px;
            background: linear-gradient(180deg, rgba(var(--wpd-primary-rgb), 0.1) 0%, transparent 100%); /* Fallback */
            background: linear-gradient(180deg, #ecfdf5 0%, transparent 100%);
            z-index: -1;
        }

        .wpd-container {
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .wpd-card {
            background: var(--wpd-card-bg);
            border-radius: 24px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0,0,0,0.02);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Status Section */
        .wpd-status-hero {
            padding: 40px 30px 30px;
            text-align: center;
            background: #fff;
            position: relative;
        }
        
        .wpd-icon-wrapper {
            width: 80px; height: 80px;
            margin: 0 auto 24px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            position: relative;
        }
        
        /* Status Colors */
        .status-complete .wpd-icon-wrapper { background: #d1fae5; color: #059669; }
        .status-pending .wpd-icon-wrapper { background: #fef3c7; color: #d97706; }
        .status-failed .wpd-icon-wrapper { background: #fee2e2; color: #dc2626; }

        .wpd-icon-wrapper svg { width: 40px; height: 40px; stroke-width: 2.5; }

        .wpd-title {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
            color: var(--wpd-text-main);
        }
        
        .wpd-subtitle {
            font-size: 15px;
            color: var(--wpd-text-muted);
            line-height: 1.5;
            margin: 0;
            font-weight: 500;
        }

        /* Ticket / Instruction Styling */
        .wpd-ticket {
            background: #f8fafc;
            margin: 0 24px 24px;
            padding: 24px;
            border-radius: 16px;
            border: 1px dashed #cbd5e1;
            position: relative;
        }

        /* Cutout Effect */
        .wpd-ticket::before, .wpd-ticket::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 20px; height: 20px;
            background: #fff;
            border-radius: 50%;
            transform: translateY(-50%);
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.8); /* Match card border simulation */
        }
        .wpd-ticket::before { left: -35px; } /* Push outside padding */
        .wpd-ticket::after { right: -35px; }

        .wpd-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
        }

        .wpd-bank-info {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .wpd-bank-logo {
            font-weight: 800;
            font-size: 18px;
            color: #1e293b;
        }
        .wpd-account-number {
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 16px;
            color: #334155;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        .wpd-copy-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--wpd-primary);
            font-size: 13px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .wpd-copy-btn:hover { background: #f1f5f9; }

        /* Details List */
        .wpd-details {
            padding: 0 30px 30px;
        }
        
        .wpd-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .wpd-row:last-child { border-bottom: none; }
        
        .wpd-row-label { color: var(--wpd-text-muted); font-weight: 500; }
        .wpd-row-value { color: var(--wpd-text-main); font-weight: 600; text-align: right; }

        .wpd-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wpd-total-label { font-size: 16px; font-weight: 700; color: var(--wpd-text-main); }
        .wpd-total-value { font-size: 24px; font-weight: 800; color: var(--wpd-primary); letter-spacing: -0.03em; }

        /* Actions */
        .wpd-actions {
            padding: 0 30px 40px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .btn-primary {
            background: var(--wpd-btn);
            color: white;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3); /* Pink glow matches default btn */
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(236, 72, 153, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--wpd-text-main);
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
        
        .btn-ghost {
            background: transparent;
            color: var(--wpd-text-muted);
            font-size: 14px;
            padding: 10px;
        }
        .btn-ghost:hover { color: var(--wpd-primary); }

        /* Animation */
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .wpd-card { animation: popIn 0.5s cubic-bezier(0.16, 1, 0.3, 1); }

    </style>
</head>
<body class="status-<?php echo esc_attr($status); ?>">

<div class="wpd-container">
    <div class="wpd-card">
        
        <!-- Hero Status -->
        <div class="wpd-status-hero">
            <div class="wpd-icon-wrapper">
                <?php if ( $status == 'complete' ) : ?>
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <?php elseif ( $status == 'failed' ) : ?>
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <?php else : ?>
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php endif; ?>
            </div>
            
            <h1 class="wpd-title">
                <?php 
                if ($status == 'complete') echo 'Donasi Berhasil!';
                elseif ($status == 'failed') echo 'Mohon Maaf';
                else echo 'Selesaikan Pembayaran'; 
                ?>
            </h1>
            
            <p class="wpd-subtitle">
                <?php 
                if ($status == 'complete') echo 'Terima kasih atas kontribusi Anda.';
                elseif ($status == 'failed') echo 'Transaksi Anda gagal diproses.';
                else echo 'Transfer sesuai nominal di bawah ini.'; 
                ?>
            </p>
        </div>

        <!-- Payment Instructions (Ticket Style) -->
        <?php if ( $status == 'pending' && $gateway ) : ?>
            <div class="wpd-ticket">
                <span class="wpd-label">Instruksi Pembayaran</span>
                
                <!-- This output needs to be clean. We will trust the gateway output but wrap it nicely if possible, 
                     or rewrite the Manual gateway output method next to return cleaner HTML/Array.
                     For now, let's output it and handle raw HTML carefully. -->
                <div class="gateway-instruction-content">
                     <?php echo $gateway->get_payment_instructions( $donation_id ); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Details -->
        <div class="wpd-details">
            <div class="wpd-row">
                <span class="wpd-row-label">No. Referensi</span>
                <span class="wpd-row-value">#<?php echo $donation_id; ?></span>
            </div>
            <div class="wpd-row">
                <span class="wpd-row-label">Program</span>
                <span class="wpd-row-value"><?php echo esc_html( $title ); ?></span>
            </div>
            <div class="wpd-row">
                <span class="wpd-row-label">Tanggal</span>
                <span class="wpd-row-value"><?php echo date_i18n( 'd M Y, H:i', strtotime( $donation->created_at ) ); ?></span>
            </div>
            <div class="wpd-row">
                <span class="wpd-row-label">Atas Nama</span>
                <span class="wpd-row-value"><?php echo $donation->is_anonymous ? 'Hamba Allah' : esc_html( $donation->name ); ?></span>
            </div>

            <div class="wpd-total">
                <span class="wpd-total-label">Total Donasi</span>
                <span class="wpd-total-value">Rp <?php echo number_format( $amount, 0, ',', '.' ); ?></span>
            </div>
        </div>

        <!-- Actions -->
        <div class="wpd-actions">
            <?php if ( $status == 'complete' ) : ?>
                <a href="<?php echo home_url( '/?wpd_receipt=' . $donation_id ); ?>" target="_blank" class="btn btn-secondary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:8px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Kwitansi
                </a>
            <?php endif; ?>

            <?php if ( $status == 'pending' && $gateway_id == 'manual' ) : 
                // Get Confirmation URL
                $settings_gen = get_option('wpd_settings_general');
                $conf_page_id = isset($settings_gen['confirmation_page']) ? intval($settings_gen['confirmation_page']) : 0;
                
                if ( $conf_page_id ) {
                    $conf_url = add_query_arg('donation_id', $donation_id, get_permalink($conf_page_id));
                    echo '<a href="' . esc_url($conf_url) . '" class="btn btn-primary">Konfirmasi Pembayaran</a>';
                } else {
                    echo '<a href="#" onclick="confirmPayment()" class="btn btn-primary">Konfirmasi Pembayaran</a>';
                }
            ?>
            <?php endif; ?>

            <a href="<?php echo get_permalink( $campaign_id ); ?>" class="btn btn-ghost">
                Kembali ke Halaman Program
            </a>
        </div>

        <?php 
        $gen_settings = get_option('wpd_settings_general', []);
        $is_branding_removed = !empty($gen_settings['remove_branding']);
        
        if ( ! $is_branding_removed ) : ?>
            <div class="wpd-powered-by" style="text-align:center; padding-bottom: 20px; color:#9ca3af; font-size:13px; margin-top: -20px;">
                Powered by <a href="https://donasai.com" target="_blank" style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Confetti Effect for Complete Status
    <?php if ( $status == 'complete' ) : ?>
    window.addEventListener('load', () => {
        var count = 200;
        var defaults = {
            origin: { y: 0.7 },
            zIndex: 9999
        };

        function fire(particleRatio, opts) {
            confetti(Object.assign({}, defaults, opts, {
                particleCount: Math.floor(count * particleRatio)
            }));
        }

        fire(0.25, { spread: 26, startVelocity: 55 });
        fire(0.2, { spread: 60 });
        fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
        fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
        fire(0.1, { spread: 120, startVelocity: 45 });
    });
    <?php endif; ?>

    function confirmPayment() {
        // Confirmation Page Logic
        <?php 
        $settings = get_option('wpd_settings_general');
        $conf_page_id = isset($settings['confirmation_page']) ? intval($settings['confirmation_page']) : 0;
        $conf_url = $conf_page_id ? get_permalink($conf_page_id) : '';
        
        if ($conf_url) : 
        ?>
            // Redirect to Confirmation Page
            window.location.href = "<?php echo add_query_arg('donation_id', $donation_id, $conf_url); ?>";
        <?php else : ?>
            // WhatsApp Logic (Default)
            const phone = "<?php echo get_option('wpd_settings_general')['whatsapp_number'] ?? ''; ?>"; 
            if(phone) {
                 const cleanPhone = phone.replace(/\D/g,'');
                 const text = encodeURIComponent("Halo Admin, saya sudah transfer untuk donasi #<?php echo $donation_id; ?> sebesar Rp <?php echo number_format($amount,0,',','.'); ?>. Mohon dicek. Terima kasih.");
                 window.open(`https://wa.me/${cleanPhone}?text=${text}`, '_blank');
            } else {
                alert('Silakan hubungi admin untuk konfirmasi.');
            }
        <?php endif; ?>
    }

    // Copy to Clipboard (will be used by manual gateway HTML)
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Nomor rekening disalin!');
        });
    }
</script>

<?php wp_footer(); ?>
</body>
</html>
