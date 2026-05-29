<?php
/**
 * Template Name: Houzez CRM
 * Author: Waqas Riaz.
 */
if ( !is_user_logged_in() || ! houzez_check_role() ) {
    wp_redirect(  home_url() );
}
get_header('dashboard'); 

$page = isset($_GET['hpage']) ? $_GET['hpage'] : '';

$content_class = '';
if( $page == 'deals' ) {
    $content_class = 'deals-content';
}

get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <?php get_template_part('template-parts/dashboard/topbar');  ?>

    <div class="dashboard-content <?php echo esc_attr($content_class); ?>">
        <?php
        if( !class_exists('Houzez_CRM')) {
            ?>
            <div class="stats-box">
                <?php echo esc_html__('Please install and activate Houzez CRM plugin.', 'houzez'); ?>
            </div>
        <?php
        } else {
            
            if( (isset($_GET['hpage']) && $_GET['hpage'] == 'lead-detail') && ( isset($_GET['lead-id']) && $_GET['lead-id'] != '') ) {
                get_template_part('template-parts/dashboard/board/leads/lead-detail'); 
            
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'leads' ) { 
                get_template_part('template-parts/dashboard/board/leads/main'); 
            
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'import-leads' ) { 
                get_template_part('template-parts/dashboard/board/leads/import'); 
            
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'deals' ) {
                get_template_part('template-parts/dashboard/board/deals/main'); 
            
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'enquiries' && isset($_GET['enquiry']) && !empty($_GET['enquiry']) ) {
                get_template_part('template-parts/dashboard/board/enquires/enquiry-detail');
                
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'enquiries' ) {
                get_template_part('template-parts/dashboard/board/enquires/main');
            
            } elseif( isset($_GET['hpage']) && $_GET['hpage'] == 'activities' ) {
                get_template_part('template-parts/dashboard/board/activities');
            
            } else {
                get_template_part('template-parts/dashboard/board/activities');
            }
        }
        ?>
    </div>
</div>
<?php get_footer('dashboard'); ?>