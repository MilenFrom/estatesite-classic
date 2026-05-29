<?php
/**
 * Template Name: User Dashboard Profile
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 02/10/15
 * Time: 4:22 PM
 */

/*-----------------------------------------------------------------------------------*/
// Social Logins
/*-----------------------------------------------------------------------------------*/
if( ( isset($_GET['code']) && isset($_GET['state']) ) ){
    houzez_facebook_login($_GET);

} else if( isset( $_GET['openid_mode']) && $_GET['openid_mode'] == 'id_res' ) {
    houzez_openid_login($_GET);

} else if (isset($_GET['code'])) {
    houzez_google_oauth_login();

} else {
    if ( !is_user_logged_in() ) {
        wp_redirect(  home_url() );
    }
}

get_header('dashboard');
?> 

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <?php
        if( isset( $_GET['agents'] ) && $_GET['agents'] == 'list' ) {
            get_template_part('template-parts/dashboard/agents/main');

        } elseif( isset( $_GET['agents'] ) && $_GET['agents'] == 'add_new' ) {
            get_template_part('template-parts/dashboard/agents/add-agent');

        } elseif( isset( $_GET['hpage'] ) && $_GET['hpage'] == 'verification' ) {
            if( (houzez_is_agency() || houzez_is_agent() || houzez_is_owner() || houzez_has_role('houzez_seller') ) && houzez_option('enable_user_verification', 0) ) {
                get_template_part('template-parts/dashboard/verification/main');
            }

        } else {
            get_template_part('template-parts/dashboard/profile/profile');
        }
        ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>