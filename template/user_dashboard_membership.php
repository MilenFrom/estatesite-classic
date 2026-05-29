<?php
/**
 * Template Name: User Dashboard Membership Info
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $houzez_local;
$user_id = get_current_user_id();
$dashboard_membership = houzez_get_template_link_2('template/user_dashboard_membership.php');
$packages_page_link = houzez_get_template_link_2('template/template-packages.php');
$agent_agency_id = houzez_get_agent_agency_id( $user_id );

if( $agent_agency_id ) {
    $user_id = $agent_agency_id;
}
$package_id = houzez_get_user_package_id( $user_id );

get_header('dashboard'); ?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_membership', 'Membership'); ?></h2> 
            </div> 
        </div> 

        <?php if( !empty($package_id) ) { ?>
                <div class="houzez-membership">
                    <?php houzez_get_user_current_package( $user_id ); ?>
                </div>

                <?php
                if( ! $agent_agency_id ) {
                    $stripe_profile_user    =   get_user_meta($user_id,'fave_stripe_user_profile',true);
                    $subscription_id        =   get_user_meta($user_id, 'houzez_stripe_subscription_id', true );
                    $paypal_subscription_id =   get_user_meta($user_id, 'houzez_paypal_recurring_profile_id', true );
                    $is_recurring_membership =   get_user_meta($user_id, 'houzez_is_recurring_membership', true );
                    $enable_stripe_status   =   houzez_option('enable_stripe');
                    $enable_paypal_status   =   houzez_option('enable_paypal');
                    
                    ?>
                    <div class="houzez-membership-btn mt-3">
                        <ul class="d-flex align-items-center gap-2">
                            <li><a href="<?php echo esc_url($packages_page_link); ?>" class="btn btn-primary"><?php esc_html_e('Change Membership Plan', 'houzez'); ?></a></li>
                            <?php if( $subscription_id != '' && $enable_stripe_status != 0 ) { ?>
                                <li><a href="#" id="houzez_stripe_cancel" data-message="<?php echo esc_html__('Done: Subscription will be cancelled at the end of current period', 'houzez'); ?>" class="btn btn-primary-outlined"><?php esc_html_e('Cancel Stripe Subscription', 'houzez'); ?></a></li>
                                <li><span id="stripe_cancel_success" class="text-success"></span></li>
                            <?php } ?>
                            
                            <?php if( $paypal_subscription_id != '' && $enable_paypal_status != 0 ) { ?>
                                <li><a href="#" id="houzez_paypal_cancel" data-message="<?php echo esc_html__('Done: Subscription will be cancelled at the end of current period', 'houzez'); ?>" class="btn btn-primary-outlined"><?php esc_html_e('Cancel PayPal Subscription', 'houzez'); ?></a></li>
                                <li><span id="paypal_cancel_success" class="text-success"></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php
                }
            } else { ?>

                <div class="houzez-membership">
                    <div class="membership-inner d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex flex-column">
                            <p class="mb-3"><?php esc_html_e("You don't have any membership.", 'houzez'); ?></p>
                            <a href="<?php echo esc_url($packages_page_link); ?>" class="btn btn-primary"><?php esc_html_e('Get Membership Plan', 'houzez'); ?></a>
                        </div>
                    </div>
                </div>
                
        <?php }  ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>