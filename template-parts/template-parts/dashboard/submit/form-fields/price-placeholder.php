<div class="form-group mb-3">
	<label class="form-label" for="property_price_placeholder">
		<?php echo houzez_option('cl_price_placeholder', 'Price Placeholder'); ?>	
	</label>

	<input class="form-control" name="property_price_placeholder" id="property_price_placeholder" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_price_placeholder');
    }
    ?>" placeholder="<?php echo houzez_option('cl_price_placeholder_plac', 'Price on Request'); ?>" type="text">

</div>