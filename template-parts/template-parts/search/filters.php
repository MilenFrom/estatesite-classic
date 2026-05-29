<?php
$search_style = houzez_option('search_style', 'style_1');
$halfmap_search_style = houzez_option('halfmap_search_layout', 'v1');

if(isset($_GET['search_style'])) {
	$search_style = $_GET['search_style'];
}

$search_builder = houzez_search_builder();
$layout = $search_builder['enabled'];
if( (isset($_GET['search_style']) && $_GET['search_style'] == 'style_3') || ( isset($_GET['halfmap_search']) && $_GET['halfmap_search'] == 'v3') ) {
	$layout = houzez_dummy_search_style_3();
}
unset($layout['placebo']);

$advanced_fields = array_slice($layout, houzez_search_builder_first_row());

if(!taxonomy_exists('property_country')) {
    unset($layout['country']);
}

if(!taxonomy_exists('property_state')) {
    unset($layout['state']);
}

if(!taxonomy_exists('property_city')) {
    unset($layout['city']);
}

if(!taxonomy_exists('property_area')) {
    unset($layout['areas']);
}

$enable_radius_search = houzez_option('enable_radius_search');
if($enable_radius_search != 1) {
	unset($layout['geolocation']);
}

if( houzez_is_price_range_search() && !houzez_search_style() ) {
	unset($layout['min-price'], $layout['max-price']);
}

if( houzez_search_style() && array_key_exists('price', $layout) ) {
	unset($layout['min-price'], $layout['max-price']);
} 
unset($advanced_fields['price']);
?>

<div class="advanced-search-filters search-v1-v2">
	<div class="row row-cols-2 row-cols-lg-5 g-2">
		<?php
		if ($advanced_fields) {
			foreach ($advanced_fields as $key=>$value) {
				if(in_array($key, houzez_search_builtIn_fields())) {
					if($key == 'price' || ($key == 'min-price')) {
						
						get_template_part('template-parts/search/fields/currency');
						
					}
					echo '<div class="col">';
						get_template_part('template-parts/search/fields/'.$key);
					echo '</div>';
					
				} else {

					echo '<div class="col">';
						Houzez_Property_Search::get_custom_search_field($key);
					echo '</div>';
					
				}
			}
		}
		
		if( houzez_is_price_range_search() && !houzez_search_style() ) { 
			get_template_part('template-parts/search/fields/currency');
		}
		?>

	</div>

	<?php if( houzez_is_price_range_search() && !houzez_search_style() ) { ?>
	<div class="row my-4">
		<div class="col-12 col-lg-6 <?php if(houzez_is_half_map()) { echo 'flex-search-full'; } ?>">
			<?php get_template_part('template-parts/search/fields/price', 'range', array('unique_id' => 'price_range_desktop')); ?>
		</div>
	</div>
	<?php } ?>
</div>

<?php 
if(houzez_is_other_featuers_search()) {
	get_template_part('template-parts/search/other-features');
}