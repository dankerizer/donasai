<?php
/**
 * Printable Receipt Template
 */

$donation_id = isset( $_GET['wpd_receipt'] ) ? intval( $_GET['wpd_receipt'] ) : 0;
$donation = null;

if ( $donation_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';
    $donation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $donation_id ) );
}

if ( ! $donation ) {
    wp_die( 'Receipt not found.' );
}

// Security: Check if user owns donation or has hash (for public link)
// For MVP, if logged in & user_id match OR if basic hash (simple ID check for now, but ideally hash)
// Let's assume hash check if we implement it, but for start: logged in check
$current_user = get_current_user_id();
if ( $donation->user_id > 0 && $donation->user_id != $current_user && ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized access.' );
}

$campaign_title = get_the_title( $donation->campaign_id );
$settings_bank  = get_option( 'wpd_settings_bank', [] );
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $donation->id; ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; padding: 40px; }
        .receipt-container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2563eb; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .details-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .details-label { font-weight: bold; color: #555; }
        .amount-row { font-size: 24px; font-weight: bold; color: #059669; margin: 20px 0; border-top: 1px dashed #ddd; border-bottom: 1px dashed #ddd; padding: 15px 0; text-align: center; }
        .footer { margin-top: 40px; font-size: 12px; color: #888; text-align: center; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; background: #eee; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-complete { background: #d1fae5; color: #065f46; }
        .print-btn { display: block; width: 100%; padding: 10px; background: #333; color: white; border: none; cursor: pointer; margin-top: 20px; text-decoration: none; text-align: center; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .receipt-container { border: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="receipt-container">
    <div class="header">
        <h1><?php echo get_bloginfo( 'name' ); ?></h1>
        <p>Official Donation Receipt</p>
    </div>

    <div style="text-align:center; margin-bottom:30px;">
        <span class="status-badge status-<?php echo esc_attr( $donation->status ); ?>">
            <?php echo esc_html( $donation->status ); ?>
        </span>
    </div>

    <div class="details-row">
        <span class="details-label">Date:</span>
        <span><?php echo date( 'd M Y, H:i', strtotime( $donation->created_at ) ); ?></span>
    </div>
    <div class="details-row">
        <span class="details-label">Receipt No:</span>
        <span>#inv-<?php echo $donation->id; ?></span>
    </div>
    <div class="details-row">
        <span class="details-label">Donor Name:</span>
        <span><?php echo esc_html( $donation->name ); ?></span>
    </div>
    <div class="details-row">
        <span class="details-label">Campaign:</span>
        <span><?php echo esc_html( $campaign_title ); ?></span>
    </div>

    <div class="amount-row">
        Rp <?php echo number_format( $donation->amount, 0, ',', '.' ); ?>
    </div>

    <div class="details-row">
        <span class="details-label">Payment Method:</span>
        <span><?php echo ucfirst( $donation->payment_method ); ?></span>
    </div>
    
    <div class="footer">
        <p>Terima kasih atas donasi Anda. Semoga berkah.</p>
        <p><?php echo get_bloginfo( 'url' ); ?></p>
        
        <!-- WhatsApp Share -->
        <?php 
            $wa_text = urlencode( "Saya baru saja berdonasi untuk campaign *$campaign_title*. Yuk bantu juga! " . get_permalink($donation->campaign_id) );
            $wa_link = "https://wa.me/?text=$wa_text";
        ?>
        <div style="margin-top:20px;">
            <a href="<?php echo esc_url($wa_link); ?>" target="_blank" style="display:inline-block; background:#25D366; color:white; padding:8px 16px; border-radius:20px; text-decoration:none; font-weight:600; font-size:14px;">
                Share ke WhatsApp
            </a>
        </div>

        <?php 
        $gen_settings = get_option('wpd_settings_general', []);
        $is_branding_removed = !empty($gen_settings['remove_branding']);
        
        if ( ! $is_branding_removed ) : ?>
            <div class="wpd-powered-by" style="margin-top: 30px; opacity: 0.7;">
                Powered by <a href="https://donasai.com" target="_blank" style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
            </div>
        <?php endif; ?>
    </div>

    <a href="#" onclick="window.print(); return false;" class="print-btn">Print Receipt</a>
</div>

<!-- Simple Confetti Canvas -->
<canvas id="confetti" style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:999;"></canvas>

<script>
    // Simple Confetti Script
    (function() {
        if('<?php echo $donation->status; ?>' !== 'complete') return;
        
        const canvas = document.getElementById('confetti');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const pieces = [];
        const colors = ['#f00', '#0f0', '#00f', '#ff0', '#0ff', '#f0f'];
        
        for(let i=0; i<100; i++) {
            pieces.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                color: colors[Math.floor(Math.random() * colors.length)],
                size: Math.random() * 5 + 5,
                speed: Math.random() * 5 + 2
            });
        }
        
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for(let i=0; i<pieces.length; i++) {
                const p = pieces[i];
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, p.size, p.size);
                p.y += p.speed;
                if(p.y > canvas.height) p.y = -20;
            }
            requestAnimationFrame(draw);
        }
        
        // Only run for 5 seconds
        draw();
        setTimeout(() => { canvas.style.display = 'none'; }, 5000);
    })();
</script>

</body>
</html>
<?php exit; ?>
