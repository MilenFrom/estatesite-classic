<?php
global $post,$featured_image_url, $total_images, $property_gallery_popup_type;

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_v6');
$images_ids = get_post_meta($post->ID, 'fave_property_images', false);
$i = 0; $j = 0;
$total_images = count($images_ids);
$property_gallery_popup_type = houzez_get_popup_gallery_type();
$gallery_token = wp_generate_password(5, false, false);


$featured_image_id = get_post_thumbnail_id($post->ID);
$featured_image = wp_get_attachment_image_src( $featured_image_id, $image_size, true );
$featured_image_url = $featured_image[0] ?? '';

$property_gallery_popup_type = houzez_get_popup_gallery_type(); 
if( ! has_post_thumbnail( $post->ID ) || get_the_post_thumbnail($post->ID) == "" ) {
	$featured_image_url = houzez_get_image_placeholder_url($image_size);
}

$builtin_gallery_class = ' houzez-trigger-popup-slider-js';
$dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
$layout = houzez_option('property_blocks');
$layout = $layout['enabled'];
?>
<div class="property-banner" role="region">
	<div class="d-block d-md-none" role="region">
		<?php get_template_part('property-details/partials/gallery-v6'); ?>
	</div><!-- visible-on-mobile -->

	<div class="container d-none d-md-block" role="region">
		<div class="hs-gallery-v4-grid pb-4">
			<?php
			if(!empty($images_ids)) {
				foreach( $images_ids as $image ) { 
					
					$image_id = $image;
					$image_data = wp_get_attachment_image_src( $image_id, $image_size );
					
					if(!$image_data) {
		                continue;
		            }
		            $i++;

					$image_data_url = $image_data[0] ?? '';

					if( $property_gallery_popup_type == 'photoswipe' ) {
						$full_image = wp_get_attachment_image_src( $image_id, 'full' );
						$full_image_url = $full_image[0] ?? '';
						$dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-houzez-fancybox data-fancybox="gallery"';
						$builtin_gallery_class = '';
					}
				
					if($i == 1) {
					?>
					<div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-01">
						<a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" class="img-wrap-1<?php echo esc_attr($builtin_gallery_class); ?>" <?php echo $dataModal; ?>>
							<img class="img-fluid" src="<?php echo esc_url($image_data_url); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
						</a>
					</div><!-- col-md-8 -->
					<?php } elseif($i == 2 || $i == 3) { ?>

					<?php if($i == 2) { ?>
					<div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-02">
						
						<?php } ?>
							<a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" <?php echo $dataModal; ?> class="<?php echo esc_attr($builtin_gallery_class); ?> img-wrap-<?php echo esc_attr($i); ?>">
								<?php if($total_images > 3 && $i == 3) { ?>
								<div class="img-wrap-3-text"><i class="houzez-icon icon-picture-sun me-1" aria-hidden="true"></i> <?php echo $total_images-3; ?> <?php echo esc_html__('More', 'houzez'); ?></div>
								<?php } ?>

								<img class="img-fluid" src="<?php echo esc_url($image_data_url); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
							</a>
						<?php if( ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 3 ) { ?>
						
					</div><!-- col-md-4 -->
					<?php } ?>
					<?php } else { ?>
						<a class="img-wrap-1 gallery-hidden" <?php echo $dataModal; ?>>
							<img class="img-fluid" src="<?php echo esc_url($image_data_url); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
						</a>
					<?php
					}
					$j++;
				}
			}?>
		</div><!-- hs-gallery-v4-grid -->

		<?php 
		if( ! array_key_exists( 'overview-v2', $layout ) ) { 
			$args = array(
				'overview' => 'v3',
			);	
		?>
		<div class="row pb-4">
			<div class="col-md-12">
				<div class="block-wrap m-0 p-0 border-0">
					<div class="d-flex property-overview-data text-center" role="list">
						<?php 
						set_query_var('args', $args);
						get_template_part('property-details/partials/overview-data-v3'); 
						?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div><!-- hidden-on-mobile -->
</div><!-- property-banner -->

