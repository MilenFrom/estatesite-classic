<?php 
$rooms = isset($_GET['rooms']) ? $_GET['rooms'] : '';
$rooms_count = 0;
if( !empty($rooms) ) {
	$rooms_count = $rooms;
}
?>
<div class="btn-group rooms-field-wrap" role="group">
	<button type="button" class="btn btn-light-grey-outlined" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" aria-controls="rooms-dropdown">
		<i class="houzez-icon icon-hotel-double-bed-1 me-1" aria-hidden="true"></i> <?php echo houzez_option('srh_rooms', 'Rooms'); ?>
	</button>
	<div id="beds-dropdown" class="dropdown-menu dropdown-menu-small dropdown-menu-right advanced-search-dropdown clearfix" role="menu">

		<div class="d-flex align-items-center justify-content-between size-calculator mb-3" role="group">
			<div class="d-flex align-items-center">
				<span class="quantity-calculator rooms_count"><?php echo esc_attr($rooms_count); ?></span>
				<span class="calculator-label" aria-hidden="true"><?php echo houzez_option('srh_rooms', 'Rooms'); ?></span>
			</div>
			<div class="d-flex align-items-center gap-2">
				<button class="btn p-0 btn-primary-outlined btn_rooms_plus" type="button">
					<i class="houzez-icon icon-add"></i>
				</button>
				<button class="btn p-0 btn-primary-outlined btn_rooms_minus" type="button">
					<i class="houzez-icon icon-subtract"></i>
				</button>
			</div>
			<input type="hidden" name="rooms" class="rooms <?php houzez_ajax_search(); ?>" value="<?php echo esc_attr($rooms); ?>">
		</div>
		
		<div class="d-flex gap-2 mt-2 justify-content-start">
			<button class="btn btn-apply btn-primary" type="button"><?php echo houzez_option('srh_apply', 'Apply'); ?></button>
			<button class="btn btn-clear clear-rooms btn-primary-outlined" type="button"><?php echo houzez_option('srh_clear', 'Clear'); ?></button>
		</div>
	</div><!-- advanced-search-dropdown -->
</div><!-- btn-group -->