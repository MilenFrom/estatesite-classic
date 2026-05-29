<?php
$energy_mode = houzez_option('energy_class_mode', 'standard');
if ($energy_mode === 'french_eu') : ?>
<div class="form-group mb-3">
    <label class="form-label" for="ghg_emissions_class">
        <?php echo houzez_option('cl_ghg_emissions', 'GHG Emissions Class').houzez_required_field('ghg_emissions_class'); ?>
    </label>

    <select name="ghg_emissions_class" id="ghg_emissions_class" <?php houzez_required_field_2('ghg_emissions_class'); ?> class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('cl_ghg_emissions_plac', 'Select'); ?>" data-live-search="false" data-selected-text-format="count" data-actions-box="true">
    <option value=""><?php echo houzez_option('cl_ghg_emissions_plac', 'Select GHG Emissions Class'); ?></option>

    <?php
    $ghg_emissions_array = houzez_option('ghg_emissions_class_data', 'A, B, C, D, E, F, G'); 
    $ghg_emissions_array = explode(',', $ghg_emissions_array);

    if( ! empty( $ghg_emissions_array ) ) {

         foreach ($ghg_emissions_array as $ghg_class) { 
            $ghg_class = trim($ghg_class);
            ?>

            <option <?php selected(houzez_get_field_meta('ghg_emissions_class'), esc_attr($ghg_class)); ?> value="<?php echo esc_attr($ghg_class);?>"><?php echo esc_attr($ghg_class);?></option>

         <?php
         }

    }

    ?>

	</select><!-- selectpicker -->
</div>
<?php endif; ?>