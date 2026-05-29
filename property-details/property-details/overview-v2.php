<?php
$map_data_json = houzez_get_single_listing_map_data_json( get_the_ID() );
$map_options = houzez_get_map_options();
$map_options_json = esc_attr( wp_json_encode( $map_options ) );
?>
<div class="property-overview-wrap property-overview-wrap-v2 property-section-wrap" id="property-overview-wrap-v2" role="region">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('sps_overview', 'Overview'); ?></h2>
		</div><!-- block-title-wrap -->
		<div class="row g-4">
			<div class="col-md-8 col-sm-12">
				<div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 g-4 property-overview-data" role="list">
					<?php get_template_part('property-details/partials/overview-data-v2'); ?>
				</div><!-- property-overview-data -->
			</div><!-- col-md-8 col-sm-12 -->
			<div class="col-md-4 col-sm-12">
				<div id="houzez-overview-listing-map" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'>
				</div><!-- block-map-wrap -->
			</div><!-- col-md-4 col-sm-12 -->
		</div><!-- row -->
	</div><!-- block-wrap -->
</div><!-- property-overview-wrap -->