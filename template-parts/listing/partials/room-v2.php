<?php
$prop_room  = houzez_get_listing_data('property_rooms');
$prop_room_label = ($prop_room > 1 ) ? houzez_option('glc_rooms', 'Rooms') : houzez_option('glc_room', 'Room');

$output = '';
if( $prop_room != '' ) { 
	$output .= '<li class="h-rooms pe-2" role="listitem">';
		$output .= '<span class="d-flex align-items-center gap-2">'.esc_attr($prop_room).' ';
		
		if(houzez_option('icons_type') == 'font-awesome') {
			$output .= '<i class="'.houzez_option('fa_room').'"></i>';

		} elseif (houzez_option('icons_type') == 'custom') {
			$cus_icon = houzez_option('room');
			if(!empty($cus_icon['url'])) {

				$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
				$output .= '<img class="img-fluid me-1" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($alt).'">';
			}
		} else {
			$output .= '<i class="houzez-icon icon-architecture-door"></i>';
		}

		$output .= '</span>';
		$output .= ' '.$prop_room_label;
	$output .= '</li>';
}
echo $output;