<?php
global $post;
$map_data_json = houzez_get_single_listing_map_data_json( $post->ID );
$map_options = houzez_get_map_options();
$map_options_json = esc_attr( wp_json_encode( $map_options ) );
?>
<div class="map-wrap">
	<div id="houzez-single-listing-map" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'></div>	
</div>