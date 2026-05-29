<?php
/**
 * Template Name1: Template Projects Listings
 */
get_header();

global $post, $total_listing_found;

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if( isset($sticky_sidebar['project_listings']) && $sticky_sidebar['project_listings'] != 0 ) { 
    $is_sticky = 'houzez_sticky'; 
}

$page_content_position = houzez_get_listing_data('listing_page_content_area');

$query_args = array(
    'post_type' => 'project',
    'post_status' => 'publish'
);

$query_args = apply_filters( 'houzez_project_filter', $query_args );
$query_args = houzez_prop_sort ( $query_args );

$listings_query = new WP_Query( $query_args );
$total_listing_found = $listings_query->found_posts;

$fave_prop_no = get_post_meta( $post->ID, 'fave_prop_no', true );
$fave_prop_no = !empty($fave_prop_no) ? (int)$fave_prop_no : null;
?>
<section class="listing-wrap listing-v6 projects-listing-v6">
    <div class="container">
        
        <div class="page-title-wrap">

            <?php get_template_part('template-parts/project/listing-page-title'); ?>

        </div><!-- page-title-wrap -->

        <div class="row">
            <div class="col-lg-8 col-md-12 bt-content-wrap <?php echo houzez_option('template_sidebar_pos', ''); ?>"> 

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

                <?php get_template_part( 'template-parts/project/listing', 'tools' ); ?>

                <div class="listing-view grid-view card-deck">
                    <?php
                    if ( $listings_query->have_posts() ) :
                        while ( $listings_query->have_posts() ) : $listings_query->the_post();

                            get_template_part('template-parts/project/item', 'v1');

                        endwhile;
                        wp_reset_postdata();
                    else:
                        get_template_part('template-parts/project/item', 'none');
                    endif;
                    ?>   
                </div><!-- listing-view -->

                <?php houzez_pagination( $listings_query->max_num_pages, $total_listing_found, $fave_prop_no ); ?>

            </div><!-- bt-content-wrap -->
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo esc_attr($is_sticky); ?>">
                <?php get_sidebar('project'); ?>
            </div><!-- bt-sidebar-wrap -->
        </div><!-- row -->
    </div><!-- container -->
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
?>

<?php get_footer(); ?>