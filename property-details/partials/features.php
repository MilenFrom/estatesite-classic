<?php
global $property_features, $ele_settings;

$data_column_class = isset($ele_settings['data_columns']) && !empty($ele_settings['data_columns']) ? $ele_settings['data_columns'] : houzez_option('prop_features_cols', 'list-3-cols');

// Set column class based on the number of columns
$column_class = 'col-md-6'; // default
if($data_column_class == 'list-1-cols') {
    $column_class = 'col-md-12';
} elseif($data_column_class == 'list-3-cols') {
    $column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';
}
$all_features  = houzez_build_features_array();
$single_feature =  '';
$output_html = '';
$child_check = '';
$child_check_class = '';
$has_child = false;

if( is_array($all_features) ) {

    foreach( $all_features as $key => $item ) {

        if( count( $item['childs']) > 0 ) {
            $child_check_class = 'ps-4';
            $inner_output =  '<div class="features group_name fw-bold my-4">'.$item['name'].'</div>';
            $inner_output .=  '<ul class="list-unstyled row g-4 '.$child_check_class.'" role="list">';

            $child_check = '';

            if( is_array($item['childs']) ) {
                $i = 0;
                foreach($item['childs'] as $key_ch => $child) {

                    $child_term_id = $item['child_ids'][$i];
                    $temp   = houzez_feature_output( $child, $child_term_id, $property_features, $column_class );
                    $inner_output .= $temp;
                    $child_check  .= $temp;

                    $i++;
                }
            }
            $inner_output .= '</ul>';

            if( $child_check != '' ) {
                $has_child = true;
                $output_html .= $inner_output;
            }

        } else {
            $single_feature  .= houzez_feature_output( $item['name'], $item['term_id'], $property_features, $column_class );
        }

    }

}
if( $single_feature !='' ) {
    if( $has_child ) {
        $output_html .= '<div class="features group_name fw-bold my-4">'.esc_html__('Other Features','houzez').'</div>';
    }
    $output_html .= '<ul class="list-unstyled row g-4 '.$child_check_class.'" role="list">'.$single_feature .'</ul>';
}

echo $output_html;
