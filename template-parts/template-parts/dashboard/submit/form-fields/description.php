<div class="form-group mb-3">
	<label class="form-label" for="property_description"><?php echo houzez_option('cl_content', 'Content'); ?></label>
	<?php
	$editor_id = 'property_description';
	$settings = array(
	    'media_buttons' => false,
	    'textarea_rows' => 10,
	);
	if (houzez_edit_property()) {
	    global $property_data;
	    wp_editor($property_data->post_content, $editor_id, $settings);
	} else {
	    wp_editor('', $editor_id, $settings);
	}
	?>
</div>