<?php 
if(is_author()) {
	global $author_id;
	$rating_id = $author_id;
} else {
	$rating_id = get_the_ID();
}
$total_ratings = get_post_meta($rating_id, 'houzez_total_rating', true);

if( empty( $total_ratings ) ) {
	$total_ratings = 0;
}

$is_single_realtor = $args['is_single_realtor'] ?? false;

if( $is_single_realtor ) {
	$rating_score_wrap_class = 'rating-score-wrap d-flex align-items-center gap-2 mb-1';
	$star_class = 'star d-flex align-items-center gap-1';
} else {
	$rating_score_wrap_class = 'rating-score-wrap flex-grow-1';
	$star_class = 'star d-flex align-items-center';
}
?>
<div class="<?php echo esc_attr($rating_score_wrap_class); ?>" role="complementary">
	<span class="<?php echo esc_attr($star_class); ?>" role="img">
	    <?php if ( is_singular( array( 'houzez_agent', 'houzez_agency' ) ) && $total_ratings > 0 ) : ?>
		    <span class="rating-score-text"><?php echo esc_attr( round( (float) $total_ratings, 2 ) ); ?></span>
		<?php elseif ( is_author() && $total_ratings > 0 ) : ?>
		    <span class="rating-score-text"><?php echo esc_attr( round( (float) $total_ratings, 2 ) ); ?></span>
		<?php endif; ?>

		<?php echo houzez_get_stars($total_ratings, false); ?>

	    <?php if(is_singular( array('houzez_agent', 'houzez_agency', 'fts_builder') ) || is_author() ) { ?>
	        <a class="all-reviews" href="#review-scroll"><?php echo houzez_option('agency_lb_all_reviews', esc_html__('See all reviews', 'houzez')); ?></a>
	    <?php } ?>
	</span>
</div><!-- rating-score-wrap -->