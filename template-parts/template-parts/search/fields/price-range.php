<?php 
$min_price = isset($_GET['min-price']) && $_GET['min-price'] != '' ? $_GET['min-price'] : '';
$max_price = isset($_GET['max-price']) && $_GET['max-price'] != '' ? $_GET['max-price'] : '';

// Generate a unique ID for this price range field
$unique_id = isset($args['unique_id']) ? $args['unique_id'] : 'default';
?>
<div class="range-wrap" data-price-range-id="<?php echo esc_attr($unique_id); ?>">
	<div class="range-text mb-3">
		<span class="range-title"><?php echo houzez_option('srh_price_range', 'Price Range'); ?></span><i class="houzez-icon icon-arrow-right-1"></i>
		<span class="min-price-range"></span>
		<i class="houzez-icon icon-arrow-right-1"></i>
		<span class="max-price-range"></span>
	</div><!-- range-text -->
	<div class="range-wrap">
		<div class="sliders_control">
			<input id="fromSlider_<?php echo esc_attr($unique_id); ?>" type="range" name="min-price" step="50" value="<?php echo esc_attr($min_price); ?>" min="" max="" class="hz-price-range-from"/>
			<input id="toSlider_<?php echo esc_attr($unique_id); ?>" type="range" name="max-price" step="50" value="<?php echo esc_attr($max_price); ?>" min="" max="" class="hz-price-range-to"/>
		</div>
	</div>
</div><!-- range-wrap -->