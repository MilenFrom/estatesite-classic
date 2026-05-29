<?php
$garage = houzez_get_listing_data('property_garage');

if( $garage != "" ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	$garage_label = ($garage > 1 ) ? houzez_option('spl_garages', 'Garages') : houzez_option('spl_garage', 'Garage');
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('garage', $garage, $garage_label, $version);
}