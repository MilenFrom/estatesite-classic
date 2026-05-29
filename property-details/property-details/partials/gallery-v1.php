<?php 
global $post, $top_area, $map_street_view, $gallery_token, $total_images, $property_gallery_popup_type; 

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_v1');
$featured_image = houzez_get_image_url('full');
$featured_image_url = $featured_image[0] ?? '';
?>

<div class="property-image-count" role="status">
    <i class="houzez-icon icon-picture-sun" aria-hidden="true"></i> <span><?php echo esc_attr($total_images); ?></span>
</div>

<?php 
if(houzez_option('agent_form_above_image') ) {
    get_template_part('property-details/agent-form'); 
}?>

<?php
// Get featured image dimensions
$featured_image_id = get_post_thumbnail_id($post->ID);
$featured_image_full = wp_get_attachment_image_src($featured_image_id, 'full');
$featured_width = $featured_image_full[1] ?? '';
$featured_height = $featured_image_full[2] ?? '';
?>
<?php if( $property_gallery_popup_type == "photoswipe" ) { ?>
    <div>
        <a href="#" class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-src="<?php echo esc_url($featured_image_url); ?>" data-houzez-fancybox data-fancybox="gallery-v1"></a>
        <?php
        $images_ids = get_post_meta($post->ID, 'fave_property_images', false);
        if (($key = array_search($featured_image_id, $images_ids)) !== false) {
            unset($images_ids[$key]);
        }
        if(!empty($images_ids)) {
            foreach( $images_ids as $image_id ) {
                $image_data = wp_get_attachment_image_src($image_id, 'full');

                // Skip this iteration if image_data is false
                if(!$image_data) {
                    continue;
                }

                $image_url = $image_data[0] ?? '';
                $image_width = $image_data[1] ?? '';
                $image_height = $image_data[2] ?? '';
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if (empty($image_alt)) {
                    $image_alt = get_the_title();
                }
                ?>
                <div class="gallery-hidden" aria-hidden="true">
                    <a href="#" data-src="<?php echo esc_url($image_url); ?>" data-houzez-fancybox data-fancybox="gallery-v1" tabindex="-1">
                        <img class="img-fluid"
                             src="<?php echo esc_url($image_url); ?>"
                             <?php if($image_width && $image_height): ?>width="<?php echo esc_attr($image_width); ?>" height="<?php echo esc_attr($image_height); ?>"<?php endif; ?>
                             loading="lazy"
                             decoding="async"
                             alt="<?php echo esc_attr($image_alt); ?>">
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </div>
<?php } else { ?>
    <a class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-bs-toggle="modal" data-bs-target="#property-lightbox" href="#" role="button"></a>
<?php } ?>
<img class="property-featured-image w-100 h-100 left-0 top-0"
     src="<?php echo esc_url($featured_image_url); ?>"
     <?php if($featured_width && $featured_height): ?>width="<?php echo esc_attr($featured_width); ?>" height="<?php echo esc_attr($featured_height); ?>"<?php endif; ?>
     fetchpriority="high"
     decoding="async"
     alt="<?php echo esc_attr(get_the_title()); ?>"
     role="img">