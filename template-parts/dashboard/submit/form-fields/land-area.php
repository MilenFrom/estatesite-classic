<div class="form-group mb-3">
	<label class="form-label" for="property_land">
		<?php echo houzez_option('cl_land_size', 'Land Area').houzez_required_field( 'land_area' ); ?>
	</label>

	<input class="form-control" id="property_land" <?php houzez_required_field_2('land_area'); ?> name="property_land" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_land');
    }
    ?>" placeholder="<?php echo houzez_option('cl_land_size_plac', 'Enter property land area size'); ?>" type="text">
	<small class="form-text text-muted"><?php echo houzez_option('cl_only_digits', 'Only digits'); ?></small>
</div>