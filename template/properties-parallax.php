<?php
/**
 * Template Name: Template Listings Parallax
 */
get_header(); ?>


<?php
global $post;
$page_content_position = houzez_get_listing_data('listing_page_content_area');

$latest_listing_args = array(
    'post_type' => 'property',
    'post_status' => 'publish'
);

$latest_listing_args = apply_filters( 'houzez20_property_filter', $latest_listing_args );

$latest_listing_args = houzez_prop_sort ( $latest_listing_args );

$listings_query = new WP_Query( $latest_listing_args );
$total_listing_found = $listings_query->found_posts;
$fave_prop_no = get_post_meta( $post->ID, 'fave_prop_no', true );
$fave_prop_no = !empty($fave_prop_no) ? (int)$fave_prop_no : null;


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

<section class="listing-wrap listing-view" role="region" >
<?php	
$i = 1;
if ( $listings_query->have_posts() ) :
    while ( $listings_query->have_posts() ) : $listings_query->the_post(); 

    $post_meta_data     = get_post_custom(get_the_ID());
	$prop_images        = get_post_meta( get_the_ID(), 'fave_property_images', false );
	$prop_address       = get_post_meta( get_the_ID(), 'fave_property_map_address', true );
	$prop_featured      = get_post_meta( get_the_ID(), 'fave_featured', true );

	$thumb_id = get_post_thumbnail_id( $post->ID );
	$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', false);
	$thumb_url = $thumb_url_array[0] ?? '';
    ?>

    <div class="item-listing-wrap item-listing-parallax" style="height: 600px;">
        <a class="item-listing-parallax-link" href="<?php echo esc_url(get_permalink()); ?>"></a>
        <div class="item-parallax-inner houzez-parallax" data-parallax-bg-image="<?php echo esc_url($thumb_url); ?>">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="item-parallax-wrap" data-aos="fade">
                    <div class="labels-wrap d-flex align-items-center mb-2" role="group">
                        <?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
                        <?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
                    </div>
                    <h2 class="item-title mb-2">
                        <a href="<?php echo esc_url(get_permalink()); ?>" role="link"><?php the_title(); ?></a>
                    </h2><!-- item-title -->
                    <address class="item-address mb-3" role="contentinfo">
                        <i class="houzez-icon icon-pin me-1" aria-hidden="true"></i>
                        <span><?php echo $prop_address; ?></span>
                    </address>
                    <ul class="item-price-wrap d-flex flex-column gap-2 mb-4" role="list">
                        <?php echo houzez_listing_price_v1(); ?>	
                    </ul>
                    <?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
                </div><!-- item-parallax-wrap -->
            </div>
        </div><!-- parallax -->
    </div><!-- item-listing-parallax -->

<?php
    endwhile;
else:
    get_template_part('template-parts/listing/item', 'none');
endif;
?>
</section>

<?php houzez_pagination( $listings_query->max_num_pages, $total_listing_found, $fave_prop_no ); ?>

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