<?php
global $post, $ele_thumbnail_size, $image_size; 
// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_grid_v3');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap hz-item-gallery-js item-wrap-v3 hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap">
		<div class="item-header">
			<?php get_template_part('template-parts/listing/partials/item-image'); ?>
		</div>
		<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
		<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
		<div class="item-body w-100">
			<div class="d-flex flex-column justify-content-end h-100">
				<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
				<?php get_template_part('template-parts/listing/partials/item-price'); ?>
			</div>
			<?php get_template_part('template-parts/listing/partials/item-tools'); ?>
		</div>
		<div class="preview_loader"></div>
	</div>
	<div class="item-wrap-outside mt-2">
		<?php get_template_part('template-parts/listing/partials/item-title'); ?>
		<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
	</div>
</div>