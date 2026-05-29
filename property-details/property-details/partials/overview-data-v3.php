<?php
// Used to top-area-v6 and top-area-v7
$overview_data_composer = houzez_option('overview_data_composer');
$overview_data = $overview_data_composer['enabled'];

$args = array(
    'overview' => 'v3',
);

$i = 0;
if ($overview_data) {
    unset($overview_data['placebo']);
    foreach ($overview_data as $key => $value) {
        $i++;
        
        if(array_key_exists($key, houzez_overview_composer_fields())) {
            get_template_part('property-details/partials/overview/'.$key, null, $args);
        } else {
            $meta_type = false;
            $custom_field_value = get_post_meta(get_the_ID(), 'fave_'.$key, $meta_type);

            if (!empty($custom_field_value)) {
                $field_title = houzez_wpml_translate_single_string($value);
                if (is_array($custom_field_value)) {
                    $custom_field_value = houzez_array_to_comma($custom_field_value);
                } else {
                    $custom_field_value = houzez_wpml_translate_single_string($custom_field_value);
                }

                // Use the helper function to generate the HTML
                echo houzez_get_overview_item($key, $custom_field_value, $field_title, 'v3');
            }
        }
    }
}
