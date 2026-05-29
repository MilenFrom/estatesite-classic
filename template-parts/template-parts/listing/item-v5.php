<?php
global $post, $ele_thumbnail_size, $image_size;
$property_type = houzez_taxonomy_simple('property_type');
// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_grid_v5');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap hz-item-gallery-js item-wrap-v5 hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap h-100">
		<div class="d-flex flex-column align-items-center h-100">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
				<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
				<?php get_template_part('template-parts/listing/partials/item-tools'); ?>
				<?php get_template_part('template-parts/listing/partials/item-image'); ?>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body w-100 d-flex flex-column flex-fill align-items-center">
				<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				<div class="item-v5-price mb-1">
					<?php echo houzez_listing_price_v5(); ?>
				</div>
				<?php if(!empty($property_type) && houzez_option('disable_type', 1)) { ?>
				<div class="item-v5-type d-flex flex-grow-1 text-center mb-3">
					<?php echo esc_attr($property_type); ?>
				</div>
				<?php } ?>
				<?php get_template_part('template-parts/listing/partials/item-features-v6'); ?>
			</div>
		</div>
	</div>
</div>