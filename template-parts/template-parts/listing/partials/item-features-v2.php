<?php
// $args parameter to determine if this is a half-map view
$args = isset($args) ? $args : '';
$is_half_map = (isset($args['is_half_map']) && $args['is_half_map']) ? true : false;
?>
<ul class="item-amenities <?php echo houzez_v2_meta_type(); ?> d-flex flex-wrap align-items-center gap-2" role="list">
	<?php
	$listing_data_composer = houzez_option('listing_data_composer');
	// Ensure 'enabled' key exists in the array and is an array itself
	$data_composer = isset($listing_data_composer['enabled']) && is_array($listing_data_composer['enabled']) ? $listing_data_composer['enabled'] : [];
	
	$breakpoint = 4;
	if(houzez_is_demo()) {
		$breakpoint = 3;
	}

	$property_type = houzez_taxonomy_simple('property_type');

	$output = '';
	if(!empty($property_type)) {
		$output .= '<li class="h-type d-flex w-100 mb-2" role="listitem">';
			$output .= '<span>'.esc_attr($property_type).'</span>';
		$output .= '</li>';
	}

	if(houzez_option('disable_type', 1)) { 
		echo $output;
	}

	$i = 0;
	if ($data_composer) {
		unset($data_composer['placebo']);
		foreach ($data_composer as $key=>$value) { $i ++;
			if(in_array($key, houzez_listing_composer_fields())) {

				get_template_part('template-parts/listing/partials/'.$key.'-v2');

			} else {
				$custom_field_value = houzez_get_listing_data($key);
				$output = '';
				if( $custom_field_value != '' ) { 
					$output .= '<li class="h-'.$key.' pe-2" role="listitem">';
						$output .= '<span class="d-flex align-items-center gap-2">'.esc_attr($custom_field_value).' ';
						
						if(houzez_option('icons_type') == 'font-awesome') {
							$output .= ' <i class="'.houzez_option('fa_'.$key).' pe-2" aria-hidden="true"></i>';

						} elseif (houzez_option('icons_type') == 'custom') {
							$cus_icon = houzez_option($key);
							if(!empty($cus_icon['url'])) {

								$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
								$output .= '<img class="img-fluid me-1" src="'.esc_url($cus_icon['url']).'" width="20" height="20" alt="'.esc_attr($alt).'">';
							}
						}

						$value = houzez_wpml_translate_single_string($value);
						$output .= '</span>';
						$output .= esc_attr($value);
					$output .= '</li>';
				}
				echo $output;
			}
		if($i == $breakpoint)
			break;
		}
	}
	?>
</ul>