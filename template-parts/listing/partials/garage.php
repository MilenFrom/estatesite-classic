<?php
$prop_garage = houzez_get_listing_data('property_garage');
$prop_garage_label = ($prop_garage > 1 ) ? houzez_option('glc_garages', 'Garages') : houzez_option('glc_garage', 'Garage');

$output = '';
if( !empty( $prop_garage) ) {
	$output .= '<li class="h-cars d-flex align-items-center me-1">';
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_garage').' me-2"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('garage');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="18" height="18" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-car-1 me-2"></i>';
		}
		$output .= '<span class="item-amenities-text">'.esc_attr($prop_garage_label).':</span> <span class="hz-figure">'.esc_attr($prop_garage).'</span>';
	$output .= '</li>';
}
echo $output;