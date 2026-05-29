<?php 
global $post, $random_token, $ele_thumbnail_size, $image_size, $listing_agent_info, $buttonsComposer; 

$random_token = houzez_random_token();

$defaultButtons = array(
    'enabled' => array(
        'call' => 'Call',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        // Add other buttons as needed
    )
);

$listingButtonsComposer = houzez_option('listing_buttons_composer', $defaultButtons);

// Ensure that 'enabled' index exists
$buttonsComposer = isset($listingButtonsComposer['enabled']) ? $listingButtonsComposer['enabled'] : [];
// Remove the 'placebo' element
unset($buttonsComposer['placebo']);

$listing_agent_info = houzez20_get_property_agent();

$video_url = houzez_get_listing_data('video_url');
$virtual_tour = houzez_get_listing_data('virtual_tour');

$agent_info = $listing_agent_info['agent_info'] ?? '';

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_list_v4');
$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;

$thumb_id = get_post_thumbnail_id($post->ID);

$image_01 = $image_02 = $alt_text_01 = $alt_text_02 = '';
$gallery_ids = get_post_meta($post->ID, 'fave_property_images', false);

// Ensure $gallery_ids is a flat array
$gallery_ids = !empty($gallery_ids) && is_array($gallery_ids) ? array_values($gallery_ids) : [];

// Exclude $thumb_id from $gallery_ids
$gallery_ids = array_diff($gallery_ids, [$thumb_id]);

if (!empty($gallery_ids)) {
    $images_ids = array_slice($gallery_ids, 0, 2);

    if (!empty($images_ids[0])) {
        $image_01 = wp_get_attachment_image_url($images_ids[0], $thumbnail_size);
        $alt_text_01 = get_post_meta($images_ids[0], '_wp_attachment_image_alt', true);
        if (empty($alt_text_01)) {
            $alt_text_01 = get_the_title();
        }
    }

    if (!empty($images_ids[1])) {
        $image_02 = wp_get_attachment_image_url($images_ids[1], $thumbnail_size);
        $alt_text_02 = get_post_meta($images_ids[1], '_wp_attachment_image_alt', true);
        if (empty($alt_text_02)) {
            $alt_text_02 = get_the_title();
        }
    }
}
$args = array('item_title' => 'v2');
?>
<div class="item-listing-wrap item-wrap-v10 hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>">
	<div class="item-wrap item-wrap-v10-half-map item-wrap-no-frame d-flex flex-column flex-lg-row">
		<div class="item-header-wrap d-flex flex-column">
			<div class="item-header-wrap-left">
				<div class="item-header item-header-1">
					<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
					<div class="labels-wrap mb-2 d-block d-lg-none" role="group">
						<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
					</div>
					<?php get_template_part('template-parts/listing/partials/item', 'tools', array('tools_version' => 'v2')); ?>
					<a href="<?php the_permalink(); ?>" class="item-v10-image d-flex justify-content-center align-items-center image-wrap">
						<?php
					    if( has_post_thumbnail( $post->ID ) && get_the_post_thumbnail($post->ID) != '' ) {
					        $thumb_id_main = get_post_thumbnail_id($post->ID);
					        $alt_text_main = get_post_meta($thumb_id_main, '_wp_attachment_image_alt', true);
					        if (empty($alt_text_main)) {
					            $alt_text_main = get_the_title();
					        }
					        the_post_thumbnail( $thumbnail_size, array('class' => 'img-fluid', 'alt' => esc_attr($alt_text_main)) );
					    }else{
					        houzez_image_placeholder( $thumbnail_size );
					    }
					    ?>
					</a>
					<div class="preview_loader"></div>
				</div>
			</div>
			<div class="item-header-wrap-right d-flex flex-row flex-basis-100">
				<div class="item-header-2 item-header-with-button">
					<a <?php houzez_listing_link_target(); ?> href="<?php the_permalink(); ?>" class="item-v10-image d-flex justify-content-center align-items-center image-wrap">
						<?php if( $virtual_tour ) { ?>
						<span class="btn px-2 py-1" href="#"><i class="houzez-icon icon-view me-1"></i> <?php echo esc_html__('360° Tour', 'houzez');?></span>
						<?php } ?>
						<?php
					    if( $image_01 != '' ) {
					        ?>
					        <img class="img-fluid" src="<?php echo $image_01; ?>" alt="<?php echo $alt_text_01; ?>">
					        <?php
					    }else{
					        houzez_image_placeholder( $thumbnail_size );
					    }
					    ?>
					</a>
				</div>
				<div class="item-header-2 item-header-with-button">
					<a <?php houzez_listing_link_target(); ?> href="<?php the_permalink(); ?>" class="item-v10-image d-flex justify-content-center align-items-center image-wrap">
						<?php if( $video_url ) { ?>
						<span class="btn px-2 py-1" href="#"><i class="houzez-icon icon-video-player-movie-1 me-1"></i> <?php echo esc_html__('Video', 'houzez');?></span>
						<?php } ?>
						<?php
					    if( $image_02 != '' ) {
					        ?>
					        <img class="img-fluid" src="<?php echo $image_02; ?>" alt="<?php echo $alt_text_02; ?>">
					        <?php
					    }else{
					        houzez_image_placeholder( $thumbnail_size );
					    }
					    ?>
					</a>
				</div>
			</div>
		</div>
		<div class="item-body-wrap d-flex flex-column justify-content-between">
			<div class="item-body d-flex flex-column justify-content-center h-100">
				<div class="labels-wrap mb-2 d-md-none d-lg-block" role="group">
					<?php get_template_part('template-parts/listing/partials/item-labels-v2');?>
				</div>
				
				<?php get_template_part('template-parts/listing/partials/item', 'title', $args); ?>

				<?php get_template_part('template-parts/listing/partials/item-address'); ?>
				
				<ul class="item-price-wrap d-flex flex-column gap-2 mt-2 mb-4" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
			</div>

			<?php if( !empty( $agent_info[0] ) ) { ?>
			<div class="item-footer-author-tool-wrap d-flex justify-content-between">
				<div class="item-author-wrap d-flex">
					<?php 
					if(houzez_option('disable_agent', 1)) { ?>
					<div class="item-author d-sm-none d-xl-flex gap-2 align-items-center">
						<img class="img-fluid" src="<?php echo $agent_info[0]['picture']; ?>" alt="<?php echo esc_attr($agent_info[0]['agent_name'] ?? ''); ?>">
						<?php echo $agent_info[0]['agent_name']; ?>
					</div>
					<?php } ?>	
				</div>
				<div class="item-buttons-wrap d-flex justify-content-between gap-1">
					<?php 
					$args = array('is_half_map' => true);
					get_template_part('template-parts/listing/partials/item-btns-cew-v2', null, $args); ?>	
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php get_template_part('template-parts/listing/partials/modal-phone-number'); ?>
	<?php get_template_part('template-parts/listing/partials/modal-agent-contact-form'); ?>
</div>