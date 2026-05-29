<?php
/**
 * Template Name: User Dashboard Insight
 * Author: Waqas Riaz.
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}
get_header('dashboard');
?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">

        <?php if( !class_exists('Fave_Insights')) { ?>
            <div class="stats-box">
                <?php echo esc_html__('Please install and activate Favethemes Insights plugin.', 'houzez'); ?>
            </div>
        <?php } else { 
            
            $show_statistics = true;
            $author_id = 0;
            $user_id = get_current_user_id();

            $insights = new Fave_Insights();

            $listing_id = isset($_GET['listing_id']) ? $_GET['listing_id'] : '';

            if(!empty($listing_id)) {
                $insights_stats = $insights->fave_listing_stats($_GET['listing_id']);
                $author_id = get_post_field( 'post_author', $listing_id );
            } else {
                $insights_stats = $insights->fave_user_stats($user_id);
            }
            ?>
            <div class="heading d-flex align-items-center justify-content-between">
                <div class="heading-text">
                    <h2><?php echo houzez_option('dsh_insight', 'Insights'); ?></h2> 
                </div>
                <div class="add-export-btn">
                    <?php get_template_part('template-parts/dashboard/insights/filter'); ?>
                </div>
            </div>

            <?php get_template_part('template-parts/dashboard/insights/stats'); ?>

            <div class="houzez-data-content">
                <?php get_template_part('template-parts/dashboard/insights/chart'); ?>
                <?php get_template_part('template-parts/dashboard/insights/bottom-charts'); ?>
                <?php get_template_part('template-parts/dashboard/insights/referrals'); ?>
            </div>

        <?php } ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>