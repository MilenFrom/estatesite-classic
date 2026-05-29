<?php
/**
 * Common template for property taxonomies
 * Created by Claude.
 * Date: 06/03/25
 * Time: 3:30 PM
 * 
 * @param string $args['default_view'] Default view type (grid or list)
 * @param string $item_version Default item template (v1, v2, etc.)
 * @param string $args['layout'] Layout type (default, left-sidebar, right-sidebar, no-sidebar)
 * @param int $args['grid_columns'] Number of columns for grid view (2, 3, 4)
 * @param bool $args['show_sidebar'] Whether to show sidebar
 * @param string $args['content_position'] Position of taxonomy content (above or below)
 * @param bool $args['show_switch'] Whether to show the view switcher
 */

// Main execution starts here
global $total_listing_found;

// Get current taxonomy information with validation
$taxonomy_data = houzez_get_current_taxonomy_data();
$current_term = $taxonomy_data['current_term'];
$taxonomy_title = $taxonomy_data['taxonomy_title'];
$taxonomy_name = $taxonomy_data['taxonomy_name'];

// If no valid taxonomy term was found, use a fallback title
if (empty($taxonomy_title)) {
    $taxonomy_title = esc_html__('Properties', 'houzez');
}

// Get arguments from query var if available
$passed_args = get_query_var('taxonomy_template_args');

// Get the default view from options
$default_view_option = houzez_option('taxonomy_posts_layout', 'list-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = true;
if (in_array($default_view_option, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v4'))) {
    $show_switch = false;
}

// Default arguments
$default_args = array(
    'default_view' => $default_view_option,
    'layout' => houzez_option('taxonomy_layout', 'right-sidebar'),
    'grid_columns' => houzez_option('taxonomy_grid_columns', '3'),
    'content_position' => houzez_option('taxonomy_content_position', 'above'),
    'show_switch' => $show_switch,
);

// Merge provided args with defaults
$args = !empty($passed_args) ? wp_parse_args($passed_args, $default_args) : $default_args;

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Set container class based on layout
$container_class = 'container';
$container_class = apply_filters('houzez_taxonomy_container_class', $container_class, $args['layout']);

// Set up sidebar and content classes
$show_sidebar = ($args['layout'] != 'no-sidebar');

// Force no sidebar for list-v4
if ($current_item_template == 'list-v4') {
    $show_sidebar = false;
    $args['layout'] = 'no-sidebar';
}

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if (isset($sticky_sidebar['property_listings']) && $sticky_sidebar['property_listings'] != 0) {
    $is_sticky = 'houzez_sticky';
}

// Set content column class based on layout and sidebar
$content_col_class = $show_sidebar ? 'col-lg-8 col-md-12 bt-content-wrap' : 'col-lg-12 col-md-12';
$sidebar_class = 'col-lg-4 col-md-12 bt-sidebar-wrap';

if ($args['layout'] == 'left-sidebar') {
    $content_col_class .= ' order-lg-2'; // Right side on desktop (lg)
    $sidebar_class .= ' order-lg-1'; // Left side on desktop (lg)
}
$content_col_class = apply_filters('houzez_taxonomy_content_class', $content_col_class, $show_sidebar);

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

// Set up query arguments
$sort_args = array('post_status' => 'publish');
$sort_args = apply_filters('houzez_sold_status_filter', $sort_args);
$sort_args = houzez_prop_sort($sort_args);
global $wp_query;
$args_query = array_merge($wp_query->query_vars, $sort_args);

// Create new query with our arguments
$taxonomy_query = new WP_Query($args_query);

// Get total listings found
$total_listing_found = $taxonomy_query->found_posts;

$property_label = houzez_option('cl_property', 'Property');
if ($total_listing_found > 1) {
    $property_label = houzez_option('cl_properties', 'Properties');
}

$max_num_pages = $taxonomy_query->max_num_pages;

$class_margin = '';
if( $max_num_pages <= 1 && ! $show_sidebar ) {
    $class_margin = 'mb-4';
}

do_action('houzez_before_taxonomy_template');
?>

<section class="listing-wrap listing-<?php echo esc_attr($view_settings['item_version']); ?> <?php echo esc_attr($class_margin); ?>" role="region">
    <?php do_action('houzez_before_taxonomy_wrap'); ?>
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb'); ?> 
            <div class="d-flex align-items-center">
                <div class="page-title flex-grow-1">
                    <h1><?php echo esc_html($taxonomy_title); ?></h1>
                </div><!-- page-title -->
                <?php 
                if ($args['show_switch']) {
                    get_template_part('template-parts/listing/listing-switch-view'); 
                }?> 
            </div><!-- d-flex -->  
        </div><!-- page-title-wrap -->

        <div class="row">
            <div class="<?php echo esc_attr($content_col_class); ?>">
                <?php do_action('houzez_before_taxonomy_content'); ?>
                
                <?php
                if ($args['content_position'] == 'above' && $current_term) { ?>
                    <article class="taxonomy-description">
                        <?php echo wp_kses_post(term_description()); ?>
                    </article>
                <?php
                }?>
                
                <?php get_template_part('template-parts/listing/listing-tools'); ?>

                <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                    <?php do_action('houzez_before_taxonomy_items'); ?>
                    <?php
                    if ($taxonomy_query->posts) :
                        foreach ($taxonomy_query->posts as $post) :
                            setup_postdata($post);
                            get_template_part('template-parts/listing/item', $current_item_template);
                        endforeach;
                        wp_reset_postdata();
                    else:
                        get_template_part('template-parts/listing/item-none');
                    endif;
                    ?> 
                    <?php do_action('houzez_after_taxonomy_items'); ?>
                </div><!-- listing-view -->
                
                <?php houzez_pagination($max_num_pages, $total_listing_found, houzez_option('taxonomy_num_posts')); ?>
                
            </div><!-- bt-content-wrap -->

            <?php if ($show_sidebar) : ?>
            <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo esc_attr($is_sticky); ?>">
                <?php get_sidebar('property'); ?>
            </div><!-- bt-sidebar-wrap -->
            <?php endif; ?>
        </div><!-- row -->
    </div><!-- container -->
    <?php do_action('houzez_after_taxonomy_wrap'); ?>
</section><!-- listing-wrap -->

<?php
if ($args['content_position'] == 'bottom' && $current_term) { ?>
    <section class="content-wrap">
        <div class="container">
            <article class="taxonomy-description">
                <?php echo wp_kses_post(term_description()); ?>
            </article>
        </div>
    </section>
<?php
} 