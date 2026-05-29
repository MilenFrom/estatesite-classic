<?php
global $hide_fields;
$address = houzez_get_listing_data('property_address');
$zipcode = houzez_get_listing_data('property_zip');

$country = houzez_taxonomy_simple('property_country');
$city = houzez_taxonomy_simple('property_city');
$state = houzez_taxonomy_simple('property_state');
$area = houzez_taxonomy_simple('property_area');
$columns = houzez_option('prop_address_cols', '2');

// Set column class based on the number of columns
$column_class = 'col-md-6'; // default
if($columns == 'list-1-cols') {
    $column_class = 'col-md-12';
} elseif($columns == 'list-3-cols') {
    $column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';
}

if( !empty($address) && $hide_fields['address'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="address-label">'.houzez_option('spl_address', 'Address').':</strong> <span aria-labelledby="address-label">'.esc_attr( $address ).'</span>
            </div>
          </li>';
}
if( !empty( $city ) && $hide_fields['city'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="city-label">'.houzez_option( 'spl_city', 'City' ).':</strong> <span aria-labelledby="city-label">'.esc_attr( $city ).'</span>
            </div>
          </li>';
}
if( !empty( $state ) && $hide_fields['state'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="state-label">'.houzez_option('spl_state', 'County/State').':</strong> <span aria-labelledby="state-label">'.esc_attr( $state ).'</span>
            </div>
          </li>';
}
if( !empty($zipcode) && $hide_fields['zip'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="zip-label">'.houzez_option('spl_zip', 'Zip/Postal Code').':</strong> <span aria-labelledby="zip-label">'.esc_attr( $zipcode ).'</span>
            </div>
          </li>';
}
if( !empty( $area ) && $hide_fields['area'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="area-label">'.houzez_option( 'spl_area', 'Area' ).':</strong> <span aria-labelledby="area-label">'.esc_attr( $area ).'</span>
            </div>
          </li>';
}
if( !empty($country) && $hide_fields['country'] != 1 ) {
    echo '<li class="'.$column_class.' d-flex justify-content-between">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong id="country-label">'.houzez_option('spl_country', 'Country').':</strong> <span aria-labelledby="country-label">'.esc_attr($country).'</span>
            </div>
          </li>';
}