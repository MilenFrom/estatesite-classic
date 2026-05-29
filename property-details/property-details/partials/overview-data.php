<?php
$overview_data_composer = houzez_option('overview_data_composer');
$overview_data = $overview_data_composer['enabled'];

// Get the overview columns option directly
$overview_cols = houzez_option('overview_cols', 6);

// Set the default overview version
$overview_version = 'v1';

// Define field mapping for quicker lookup
$field_mapping = houzez_overview_composer_fields();

$actual_items = 0;
$html_output = '';

if ($overview_data) {
    unset($overview_data['placebo']);
    
    foreach ($overview_data as $key => $value) {
       
        $has_data = false;
        
        if (array_key_exists($key, $field_mapping)) {
            // Check if we have field mapping for this key'
            if (isset($field_mapping[$key])) {
                $field_info = $field_mapping[$key];
                
                if ($field_info['type'] === 'meta') {
                    $field_value = houzez_get_listing_data($field_info['field']);
                    if ($field_info['check'] === 'not_empty') {
                        $has_data = !empty($field_value);
                    } else {
                        $has_data = ($field_value !== "");
                    }
                } elseif ($field_info['type'] === 'taxonomy') {
                    $has_data = !empty(houzez_taxonomy_simple($field_info['field']));
                }
            }
            
            if ($has_data) {
                $actual_items++;
                $overview_args = array('overview' => $overview_version);
                ob_start();
                get_template_part('property-details/partials/overview/'.$key, null, $overview_args);
                $html_output .= ob_get_clean();
            }
        } else {
            // Custom fields check
            $meta_type = false;
            $custom_field_value = get_post_meta(get_the_ID(), 'fave_'.$key, $meta_type);
            
            if (!empty($custom_field_value)) {
                $actual_items++;
                $field_title = houzez_wpml_translate_single_string($value);
                if (is_array($custom_field_value)) {
                    $custom_field_value = houzez_array_to_comma($custom_field_value);
                } else {
                    $custom_field_value = houzez_wpml_translate_single_string($custom_field_value);
                }

                // Use the helper function to generate the HTML
                $html_output .= houzez_get_overview_item($key, $custom_field_value, $field_title, $overview_version);
            }
        }
    }
}

// Determine the actual number of columns to use
$cols_to_use = ($actual_items > 0 && $actual_items < $overview_cols) ? $actual_items : $overview_cols;

// Output the row with the correct number of columns
echo '<div class="row row-cols-sm-2 row-cols-md-' . $cols_to_use . ' g-4" role="list">';
echo $html_output;
echo '</div>';