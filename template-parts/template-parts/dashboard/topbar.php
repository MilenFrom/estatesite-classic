<!-- Dashboard Header -->
<div class="header">
    <!-- Left Section -->
    <div class="header-left">
        <ul class="header-actions">
            <li>
                <a href="javascript:void(0)" class="menu-btn">
                    <i class="houzez-icon icon-navigation-menu"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo home_url(); ?>" target="_blank" class="vist-btn">
                    <i class="houzez-icon icon-share-2"></i>
                    <span><?php echo esc_html__('Visit Site', 'houzez'); ?></span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Right Section -->
    <div class="header-right d-flex align-items-center gap-16">
    
        <!-- User Dropdown -->
        <?php if( is_user_logged_in() ){ ?>
        <div class="dropdown">
            <?php
            $current_user = wp_get_current_user();
            $user_display_name = $current_user->display_name;
            $user_email = $current_user->user_email;
            $userID = get_current_user_id();
            $user_custom_picture = houzez_get_profile_pic($userID);
            $dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
            $verification_link = add_query_arg( 'hpage', 'verification', $dash_profile_link );
            ?>
            <button class="user-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img width="40" height="40" alt="author" src="<?php echo esc_url($user_custom_picture); ?>" class="rounded user-img">
                <span class="user-name"><?php echo esc_html($user_display_name); ?></span>
                <i class="houzez-icon icon-arrow-down-1"></i>
            </button>
            
            <ul class="dropdown-menu">
                <!-- User Info -->
                <li>
                    <div class="px-4 py-3">
                        <h6 class="mb-1"><?php echo esc_html($user_display_name); ?></h6>
                        <small class="text-muted"><?php echo esc_html($user_email); ?></small>
                    </div>
                </li>
                
                <!-- Menu Items -->
                 <?php if ( !empty($dash_profile_link) ) { ?>
                <li>
                    <a class="dropdown-item" href="<?php echo $dash_profile_link; ?>">
                        <i class="houzez-icon icon-single-neutral-circle me-2"></i>
                        <span><?php echo esc_html__('View Profile', 'houzez'); ?></span>
                    </a>
                </li>

                <?php if( (houzez_is_agency() || houzez_is_agent() || houzez_is_owner() ) && houzez_option('enable_user_verification', 0) ) { ?>
                <li>
                    <a class="dropdown-item" href="<?php echo $verification_link; ?>">
                        <i class="houzez-icon icon-check-circle-1 me-2"></i>
                        <span><?php echo esc_html__('Verification', 'houzez'); ?></span>
                    </a>
                </li>
                <?php } ?>

                <?php } ?>
                <li><hr class="dropdown-divider"></li>
                
                <li>
                    <a class="dropdown-item" href="<?php echo wp_logout_url(home_url()); ?>">
                        <i class="houzez-icon icon-logout-1 me-2"></i>
                        <span><?php echo esc_html__('Sign Out', 'houzez'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <?php } ?>
        
    </div>
</div>