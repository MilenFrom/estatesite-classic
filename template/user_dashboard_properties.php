<?php
/**
 * Template Name: User Dashboard Properties
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 15/10/15
 * Time: 3:33 PM
 */
if ( !is_user_logged_in() || !houzez_check_role() ) {
    wp_redirect(  home_url() );
}

global $houzez_local, $prop_featured, $current_user, $post, $properties_query, $delete_properties_nonce;

wp_get_current_user();
$userID         = get_current_user_id();
$user_login     = $current_user->user_login;
$paid_submission_type = esc_html ( houzez_option('enable_paid_submission','') );
$packages_page_link = houzez_get_template_link('template/template-packages.php');
$dashboard_add_listing = houzez_get_template_link_2('template/user_dashboard_submit.php');

$delete_properties_nonce = wp_create_nonce( 'delete_properties_nonce' );

get_header('dashboard');


// Get 'post_status' parameter from URL and set 'qry_status' accordingly
$post_status = isset($_GET['post_status']) ? $_GET['post_status'] : null;
switch ($post_status) {
    case 'approved':
        $qry_status = 'publish';
        break;
    case 'sold':
        $qry_status = 'houzez_sold';
        break;
    case 'pending':
    case 'expired':
    case 'disapproved':
    case 'draft':
    case 'on_hold':
        $qry_status = $post_status;
        break;
    default:
        $qry_status = 'any';
}

// Get 'sortby' parameter if set
$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : '';

// Default number of properties and page number
$allowed_per_page = [10,20,50,100];
$no_of_prop = isset($_GET['per_page']) && in_array( intval($_GET['per_page']), $allowed_per_page ) ? intval($_GET['per_page']) : 10;
$paged = get_query_var('paged') ?: get_query_var('page') ?: 1;

$tax_query = [];

// Define the initial args for the WP query
$args = [
    'post_type'      => 'property',
    'paged'          => $paged,
    'posts_per_page' => $no_of_prop,
    'suppress_filters' => false
];

$args = houzez_prop_sort ( $args );

if( houzez_is_admin() || houzez_is_editor() ) {
    if( isset( $_GET['user'] ) && $_GET['user'] != '' ) {
        $args['author'] = intval($_GET['user']);

    } else if( isset( $_GET['post_status'] ) && $_GET['post_status'] == 'mine' ) {
        $args['author'] = $userID;
    }
} else if( houzez_is_agency() ) {
    $agents = houzez_get_agency_agents($userID);
    
    if( isset( $_GET['user'] ) && $_GET['user'] != '' ) {
        $requested_user = intval($_GET['user']);
        // Only set author if requested user is current user or one of their agents
        if($requested_user === $userID || in_array($requested_user, $agents)) {
            $args['author'] = $requested_user;
        } else {
            // If requested user is not authorized, show no properties
            $args['author'] = -1; // This will return no results
        }
    } else if( isset( $_GET['post_status'] ) && $_GET['post_status'] == 'mine' ) {
        $args['author'] = $userID;
    } else if( $agents ) {
        if (!in_array($userID, $agents)) {
            $agents[] = $userID;
        }
        $args['author__in'] = $agents;
    } else {
        $args['author'] = $userID;
    }
} else {
    $args['author'] = $userID;
}

$args = apply_filters('houzez20_search_filters', $args);

// Set post status but exclude auto-draft when 'any' is selected
if ($qry_status == 'any') {
    $args['post_status'] = ['publish', 'pending', 'draft', 'expired', 'houzez_sold', 'disapproved', 'on_hold', 'private', 'future'];
} else {
    $args['post_status'] = [$qry_status];
}


$properties_query = new WP_Query($args); 
?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar -->
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_props', 'Properties'); ?></h2> 
            </div>
        <div class="add-export-btn">
            <ul class="d-flex align-items-center gap-2">
                <li><a href="<?php echo esc_url($dashboard_add_listing); ?>" class="btn btn-primary"><i class="houzez-icon icon-add-circle me-2"></i><?php echo esc_html__('Add New', 'houzez'); ?></a></li>
                </ul>
            </div>
        </div>

        <div id="houzez_messages"></div>

        <?php if( $properties_query->have_posts() || isset($_GET['is_search_result']) ): ?>
        
        <?php get_template_part('template-parts/dashboard/property/tabs'); ?>

        <div class="houzez-data-content"> 
            <?php get_template_part('template-parts/dashboard/property/filters'); ?>

            <div class="houzez-data-table">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th data-label="<?php echo esc_html__('Select', 'houzez'); ?>">
                                    <label class="control control--checkbox">
                                    <input type="checkbox" class="checkbox-delete form-check-input" id="listing_select_all" name="listing_multicheck">
                                    <span class="control__indicator"></span>
                                    </label>
                                </th>
                                <th data-label="<?php echo esc_html__('Thumbnail', 'houzez'); ?>" class="px-0"><?php echo esc_html__('Thumbnail', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Title', 'houzez'); ?>"><?php echo esc_html__('Title', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Status', 'houzez'); ?>"><?php echo esc_html__('Status', 'houzez'); ?></th>
                                <th data-label="" class="px-2"></th>
                                <th data-label="<?php echo esc_html__('ID', 'houzez'); ?>"><?php echo esc_html__('ID', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Price', 'houzez'); ?>"><?php echo esc_html__('Price', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Type', 'houzez'); ?>" class="px-2"><?php echo esc_html__('Type', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Date', 'houzez'); ?>" class="px-2"><?php echo esc_html__('Date', 'houzez'); ?></th>
                                <th data-label="<?php echo esc_html__('Actions', 'houzez'); ?>" class="text-center ps-0"><?php echo esc_html__('Actions', 'houzez'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while( $properties_query->have_posts() ): $properties_query->the_post();
                                get_template_part('template-parts/dashboard/property/property-item');
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php get_template_part('template-parts/dashboard/property/pagination'); ?>
        </div> 
        <?php else: ?>
        <div class="stats-box">
            <div class="dashboard-content-block">
                <?php echo esc_html__("You don't have any property listed.", 'houzez'); ?> <a href="<?php echo esc_url($dashboard_add_listing); ?>"><strong><?php echo esc_html__('Create a listing', 'houzez'); ?></strong></a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>