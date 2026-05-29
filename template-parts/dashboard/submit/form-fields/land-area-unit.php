<?php global $area_prefix_default, $area_prefix_changeable; ?>
<div class="form-group mb-3">
	<label class="form-label" for="property_land_postfix"><?php echo houzez_option('cl_land_size_postfix', 'Land Area Size Postfix'); ?></label>

	<input class="form-control" id="property_land_postfix" name="property_land_postfix" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_land_postfix');
    } else { echo esc_html($area_prefix_default); }
    ?>" placeholder="<?php echo houzez_option('cl_land_size_postfix_plac', 'Enter property land area postfix'); ?>" type="text" <?php if( $area_prefix_changeable != 1 ){ echo 'readonly'; }?>>
	<small class="form-text text-muted"><?php echo houzez_option('cl_land_size_postfix_tooltip', 'For example: Sq Ft'); ?></small>
</div>