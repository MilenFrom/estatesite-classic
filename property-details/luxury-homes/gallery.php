<?php
global $post;

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_block_gallery');

$visible_images = houzez_option('luxury_gallery_visible', 12);
$images_in_row = houzez_option('luxury_gallery_columns', 4);
$images_in_row = intval($images_in_row);

if( empty($visible_images) ) {
    $visible_images = 9;
}

$property_gallery_popup_type = houzez_get_popup_gallery_type();

$builtin_gallery_class = ' houzez-trigger-popup-slider-js';
$dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
$images_ids = get_post_meta($post->ID, 'fave_property_images', false);

// Define column classes based on $images_in_row
$col_class = '';
switch ($images_in_row) {
    case 2:
        $col_class = 'col-md-6 col-sm-6';
        break;
    case 3:
        $col_class = 'col-md-4 col-sm-6';
        break;
    case 4:
        $col_class = 'col-md-3 col-sm-6';
        break;
    case 5:
        $col_class = 'col-md-2-4';
        break;
    case 6:
        $col_class = 'col-md-2 col-sm-4';
        break;
    case 7:
    case 8:
    case 9:
    case 10:
        $col_class = 'col-custom-' . $images_in_row;
        break;
    default:
        $col_class = 'col-md-3 col-sm-6';
}

if( !empty($images_ids) ) {

	$total_images = count($images_ids);
    $remaining_images = $total_images - $visible_images;
	?>

	<div class="fw-property-gallery-wrap fw-property-section-wrap" id="property-gallery-wrap" role="region">
		<div class="row g-0">
			<?php 
			$i = 0;
			foreach( $images_ids as $image_id ) { $i++; 
				$image_data = wp_get_attachment_image_src( $image_id, $image_size );

				// Skip this iteration if image_data is false
				if(!$image_data) {
					continue;
				}

				$image_url = $image_data[0];
				$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
				if (empty($image_alt)) {
				    $image_alt = get_the_title();
				}

				if( $property_gallery_popup_type == 'photoswipe' ) {
					$full_image = wp_get_attachment_image_src( $image_id, 'full' );
					$dataModal = 'href="#" data-src="'.esc_url($full_image[0]).'" data-houzez-fancybox data-fancybox="block-gallery"';
					$builtin_gallery_class = '';
				} ?>

				<div class="<?php echo esc_attr($col_class); ?> <?php if ( $i > $visible_images || ( $i == $visible_images && $remaining_images > 0 ) ) { echo 'position-relative'; } ?>">
					<?php if ( $i == $visible_images && $remaining_images > 0 ) {
						$full_image_src = wp_get_attachment_image_src( $image_id, 'full' );
						$full_url = $full_image_src[0];
					?>
						<div class="img-wrap-3-text"><i class="houzez-icon icon-picture-sun me-1" aria-hidden="true"></i> <?php echo '+ '.esc_html( $remaining_images ); ?></div>
						<a <?php echo $dataModal; ?> data-slider-no="<?php echo esc_attr( $i ); ?>" class="gallery-grid-item<?php echo $builtin_gallery_class; ?><?php if ( $i > $visible_images ) { echo ' gallery-hidden'; } ?>"<?php if ( $i > $visible_images ) { echo ' aria-hidden="true" tabindex="-1"'; } ?>>
							<img class="img-fluid" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
						</a>
					<?php } else { ?>
						<a <?php echo $dataModal; ?> data-slider-no="<?php echo esc_attr( $i ); ?>" class="gallery-grid-item<?php echo $builtin_gallery_class; ?><?php if ( $i > $visible_images ) { echo ' gallery-hidden'; } ?>"<?php if ( $i > $visible_images ) { echo ' aria-hidden="true" tabindex="-1"'; } ?>>
							<img class="img-fluid" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
						</a>
					<?php } ?>
				</div>

			<?php } ?>
		</div><!-- row -->
	</div><!-- fw-property-gallery-wrap -->
<?php } ?>