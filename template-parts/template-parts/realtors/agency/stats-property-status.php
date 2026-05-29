<?php
global $properties_ids;

$stats = houzez_get_realtor_tax_stats('property_status', 'fave_property_agency', $properties_ids);

$taxonomies = $stats['taxonomies'];
$tax_chart_data = $stats['tax_chart_data'];
$taxs_list_data = $stats['taxs_list_data'];
$total_count = $stats['total_count'];
$total_top_count = $stats['total_top_count'];
$other_percent = $stats['other_percent'];
$others = $stats['others'];

$token = uniqid();

if( !empty($taxonomies) ) { ?>
<div class="agent-profile-chart-wrap">
	<h2 class="mb-3"><?php echo houzez_option('agency_lb_property_status', wp_kses(__( '<span>Property</span> Status', 'houzez' ), houzez_allowed_html() )); ?></h2>
	
	<div class="d-flex align-items-center gap-3">
		<div class="agent-profile-chart">
			<canvas class="houzez-realtor-stats-js" data-token="<?php echo esc_attr( $token )?>" id="stats-property-<?php echo esc_attr($token); ?>" data-chart="<?php echo json_encode($tax_chart_data); ?>" width="100" height="100"></canvas>
		</div><!-- agent-profile-chart -->
		<div class="agent-profile-data mt-2">
			<ul class="list-unstyled m-0 p-0">
				<?php
				$j = $k = 0;
				if(!empty($taxs_list_data) && !empty($total_count)) {
					foreach ($taxs_list_data as $taxnonomy) { $j++;

						if($j <= $total_top_count) {

							$percent = round($tax_chart_data[$k]);
							if(!empty($percent)) {
							echo '<li class="stats-data-'.$j.'">
									<i class="houzez-icon icon-sign-badge-circle me-1"></i> <strong>'.esc_attr($percent).'%</strong> <span>'.esc_attr($taxnonomy).'</span>
								</li>';
							}
						}
						$k++;
					}

					if(!empty($others)) {
						$num = '4';
						if($j <= 2) {
							$num = '3';
						}
						echo '<li class="stats-data-'.$num.'">
								<i class="houzez-icon icon-sign-badge-circle me-1"></i> <strong>'.round($other_percent).'%</strong> <span>'.esc_html__('Other', 'houzez').'</span>
							</li>';
					}
					
				}
				?>
			</ul>
		</div><!-- agent-profile-data -->
	</div><!-- d-flex -->
</div><!-- agent-profile-chart-wrap -->
<?php } ?>