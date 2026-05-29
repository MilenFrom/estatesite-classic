<?php
/**
 * Template Name: User Dashboard Verification
 * Created by PhpStorm.
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url());
}

get_header('dashboard');
?> 

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <?php get_template_part('template-parts/dashboard/verification/main'); ?>
    </div>
</div>

<?php get_footer('dashboard'); ?> 