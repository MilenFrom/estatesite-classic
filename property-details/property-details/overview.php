<?php
$prop_id = houzez_get_listing_data('property_id');
?>
<div class="property-overview-wrap property-section-wrap" id="property-overview-wrap" role="region">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('sps_overview', 'Overview'); ?></h2>

			<?php if( !empty( $prop_id ) && houzez_option('show_id_head', 1) ) { ?>
			<div><strong><?php echo houzez_option('spl_prop_id', 'Property ID'); ?>:</strong> <?php echo houzez_propperty_id_prefix($prop_id); ?></div>
			<?php } ?>
		</div><!-- block-title-wrap -->
		
		<div class="property-overview-data" role="list">
			<?php get_template_part('property-details/partials/overview-data'); ?>
		</div><!-- property-overview-data -->
	</div><!-- block-wrap -->
</div><!-- property-overview-wrap -->