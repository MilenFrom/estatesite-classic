<?php
global $post;
$search_style = houzez_option('halfmap_search_layout', 'v1');
$search_builder = houzez_search_builder();
$layout = $search_builder['enabled'];

if(empty($layout)) {
	$layout = array();
}
unset($layout['placebo']);

if(houzez_is_radius_search() != 1) {
	unset($layout['geolocation']);
}

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

if(houzez_option('price_range_halfmap')) {
	unset($layout['min-price'], $layout['max-price']);
}

if($search_style != 'v3') {
	unset($layout['price']);
}
$advanced_fields = array_slice($layout, houzez_search_builder_first_row());
?>
<section class="advanced-search advanced-search-half-map">
	<div class="container">
		<form id="desktop-search-form" class="houzez-search-form-js houzez-search-filters-js" method="get" autocomplete="off" action="<?php echo esc_url( houzez_get_search_template_link() ); ?>">

		<?php do_action('houzez_search_hidden_fields'); ?>

		<?php
		if(array_key_exists('keyword', $layout)) {?>

			<div class="d-flex pb-2">
				<div class="flex-search flex-grow-1">
					<?php get_template_part('template-parts/search/fields/keyword'); ?>
				</div>
			</div>
		<?php
		unset($layout['keyword']);
		} ?>
		<?php
		if(array_key_exists('geolocation', $layout)) { ?>
			<div class="d-flex">
				<div class="flex-fill">
					<?php get_template_part('template-parts/search/fields/geolocation');?>
				</div>
			</div>
			<?php get_template_part('template-parts/search/fields/distance-range'); ?>
		<?php
		unset($layout['geolocation']);
		}?>

		<div class="row row-cols-2 row-cols-lg-4 g-2">
			<?php
			if ($layout) {
				$i = 0;
				foreach ($layout as $key=>$value) { $i++;
				
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
			if(houzez_option('price_range_halfmap')) { 
				get_template_part('template-parts/search/fields/currency');
			}
			?>
		</div>

		<?php if(houzez_option('price_range_halfmap')) { ?>
		<div class="row my-4">
			<div class="col-12 col-lg-12">
				<?php get_template_part('template-parts/search/fields/price', 'range', array('unique_id' => 'price_range_halfmap')); ?>
			</div>
		</div>
		<?php } ?>

		<div class="half-map-features-list-wrap pb-4">
			<?php 
			if(houzez_option('search_other_features_halfmap')) {
				get_template_part('template-parts/search/other','features', array('is_side_search' => true));
			}
			?>
		</div><!-- half-map-features-list-wrap -->
		
		<div class="d-flex half-map-buttons-wrap">
			<button type="submit" class="btn btn-search half-map-search-js-btn btn-secondary w-100"><?php echo houzez_option('srh_btn_search', 'Search'); ?></button>
			<?php get_template_part('template-parts/search/save-search-btn'); ?>
			<?php get_template_part('template-parts/search/fields/reset-btn'); ?>
		</div>
	</form>
	</div><!-- container -->
</section><!-- advanced-search -->