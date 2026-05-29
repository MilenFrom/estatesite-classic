<?php
$prop_bath  = houzez_get_listing_data('property_bathrooms');
$prop_bath_label = ($prop_bath > 1 ) ? houzez_option('glc_bathrooms', 'Bathrooms') : houzez_option('glc_bathroom', 'Bathroom');

$output = '';
if( $prop_bath != '' ) {
	$output .= '<li class="h-baths pe-2" role="listitem">';
		$output .= '<span class="d-flex align-items-center gap-2">'.$prop_bath.' ';
		
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_bath').'"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('bath');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-1" src="'.esc_url($cus_icon['url']).'" width="20" height="20" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-bathroom-shower-1"></i>';
		}

		$output .= '</span>';
		$output .= $prop_bath_label;
	$output .= '</li>';
}
echo $output;