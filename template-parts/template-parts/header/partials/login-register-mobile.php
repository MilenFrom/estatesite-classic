<?php
/**
 * Mobile Login/Register Navigation Template
 *
 * Displays the mobile navigation menu for non-logged-in users with login, register,
 * create listing, and favorites functionality.
 *
 * @package Houzez
 * @since Houzez 1.0
 */

// Create listing variables
$create_listing_enable = houzez_option('create_lisiting_enable');
$header_create_listing_template = houzez_get_template_link_2('template/user_dashboard_submit.php');
$create_listing_button_required_login = houzez_option('create_listing_button');
$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');

// Custom create listing options
$custom_create_listing_btn = houzez_option('custom_create_lisiting_btn', 0);
$custom_create_listing_link = houzez_option('custom_create_lisiting_link');
$custom_create_listing_title = houzez_option('custom_create_lisiting_title');

// Favorites variables
$favorite_template = houzez_get_template_link_2('template/user_dashboard_favorites.php');
$add_to_favorite = houzez_option('add_to_favorite', 0);

// Override default create listing if custom is enabled
if ($custom_create_listing_btn && !empty($custom_create_listing_link)) {
    $header_create_listing_template = $custom_create_listing_link;
    $create_listing_title = !empty($custom_create_listing_title) ? $custom_create_listing_title : $create_listing_title;
}

// Check if login/register functionality is available
$login_register_enabled = class_exists('Houzez_login_register') && (houzez_option('header_login') || houzez_option('header_register'));
?>

<div class="offcanvas offcanvas-end offcanvas-login-register" tabindex="-1" id="hz-offcanvas-login-register" aria-labelledby="hz-offcanvas-login-register-label">
    <div class="offcanvas-header">
        <div class="offcanvas-title fs-6" id="hz-offcanvas-login-register-label"><?php echo esc_html__( 'Account', 'houzez' ); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas">
            <i class="houzez-icon icon-close"></i>
        </button>
    </div>
    <nav class="navi-login-register" id="navi-user">
        
        <?php if ($create_listing_enable) : ?>
        <!-- Create Listing Button - Top of menu -->
        <div class="nav-header pt-2 pb-2 d-flex justify-content-center">
            <a class="btn btn-create-listing" href="<?php echo esc_url($header_create_listing_template); ?>" role="button">
                <?php echo esc_attr($create_listing_title); ?>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($login_register_enabled) : ?>
        <!-- Navigation List for Authentication and Favorites -->
        <ul class="logged-in-nav" role="menu">
            
            <?php if (houzez_option('header_login')) : ?>
            <!-- Login Button - Triggers modal -->
            <li class="login-link" role="none">
                <a href="#" data-bs-toggle="modal" data-bs-target="#login-register-form" role="menuitem">
                    <i class="houzez-icon icon-lock-5 me-1" aria-hidden="true"></i> 
                    <span><?php echo esc_html__('Login', 'houzez'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (houzez_option('header_register')) : ?>
            <!-- Register Button - Triggers modal -->
            <li class="register-link" role="none">
                <a href="#" data-bs-toggle="modal" data-bs-target="#login-register-form" role="menuitem">
                    <i class="houzez-icon icon-single-neutral-circle me-1" aria-hidden="true"></i>
                    <span><?php echo esc_html__('Register', 'houzez'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (!$add_to_favorite) : ?>
            <!-- Favorites Link - With counter bubble -->
            <li class="favorite-link" role="none">
                <a class="favorite-btn" href="<?php echo esc_url($favorite_template); ?>" role="menuitem">
                    <i class="houzez-icon icon-love-it me-1" aria-hidden="true"></i>
                    <span><?php echo houzez_option('dsh_favorite', 'Favorites'); ?></span>
                    <span class="btn-bubble frvt-count">0</span>
                </a>
            </li>
            <?php endif; ?>
            
        </ul><!-- End of navigation list -->
        <?php endif; ?>
    </nav><!-- End of mobile navigation wrapper -->
</div>





