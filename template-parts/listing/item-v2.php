<?php
global $post, $ele_thumbnail_size, $image_size, $hide_button, $hide_author_date; 

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_grid_v2');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;

// If $hide_author_date is true, show the author and date
// If $hide_author_date doesn't exist, use theme options
$show_author_date = isset($hide_author_date) ? $hide_author_date : (houzez_option('disable_date', 1) || houzez_option('disable_agent', 1));
?>
<div class="item-listing-wrap item-wrap-v2 hz-item-gallery-js hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap item-wrap-no-frame h-100">
		<div class="d-flex flex-column align-items-center h-100">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
				<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
				<?php get_template_part('template-parts/listing/partials/item-price');?>
				<?php get_template_part('template-parts/listing/partials/item-tools'); ?>
				<?php get_template_part('template-parts/listing/partials/item-image'); ?>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body w-100 flex-grow-1">
				<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				<?php get_template_part('template-parts/listing/partials/item-address'); ?>
				<?php 
				if( houzez_option('des_item_v2', 0) ) {
					get_template_part('template-parts/listing/partials/item-description'); 
				}
				?>
				<?php get_template_part('template-parts/listing/partials/item-features-v2'); ?>
			</div>
			<?php if($show_author_date) { ?>
			<div class="item-footer d-flex justify-content-between w-100">
				<?php get_template_part('template-parts/listing/partials/item-author'); ?>
				<?php get_template_part('template-parts/listing/partials/item-date'); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>