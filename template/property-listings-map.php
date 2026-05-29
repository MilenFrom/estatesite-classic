<?php
/**
 * Template Name: Property Listing Half Map
 * Updated to match half-map-search-results.php structure
 * Date: 06/03/25
 */
get_header();
global $search_qry, $paged;

// Set up the default view
$default_view_option = houzez_option('halfmap_posts_layout', 'list-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = true;
if (in_array($default_view_option, array('grid-view-v3', 'grid-view-v4', 'grid-view-v6', 'grid-view-v7', 'list-view-v4', 'list-view-v7'))) {
    $show_switch = false;
}

// Default arguments
$args = array(
    'default_view' => $default_view_option,
    'layout' => houzez_option('search_result_layout', 'right-sidebar'),
    'grid_columns' => houzez_option('search_grid_columns', '2'),
    'content_position' => houzez_get_listing_data('listing_page_content_area'),
    'show_switch' => $show_switch,
);

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Force no sidebar for list-v4
if ($current_item_template == 'list-v4') {
    $args['layout'] = 'no-sidebar';
}

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

// Search settings
$enable_save_search = houzez_option('enable_disable_save_search');
$search_num_posts = houzez_option('search_num_posts');
$number_of_prop = $search_num_posts ? $search_num_posts : 9;

// Set up query arguments
$search_qry = array(
    'post_type' => 'property',
    'posts_per_page' => $number_of_prop,
    'paged' => $paged,
    'post_status' => 'publish'
);

$search_qry = apply_filters('houzez20_property_filter', $search_qry);
$search_qry = houzez_prop_sort($search_qry);
$search_query = new WP_Query($search_qry);  
$total_properties = $search_query->found_posts;

// Half map search settings
$enable_search = houzez_option('enable_halfmap_search', 1);
$search_style = houzez_option('halfmap_search_layout', 'v4');

if (isset($_GET['halfmap_search']) && $_GET['halfmap_search'] != '') {
    $search_style = $_GET['halfmap_search'];
}

if (wp_is_mobile()) {
    $search_style = 'v1';
}

if ($enable_search != 0 && $search_style != 'v4') {
    get_template_part('template-parts/search/search-half-map-header');
}
get_template_part('template-parts/search/search-mobile-nav');

 // Check for specific half-map template variations
 $template_name = $current_item_template;
 $half_map_template = '';
 
 // Define templates that have half-map variations
 $half_map_templates = array('list-v1', 'list-v2', 'list-v4', 'list-v7');
 
 // Check if current template has a half-map variation
 if (in_array($template_name, $half_map_templates)) {
     $template_name = $template_name . '-half-map';
 }  

$map_data = houzez_get_half_map_data();
$map_options = houzez_get_map_options();
$map_options_json = esc_attr( wp_json_encode( $map_options ) );
?>
<section class="half-map-wrap d-flex w-100">
    <div id="map-view-wrap" class="half-map-left-wrap" role="complementary">
        <div class="map-wrap">
            <?php get_template_part('template-parts/map-buttons'); ?>
            
            <div id="houzez-properties-map" data-map='<?php echo $map_data; ?>' data-options='<?php echo $map_options_json; ?>'></div>

            <div id="houzez-map-message" class="houzez-map-message">
                <div class="map-info-message"></div>
            </div>

            <div id="houzez-map-loading" class="houzez-map-loading">
                <div class="mapPlaceholder">
                    <div class="loader-ripple spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                        <div class="bounce4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- half-map-left-wrap -->

    <div id="half-map-listing-area" class="half-map-right-wrap listing-<?php echo esc_attr($item_version); ?>" role="main">
        <?php 
        if ($enable_search != 0 && $search_style == 'v4') {
            get_template_part('template-parts/search/search-half-map');
        }
        ?>

        <?php
        if ($args['content_position'] !== '1') {
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    ?>
                    <article <?php post_class(); ?>>
                        <?php the_content(); ?>
                    </article>
                    <?php
                }
            } 
        }?>

        <div class="page-title-wrap p-4">
            <div class="d-flex align-items-center">
                <div class="page-title flex-grow-1">
                    <span id="total-results" data-total-results="<?php echo esc_attr($total_properties); ?>"><?php echo esc_attr($total_properties); ?></span> <?php esc_html_e('Results Found', 'houzez');?>
                </div>

                <?php get_template_part('template-parts/listing/listing-sort-by'); ?>  
                <?php 
                if ($args['show_switch']) {
                    get_template_part('template-parts/listing/listing-switch-view'); 
                }?> 
            </div><!-- d-flex -->  
        </div><!-- page-title-wrap -->

        <div id="houzez_ajax_container">
            <div class="<?php echo esc_attr($listing_view_class); ?> mx-0" role="list" data-view="<?php echo esc_attr($current_view); ?>" data-layout="<?php echo esc_attr($template_name);?>" data-css="<?php echo esc_attr($listing_view_class); ?> mx-0">
                <?php
                if ($search_query->have_posts()) :
                    while ($search_query->have_posts()) : $search_query->the_post();
                        get_template_part('template-parts/listing/item', $template_name);
                    endwhile;
                else:
                    echo '<div class="search-no-results-found-wrap">';
                        echo '<div class="search-no-results-found flex-grow-1 text-center mx-4">';
                            esc_html_e('No results found', 'houzez');
                        echo '</div>';
                    echo '</div>';
                endif;
                wp_reset_postdata();
                ?>
            </div><!-- listing-view -->
            
            <?php houzez_ajax_pagination($search_query->max_num_pages); ?>
        </div><!-- houzez_ajax_container -->

        <?php
        if ('1' === $args['content_position']) {
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    ?>
                    <section class="content-wrap">
                        <?php the_content(); ?>
                    </section>
                    <?php
                }
            }
        }
        ?>
    </div><!-- half-map-right-wrap -->
</section><!-- half-map-wrap -->

<?php get_template_part('template-parts/listing/partials/mobile-map-switch'); ?>
<?php get_footer(); ?>