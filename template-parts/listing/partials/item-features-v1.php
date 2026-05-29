<?php
// $args parameter to determine if this is a half-map view
$args = isset($args) ? $args : '';
$is_half_map = (isset($args['is_half_map']) && $args['is_half_map']) ? true : false;
?>
<ul class="item-amenities d-flex flex-wrap align-items-center gap-2 mb-2 <?php echo houzez_v1_4_meta_type(); ?>" role="list">
	<?php
	$listing_data_composer = houzez_option('listing_data_composer');
	// Ensure 'enabled' key exists in the array and is an array itself
	$data_composer = isset($listing_data_composer['enabled']) && is_array($listing_data_composer['enabled']) ? $listing_data_composer['enabled'] : [];

	$i = 0;
	if ($data_composer) {
		unset($data_composer['placebo']);
		foreach ($data_composer as $key=>$value) { $i ++;
			if(in_array($key, houzez_listing_composer_fields())) {

				get_template_part('template-parts/listing/partials/'.$key);

			} else {
				$custom_field_value = houzez_get_listing_data($key);
				$output = '';
				if( $custom_field_value != '' ) { 
					$output .= '<li class="h-'.$key.' d-flex align-items-center me-1" role="listitem">';

						if(houzez_option('icons_type') == 'font-awesome') {
							$output .= '<i class="'.houzez_option('fa_'.$key).' me-2" aria-hidden="true"></i>';

						} elseif (houzez_option('icons_type') == 'custom') {
							$cus_icon = houzez_option($key);
							if(!empty($cus_icon['url'])) {

								$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
								$output .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="18" height="18" alt="'.esc_attr($alt).'">';
							}
						}
						
						$value = houzez_wpml_translate_single_string($value);
						$output .= '<span class="item-amenities-text">'.esc_attr($value).': </span> <span class="hz-figure">'.esc_attr($custom_field_value).'</span>';
						
					$output .= '</li>';
				}
				echo $output;
			}
		if($i == 4)
			break;
		}
	}

	get_template_part('template-parts/listing/partials/type');
	?>
</ul>