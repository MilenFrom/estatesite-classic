<?php 
$review_likes = get_post_meta(get_the_ID(), 'review_likes', true); 
$review_dislikes = get_post_meta(get_the_ID(), 'review_dislikes', true);
if(empty($review_likes)) {
	$review_likes = 0;
}
if(empty($review_dislikes)) {
	$review_dislikes = 0;
}
?>
<li id="review-<?php the_ID(); ?>" class="property-review mb-4">
	<div class="d-flex gap-4">
		<div class="review-image">
			<img class="rounded-circle" src="<?php echo esc_url( houzez_get_profile_pic() );?>" width="64" height="64" alt="thumb">
		</div>
		<div class="review-message d-flex flex-column flex-grow-1">
			<div class="d-flex align-items-center gap-3">
				<h4 class="review-title mb-0"><?php the_title(); ?></h4>
				<div class="rating-score-wrap flex-grow-1" role="complementary">
					<span class="star d-flex align-items-center" role="img">	
						<?php echo houzez_get_stars(get_post_meta(get_the_ID(), 'review_stars', true), false); ?>
					</span>
				</div>
			</div><!-- d-flex -->
			<time class="review-date my-2" datetime="<?php echo get_the_time('c'); ?>"><i class="houzez-icon icon-attachment me-1"></i> <?php printf( esc_html__( '%s ago', 'houzez' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></time>

			<div>
				<?php the_content(); ?>
			</div>

			<div class="review-like mt-2 text-end">
				<ul class="likes-container-js list-inline">
					<li class="list-inline-item"><span class="vote-msg"></span></li>
					<?php get_template_part('template-parts/loader'); ?>
					<li class="list-inline-item review-like-button">
						<a class="hz-like-dislike-js" data-id="<?php echo intval(get_the_ID()); ?>" data-type="likes" data-msg="<?php esc_html_e('You have already voted', 'houzez'); ?>" role="button">
							<i class="houzez-icon icon-like me-1"></i>
						</a> 
						<span class="likes-count"><?php echo esc_attr($review_likes); ?></span>
					</li>
					<li class="list-inline-item review-dislike-button">
						<a class="hz-like-dislike-js" data-id="<?php echo intval(get_the_ID()); ?>" data-type="dislikes" data-msg="<?php esc_html_e('You have already voted', 'houzez'); ?>" role="button">
							<i class="houzez-icon icon-dislike me-1"></i>
						</a> 
						<span class="dislikes-count"><?php echo esc_attr($review_dislikes); ?></span>
					</li>
				</ul>
			</div>
		</div><!-- review-message -->
	</div><!-- d-flex -->
</li><!-- property-review -->