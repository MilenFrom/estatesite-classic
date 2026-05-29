<?php
$bathrooms = houzez_get_listing_data('property_bathrooms');

if( $bathrooms != "" ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	$bathrooms_label = ($bathrooms > 1 ) ? houzez_option('spl_bathrooms', 'Bathrooms') : houzez_option('spl_bathroom', 'Bathroom');
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('bath', $bathrooms, $bathrooms_label, $version);
}
?>