<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Printable Receipt Template
 */

$donation_id = isset($_GET['wpd_receipt']) ? intval($_GET['wpd_receipt']) : 0;
$donation = null;

if ($donation_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $donation_id));
}

if (!$donation) {
    wp_die('Receipt not found.');
    wp_die('Donation not found');
}

// Simple Receipt Styling
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php esc_html_e('Kuitansi Donasi', 'donasai'); ?> #<?php echo esc_html($donation->id); ?></title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .receipt-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2d3748;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #718096;
            font-size: 0.9em;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .details td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .total {
            font-size: 1.25em;
            font-weight: bold;
            color: #10b981;
            border-top: 2px solid #eee;
            padding-top: 15px;
            margin-top: 10px;
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.85em;
            color: #a0aec0;
        }

        @media print {
            body {
                background: white;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="receipt-container">
    </div>
    <div class="details-row">
        <span class="details-label"><?php esc_html_e('Campaign:', 'donasai'); ?></span>
        <span><?php echo esc_html($campaign_title); ?></span>
    </div>

    <div class="amount-row">
        Rp <?php echo esc_html(number_format($donation->amount, 0, ',', '.')); ?>
    </div>

    <div class="details-row">
        <span class="details-label"><?php esc_html_e('Payment Method:', 'donasai'); ?></span>
        <span><?php echo esc_html(ucfirst($donation->payment_method)); ?></span>
    </div>

    <div class="footer">
        <p><?php esc_html_e('Terima kasih atas donasi Anda. Semoga berkah.', 'donasai'); ?></p>
        <p><?php echo esc_url(get_bloginfo('url')); ?></p>

        <!-- WhatsApp Share -->
        <?php
        $wa_text = urlencode("Saya baru saja berdonasi untuk campaign *$campaign_title*. Yuk bantu juga! " . get_permalink($donation->campaign_id));
        $wa_link = "https://wa.me/?text=$wa_text";
        ?>
        <div style="margin-top:20px;">
            <a href="<?php echo esc_url($wa_link); ?>" target="_blank"
                style="display:inline-block; background:#25D366; color:white; padding:8px 16px; border-radius:20px; text-decoration:none; font-weight:600; font-size:14px;">
                Share ke WhatsApp
            </a>
        </div>

        <?php
        $gen_settings = get_option('wpd_settings_general', []);
        $is_branding_removed = !empty($gen_settings['remove_branding']);

        if (!$is_branding_removed): ?>
            <div class="wpd-powered-by" style="margin-top: 30px; opacity: 0.7;">
                Powered by <a href="https://donasai.com" target="_blank"
                    style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
            </div>
        <?php endif; ?>
    </div>

    <a href="#" onclick="window.print(); return false;" class="print-btn">Print Receipt</a>
    </div>

    <!-- Simple Confetti Canvas -->
    <canvas id="confetti"
        style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:999;"></canvas>

    <script>
        // Simple Confetti Script
        (function () {
            if ('<?php echo esc_js($donation->status); ?>' !== 'complete') return;

            const canvas = document.getElementById('confetti');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const pieces = [];
            const colors = ['#f00', '#0f0', '#00f', '#ff0', '#0ff', '#f0f'];

            for (let i = 0; i < 100; i++) {
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
                for (let i = 0; i < pieces.length; i++) {
                    const p = pieces[i];
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                    p.y += p.speed;
                    if (p.y > canvas.height) p.y = -20;
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