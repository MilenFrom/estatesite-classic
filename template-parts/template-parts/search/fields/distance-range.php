<?php
$checked = true;
$radius_unit = houzez_option('radius_unit');
$enable_radius_search = houzez_option('enable_radius_search_halfmap');

$default_radius = isset($_GET['radius']) ? $_GET['radius'] : houzez_option('houzez_default_radius', 30);

?>
<div class="range-wrap d-flex align-items-center gap-4 py-2">
	<div class="range-text">
		<span class="range-title d-flex align-items-center">
			<label class="control control--checkbox pb-0">
				<input name="use_radius" id="use_radius" <?php checked( true, $checked ); ?> type="checkbox"> <?php echo houzez_option('srh_radius', 'Radius'); ?>:
				<span class="control__indicator"></span>
			</label>
			<input class="form_control_container__time__input" type="text" id="radius-range-text" value="0" min="0" max="100"/> <?php echo esc_attr($radius_unit); ?>
		</span> 
	</div>
	<div class="range-wrap flex-fill py-4">
		<div class="sliders_control distance-range-wrap">
			<input id="radius-range-slider" type="range" class="distance-range" step="1" value="<?php echo esc_attr($default_radius); ?>" min="0" max="100"/>
			<input type="hidden" data-default="<?php echo esc_attr($default_radius); ?>" name="radius" id="radius-range-value">
		</div>
	</div>
</div>