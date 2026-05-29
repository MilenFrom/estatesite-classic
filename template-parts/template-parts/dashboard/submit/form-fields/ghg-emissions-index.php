<?php
$energy_mode = houzez_option('energy_class_mode', 'standard');
if ($energy_mode === 'french_eu') : ?>
<div class="form-group mb-3">
    <label class="form-label" for="ghg_emissions_index">
        <?php echo houzez_option('cl_ghg_emissions_index', 'GHG Emissions Index').houzez_required_field('ghg_emissions_index'); ?>
    </label>
    <input type="text" id="ghg_emissions_index" <?php houzez_required_field_2('ghg_emissions_index'); ?> class="form-control" name="ghg_emissions_index" value="<?php echo sanitize_text_field( houzez_get_field_meta('ghg_emissions_index') ); ?>" placeholder="<?php echo houzez_option('cl_ghg_emissions_index_plac', 'For example: 18 kg CO₂/m².an'); ?>">
</div>
<?php endif; ?>