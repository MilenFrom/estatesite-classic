<?php
$propID = get_the_ID();
$area_size = houzez_property_size( 'after' );

if( !empty( $area_size ) ) {
	// Get the version from the parameter or use default
	$version = isset($args['overview']) ? $args['overview'] : '';
	
	// Use the helper function to generate the HTML
	echo houzez_get_overview_item('area-size', $area_size, houzez_option('spl_area_size', esc_html__('Area Size', 'houzez')), $version);
}