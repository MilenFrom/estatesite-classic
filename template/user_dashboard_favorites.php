<?php
/**
 * Template Name: User Dashboard Favorite Properties
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 11/01/16
 * Time: 4:35 PM
 */


global $houzez_local, $current_user, $favorite_properties_query;
$userID     = get_current_user_id();

// If the user is logged in, retrieve favorites from user meta.
if ( is_user_logged_in() ) {
    $fav_ids = get_user_meta( $userID, 'houzez_favorites', true );
    if ( empty( $fav_ids ) || ! is_array( $fav_ids ) ) {
        $fav_ids = array();
    }
} else {
    // For non-logged-in users, use cookie or URL parameters as fallback.
    $fav_ids = isset( $_COOKIE['houzez_favorite_listings'] ) ? explode( ',', $_COOKIE['houzez_favorite_listings'] ) : array();
    if ( empty( $fav_ids[0] ) ) {
        $fav_ids = isset( $_GET['ids'] ) ? $_GET['ids'] : '';
        $fav_ids = explode( ',', $fav_ids );
    }
}

// Sanitize favorite IDs: convert to integers and remove empty values.
$fav_ids = array_map('absint', $fav_ids);
$fav_ids = array_filter($fav_ids);
if ( empty($fav_ids) ) {
    // No favorites; set a non-existent ID to prevent any posts from showing.
    $fav_ids = array( 0 );
}

$args = array(
    'post_type' => 'property',
    'post__in' => $fav_ids,
    'posts_per_page' => -1
);
$favorite_properties_query = new WP_Query($args);

get_header('dashboard');
?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_favorite', 'Favorites'); ?></h2> 
            </div>
        </div>

        <?php 
        if ( $favorite_properties_query->have_posts() ) {?>
        <div class="houzez-data-content mt-4"> 
            <div class="houzez-data-table">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                            <th data-label="<?php echo esc_html__('Thumbnail', 'houzez'); ?>"><?php echo esc_html__('Thumbnail', 'houzez'); ?></th>
                            <th data-label="<?php echo esc_html__('Title', 'houzez'); ?>"><?php echo esc_html__('Title', 'houzez'); ?></th>
                            <th data-label="<?php echo esc_html__('Status', 'houzez'); ?>"><?php echo esc_html__('Status', 'houzez'); ?></th>
                            <th data-label="<?php echo esc_html__('ID', 'houzez'); ?>"><?php echo esc_html__('ID', 'houzez'); ?></th>
                            <th data-label="<?php echo esc_html__('Price', 'houzez'); ?>"><?php echo esc_html__('Price', 'houzez'); ?></th>
                            <th data-label="<?php echo esc_html__('Type', 'houzez'); ?>"><?php echo esc_html__('Type', 'houzez'); ?></th>
                            <?php if( is_user_logged_in() ): ?>
                            <th data-label="<?php echo esc_html__('Date', 'houzez'); ?>"><?php echo esc_html__('Date', 'houzez'); ?></th>
                            <?php endif; ?>
                            <th data-label="<?php echo esc_html__('Actions', 'houzez'); ?>"><?php echo esc_html__('Actions', 'houzez'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($favorite_properties_query->have_posts()) : $favorite_properties_query->the_post();
                            get_template_part('template-parts/dashboard/property/favorite-item');
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
        <?php
        } else { ?>
        <div class="stats-box">
            <?php echo esc_html__('You don\'t have any property listed.', 'houzez'); ?>
        </div>
        <?php
        }?>
    </div>
</div>

<?php get_footer('dashboard'); ?>