<?php 
$bathrooms = isset($_GET['bathrooms']) ? $_GET['bathrooms'] : '';
$baths_count = 0;
if( !empty($bathrooms) ) {
	$baths_count = $bathrooms;
}
?>
<div class="btn-group bath-field-wrap" role="group">
	<button type="button" class="btn btn-light-grey-outlined" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-controls="baths-dropdown">
		<i class="houzez-icon icon-bathroom-shower-1 me-1" aria-hidden="true"></i> <?php echo houzez_option('srh_baths', 'Baths'); ?>
	</button>
	<div id="baths-dropdown" class="dropdown-menu dropdown-menu-small dropdown-menu-right advanced-search-dropdown clearfix" role="menu">

		<div class="d-flex align-items-center justify-content-between size-calculator mb-3" role="group">
			<div class="d-flex align-items-center">
				<span class="quantity-calculator baths_count"><?php echo esc_attr($baths_count); ?></span>
				<span class="calculator-label" aria-hidden="true"><?php echo houzez_option('srh_bathrooms', 'Bathrooms'); ?></span>
			</div>
			<div class="d-flex align-items-center gap-2">
				<button class="btn p-0 btn-primary-outlined btn_count_plus <?php houzez_ajax_search(); ?>" type="button">
					<i class="houzez-icon icon-add"></i>
				</button>
				<button class="btn p-0 btn-primary-outlined btn_count_minus <?php houzez_ajax_search(); ?>" type="button">
					<i class="houzez-icon icon-subtract"></i>
				</button>
			</div>
			<input type="hidden" name="bathrooms" class="bathrooms" value="<?php echo esc_attr($bathrooms); ?>">
		</div>
		
		<div class="d-flex gap-2 mt-2 justify-content-start">
			<button class="btn btn-apply btn-primary" type="button"><?php echo houzez_option('srh_apply', 'Apply'); ?></button>
			<button class="btn btn-clear clear-baths btn-primary-outlined" type="button"><?php echo houzez_option('srh_clear', 'Clear'); ?></button>
		</div>
	</div><!-- advanced-search-dropdown -->
</div><!-- btn-group -->