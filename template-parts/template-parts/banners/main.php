<?php
global $post, $banner_type;
$post_id = isset($post->ID) ? $post->ID : '';
if(empty($post_id) && !houzez_is_tax()) {
    return;
}

if(is_page_template(array('template/template-search.php')) && houzez_option('search_result_page') == 'half_map') {
    return;
}

if(houzez_is_taxonomy_map()) {
    $banner_type = 'property_map';
} else {
    $banner_type = get_post_meta( $post_id, 'fave_header_type', true);
}

if( !empty( $banner_type ) && $banner_type != 'none' ) {

    do_action('houzez_before_banner');

    if( $banner_type == 'property_slider' ) {
        get_template_part( 'template-parts/banners/property', 'slider' );

    } elseif( $banner_type == 'rev_slider' ) {
        get_template_part( 'template-parts/banners/revolution', 'slider' );

    } elseif( $banner_type == 'property_map' ) {
        get_template_part( 'template-parts/banners/map' );

    } elseif( $banner_type == 'static_image' ) {
        get_template_part( 'template-parts/banners/parallax' );

    } elseif( $banner_type == 'video' ) {
        get_template_part( 'template-parts/banners/video' );
    }

    do_action('houzez_after_banner');
}
?>