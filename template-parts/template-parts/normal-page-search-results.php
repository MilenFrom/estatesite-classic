<?php
/**
 * Normal Page Search Results Template
 * Updated to match taxonomy-common.php structure
 * Date: 06/03/25
 */
global $post, $paged, $listing_founds, $search_qry;

// Set up the default view
$default_view_option = houzez_option('search_result_posts_layout', 'list-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = true;
if (in_array($default_view_option, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v4', 'list-view-v7'))) {
    $show_switch = false;
}

// Default arguments
$args = array(
    'default_view' => $default_view_option,
    'layout' => houzez_option('search_result_layout', 'right-sidebar'),
    'grid_columns' => houzez_option('search_grid_columns', '3'),
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

// Set container class based on layout
$container_class = 'container';
$container_class = apply_filters('houzez_search_container_class', $container_class, $args['layout']);

// Set up sidebar and content classes
$show_sidebar = ($args['layout'] != 'no-sidebar');

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if (isset($sticky_sidebar['search_sidebar']) && $sticky_sidebar['search_sidebar'] != 0) {
    $is_sticky = 'houzez_sticky';
}

// Set content column class based on layout and sidebar
$content_col_class = $show_sidebar ? 'col-lg-8 col-md-12 bt-content-wrap' : 'col-lg-12 col-md-12';
if ($args['layout'] == 'left-sidebar') {
    $content_col_class .= ' order-lg-2 order-1 wrap-order-first'; // Right side on desktop (lg), first on mobile
}
$content_col_class = apply_filters('houzez_search_content_class', $content_col_class, $show_sidebar);

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

// Search settings
$enable_save_search = houzez_option('enable_disable_save_search');
$search_num_posts = houzez_option('search_num_posts');
$number_of_prop = $search_num_posts ? $search_num_posts : 9;

if (is_front_page()) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

// Set up query arguments
$search_qry = array(
    'post_type' => 'property',
    'posts_per_page' => $number_of_prop,
    'paged' => $paged,
    'post_status' => 'publish'
);

$search_qry = apply_filters('houzez20_search_filters', $search_qry);
$search_qry = apply_filters('houzez_sold_status_filter', $search_qry);
$search_qry = houzez_prop_sort($search_qry);
$search_query = new WP_Query($search_qry);

// Get total records found
$total_records = $search_query->found_posts;

$record_found_text = esc_html__('Result Found', 'houzez');
if ($total_records > 1) {
    $record_found_text = esc_html__('Results Found', 'houzez');
}
?>
<section class="listing-wrap listing-<?php echo esc_attr($item_version); ?>" role="region">
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb'); ?> 
            <div class="d-flex align-items-center">
                <div class="page-title flex-grow-1">
                    <h1><?php the_title(); ?></h1>
                </div><!-- page-title -->
                <?php 
                if ($args['show_switch']) {
                    get_template_part('template-parts/listing/listing-switch-view'); 
                }?> 
            </div><!-- d-flex -->  
        </div><!-- page-title-wrap -->

        <div class="row">
            <div class="<?php echo esc_attr($content_col_class); ?>">
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

                <div class="listing-tools-wrap">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <strong><?php echo esc_attr($total_records); ?> <?php echo esc_attr($record_found_text); ?></strong>
                        </div>
                        <?php get_template_part('template-parts/listing/listing-sort-by'); ?>   
                        <?php
                        if ($enable_save_search != 0) {
                            get_template_part('template-parts/search/save-search-btn');
                        }?> 
                    </div><!-- d-flex -->
                </div><!-- listing-tools-wrap -->

                <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                    <?php
                    if ($search_query->have_posts()) :
                        while ($search_query->have_posts()) : $search_query->the_post();
                            get_template_part('template-parts/listing/item', $current_item_template);
                        endwhile;
                    else:
                        echo '<div class="search-no-results-found-wrap">';
                            echo '<div class="search-no-results-found">';
                                esc_html_e('No results found', 'houzez');
                            echo '</div>';
                        echo '</div>';
                    endif;
                    wp_reset_postdata();
                    ?> 
                </div><!-- listing-view -->

                <?php houzez_pagination($search_query->max_num_pages, $total_records, $number_of_prop); ?>

            </div><!-- bt-content-wrap -->

            <?php if ($show_sidebar) : ?>
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo $args['layout'] == 'left-sidebar' ? 'order-lg-1 order-2' : ''; ?> <?php echo esc_attr($is_sticky); ?>">
                <aside class="sidebar-wrap">
                    <?php
                    if (is_active_sidebar('search-sidebar')) {
                        dynamic_sidebar('search-sidebar');
                    }
                    ?>
                </aside>
            </div><!-- bt-sidebar-wrap -->
            <?php endif; ?>
        </div><!-- row -->
    </div><!-- container -->
</section><!-- listing-wrap -->

<?php
if ('1' === $args['content_position']) {
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            ?>
            <section class="content-wrap">
                <div class="container">
                    <?php the_content(); ?>
                </div>
            </section>
            <?php
        }
    }
}
?>