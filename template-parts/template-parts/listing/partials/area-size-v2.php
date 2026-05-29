<?php
global $post;
$propID = $post->ID;
$prop_size = houzez_get_listing_data('property_size');
$listing_area_size = houzez_get_listing_area_size( $propID );
$listing_size_unit = houzez_get_listing_size_unit( $propID );

$output = '';
if( !empty( $listing_area_size ) ) {
	$output .= '<li class="h-area pe-2" role="listitem">';
		$output .= '<span class="d-flex align-items-center gap-2">'.$listing_area_size.' ';
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_area-size').'"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('area-size');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-1" src="'.esc_url($cus_icon['url']).'" width="20" height="20" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-ruler-triangle"></i>';
		}
		$output .= '</span>';
		$output .= $listing_size_unit;
	$output .= '</li>';
}
echo $output;