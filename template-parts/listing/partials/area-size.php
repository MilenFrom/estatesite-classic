<?php
global $post;
$propID = $post->ID;
$prop_size = houzez_get_listing_data('property_size');
$listing_area_size = houzez_get_listing_area_size( $propID );
$listing_size_unit = houzez_get_listing_size_unit( $propID );

$output = '';
if( !empty( $listing_area_size ) ) {
	$output .= '<li class="h-area d-flex align-items-center me-1">';
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_area-size').' me-2"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('area-size');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="18" height="18" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-ruler-triangle me-2"></i>';
		}
		
		$output .= '<span class="hz-figure me-1">'.esc_attr($listing_area_size).'</span> <span class="hz-figure area_postfix">'.esc_attr($listing_size_unit).'</span>';
	$output .= '</li>';
}
echo $output;