<?php
global $post, $top_area, $map_street_view, $featured_image_url, $gallery_token, $total_images, $property_gallery_popup_type;

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('property_detail_v2');
$featured_image = houzez_get_image_url($image_size);
$featured_image_url = $featured_image[0] ?? '';
$property_gallery_popup_type = houzez_get_popup_gallery_type(); 
if( ! has_post_thumbnail( $post->ID ) || get_the_post_thumbnail($post->ID) == "" ) {
	$featured_image_url = houzez_get_image_placeholder_url('full');
}
$gallery_token = wp_generate_password(5, false, false);

$fave_property_images = get_post_meta(get_the_ID(), 'fave_property_images', false);
$total_images = count($fave_property_images);

$gallery_active = $map_active = $street_active = $virtual_active = $video_active = "";
$active_tab = houzez_option('prop_default_active_tab', 'image_gallery');
$media_tabs = houzez_get_media_tabs();

// Get available tabs
$available_tabs = array();
foreach ($media_tabs as $key => $value) {
    if ($key == 'video') {
        $prop_video_url = houzez_get_listing_data('video_url');
        if (!empty($prop_video_url)) {
            $available_tabs[] = 'video';
        }
    } elseif ($key == '360_virtual_tour') {
        $virtual_tour = houzez_get_listing_data('virtual_tour');
        if (!empty($virtual_tour)) {
            $available_tabs[] = '360_virtual_tour';
        }
    } elseif ($key == 'gallery') {
        if (has_post_thumbnail($post->ID) || houzez_get_listing_data('gallery')) {
            $available_tabs[] = 'gallery';
        }
    } elseif ($key == 'map') {
        $available_tabs[] = 'map_view';
    } elseif ($key == 'street_view') {
        $available_tabs[] = 'street_view';
    }
}

// If current active tab is not available, select the first available tab
if (!in_array($active_tab, $available_tabs) && !empty($available_tabs)) {
    $active_tab = $available_tabs[0];
}

if ($active_tab == 'map_view') {
    $map_active = 'show active';
} elseif ($active_tab == 'street_view') {
    $street_active = 'show active';
} elseif ($active_tab == '360_virtual_tour') {
    $virtual_active = 'show active';
} elseif ($active_tab == 'video') {
    $video_active = 'show active';
} else {
    $gallery_active = 'show active';
}

if ($media_tabs): foreach ($media_tabs as $key=>$value) {
    switch($key) {

    case 'gallery': 
        if(in_array('gallery', $available_tabs)) { ?>
            <div class="tab-pane <?php echo esc_attr($gallery_active); ?>" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab" aria-hidden="false"<?php echo ($top_area == 'v2') ? ' style="background-image: url(\''.esc_url($featured_image_url).'\');"' : ''; ?>>
                <?php
                if( $top_area == 'v1' ) {
                    get_template_part('property-details/partials/gallery-v1');
                } elseif( $top_area == 'v2' ) {
                    get_template_part('property-details/partials/gallery-v2');
                } elseif( $top_area == 'v3' || $top_area == 'v4' ) {
                    get_template_part('property-details/partials/gallery-v3-4');
                } elseif( $top_area == 'v5' ) {
                    get_template_part('property-details/partials/gallery-v5');
                }
                ?>
            </div>
        <?php }
        break;

    case 'map':
        if(in_array('map_view', $available_tabs)) { ?>
            <div class="tab-pane <?php echo esc_attr($map_active); ?>" id="pills-map" role="tabpanel" aria-labelledby="pills-map-tab" aria-hidden="<?php echo ($map_active == 'show active') ? 'false' : 'true'; ?>">
                <?php get_template_part('property-details/partials/map'); ?>
            </div>
        <?php }
        break;

    case 'street_view':
        if(in_array('street_view', $available_tabs)) { ?>
            <div class="tab-pane <?php echo esc_attr($street_active); ?>" id="pills-street-view" role="tabpanel" aria-labelledby="pills-street-view-tab" aria-hidden="<?php echo ($street_active == 'show active') ? 'false' : 'true'; ?>">
                <?php 
                if(isset($map_street_view) && $map_street_view != '') {
                    echo $map_street_view;
                } 
                ?>
            </div>
        <?php }
        break;

    case '360_virtual_tour': 
        if(in_array('360_virtual_tour', $available_tabs)) { ?>
            <div class="tab-pane houzez-360-virtual-tour <?php echo esc_attr($virtual_active); ?>" id="pills-360tour" role="tabpanel" aria-labelledby="pills-360tour-tab" aria-hidden="<?php echo ($virtual_active == 'show active') ? 'false' : 'true'; ?>">
                <div class="loader-360" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1;">
                    <div class="loading-overlay" style="text-align: center; background: rgba(255,255,255,0.8); padding: 20px; border-radius: 5px;">
                        <div class="loader-ripple">
                            <div></div>
                            <div></div>
                        </div>
                        <p style="margin-top: 10px;"><?php esc_html_e('Loading Virtual Tour...', 'houzez'); ?></p>
                    </div>
                </div>
                <div id="virtual-tour-iframe-container" style="height: 100%; width: 100%;">
                <?php 
                $virtual_tour = houzez_get_listing_data('virtual_tour');
                if (!empty($virtual_tour)) {
                    if (strpos($virtual_tour, '<iframe') !== false || strpos($virtual_tour, '<embed') !== false) {
                        $virtual_tour = str_replace('<iframe', '<iframe onload="jQuery(\'.loader-360\').hide();"', $virtual_tour);
                        echo $virtual_tour;
                    } else { 
                        echo '<iframe onload="jQuery(\'.loader-360\').hide();" src="'.$virtual_tour.'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
                    }
                }
                ?>
                </div>
            </div>
        <?php }
        break;

    case 'video':
        if(in_array('video', $available_tabs)) { ?>
            <div class="tab-pane <?php echo esc_attr($video_active); ?>" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab" aria-hidden="<?php echo ($video_active == 'show active') ? 'false' : 'true'; ?>">
                <?php 
                $prop_video_url = houzez_get_listing_data('video_url');
                if (!empty($prop_video_url)) {
                    $embed_code = wp_oembed_get($prop_video_url); 
                    echo $embed_code; 
                }
                ?>
            </div>
        <?php }
        break;
    
} // end switch

} endif; ?>






