<?php
/**
 * Common template for property listings
 * Created by Waqas Riaz.
 * Date: 06/03/25
 * Time: 2:30 PM
 * 
 * @param string $args['default_view'] Default view type (grid or list)
 * @param string $args['item_template'] Default item template to load (v1 or list-v1)
 * @param string $args['layout'] Layout type (default, fullwidth)
 * @param int $args['columns'] Number of columns (2, 3, 4)
 * @param bool $args['show_sidebar'] Whether to show sidebar
 * @param string $args['custom_listing_class'] Optional custom class for the listing view
 * @param bool $args['show_switch'] Whether to show the view switcher (defaults to true for v1, v2, v7 templates)
 */

$default_view = $args['default_view'] ?? 'grid';
$item_template = $args['item_template'] ?? 'v1';
$layout = $args['layout'] ?? 'with-sidebar';
$columns = $args['columns'] ?? 2;
$show_sidebar = $args['show_sidebar'] ?? true;
$show_switch = $args['show_switch'] ?? false;

$sidebar_position = houzez_option('template_sidebar_pos', 'right-sidebar');

// Allow filtering of template arguments
$args = apply_filters('houzez_listing_template_args', $args);

$listing_data = houzez_setup_listing_template($default_view);
extract($listing_data);

// Determine which item template to use based on current view
$current_item_template = $current_view == 'list' ? 'list-' . $item_template : $item_template;
$current_item_template = apply_filters('houzez_listing_item_template', $current_item_template, $current_view, $item_template);

// Set container class based on layout
$container_class = 'container';
$container_class = apply_filters('houzez_listing_container_class', $container_class, $layout);

// Set content column class based on layout and sidebar
$content_col_class = $show_sidebar ? 'col-lg-8 col-md-12 bt-content-wrap' : 'col-lg-12 col-md-12';
$sidebar_class = 'col-lg-4 col-md-12 bt-sidebar-wrap';

if ($sidebar_position == 'left-sidebar' && $show_sidebar) {
    $content_col_class .= ' order-lg-2'; // Right side on desktop (lg)
    $sidebar_class .= ' order-lg-1'; // Left side on desktop (lg)
}
$content_col_class = apply_filters('houzez_listing_content_class', $content_col_class, $show_sidebar);

$max_num_pages = $listings_query->max_num_pages;

$class_margin = '';
if( $max_num_pages <= 1 && ! $show_sidebar ) {
    $class_margin = 'mb-4';
}

// Set listing view class based on view type and columns
$listing_view_class = $args['custom_listing_class'] ?? 'listing-view';

// If no custom class provided, build the class based on view type
if (!isset($args['custom_listing_class'])) {
    if ($current_view == 'list') {
        $listing_view_class .= ' list-view row gy-4 gx-4';
    } else {
        $listing_view_class .= ' grid-view row';
        if ($layout == 'fullwidth') {
            if ($columns == 4) {
                $listing_view_class .= ' row-cols-1 row-cols-xl-4 row-cols-lg-3 row-cols-md-2 gy-4 gx-4';
            } elseif ($columns == 3) {
                $listing_view_class .= ' row-cols-lg-3 row-cols-md-2 row-cols-sm-1 gy-4 gx-4';
            } else {
                $listing_view_class .= ' row-cols-1 row-cols-md-2 gy-4 gx-4';
            }
        } else {
            $listing_view_class .= ' row-cols-1 row-cols-md-2 gy-4 gx-4';
        }
    }
}
$listing_view_class = apply_filters('houzez_listing_view_class', $listing_view_class, $current_view, $layout, $columns);
?>
<section class="listing-wrap listing-<?php echo esc_attr($current_item_template); ?> <?php echo esc_attr($class_margin); ?>" role="region">
    <?php do_action('houzez_before_listing_wrap'); ?>
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="page-title-wrap">
            <?php get_template_part('template-parts/page/breadcrumb');?> 
            <div class="d-flex align-items-center">
                <?php 
                get_template_part('template-parts/page/page-title'); 
                if ($show_switch) {
                    get_template_part('template-parts/listing/listing-switch-view');
                }?> 
            </div><!-- d-flex -->  
        </div><!-- page-title-wrap -->

        <div class="row">
            <div class="<?php echo esc_attr($content_col_class); ?>">
                <?php do_action('houzez_before_listing_content'); ?>
                
                <?php
                if ( $page_content_position !== '1' ) {
                    if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post();
                            ?>
                            <article <?php post_class(); ?>>
                                <?php the_content(); ?>
                            </article>
                            <?php
                        }
                    } 
                }?>
                <?php get_template_part( 'template-parts/listing/listing', 'tools' ); ?>

                <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                    <?php do_action('houzez_before_listing_items'); ?>
                    <?php
                    if ( $listings_query->have_posts() ) :
                        while ( $listings_query->have_posts() ) : $listings_query->the_post();
                            get_template_part('template-parts/listing/item', $current_item_template, array('default_view' => $current_view));
                        endwhile;
                        wp_reset_postdata();
                    else:
                        get_template_part('template-parts/listing/item-none');
                    endif;
                    ?>   
                </div><!-- listing-view -->
                <?php houzez_pagination( $listings_query->max_num_pages, $total_listing_found, $fave_prop_no ); ?>
                <?php do_action('houzez_after_listing_items'); ?>
            </div><!-- bt-content-wrap -->
            <?php if($show_sidebar): ?>
            <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo esc_attr($is_sticky); ?>">
                <?php get_sidebar('property');?> 
            </div><!-- bt-sidebar-wrap -->
            <?php endif; ?>
        </div><!-- row -->
    </div><!-- container -->
    <?php do_action('houzez_after_listing_wrap'); ?>
</section><!-- listing-wrap -->

<?php
if ('1' === $page_content_position ) {
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            ?>
            <section class="content-wrap">
                <?php the_content(); ?>
            </section>
            <?php
        }
    }
} 