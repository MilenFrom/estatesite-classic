<?php global $post, $top_area, $map_street_view, $featured_image_url, $gallery_token, $total_images, $property_gallery_popup_type; ?>

<div class="property-image-count" role="status">
    <i class="houzez-icon icon-picture-sun" aria-hidden="true"></i> <span><?php echo esc_html($total_images); ?></span>
</div>
<?php get_template_part('property-details/partials/gallery-variable-width'); ?>