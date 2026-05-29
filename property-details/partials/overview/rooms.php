<?php
$rooms = houzez_get_listing_data('property_rooms');

if( $rooms != "" ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	$rooms_label = ($rooms > 1 ) ? houzez_option('spl_rooms', 'Rooms') : houzez_option('spl_room', 'Room');
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('room', $rooms, $rooms_label, $version);
}