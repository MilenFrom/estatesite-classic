<?php
global $post, $property_gallery_popup_type;
$images_ids = get_post_meta($post->ID, 'fave_property_images', false);
$featured_image_id = get_post_thumbnail_id($post->ID);
$exclude_featured = houzez_option('detail_exclude_featured_img', 0);

if (!empty($featured_image_id)) {
    if ($exclude_featured == 1) {
        $images_ids = array_diff($images_ids, [$featured_image_id]);
        $images_ids = array_values($images_ids);
    } else {
        $images_ids = array_diff($images_ids, [$featured_image_id]);
        array_unshift($images_ids, $featured_image_id);
    }
}

$gallery_caption = houzez_option('gallery_caption', 0);
$output = '';

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_v5');

$builtin_gallery_class = ' houzez-trigger-popup-slider-js';
$dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';

if( !empty($images_ids) && count($images_ids)) {
?>
<div class="top-gallery-section top-gallery-variable-width-section" role="region">

	<div class="listing-slider-variable-width">
		<?php
		$j = 0;
		foreach( $images_ids as $image_id ) {
			$image_data = wp_get_attachment_image_src($image_id, $image_size);

			// Skip this iteration if image_data is false
			if(!$image_data) {
				continue;
			}
			$j++;

			$image_url = $image_data[0] ?? '';
			$image_width = $image_data[1] ?? '';
			$image_height = $image_data[2] ?? '';
			$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
			if (empty($image_alt)) {
			    $image_alt = get_the_title();
			}
			$image_title = get_the_title($image_id);
			$image_caption = wp_get_attachment_caption($image_id);

			if( $property_gallery_popup_type == 'photoswipe' ) {
				$full_image= wp_get_attachment_image_src($image_id, 'full');
				$full_image_url = $full_image[0] ?? '';
				$dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-fancybox="gallery-variable-width"';
				$builtin_gallery_class = '';
			}

			// First image gets high priority, others get lazy loading
			$loading_attr = ($j === 1) ? 'fetchpriority="high"' : 'loading="lazy"';
			$width_attr = ($image_width && $image_height) ? 'width="'.esc_attr($image_width).'" height="'.esc_attr($image_height).'"' : '';

			echo '<div>
				<a href="#" data-slider-no="'.esc_attr($j).'" class="'.$builtin_gallery_class.'" '.$dataModal.'>
					<img class="img-responsive img-fluid" src="'.esc_url($image_url).'" '.$width_attr.' '.$loading_attr.' decoding="async" alt="'.esc_attr($image_alt).'" title="'.esc_attr($image_title).'">
				</a>';

				if( !empty($image_caption) && $gallery_caption != 0 ) {
						echo '<span class="hz-image-caption">'.esc_attr($image_caption).'</span>';
					}

				echo '</div>';
		}?>
	</div>

</div><!-- top-gallery-section -->
<?php } ?>