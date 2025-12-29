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
    </div>

    <a href="#" onclick="window.print(); return false;" class="print-btn">Print Receipt</a>
</div>

</body>
</html>
<?php exit; ?>
