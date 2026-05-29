<?php
/**
 * Template Name: User Dashborad Messages
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/12/16
 * Time: 7:47 PM
 * Since v1.5.0
 */

if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $wpdb;

$userID = get_current_user_id();


get_header('dashboard'); ?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">

    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <?php
        if ( isset( $_REQUEST['thread_id'] ) && !empty( $_REQUEST['thread_id'] ) ) {

            get_template_part('template-parts/dashboard/messages/message-detail');

        } else {

            get_template_part('template-parts/dashboard/messages/messages');

        }
            ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>