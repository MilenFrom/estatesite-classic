<?php
global $settings;

$multi_units  = houzez_get_listing_data('multi_units');
$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_sub_listings', 'Sub Listings');

$section_header = isset($settings['section_header']) ? $settings['section_header'] : true;

if( isset($multi_units[0]['fave_mu_title']) && !empty( $multi_units[0]['fave_mu_title'] ) ) {
?>
<div class="property-sub-listings-table-wrap property-section-wrap" id="property-sub-listings-wrap">
	<div class="block-wrap">
		
		<?php if( $section_header ) { ?>
		<div class="block-title-wrap">
			<h2><?php echo $section_title; ?></h2>
		</div><!-- block-title-wrap -->
		<?php } ?>

		<div class="block-content-wrap">
			<table class="sub-listings-table table-lined responsive-table" role="table">
				<thead role="rowgroup">
					<tr role="row">
						<th role="columnheader" scope="col"><?php esc_html_e('Title', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Type', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Price', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Beds', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Baths', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Size', 'houzez'); ?></th>
                        <th role="columnheader" scope="col"><?php esc_html_e('Availability Date', 'houzez'); ?></th>
					</tr>
				</thead>
				<tbody role="rowgroup">

					<?php 
					$mu_price_postfix = '';
					foreach( $multi_units as $mu ):

						$postfix = '';
						$fave_mu_size = '';
						$fave_mu_availability_date = '';
						$fave_mu_beds = '';
						$fave_mu_baths = '';
                        if( !empty( $mu['fave_mu_price_postfix'] ) ) {
                            $mu_price_postfix = ' / '.$mu['fave_mu_price_postfix'];
                        }

                        if( !empty($mu['fave_mu_size_postfix']) ) {
                        	$postfix = houzez_get_size_unit( $mu['fave_mu_size_postfix'] );
                        }

                        if( !empty($mu['fave_mu_size']) ) {
                        	$fave_mu_size = houzez_get_area_size($mu['fave_mu_size']);
                        }

                        if( !empty($mu['fave_mu_availability_date']) ) {
                        	$fave_mu_availability_date = $mu['fave_mu_availability_date'];
                        }

                        if( $mu['fave_mu_beds'] != "" ) {
                        	$fave_mu_beds = $mu['fave_mu_beds'];
                        }
                        if( $mu['fave_mu_baths'] != "" ) {
                        	$fave_mu_baths = $mu['fave_mu_baths'];
                        }
                        ?>
						<tr role="row">
							<td role="cell" data-label="<?php esc_html_e('Title', 'houzez'); ?>">
								<strong><?php echo esc_attr( $mu['fave_mu_title'] ); ?></strong>
							</td>
							<td role="cell" data-label="<?php esc_html_e('Property Type', 'houzez'); ?>"><?php echo esc_attr( $mu['fave_mu_type'] ); ?></td>
							<td role="cell" data-label="<?php esc_html_e('Price', 'houzez'); ?>">
								<strong><?php echo houzez_get_property_price( $mu['fave_mu_price'] ).$mu_price_postfix; ?></strong>
							</td>
							<td role="cell" data-label="<?php esc_html_e('Beds', 'houzez'); ?>">
								<i class="houzez-icon icon-hotel-double-bed-1 me-1"></i>
								<?php echo esc_attr( $fave_mu_beds ); ?> 
							</td>
							<td role="cell" data-label="<?php esc_html_e('Baths', 'houzez'); ?>">
								<i class="houzez-icon icon-bathroom-shower-1 me-1"></i>
								<?php echo esc_attr( $fave_mu_baths ); ?> 
							</td>
							<td role="cell" data-label="<?php esc_html_e('Property Size', 'houzez'); ?>"><?php echo $fave_mu_size.' '.$postfix; ?></td>
							<td role="cell" data-label="<?php esc_html_e('Availability Date', 'houzez'); ?>"><?php echo esc_attr($fave_mu_availability_date); ?></td>
						</tr>
					<?php endforeach; ?>
					
				</tbody>
			</table>
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- property-address-wrap -->
<?php } ?>