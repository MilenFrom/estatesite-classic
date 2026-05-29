<?php
global $post, $ele_thumbnail_size, $image_size, $hide_button, $hide_author_date; 
// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_list_v2');

$args = array('item_title' => 'v2');
$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;

// If $hide_author_date is true, show the author and date
// If $hide_author_date doesn't exist, use theme options
$show_author_date = isset($hide_author_date) ? $hide_author_date : (houzez_option('disable_date', 1) || houzez_option('disable_agent', 1));
?>
<div class="item-listing-wrap item-wrap-v2 hz-item-gallery-js hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap item-wrap-no-frame">
		<div class="d-flex flex-md-row flex-column align-items-center">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label');?>
				<div class="labels-wrap mb-2 d-block d-md-none" role="group">
					<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
				</div>
				<ul class="item-price-wrap d-flex d-md-none flex-column gap-2" role="list">
					<?php echo houzez_listing_price_v1(); ?>	
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-tools');?>
				<?php get_template_part('template-parts/listing/partials/item-image');?>
			</div>
			<div class="item-body flex-grow-1">
				<div class="labels-wrap mb-2 d-sm-none d-md-block" role="group">
					<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
				</div>
				<?php get_template_part('template-parts/listing/partials/item', 'title', $args);?>
				<ul class="item-price-wrap d-sm-none d-md-flex flex-column gap-2 align-items-end" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-address');?>
				<div class="d-flex align-items-end justify-content-between">
					<?php get_template_part('template-parts/listing/partials/item-features-v2');?>
					<?php if($show_author_date) { ?>
					<div class="d-flex align-items-end justify-content-end d-none d-xl-flex gap-3">
						<?php get_template_part('template-parts/listing/partials/item-author');?>
					</div>	
					<?php } ?>
				</div>
			</div>
			<?php if($show_author_date) { ?>
			<div class="item-footer d-md-none d-flex justify-content-between w-100">
				<?php get_template_part('template-parts/listing/partials/item-author');?>
				<div class="ms-auto">
					<?php get_template_part('template-parts/listing/partials/item-date');?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>