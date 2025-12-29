<?php
/**
 * Single Campaign Template
 */

get_header();

$campaign_id = get_the_ID();
$progress    = wpd_get_campaign_progress( $campaign_id );

// Check for Success Message
if ( isset( $_GET['donation_success'] ) && $_GET['donation_success'] == 1 ) {
    echo '<div class="wpd-success-message" style="background:#d1fae5; color:#065f46; padding:20px; margin:20px 0; border-radius:8px; text-align:center;">';
    echo '<h3>' . __( 'Terima Kasih atas Donasi Anda!', 'wp-donasi' ) . '</h3>';
    
    // Dynamic Instructions based on Gateway
    $method = isset( $_GET['method'] ) ? sanitize_text_field( $_GET['method'] ) : 'manual';
    $donation_id = isset( $_GET['donation_id'] ) ? intval( $_GET['donation_id'] ) : 0;
    
    $gateway = WPD_Gateway_Registry::get_gateway( $method );
    
    if ( $gateway && $donation_id ) {
        echo $gateway->get_payment_instructions( $donation_id );
    } else {
        echo '<p>' . __( 'Silahkan cek email Anda untuk instruksi pembayaran.', 'wp-donasi' ) . '</p>';
    }

    // Receipt Link
    echo '<p style="margin-top:10px;"><a href="' . home_url( '/?wpd_receipt=' . $donation_id ) . '" target="_blank" class="button" style="background:#4b5563; color:white; padding:5px 10px; font-size:12px; border-radius:4px; text-decoration:none;">' . __( 'Cetak Receipt', 'wp-donasi' ) . '</a></p>';

    echo '<p style="margin-top:15px;"><a href="' . get_permalink() . '" class="button">' . __( 'Kembali ke Campaign', 'wp-donasi' ) . '</a></p>';
    echo '</div>';
}

?>

<div class="wpd-campaign-container">
	<div class="wpd-campaign-layout">
		<!-- Left: Content -->
		<div class="wpd-campaign-content">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="wpd-campaign-thumbnail">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
                <?php 
                $is_verified = get_post_meta( get_the_ID(), '_wpd_is_verified', true );
                ?>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <?php the_title(); ?>
                    <?php if ( $is_verified ) : ?>
                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z"/></svg>
                    <?php endif; ?>
                </h1>
			
			<div class="wpd-campaign-stats-mobile wpd-hide-desktop">
				<!-- Progress bar for mobile if needed -->
				<?php // duplicate progress bar here or styling logic ?>
			</div>

			<div class="wpd-campaign-body">
				<?php the_content(); ?>
			</div>
		</div>

		<!-- Right: Sidebar / Sticky Form -->
		<div class="wpd-campaign-sidebar">
			<div class="wpd-card">
				<div class="wpd-progress-section">
					<div class="wpd-progress-stats">
						<span class="wpd-collected">Rp <?php echo number_format( $progress['collected'], 0, ',', '.' ); ?></span>
						<span class="wpd-target">terkumpul dari Rp <?php echo number_format( $progress['target'], 0, ',', '.' ); ?></span>
					</div>
					<div class="wpd-progress-bar-bg">
						<div class="wpd-progress-bar-fill" style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%"></div>
					</div>
				</div>

				<div class="wpd-form-embed">
				<div class="wpd-form-embed">
					<?php echo wpd_get_donation_form_html( $campaign_id ); ?>
				</div>

				<!-- Fundraiser Action -->
				<div class="wpd-fundraiser-section" style="margin-top:20px; text-align:center; padding-top:20px; border-top:1px solid #eee;">
				    <?php if ( is_user_logged_in() ) : ?>
				        <button type="button" class="button wpd-btn-outline" onclick="wpdRegisterFundraiser(<?php echo $campaign_id; ?>)" style="width:100%; border:1px solid #059669; color:#059669; background:white;">
				            <?php _e( 'Daftar Jadi Fundraiser', 'wp-donasi' ); ?>
				        </button>
				    <?php else : ?>
				        <p style="font-size:13px;">
				            <a href="<?php echo wp_login_url( get_permalink() ); ?>" style="color:#059669; text-decoration:underline;">Login</a> untuk menjadi Fundraiser.
				        </p>
				    <?php endif; ?>
				</div>
			</div>
			
			<!-- Fundraiser Modal (Hidden) -->
			<div id="wpd-fundraiser-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
			    <div style="background:white; padding:25px; border-radius:10px; width:90%; max-width:400px; position:relative;">
			        <button onclick="document.getElementById('wpd-fundraiser-modal').style.display='none'" style="position:absolute; top:10px; right:15px; border:none; background:none; font-size:20px; cursor:pointer;">&times;</button>
			        
			        <h3 style="margin-top:0;">Fundraiser Registered!</h3>
			        <p>Bagikan link ini untuk mendapatkan komisi/pahala:</p>
			        
			        <input type="text" id="wpd-ref-link" readonly style="width:100%; padding:10px; background:#f9f9f9; border:1px solid #ddd; margin-bottom:15px; font-size:14px;">
			        
			        <button class="button" onclick="wpdCopyRef()" style="width:100%; margin-bottom:10px;">Copy Link</button>
			        <a id="wpd-wa-share" href="#" target="_blank" class="button" style="display:block; width:100%; text-align:center; background:#25D366; color:white; border:none;">Share ke WhatsApp</a>
			    </div>
			</div>

			<script>
			function wpdRegisterFundraiser(campaignId) {
			    var nonce = '<?php echo wp_create_nonce( 'wp_rest' ); ?>';
			    
			    fetch('/wp-json/wpd/v1/fundraisers', {
			        method: 'POST',
			        headers: {
			            'Content-Type': 'application/json',
			            'X-WP-Nonce': nonce
			        },
			        body: JSON.stringify({ campaign_id: campaignId })
			    })
			    .then(response => response.json())
			    .then(data => {
			        if (data.referral_link) {
			            var modal = document.getElementById('wpd-fundraiser-modal');
			            modal.style.display = 'flex';
			            
			            document.getElementById('wpd-ref-link').value = data.referral_link;
			            
			            // Setup WA Share
			            var text = "Yuk bantu donasi di campaign ini: " + data.referral_link;
			            document.getElementById('wpd-wa-share').href = "https://wa.me/?text=" + encodeURIComponent(text);
			        } else {
			            alert('Error: ' + (data.message || 'Something went wrong'));
			        }
			    })
			    .catch(err => alert('Error connecting to server'));
			}
			
			function wpdCopyRef() {
			    var copyText = document.getElementById("wpd-ref-link");
			    copyText.select();
			    document.execCommand("copy");
			    alert("Link copied!");
			}
			</script>
		</div>
	</div>
</div>

<?php
get_footer();
?>
