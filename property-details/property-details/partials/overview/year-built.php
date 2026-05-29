<?php
$year_built = houzez_get_listing_data('property_year');

if( !empty( $year_built ) ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('year-built', $year_built, houzez_option('spl_year_built', 'Year Built'), $version);
}