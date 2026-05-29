<?php 
global $post, $ele_thumbnail_size, $image_size; 
// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_grid_v6');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap item-wrap-v6 hz-item-gallery-js hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap h-100">
		<div class="d-flex flex-column align-items-center flex-fill h-100">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
				<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
				<div class="listing-image-wrap">
					<div class="listing-thumb">
						<a class="listing-featured-thumb item-v6-image" <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>">
						<?php
						$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;

					    if( has_post_thumbnail( $post->ID ) && get_the_post_thumbnail($post->ID) != '' ) {
					        $thumb_id = get_post_thumbnail_id($post->ID);
					        $alt_text = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
					        if (empty($alt_text)) {
					            $alt_text = get_the_title();
					        }
					        the_post_thumbnail( $thumbnail_size, array('class' => 'img-fluid', 'alt' => esc_attr($alt_text)) );
					    }else{
					        houzez_image_placeholder( $thumbnail_size );
					    }
					    ?>
						</a>
					</div>
				</div>
				<?php get_template_part('template-parts/listing/partials/item-tools'); ?>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body w-100 flex-fill d-flex flex-column justify-content-between">
				<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				<div class="d-flex flex-column amenities-price-wrap gap-3">
					<ul class="item-price-wrap">
						<li class="item-price mb-xl-0"><?php echo houzez_listing_price_v5(); ?></li>
					</ul>
					<?php 
					$half_map_args = array('is_half_map' => true);
					get_template_part('template-parts/listing/partials/item-features', 'v6', $half_map_args); 
					?>
				</div>
			</div>
		</div>
	</div>
</div>