<?php
/**
 * Template: User Profile
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$phone = get_user_meta( $current_user->ID, '_wpd_phone', true );

// Handle Success/Error Messages
$message = '';
if ( isset( $_GET['updated'] ) ) {
    $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Profil berhasil diperbarui.</div>';
}
?>

<div class="wpd-profile-container max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php _e( 'Edit Profil', 'wp-donasi' ); ?></h2>
    
    <?php echo $message; ?>

    <form method="post" action="" class="space-y-4">
        <?php wp_nonce_field( 'wpd_profile_update', 'wpd_profile_nonce' ); ?>
        
        <!-- Name -->
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="display_name">
                <?php _e( 'Nama Lengkap', 'wp-donasi' ); ?>
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                id="display_name" type="text" name="display_name" 
                value="<?php echo esc_attr( $current_user->display_name ); ?>" required>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user_email">
                <?php _e( 'Email', 'wp-donasi' ); ?>
            </label>
            <input class="bg-gray-100 shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-500 leading-tight cursor-not-allowed" 
                id="user_email" type="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" disabled>
            <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah.</p>
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                <?php _e( 'Nomor WhatsApp', 'wp-donasi' ); ?>
            </label>
            <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                id="phone" type="text" name="phone" 
                value="<?php echo esc_attr( $phone ); ?>" placeholder="0812...">
        </div>

        <!-- Password (Optional) -->
        <div class="border-t pt-4 mt-4">
            <h3 class="text-lg font-medium text-gray-800 mb-3"><?php _e( 'Ganti Password', 'wp-donasi' ); ?></h3>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pass1">
                    <?php _e( 'Password Baru', 'wp-donasi' ); ?>
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    id="pass1" type="password" name="pass1" autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pass2">
                    <?php _e( 'Konfirmasi Password Baru', 'wp-donasi' ); ?>
                </label>
                <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    id="pass2" type="password" name="pass2" autocomplete="new-password">
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200" type="submit" name="wpd_profile_submit">
                <?php _e( 'Simpan Perubahan', 'wp-donasi' ); ?>
            </button>
        </div>
    </form>
</div>
