<?php 
global $houzez_local, $post, $buttonsComposer, $image_size;
$key = '';
$user_id      =   get_current_user_id();
$fav_option = get_user_meta( $user_id, 'houzez_favorites', true );
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}

$icon = '';
if( $key != false || $key != '' ) {
    $icon = 'text-danger';
}

// Check if is_half_map is passed in args
$is_half_map = false;
if (isset($args) && isset($args['is_half_map'])) {
    $is_half_map = $args['is_half_map'];
}
?>
<div class="item-buttons-wrap d-flex align-items-center gap-1 w-100">
	<div class="item-buttons-left-wrap d-flex justify-content-between gap-1 w-100">
		<?php get_template_part('template-parts/listing/partials/item-btns-cew'); ?>			
	</div><!-- item-buttons-left-wrap -->
	<?php
	if(houzez_option('disable_favorite', 1) || houzez_option('disable_compare', 1) || houzez_option('disable_preview', 1) ) { ?>
	<div class="item-buttons-right-wrap item-buttons-wrap <?php echo $is_half_map ? 'd-none d-lg-flex align-items-center gap-1 w-100' : 'd-none d-md-flex align-items-center gap-1'; ?>">
		<?php if(houzez_option('disable_preview', 1)) { ?>
		<span class="hz-show-lightbox-js btn btn-primary-outlined btn-item px-2 d-flex align-items-center justify-content-center flex-fill" data-listid="<?php echo intval($post->ID)?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_preview', 'Preview'); ?>">
			<i class="houzez-icon icon-expand-3"></i>   
		</span><!-- item-tool-favorite -->
		<?php } ?>

		<?php if(houzez_option('disable_favorite', 1)) { ?>
	    <span class="add-favorite-js btn-primary-outlined btn-item px-2 d-flex align-items-center justify-content-center flex-fill" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_favorite', 'Favourite'); ?>" data-listid="<?php echo intval($post->ID)?>">
	        <i class="houzez-icon icon-love-it <?php echo esc_attr($icon); ?>"></i>
	    </span><!-- item-tool-favorite -->
	    <?php } ?>

	    <?php 
	    if(houzez_option('disable_compare', 1)) { 
	        $property_img_url = get_the_post_thumbnail_url( $post->ID, $image_size );
	        if ( empty( $property_img_url ) ) {
	            $property_img_url = houzez_get_image_placeholder_url( $image_size );
	        }
	    ?>
	    <span class="btn btn-primary-outlined btn-item px-2 d-flex align-items-center justify-content-center flex-fill item-compare houzez_compare compare-<?php echo intval($post->ID); ?> show-compare-panel" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_add_compare', 'Add to Compare'); ?>" data-listing_id="<?php echo intval($post->ID); ?>" data-listing_image="<?php echo esc_attr($property_img_url); ?>">
	        <i class="houzez-icon icon-add-circle"></i>
	    </span><!-- item-tool-compare -->
	    <?php } ?>
    </div><!-- item-buttons-right-wrap -->
	<?php } ?>
</div><!-- item-buttons-wrap --> 

