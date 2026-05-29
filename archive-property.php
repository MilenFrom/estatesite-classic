<?php
/**
 * Archive Property Template
 * Created by Waqas Riaz.
 * Date: 06/03/25
 * Time: 3:30 PM
 */
get_header();

global $post, $total_listing_found;

// Set up the default view
$default_view_option = houzez_option('taxonomy_posts_layout', 'list-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = true;
if (in_array($default_view_option, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $show_switch = false;
}

// Default arguments
$args = array(
    'default_view' => $default_view_option,
    'layout' => houzez_option('taxonomy_layout', 'right-sidebar'),
    'grid_columns' => houzez_option('taxonomy_grid_columns', '3'),
    'content_position' => houzez_option('taxonomy_content_position', 'above'),
    'show_switch' => $show_switch,
);

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

// Create the query
$property_query = new WP_Query($args_query);

// Get total listings found
$total_listing_found = $property_query->found_posts;

$property_label = houzez_option('cl_property', 'Property');
if ($total_listing_found > 1) {
    $property_label = houzez_option('cl_properties', 'Properties');
}

do_action('houzez_before_taxonomy_template');
?>

<section class="listing-wrap listing-<?php echo esc_attr($view_settings['item_version']); ?>" role="region">
    <?php do_action('houzez_before_taxonomy_wrap'); ?>
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb'); ?> 
            <div class="d-flex align-items-center">
                <div class="page-title flex-grow-1">
                    <h1><?php echo post_type_archive_title(); ?></h1>
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
                if ($args['content_position'] == 'above') { ?>
                    <article class="taxonomy-description">
                        <?php echo wp_kses_post(term_description()); ?>
                    </article>
                <?php
                }?>
                
                <?php get_template_part('template-parts/listing/listing-tools'); ?>

                <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                    <?php do_action('houzez_before_taxonomy_items'); ?>
                    <?php
                    if ($property_query->posts) :
                        foreach ($property_query->posts as $post) :
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
                
                <?php houzez_pagination($property_query->max_num_pages, $total_listing_found, houzez_option('taxonomy_num_posts')); ?>
                
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
if ($args['content_position'] == 'bottom') { ?>
    <section class="content-wrap">
        <div class="container">
            <article class="taxonomy-description">
                <?php echo wp_kses_post(term_description()); ?>
            </article>
        </div>
    </section>
<?php
}

get_footer();
?>