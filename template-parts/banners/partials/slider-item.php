<?php
$slider_img = get_post_meta( get_the_ID(), 'fave_prop_slider_image', true );
$img_url = wp_get_attachment_image_src( $slider_img, 'full', true );
$img_url = $img_url[0];
if(empty($slider_img)) {
	$img_url = wp_get_attachment_url( get_post_thumbnail_id() );
}
?>
<div class="property-slider-item-wrap d-flex justify-content-start align-items-center" style="background-image: url('<?php echo esc_url($img_url); ?>');"	>
	<div class="property-slider-item">
		<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
		<?php get_template_part('template-parts/listing/partials/item-title'); ?>
		<?php get_template_part('template-parts/listing/partials/item-address'); ?>
		<ul class="item-price-wrap d-flex flex-column gap-2 mb-3" role="list">
			<?php echo houzez_listing_price_v1(); ?>
		</ul>
		<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
		<?php get_template_part('template-parts/listing/partials/item-btn'); ?>
		<?php if(houzez_option('disable_date', 1) || houzez_option('disable_agent', 1)) { ?>
		<div class="d-flex mt-3">
				<?php get_template_part('template-parts/listing/partials/item-author'); ?>
				<?php get_template_part('template-parts/listing/partials/item-date'); ?>
			</div>
		<?php } ?>
	</div>
</div>