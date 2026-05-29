<div class="form-group mb-3">
	<label class="form-label" for="property_google_street_view"><?php echo houzez_option('cl_street_view', 'Street View'); ?></label>
	<select name="property_google_street_view" class="selectpicker form-control bs-select-hidden" title="" data-live-search="false">
		<option <?php selected( houzez_get_field_meta('property_map_street_view'), 'hide' ); ?> value="hide"><?php echo houzez_option('cl_hide', 'Hide'); ?></option>
        <option <?php selected( houzez_get_field_meta('property_map_street_view'), 'show' ); ?> value="show"><?php echo houzez_option('cl_show', 'Show'); ?></option>
	</select><!-- selectpicker -->
</div>