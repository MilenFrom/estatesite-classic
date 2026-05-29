<?php
$attachments = get_post_meta(get_the_ID(), 'fave_attachments', false);
$documents_download = houzez_option('documents_download');

$prop_id = houzez_get_listing_data('property_id');
$prop_price = houzez_get_listing_data('property_price');
$prop_size = houzez_get_listing_data('property_size');
$land_area = houzez_get_listing_data('property_land');
$bedrooms = houzez_get_listing_data('property_bedrooms');
$rooms = houzez_get_listing_data('property_rooms');
$bathrooms = houzez_get_listing_data('property_bathrooms');
$year_built = houzez_get_listing_data('property_year');
$garage = houzez_get_listing_data('property_garage');
$property_status = houzez_taxonomy_simple('property_status');
$property_type = houzez_taxonomy_simple('property_type');
$garage_size = houzez_get_listing_data('property_garage_size');
$additional_features_enable = houzez_get_listing_data('additional_features_enable');
$additional_features = get_post_meta( get_the_ID(), 'additional_features', true);

$icon_prop_id = houzez_option('icon_prop_id', false, 'url' );
$icon_bedrooms = houzez_option('icon_bedrooms', false, 'url' );
$icon_rooms = houzez_option('icon_rooms', false, 'url' );
$icon_bathrooms = houzez_option('icon_bathrooms', false, 'url' );
$icon_prop_size = houzez_option('icon_prop_size', false, 'url' );
$icon_prop_land = houzez_option('icon_prop_land', false, 'url' );
$icon_garage_size = houzez_option('icon_garage_size', false, 'url' );
$icon_garage = houzez_option('icon_garage', false, 'url' );
$icon_year = houzez_option('icon_year', false, 'url' );

$bathrooms_text = ($bathrooms > 1 ) ? houzez_option('spl_bathrooms', 'Bathrooms') : houzez_option('spl_bathroom', 'Bathroom');

$bedrooms_text = ($bedrooms > 1 ) ? houzez_option('spl_bedrooms', 'Bedrooms') : houzez_option('spl_bedroom', 'Bedroom');

$rooms_text = ($rooms > 1 ) ? houzez_option('spl_rooms', 'Rooms') : houzez_option('spl_room', 'Room');

$garage_label = ($garage > 1 ) ? houzez_option('spl_garages', 'Garages') : houzez_option('spl_garage', 'Garage');


$hide_fields = houzez_option('hide_detail_prop_fields');
?>
<div class="fw-property-description-wrap fw-property-section-wrap" id="property-description-wrap">
	<div class="container">
		<div class="block-wrap">
			<div class="block-title-wrap">
				<h2 class="text-center"><?php echo houzez_option('sps_description', 'Description'); ?></h2>	
			</div><!-- block-title-wrap -->
			<div class="block-content-wrap text-center">
				<div class="fw-property-description-content mx-auto">
					<?php 
					// Get the raw post content
					global $post;
					$content = $post->post_content;
					
					// Process content with auto excerpt if enabled
					$processed_content = houzez_auto_excerpt_content($content, 'property');
					
					if( $processed_content['has_more'] ) {
						// Apply content filters to both parts
						$content_before_more = apply_filters( 'the_content', $processed_content['content_before'] );
						$content_after_more = apply_filters( 'the_content', $processed_content['content_after'] );
						
						// Get the read more text from settings or use default
						$more_link_text = houzez_option('read_more_text', __( 'Read More', 'houzez' ));
						$more_link = '<p><a href="#" class="houzez-read-more-link" onclick="this.style.display=\'none\'; this.parentNode.nextElementSibling.style.display=\'block\'; return false;">' . $more_link_text . '</a></p>';
						
						// Output the content with read more functionality
						echo $content_before_more;
						echo $more_link;
						echo '<div class="houzez-more-content" style="display: none;">' . $content_after_more . '</div>';
					} else {
						// No more tag needed, just display the content normally
						echo apply_filters( 'the_content', $processed_content['content'] );
					}
					?>
				</div>
				
				<?php 
				if(!empty($attachments)) { ?>
				<div class="fw-property-documents-wrap mx-auto">
					<div class="block-title-wrap my-4 py-4 d-flex flex-column align-items-center">
						<h3>
							<span class="px-3"><?php echo houzez_option('sps_documents', 'Property Documents'); ?></span>
						</h3>
					</div><!-- block-title-wrap -->
					<div class="property-documents">
						<?php 
						foreach( $attachments as $attachment_id ):
							$attachment_meta = houzez_get_attachment_metadata($attachment_id); 

							if(!empty($attachment_meta )):
							?>
							<div class="property-document-title mb-2">
								<i class="houzez-icon icon-task-list-plain-1 me-1"></i> <?php echo esc_attr( $attachment_meta->post_title ); ?> - 
								<?php if( $documents_download == 1 ) {
								if( is_user_logged_in() ) { ?>
								<a href="<?php echo esc_url( $attachment_meta->guid ); ?>" target="_blank"><?php esc_html_e( 'Download', 'houzez' ); ?></a>
								<?php } else { ?>
									<a href="#" data-toggle="modal" data-target="#login-register-form"><?php esc_html_e( 'Download', 'houzez' ); ?></a>
								<?php } ?>
							<?php } else { ?>
								<a href="<?php echo esc_url( $attachment_meta->guid ); ?>" target="_blank"><?php esc_html_e( 'Download', 'houzez' ); ?></a>
							<?php } ?>
							</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div><!-- property-documents -->
				</div><!-- fw-property-documents-wraps -->
				<?php } ?>

				<div class="fw-property-details-wrap text-start mx-auto">
					<div class="block-title-wrap my-4 py-4 d-flex flex-column align-items-center">
						<h3>
							<span><?php echo houzez_option('sps_details', 'Details'); ?></span>
						</h3>
					</div><!-- block-title-wrap -->
					<div class="d-flex justify-content-lg-center justify-content-start flex-wrap fw-property-amenities-wrap row row-cols-2 row-cols-lg-5 gx-0">
						
						<?php
						if( !empty( $prop_id ) && $hide_fields['prop_id'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_prop_id) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_prop_id); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<?php echo houzez_option('spl_prop_id', 'Property ID'); ?>
									<strong><?php echo houzez_propperty_id_prefix( $prop_id ); ?></strong>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php if( !empty( $bedrooms ) && $hide_fields['bedrooms'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_bedrooms) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_bedrooms); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<strong><?php echo esc_attr( $bedrooms ); ?></strong>
									<?php echo $bedrooms_text; ?>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php if( !empty( $rooms ) && $hide_fields['rooms'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_rooms) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_rooms); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<strong><?php echo esc_attr( $rooms ); ?></strong>
									<?php echo $rooms_text; ?>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php if( !empty( $bathrooms ) && $hide_fields['bathrooms'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_bathrooms) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_bathrooms); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<strong><?php echo esc_attr( $bathrooms ); ?></strong>
									<?php echo $bathrooms_text; ?>
								</div>
							</div>
						</div>
						<?php } ?>
				
						<?php if( !empty( $prop_size ) && $hide_fields['area_size'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_prop_size) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_prop_size); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<?php echo houzez_option('spl_prop_size', 'Property Size'); ?>
									<strong><?php echo houzez_property_size( 'after' ); ?></strong>
								</div>
							</div>
						</div>
						<?php } ?>


						<?php if( !empty( $land_area ) && $hide_fields['land_area'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_prop_land) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_prop_land); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<?php echo houzez_option('spl_land', 'Land Area'); ?>
									<strong><?php echo houzez_property_land_area( 'after' ); ?></strong>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php if( !empty( $garage ) && $hide_fields['garages'] != 1 ) { ?>
					   
						<?php if( !empty( $garage_size ) ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_garage_size) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_garage_size); ?>" alt="" width="50" height="50">
								<?php } ?>

								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<?php echo houzez_option('spl_garage_size', 'Garage Size'); ?>
									<strong><?php echo esc_attr( $garage_size ); ?></strong>
								</div>
							</div>
						</div>
						<?php } ?>

						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_garage) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_garage); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<strong><?php echo esc_attr( $garage ); ?></strong>
									<?php echo $garage_label; ?>
								</div>
							</div>
						</div>
						<?php } ?>
						
						<?php if( !empty( $year_built ) && $hide_fields['year_built'] != 1 ) { ?>
						<div class="fw-property-amenities px-4 mb-4 no-wrap">
							<div class="d-flex flex-column flex-lg-row align-items-center gap-3">
								<?php if( !empty($icon_year) ) { ?>
									<img class="img-fluid" src="<?php echo esc_url($icon_year); ?>" alt="" width="50" height="50">
								<?php } ?>
								<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">
									<?php echo houzez_option('spl_year_built', 'Year Built'); ?>
									<strong><?php echo esc_attr( $year_built ); ?></strong>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php
						//Custom Fields
						if(class_exists('Houzez_Fields_Builder')) {
						$fields_array = Houzez_Fields_Builder::get_form_fields(); 

							if(!empty($fields_array)) {
								foreach ( $fields_array as $value ) {

									$field_type = $value->type;
									$meta_type = true;

									if( $field_type == 'checkbox_list' || $field_type == 'multiselect' ) {
										$meta_type = false;
									}

									$item_out = '';
									$data_value = get_post_meta( get_the_ID(), 'fave_'.$value->field_id, $meta_type );
									$field_title = $value->label;
									
									$field_title = houzez_wpml_translate_single_string($field_title);

									if( $meta_type == true ) {
										$data_value = houzez_wpml_translate_single_string($data_value);
									} else {
										$data_value = houzez_array_to_comma($data_value);
									}

									$field_id = houzez_clean_20($value->field_id);

									if(!empty($data_value) && $hide_fields[$field_id] != 1) {

										$custom_icon = houzez_option('c_'.$field_id, false, 'url' );

										$item_out .= '<div class="fw-property-amenities px-4 mb-4 no-wrap">';
										$item_out .= '<div class="d-flex flex-column flex-lg-row align-items-center gap-3">';

										if( !empty($custom_icon) ) {
											$item_out .= '<img class="img-fluid" src="'.esc_url($custom_icon).'" alt="" width="50" height="50">';
										}

										$item_out .= '<div class="fw-property-amenities-data d-flex flex-column align-items-center align-items-lg-start">';
											$item_out .= esc_attr($field_title);
											$item_out .= '<strong>'.esc_attr( $data_value ).'</strong>';
										$item_out .= '</div>';

										$item_out .= '</div>';
										$item_out .= '</div>';

										echo $item_out;
									}
								}
							}
						}
						?>

					</div><!-- d-flex -->
					
				</div><!-- fw-property-details-wrap -->

				<?php if( $hide_fields['updated_date'] != 1 ) { ?>
				<div class="small-text text-secondary text-center d-block"><i class="houzez-icon icon-calendar-3 me-1"></i> <?php esc_html_e( 'Updated on', 'houzez' ); ?> <?php the_modified_time('F j, Y'); ?> <?php esc_html_e( 'at', 'houzez' ); ?> <?php the_modified_time('g:i a'); ?> </div>	
				<?php } ?>

			</div><!-- fw-property-documents-wrap -->
		</div><!-- block-wrap -->
	</div><!-- container -->
</div><!-- property-description-wrap -->