<?php
global $post;
$google_map_address = houzez_get_listing_data('property_map_address');
$google_map_address_url = "http://maps.google.com/?q=".$google_map_address;
?>
<div class="property-address-wrap property-section-wrap" id="property-address-wrap" role="region">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('sps_address', 'Address'); ?></h2>

			<?php if( !empty($google_map_address) ) { ?>
			<a class="btn btn-primary btn-sm hz-btn-map" href="<?php echo esc_url($google_map_address_url); ?>" target="_blank"><i class="houzez-icon icon-maps me-1" aria-hidden="true"></i> <?php echo houzez_option('spl_ogm', 'Open on Google Maps' ); ?></a>
			<?php } ?>

		</div><!-- block-title-wrap -->
		<div class="block-content-wrap">
			<ul class="row list-lined list-unstyled">
				<?php get_template_part('property-details/partials/address-data'); ?>
			</ul>	
		</div><!-- block-content-wrap -->

		<?php if(houzez_map_in_section() && houzez_get_listing_data('property_map')) {

			$map_data_json = houzez_get_single_listing_map_data_json( get_the_ID() );
			$map_options = houzez_get_map_options();
        	$map_options_json = esc_attr( wp_json_encode( $map_options ) );
		?>
		<div id="houzez-single-listing-map-address" class="block-map-wrap mt-4" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'></div><!-- block-map-wrap -->
		<?php } ?>

	</div><!-- block-wrap -->
</div><!-- property-address-wrap -->