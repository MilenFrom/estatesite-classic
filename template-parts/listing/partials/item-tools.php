<?php 
global $houzez_local, $post, $image_size; 
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

$class = 'd-flex align-items-center justify-content-center gap-1';

$version = isset($args['tools_version']) ? $args['tools_version'] : '';
if($version == 'v2') {
    $class = 'item-tools-v2';
}


if(houzez_option('disable_favorite', 1) || houzez_option('disable_compare', 1) || houzez_option('disable_preview', 1) ) { ?>
<ul class="item-tools <?php echo $class; ?>">
    <?php if(houzez_option('disable_preview', 1)) { ?>
    <li class="item-tool item-preview">
        <span class="hz-show-lightbox-js item-tool-preview text-center" data-listid="<?php echo esc_attr($post->ID); ?>">
            <span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_preview', 'Preview'); ?>">
                <i class="houzez-icon icon-expand-3"></i>   
            </span>
        </span><!-- item-tool-favorite -->
    </li><!-- item-tool -->
    <?php } ?>
    
    <?php if(houzez_option('disable_favorite', 1)) { ?>
    <li class="item-tool item-favorite">
        <span class="add-favorite-js item-tool-favorite text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_favorite', 'Favourite'); ?>" data-listid="<?php echo esc_attr($post->ID); ?>">
            <i class="houzez-icon icon-love-it <?php echo esc_attr($icon); ?>"></i>
        </span><!-- item-tool-favorite -->
    </li><!-- item-tool -->
    <?php } ?>

    <?php 
    if(houzez_option('disable_compare', 1)) { 
        $property_img_url = get_the_post_thumbnail_url( $post->ID, $image_size );
        if ( empty( $property_img_url ) ) {
            $property_img_url = houzez_get_image_placeholder_url( $image_size );
        }
    ?>
    <li class="item-tool item-compare">
        <span class="houzez_compare compare-<?php echo intval($post->ID); ?> item-tool-compare text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo houzez_option('cl_add_compare', 'Add to Compare'); ?>" data-listing_id="<?php echo esc_attr($post->ID); ?>" data-listing_image="<?php echo esc_attr($property_img_url); ?>">
            <i class="houzez-icon icon-add-circle"></i>
            <!-- <i class="houzez-icon icon-subtract-circle"></i> -->
        </span><!-- item-tool-compare -->
    </li><!-- item-tool -->
    <?php } ?>
</ul><!-- item-tools -->
<?php } ?>