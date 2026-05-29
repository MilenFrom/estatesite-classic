<?php
$floor_plans = get_post_meta( get_the_ID(), 'floor_plans', true );
global $post, $ele_settings;

$section_title = isset($ele_settings['section_title']) && !empty($ele_settings['section_title']) ? $ele_settings['section_title'] : houzez_option('sps_floor_plans', 'Floor Plans');

$show_header = isset($ele_settings['section_header']) ? $ele_settings['section_header'] : true;

if( !empty( $floor_plans ) ) {
?>
<div class="fw-property-floor-plans-wrap fw-property-section-wrap" id="property-floor-plans-wrap">
	<div class="container">
		<div class="block-wrap">
			<?php if( $show_header ) { ?>
			<div class="block-title-wrap text-center">
				<h2><?php echo $section_title; ?></h2>
			</div><!-- block-title-wrap -->
			<?php } ?>
			<div class="block-content-wrap">

				<div class="floor-plans-tabs">
					<ul class="nav nav-tabs justify-content-center">
						<?php
	                $i = 0;
	                foreach( $floor_plans as $pln ):
	                    $i++;
	                    if( $i == 1 ) {
	                        $active = 'active';
	                    } else {
	                        $active = '';
	                    }

	                    $plan_title = isset($pln['fave_plan_title']) ? esc_attr($pln['fave_plan_title']) : '';
	                    echo '<li class="nav-item"><a class="nav-link '.$active.'" href="#floor-luxury-'.$i.'" data-bs-toggle="tab">'.$plan_title.'</a></li>';
	                endforeach;
	                ?>
					</ul>
				</div>

				<div class="tab-content horizontal-tab-content" id="property-tab-content">
					<?php
	                $j = 0;
	                foreach( $floor_plans as $plan ):
	                    $j++;
	                    if( $j == 1 ) {
	                        $active_tab = 'active show';
	                    } else {
	                        $active_tab = '';
	                    }
	                    $price_postfix = '';

	                    $plan_image = isset($plan['fave_plan_image']) ? $plan['fave_plan_image'] : '';
	                    $plantitle = isset($plan['fave_plan_title']) ? esc_attr($plan['fave_plan_title']) : '';
	                    $fave_plan_price = isset($plan['fave_plan_price']) ? esc_attr($plan['fave_plan_price']) : '';


	                    if( !empty( $plan['fave_plan_price_postfix'] ) ) {
	                        $price_postfix = ' / '.$plan['fave_plan_price_postfix'];
	                    }
	                    $filetype = wp_check_filetype($plan_image);
	                    ?>

					<div class="tab-pane fade <?php echo esc_attr($active_tab); ?>" id="floor-luxury-<?php echo esc_attr($j); ?>" role="tabpanel">
						<div class="floor-plan-wrap py-4">
							<div class="row g-4 align-items-center">
								<div class="col-lg-4">
									<?php if( !empty( $plan_image ) ) { ?>
				                        <?php if($filetype['ext'] != 'pdf' ) {?>
				                        <a href="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" data-lightbox="roadtrip">
				                            <img class="img-fluid" src="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" alt="image">
				                        </a>
				                        <?php } else { 
				                            
				                            $path = $plan_image;
				                            $file = basename($path); 
				                            $file = basename($path, ".pdf");
				                            echo '<a href="'.esc_url( $plan_image ).'" download>';
				                            echo $file;
				                            echo '</a>';
				                        } ?>
				                    <?php } ?>
								</div><!-- floor-plan-left-wrap -->

								<div class="col-lg-8">
									<h3 class="floor-plan-title"><?php echo esc_attr( $plantitle ); ?></h3>
									
									<?php if( !empty( $fave_plan_price ) ) { ?>
									<div>
										<strong><?php esc_html_e( 'Price', 'houzez' ); ?>:</strong> 
				                        <span><?php echo houzez_get_property_price( $fave_plan_price ).$price_postfix; ?></span>
				                     </div>
				                 	<?php } ?>

									<div class="floor-plan-description mt-3 mb-4">
										<p>
											<?php 
											if( isset($plan['fave_plan_description']) && !empty( $plan['fave_plan_description'] ) ) { 
												echo wp_kses_post( $plan['fave_plan_description'] ); 
											} 
											?>
										</p>
									</div><!-- floor-plan-description -->
									
									<div class="d-flex gap-4">
										<?php if( isset($plan['fave_plan_rooms']) && !empty( $plan['fave_plan_rooms'] ) ) { ?>
										<div class="d-flex gap-3 fw-property-floor-data-wrap align-items-center">
											<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-hotel-double-bed-140x40.png" alt="">
											<div class="fw-property-floor-data">
												<span><?php esc_html_e( 'Rooms', 'houzez' ); ?>:</span><br>
												<strong><?php echo esc_attr( $plan['fave_plan_rooms'] ); ?></strong>
											</div><!-- fw-property-floor-data -->
										</div><!-- "d-flex -->
										<?php } ?>

										<?php if( isset($plan['fave_plan_bathrooms']) && !empty( $plan['fave_plan_bathrooms'] ) ) { ?>
										<div class="d-flex gap-3 fw-property-floor-data-wrap align-items-center">
											<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-bathroom-shower-140x40.png" alt="">
											<div class="fw-property-floor-data">
												<span><?php esc_html_e( 'Baths', 'houzez' ); ?>:</span><br>
												<strong><?php echo esc_attr( $plan['fave_plan_bathrooms'] ); ?></strong>
											</div><!-- fw-property-floor-data -->
										</div><!-- "d-flex -->
										<?php } ?>

										<?php if( isset($plan['fave_plan_size']) && !empty( $plan['fave_plan_size'] ) ) { ?>
										<div class="d-flex gap-3 fw-property-floor-data-wrap align-items-center">
											<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-real-estate-dimensions-plan-140x40.png" alt="">
											<div class="fw-property-floor-data">
												<span><?php esc_html_e( 'Size', 'houzez' ); ?>:</span><br>
												<strong><?php echo esc_attr( $plan['fave_plan_size'] ); ?></strong>
											</div><!-- fw-property-floor-data -->
										</div><!-- "d-flex -->
										<?php } ?>
									</div><!-- d-flex -->
								</div><!-- floor-plan-right-wrap -->
							</div><!-- row -->
						</div><!--floor-plan-wrap -->
					</div>

					<?php endforeach; ?>
				</div>
			</div><!-- block-content-wrap -->
		</div><!-- block-wrap -->
	</div><!-- container -->
</div><!-- fw-property-floor-plans-wrap -->
<?php } ?>