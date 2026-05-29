<div class="form-group mb-3">
	<label class="form-label" for="property_size">
		<?php echo houzez_option('cl_area_size', 'Area Size').houzez_required_field('area_size'); ?>		
	</label>

	<input class="form-control" id="property_size" <?php houzez_required_field_2('area_size'); ?> name="property_size" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_size');
    }
    ?>" placeholder="<?php echo houzez_option('cl_area_size_plac', 'Enter property area size'); ?>" type="text">
	<small class="form-text text-muted"><?php echo houzez_option('cl_only_digits', 'Only digits'); ?></small>
</div>