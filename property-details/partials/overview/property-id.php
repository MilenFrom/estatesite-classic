<?php
$property_id = houzez_get_listing_data('property_id');

if( !empty( $property_id ) ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('property-id', $property_id, houzez_option('spl_prop_id', 'Property ID'), $version);
}