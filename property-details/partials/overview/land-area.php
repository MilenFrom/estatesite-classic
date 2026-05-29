<?php
$propID = get_the_ID();
$land_area = houzez_property_land_area( 'after' );
$land = get_post_meta( $propID, 'fave_property_land', true);
if( $land != "" ) { 
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('land-area', $land_area, houzez_option('spl_land', esc_html__('Land Area', 'houzez')), $version);
}