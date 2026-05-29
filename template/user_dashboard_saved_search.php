<?php
/**
 * Template Name: User Dashboard Saved Search
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 11/01/16
 * Time: 4:35 PM
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $wpdb, $houzez_local, $houzez_search_data;

$userID = get_current_user_id();

$table_name = $wpdb->prefix . 'houzez_search';
$sql = $wpdb->prepare(
    "SELECT * FROM {$table_name} WHERE auther_id = %d ORDER BY id DESC",
    $userID
);

$results = $wpdb->get_results($sql, OBJECT);

get_header('dashboard'); ?>

<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_saved_searches', 'Saved Searches'); ?></h2> 
            </div>
        </div>

        <?php
        if ( sizeof( $results ) !== 0 ) : ?>
        <div class="houzez-data-content mt-4"> 
        <div class="houzez-data-table">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                    <th><?php echo esc_html__('Search Parameters', 'houzez'); ?></th>
                    <th><?php echo esc_html__('Actions', 'houzez'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($results as $houzez_search_data) {
                        get_template_part('template-parts/dashboard/saved-search-item');
                    }
                    ?>
                </tbody>
                </table>
            </div>
            </div>
        </div> 
        <?php else : ?>
        <div class="stats-box">
            <?php echo esc_html__('You don\'t have any saved search listed.', 'houzez'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer('dashboard'); ?>