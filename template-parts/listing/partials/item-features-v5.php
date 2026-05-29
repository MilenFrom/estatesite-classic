<ul class="item-amenities item-amenities-with-icons d-flex flex-wrap align-items-center gap-2 justify-content-center">
	<?php
	$listing_data_composer = houzez_option('listing_data_composer');
	// Ensure 'enabled' key exists in the array and is an array itself
	$data_composer = isset($listing_data_composer['enabled']) && is_array($listing_data_composer['enabled']) ? $listing_data_composer['enabled'] : [];
	
	$breakpoint = 4;
	if(houzez_is_demo()) {
		$breakpoint = 3;
	}
	
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
					$output .= '<li class="h-'.$key.' d-flex align-items-center me-1">';

						if(houzez_option('icons_type') == 'font-awesome') { 
							$output .= '<i class="'.houzez_option('fa_'.$key).' me-2"></i>';

						} elseif (houzez_option('icons_type') == 'custom') {
							$cus_icon = houzez_option($key);
							if(!empty($cus_icon['url'])) {

								$alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
								$output .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($alt).'">';
							}
						}
						
						$value = houzez_wpml_translate_single_string($value);
						$output .= '<span class="item-amenities-text">'.esc_attr($value).': </span> <span>'.esc_attr($custom_field_value).'</span>';

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