<?php
/**
 * Houzez Logged-in Navigation for Elementor
 * Displays the navigation menu for logged-in users in Elementor templates
 */

// Get current user information and Elementor settings
global $current_user, $houzez_local, $ele_settings, $ele_edit_mode_settings;
$userID = get_current_user_id();
$user_custom_picture = houzez_get_profile_pic($userID);

// Elementor specific settings
$ele_show_dropdown = isset($ele_edit_mode_settings['show_dropdown']) ? $ele_edit_mode_settings['show_dropdown'] : '';

// Dashboard page links
$dashboard_link = houzez_get_template_link_2('template/user_dashboard.php');
$dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');

$houzez_check_role = houzez_check_role();
?>

<nav class="logged-in-nav-wrap navi-login-register h-100" id="navi-user">
    <div class="logged-in-nav-container d-flex justify-content-end align-items-center h-100">
        <div class="navbar-logged-in-wrap navbar <?php echo esc_attr($ele_show_dropdown); ?> h-100">
            <!-- Profile Picture Dropdown Toggle -->
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <img width="42" height="42" alt="author" src="<?php echo esc_url($user_custom_picture); ?>" class="rounded">
            </a>
            
            <!-- Dropdown Menu Items -->
            <ul class="logged-in-nav dropdown-menu <?php echo esc_attr($ele_show_dropdown); ?>">
                
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
