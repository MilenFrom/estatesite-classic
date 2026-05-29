<?php
$prop_garage = houzez_get_listing_data('property_garage');
$prop_garage_label = ($prop_garage > 1 ) ? houzez_option('glc_garages', 'Garages') : houzez_option('glc_garage', 'Garage');

$output = '';
if( !empty( $prop_garage) ) {
	$output .= '<li class="h-cars pe-2" role="listitem">';
		$output .= '<span class="d-flex align-items-center gap-2">'.$prop_garage.' ';
		
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_garage').'"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('garage');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-1" src="'.esc_url($cus_icon['url']).'" width="20" height="20" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-car-1"></i>';
		}

		$output .='</span>';
		$output .= $prop_garage_label;
	$output .= '</li>';
}
echo $output;