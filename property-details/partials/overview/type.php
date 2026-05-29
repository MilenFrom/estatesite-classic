<?php
$property_type = houzez_taxonomy_simple('property_type');

if( !empty( $property_type ) ) {
    // Get the version from the parameter or use default
    $version = isset($args['overview']) ? $args['overview'] : '';
    
    // Use the helper function to generate the HTML
    echo houzez_get_overview_item('type', $property_type, houzez_option('spl_prop_type', 'Type'), $version);
}