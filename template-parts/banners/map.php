<?php
// Encode the initial data
$map_data = houzez_get_header_map_data();
$map_options = houzez_get_map_options();
$map_options_json = esc_attr( wp_json_encode( $map_options ) );
?>
<section class="top-banner-wrap map-banner <?php houzez_banner_fullscreen(); ?>">
	
	<div class="map-wrap">
		<?php get_template_part('template-parts/map-buttons'); ?>
		
		<div id="houzez-properties-map" data-map='<?php echo $map_data; ?>' data-options='<?php echo $map_options_json; ?>'></div>
	</div>

	<?php
	if(houzez_option('adv_search_which_header_show')['header_map'] != 0) {
		get_template_part('template-parts/search/dock-search-main');
	}
	?>
</section><!-- top-banner-wrap -->