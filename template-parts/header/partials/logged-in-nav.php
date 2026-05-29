<?php
/**
 * Houzez Logged-in Navigation
 * Displays the navigation menu for logged-in users
 */

// Get current user information
global $houzez_local;
$userID = get_current_user_id();
$user_custom_picture = houzez_get_profile_pic($userID);

// Dashboard page links
$dashboard_link = houzez_get_template_link_2('template/user_dashboard.php');
$dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');

$header = houzez_option('header_style');
$phone_number = houzez_option('hd1_4_phone');
$phone_enabled = houzez_option('hd1_4_phone_enable', 0);

$create_listing_enable = houzez_option('create_lisiting_enable');
$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');

$header_create_listing_template = houzez_get_template_link_2('template/user_dashboard_submit.php');
$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');

// Custom create listing button options
$custom_create_listing_btn = houzez_option('custom_create_lisiting_btn', 0);
$custom_create_listing_link = houzez_option('custom_create_lisiting_link');
$custom_create_listing_title = houzez_option('custom_create_lisiting_title');

// Override default settings if custom create listing button is enabled
if ($custom_create_listing_btn && !empty($custom_create_listing_link)) {
    $header_create_listing_template = $custom_create_listing_link;
    $create_listing_title = !empty($custom_create_listing_title) ? $custom_create_listing_title : $create_listing_title;
}

$houzez_check_role = houzez_check_role();
?>
<nav class="logged-in-nav-wrap navi-login-register h-100" id="navi-user">
    <div class="logged-in-nav-container d-flex justify-content-end align-items-center h-100">
        <div class="login-register-nav d-flex align-items-center d-none d-md-flex">
            
            <?php if (!empty($phone_number) && $phone_enabled && ($header == 1 || $header == 4)) { ?> 
            <span class="btn-phone-number">
                <a href="tel:<?php echo esc_attr($phone_number); ?>">
                    <i class="houzez-icon icon-phone-actions-ring me-1"></i> 
                    <?php echo esc_attr($phone_number); ?>
                </a>
            </span>
            <?php } ?>
            
            <?php if ($create_listing_enable != 0 && !empty($header_create_listing_template)): ?>
            <a class="btn btn-create-listing d-none d-md-block me-2" 
            href="<?php echo esc_url($header_create_listing_template); ?>">
                <?php echo esc_attr($create_listing_title); ?>
            </a>
            <?php endif; ?>
        </div>
        <div class="navbar-logged-in-wrap navbar h-100">
            <!-- Profile Picture Dropdown Toggle -->
            <a href="#" class="dropdown-toggle d-none d-md-block" data-bs-toggle="dropdown">
                <img width="42" height="42" alt="author" src="<?php echo esc_url($user_custom_picture); ?>" class="rounded">
            </a>

            <!-- Dropdown Menu Items -->
            <ul class="logged-in-nav dropdown-menu">
                <?php
                // Dashboard/CRM menu item
                if (!empty($dashboard_link) && $houzez_check_role): ?>
                    <li class="side-menu-item">
                        <a href="<?php echo esc_url($dashboard_link); ?>">
                            <i class="houzez-icon icon-layout-dashboard me-2"></i> 
                            <?php echo houzez_option('dsh_dashboard', 'Dashboard'); ?>
                            <span class="notification-circle"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php 
                // Profile menu item
                if (!empty($dash_profile_link)): ?>
                    <li class="side-menu-item">
                        <a href="<?php echo esc_url($dash_profile_link); ?>">
                            <i class="houzez-icon icon-single-neutral-circle me-2"></i> 
                            <?php echo houzez_option('dsh_profile', 'My profile'); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Logout menu item -->
                <li class="side-menu-item">
                    <a href="<?php echo wp_logout_url(home_url()); ?>">
                        <i class="houzez-icon icon-lock-5 me-2"></i> 
                        <?php echo houzez_option('dsh_logout', 'Log out'); ?>
                    </a>
                </li>
            </ul><!-- End of dropdown menu -->
        </div><!-- End of navbar wrapper -->
    </div>
</nav><!-- .logged-in-nav-wrap -->
