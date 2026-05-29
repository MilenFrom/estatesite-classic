<div class="form-group mb-3">
	<label class="form-label" for="property_price_postfix">
		<?php echo houzez_option('cl_price_postfix', 'After The Price Label').houzez_required_field('price_label'); ?>
	</label>

	<input class="form-control" name="property_price_postfix" <?php houzez_required_field_2('price_label'); ?> id="property_price_postfix" value="<?php
    if (houzez_edit_property()) {
        houzez_field_meta('property_price_postfix');
    }
    ?>" placeholder="<?php echo houzez_option('cl_price_postfix_plac', 'Enter the label after price'); ?>" type="text">

	<small class="form-text text-muted"><?php echo houzez_option('cl_price_postfix_tooltip', 'For example: Monthly'); ?></small>
</div>