<?php
global $houzez_local;

$dashboard_link = houzez_get_template_link_2('template/user_dashboard.php');
$dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
$houzez_check_role = houzez_check_role();
?>
<div class="offcanvas offcanvas-end offcanvas-login-register" tabindex="-1" id="hz-offcanvas-login-register" aria-labelledby="hz-offcanvas-login-register-label">
    <div class="offcanvas-header">
        <div class="offcanvas-title fs-6 text-uppercase fw-medium" id="hz-offcanvas-login-register-label"><?php echo esc_html__( 'Account', 'houzez' ); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas">
            <i class="houzez-icon icon-close"></i>
        </button>
    </div>
    
    <nav class="logged-in-nav-wrap navi-login-register h-100" id="navi-user">
        <div class="logged-in-nav-container d-flex justify-content-end align-items-center h-100">
            
            <div class="navbar-logged-in-wrap navbar h-100">
            
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

</div>
