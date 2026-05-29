<div class="form-group mb-3">
	<label class="form-label" for="property_bathrooms">
		<?php echo houzez_option('cl_bathrooms', 'Bathrooms').houzez_required_field('bathrooms'); ?>
	</label>

	<input class="form-control" id="property_bathrooms" <?php houzez_required_field_2('bathrooms'); ?> name="property_bathrooms" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_bathrooms');
    }
    ?>" placeholder="<?php echo houzez_option('cl_bathrooms_plac', 'Enter number of bathrooms'); ?>" <?php houzez_input_attr_for_bbr(); ?>>

    <?php if( !houzez_is_bedsbaths_range() ) { ?>
	<small class="form-text text-muted"><?php echo houzez_option('cl_only_digits', 'Only digits'); ?></small>
	<?php } ?>
</div>