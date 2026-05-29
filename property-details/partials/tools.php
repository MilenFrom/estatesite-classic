<?php 
global $post, $loggedin_to_view; 
$key = '';
$user_id      =   get_current_user_id();
$fav_option = get_user_meta( $user_id, 'houzez_favorites', true );
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}

$print_class = '';
if( $loggedin_to_view == 1 && !is_user_logged_in() ) {
    $print_class = 'houzez-hidden';
}

$icon = '';
if( $key != false || $key != '' ) {
    $icon = 'text-danger';
}
?>
<ul class="property-item-tools list-unstyled d-flex gap-1 m-0 p-0" role="toolbar">

    <?php if( houzez_option('prop_detail_favorite') != 0 ) { ?>
    <li class="item-tool text-center houzez-favorite">
        <span class="add-favorite-js item-tool-favorite text-center" role="button" tabindex="0" data-listid="<?php echo intval($post->ID)?>">
            <i class="houzez-icon icon-love-it <?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
        </span>
    </li>
    <?php } ?>

    <?php if( houzez_option('prop_detail_share') != 0 ) { ?>
    <li class="item-tool text-center houzez-share">
        <span class="item-tool-share text-center" data-bs-toggle="dropdown" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">
            <i class="houzez-icon icon-share" aria-hidden="true"></i>
        </span>
        <div class="dropdown-menu dropdown-menu-end item-tool-dropdown-menu" role="menu">
            <?php get_template_part('property-details/partials/share'); ?>
        </div>
    </li>
    <?php } ?>

    <?php if( houzez_option('print_property_button') != 0 ) { ?>
    <li class="item-tool text-center d-none d-md-block houzez-print <?php echo esc_attr($print_class); ?>" data-propid="<?php echo intval($post->ID); ?>">
        <span class="item-tool-compare text-center" role="button" tabindex="0">
            <i class="houzez-icon icon-print-text" aria-hidden="true"></i>
        </span>
    </li>
    <?php } ?>
</ul>
