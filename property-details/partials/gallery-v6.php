<?php global $post, $top_area, $map_street_view, $featured_image_url, $total_images, $property_gallery_popup_type; ?>

<div class="property-image-count d-block d-md-none" role="status">
    <i class="houzez-icon icon-picture-sun" aria-hidden="true"></i> <span><?php echo esc_attr($total_images); ?></span>
</div>

<?php if( $property_gallery_popup_type == "photoswipe" ) { ?>
    <div>
        <a href="#" class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-src="<?php echo esc_url($featured_image_url); ?>" data-houzez-fancybox data-fancybox="gallery-mobile"></a>
        <?php
        $images_ids = get_post_meta($post->ID, 'fave_property_images', false);
        $featured_image_id = get_post_thumbnail_id($post->ID);
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
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if (empty($image_alt)) {
                    $image_alt = get_the_title();
                }
                ?>
                <div class="gallery-hidden" aria-hidden="true">
                    <a href="#" data-src="<?php echo esc_url($image_url); ?>" data-houzez-fancybox data-fancybox="gallery-mobile" tabindex="-1">
                        <img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
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
<img class="property-featured-image w-100 h-100 left-0 top-0" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" role="img">