<?php 
global $post, $ele_thumbnail_size, $image_size; 
// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_list_v1');

$args = array('item_title' => 'v2');
$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap item-wrap-v1 item-wrap-v1-half-map hz-item-gallery-js item-listing-wrap-v1 hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap item-wrap-no-frame">
		<div class="d-flex flex-lg-row flex-column align-items-center">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label');?>
				<div class="labels-wrap mb-2 d-block d-lg-none" role="group">
					<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
				</div>
				<ul class="item-price-wrap d-flex d-lg-none flex-column gap-2" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-tools');?>
				<?php get_template_part('template-parts/listing/partials/item-image');?>
			</div>
			<div class="item-body flex-grow-1">
				<div class="labels-wrap mb-2 d-md-none d-lg-block" role="group">
					<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
				</div>
				<?php get_template_part('template-parts/listing/partials/item', 'title', $args);?>
				<ul class="item-price-wrap d-none d-lg-flex flex-column gap-2 align-items-end" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-address');?>
				<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
                
				<?php if(houzez_option('disable_date', 1) || houzez_option('disable_agent', 1)) { ?>
				<div class="d-flex align-items-center justify-content-start d-none d-xl-flex gap-3">
					<?php get_template_part('template-parts/listing/partials/item-author');?>
					<?php get_template_part('template-parts/listing/partials/item-date');?>
				</div>
				<?php } ?>
			</div>
			<?php if(houzez_option('disable_date', 1) || houzez_option('disable_agent', 1)) { ?>
			<div class="item-footer d-lg-none d-flex justify-content-between w-100">
				<?php get_template_part('template-parts/listing/partials/item-author');?>
				<div class="ms-auto">
					<?php get_template_part('template-parts/listing/partials/item-date');?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div> 