<?php
$prop_bed  = houzez_get_listing_data('property_bedrooms');
$prop_bed_label = ($prop_bed > 1 ) ? houzez_option('glc_beds', 'Beds') : houzez_option('glc_bed', 'Bed');

$output = '';
if( $prop_bed != '' ) { 
	$output .= '<li class="h-beds d-flex align-items-center me-1">';

		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_bed').' me-2"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('bed');

			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="18" height="18" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-hotel-double-bed-1 me-2"></i>';
		}
		
		$output .= '<span class="item-amenities-text">'.esc_attr($prop_bed_label).':</span> <span class="hz-figure">'.esc_attr($prop_bed).'</span>';
	$output .= '</li>';
}
echo $output;