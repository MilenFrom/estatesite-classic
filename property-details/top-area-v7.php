<?php
global $post,$featured_image_url, $total_images, $property_gallery_popup_type;

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_v7');

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

$builtin_gallery_class = 'houzez-trigger-popup-slider-js';

$layout = houzez_option('property_blocks');
$layout = $layout['enabled'];
?>
<div class="property-top-wrap">
	<div class="property-banner">
		<div class="d-block d-md-none">
			<?php get_template_part('property-details/partials/gallery-v6'); ?>
		</div><!-- visible-on-mobile -->

		<div class="container d-none d-md-block">
			<div class="row">
				<div class="col-md-12">
					<div class="hs-gallery-v5-grid property-banner-grid-wrap">
						
						<?php
						if(!empty($images_ids)) {
							foreach( $images_ids as $image_id ) { 
								
								$image_data = wp_get_attachment_image_src( $image_id, $image_size );

								if(!$image_data) {
					                continue;
					            }
					            $i++;

								$image_url = $image_data[0] ?? '';
								$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
								if (empty($image_alt)) {
								    $image_alt = get_the_title();
								}

								$full_image = wp_get_attachment_image_src( $image_id, 'full' );
								$full_image_url = $full_image[0] ?? '';

								if( $property_gallery_popup_type == 'photoswipe' ) {
									$dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-houzez-fancybox data-fancybox="gallery-v7"';
									$builtin_gallery_class = '';
								} else {
									$dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
								}

								if($i == 1) { ?>
								<div class="hs-gallery-v5-grid-item hs-gallery-v5-grid-item-01">
									<div class="property-banner-item">
										<a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" <?php echo $dataModal; ?> class="img-wrap-1 <?php echo esc_attr($builtin_gallery_class); ?>">
											<img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
										</a>
									</div>
								</div>
								<?php } elseif( $i == 2 || $i == 3  || $i == 4  || $i == 5 ) { ?>
								
								<?php if($i == 2) { ?>
								<div class="hs-gallery-v5-grid-item hs-gallery-v5-grid-item-02">
								<?php } ?>
									<div class="property-banner-item">
										<a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" <?php echo $dataModal; ?> class="<?php echo esc_attr($builtin_gallery_class); ?> img-wrap-<?php echo esc_attr($i); ?>">
											<?php if($total_images > 5 && $i == 5) { ?>
											<div class="img-wrap-3-text"><i class="houzez-icon icon-picture-sun me-1"></i> <?php echo $total_images-5; ?> <?php echo esc_html__('More', 'houzez'); ?></div>
											<?php } ?>

											<img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
										</a>
									</div>
									<?php if( ($i == 5 && $total_images == 5) || ($i == 4 && $total_images == 4) || ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 5 ) { ?>
								</div> <!-- .property-banner-inner-right -->
								<?php } ?>
								<?php } else { ?>
									<div class="gallery-hidden" aria-hidden="true">
										<a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" <?php echo $dataModal; ?> tabindex="-1">
											<img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
										</a>
									</div>
								<?php } 
								$j++;
							}
						}?>
					</div><!-- .hs-gallery-v5-grid -->
				</div><!-- col-md-12 -->

				<?php 
				if( ! array_key_exists( 'overview-v2', $layout ) ) { ?>
				<div class="col-md-12">
					<div class="block-wrap border-0">
						<div class="d-flex property-overview-data" role="list">
							<?php 
							$args = array(
								'overview' => 'v3',
							);
							set_query_var('args', $args);
							get_template_part('property-details/partials/overview-data-v3'); 
							?>
						</div><!-- d-flex -->
					</div><!-- block-wrap -->
				</div><!-- col-md-12 -->
				<?php } ?>
			</div><!-- row -->
		</div><!-- hidden-on-mobile -->
	</div><!-- property-banner -->
</div><!-- property-top-wrap -->

