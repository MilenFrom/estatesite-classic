<?php
$google_map_address = houzez_get_listing_data('property_map_address');
$google_map_address_url = "http://maps.google.com/?q=".$google_map_address;
?>
<div class="fw-property-address-wrap fw-property-section-wrap" id="property-address-wrap">
	<div class="container">	
		<div class="block-wrap">
			<div class="block-title-wrap d-flex justify-content-center">
				<h2><?php echo houzez_option('sps_address', 'Address'); ?></h2>
			</div><!-- block-title-wrap -->
			<div class="block-content-wrap">
				<ul class="row list-lined list-unstyled" role="list">
					<?php get_template_part('property-details/partials/address-data'); ?>
				</ul>	
			</div><!-- block-content-wrap -->
			<?php 
			if(houzez_map_in_section() && houzez_get_listing_data('property_map')) { 
				$map_data_json = houzez_get_single_listing_map_data_json( get_the_ID() );
				$map_options = houzez_get_map_options();
	        	$map_options_json = esc_attr( wp_json_encode( $map_options ) );
				?>
				<div id="houzez-single-listing-map-address" class="block-map-wrap" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'></div><!-- block-map-wrap -->
			<?php } ?>
		</div><!-- block-wrap -->
	</div><!-- container -->
</div><!-- fw-property-address-wrap -->