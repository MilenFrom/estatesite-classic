<?php
global $post, $ele_thumbnail_size, $image_size; 

$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="listing-image-wrap">
	<div class="listing-thumb">
		<a <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>" class="listing-featured-thumb hover-effect image-wrap" role="link">
			<?php
			$featured_img_url = get_the_post_thumbnail_url($post->ID, $thumbnail_size);
		    if( $featured_img_url != '' ) {
		        $thumb_id = get_post_thumbnail_id($post->ID);
		        $alt_text = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
		        if (empty($alt_text)) {
		            $alt_text = get_the_title();
		        }
		        echo '<img class="img-fluid" src="'.esc_url($featured_img_url).'" alt="'.esc_attr($alt_text).'">';
		    }else{
		        houzez_image_placeholder( 'large' );
		    }
			?>
		</a><!-- hover-effect -->
	</div>
</div>