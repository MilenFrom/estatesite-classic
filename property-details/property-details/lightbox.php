<?php
global $post;
$size = 'full';
$properties_images = array();
$image_ids = get_post_meta($post->ID, 'fave_property_images', false);

$featured_image_id = get_post_thumbnail_id($post->ID);
$exclude_featured = houzez_option('detail_exclude_featured_img', 0);

if (!empty($featured_image_id)) {
    if ($exclude_featured == 1) {
        $image_ids = array_diff($image_ids, [$featured_image_id]);
        $image_ids = array_values($image_ids);
    } else {
        $image_ids = array_diff($image_ids, [$featured_image_id]);
        array_unshift($image_ids, $featured_image_id);
    }
}

if (!empty($image_ids)) {
    foreach ($image_ids as $image_id) {
		
        $image_url = wp_get_attachment_image_url($image_id, $size);
        $image_meta = wp_get_attachment_metadata($image_id);
        
        // Skip this iteration if image_data is false
        if(!$image_meta) {
            continue;
        }

        $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        if (empty($alt)) {
            $alt = get_the_title();
        }
        $title = get_the_title($image_id);
        $caption = wp_get_attachment_caption($image_id);
        $properties_images[$image_id] = array(
            'full_url' => $image_url,
            'alt' => $alt,
            'title' => $title,
            'caption' => $caption,
            'width' => isset($image_meta['width']) ? $image_meta['width'] : '',
            'height' => isset($image_meta['height']) ? $image_meta['height'] : ''
        );
    }
}
$userID      =   get_current_user_id();
$fav_option = get_user_meta( $userID, 'houzez_favorites', true );
$lightbox_logo = houzez_option( 'lightbox_logo', false, 'url' );
$lightbox_agent_cotnact = houzez_option('agent_form_gallery');
$lightbox_caption = houzez_option('lightbox_caption', 0); 

$lightbox_class = "";
if(!$lightbox_agent_cotnact) {
	$lightbox_class = "lightbox-gallery-full-wrap";
}

$icon = $key = '';
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}
if( $key != false || $key != '' ) {
    $icon = 'text-danger';
}
?>
<div class="property-lightbox">
	<div class="modal fade" id="property-lightbox" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="d-flex align-items-center justify-content-between w-100">
						<?php if( !empty( $lightbox_logo ) ) { ?>
						<div class="lightbox-logo">
							<img class="img-fluid" src="<?php echo esc_url( $lightbox_logo ); ?>" alt="<?php the_title(); ?>">
						</div><!-- lightbox-logo -->
						<?php } ?>
						<div class="lightbox-tools">
							<ul class="list-inline">
								<?php if( houzez_option('agent_form_gallery') ){ ?>
								<li class="list-inline-item btn-email d-md-none">
									<a href="#"><i class="houzez-icon icon-envelope"></i></a>
								</li>
								<?php } ?>
							</ul>
						</div><!-- lightbox-tools -->
					</div><!-- d-flex -->
					<button type="button" class="btn-close btn-close-white mx-2" data-bs-dismiss="modal"></button>
				</div><!-- modal-header -->

				<div class="modal-body clearfix">
					<div class="lightbox-gallery-wrap <?php echo esc_attr($lightbox_class); ?>">
						
						<?php 
						if($lightbox_agent_cotnact) { ?>
						<a class="btn-expand">
							<i class="houzez-icon icon-expand-3"></i>
						</a>
						<?php } ?>
						
						<?php if( !empty($properties_images) && count($properties_images)) { ?>
						<div class="lightbox-gallery" role="region">
						    <div id="lightbox-slider-js" class="lightbox-slider" role="list">
						        
						        <?php
						        foreach( $properties_images as $prop_image_id => $prop_image_meta ) {
						       		$output = '';
						            $output .= '<div role="listitem">';
								        $output .= '<img class="img-fluid" src="'.esc_url( $prop_image_meta['full_url'] ).'" alt="'.esc_attr($prop_image_meta['alt']).'" title="'.esc_attr($prop_image_meta['title']).'">';

								        if( !empty($prop_image_meta['caption']) && $lightbox_caption != 0 ) {
									        $output .= '<span class="hz-image-caption">'.esc_attr($prop_image_meta['caption']).'</span>';
									    }

								    $output .= '</div>';

								    echo $output;
						        }
						        ?>
						        
						    </div>
						</div><!-- lightbox-gallery -->
						<?php } else { 
			                $featured_image_url = houzez_get_image_url('full');
			                echo '<div class="lightbox-gallery" role="region">
			                    <div id="lightbox-slider-js" class="lightbox-slider" role="list">
			                        <div role="listitem">
			                            <img class="img-fluid" src="'.esc_url($featured_image_url[0]).'" alt="'.esc_attr(get_the_title()).'">
			                        </div>
			                    </div>
			                </div>';
			            } ?>

					</div><!-- lightbox-gallery-wrap -->

					<?php 
					if($lightbox_agent_cotnact) { ?>
					<div class="lightbox-form-wrap">
						<?php get_template_part('property-details/agent-form'); ?>
					</div><!-- lightbox-form-wrap -->
					<?php } ?>
				</div><!-- modal-body -->
			</div><!-- modal-content -->
		</div><!-- modal-dialog -->
	</div><!-- modal -->
</div><!-- property-lightbox -->

