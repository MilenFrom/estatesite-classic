<?php
$bedrooms = houzez_get_listing_data('property_bedrooms');

if( $bedrooms != "" ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	$bedrooms_label = ($bedrooms > 1 ) ? houzez_option('spl_bedrooms', 'Bedrooms') : houzez_option('spl_bedroom', 'Bedroom');
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('bed', $bedrooms, $bedrooms_label, $version);
}