<?php
global $insights_stats, $houzez_local;

$count_label = $houzez_local['views_label'];
$chart_data = array();
$other_data = array();
$devices = $insights_stats['others']['devices'];

$total_devices = count($devices);

$j = 0;
foreach ($devices as $b) {
	$j++;

	if( $total_devices > 4 ) {
		if( $j <= 3 ) {
			$chart_data[] = $b['count'];
		} else {
			$other_data[] = $b['count'];
		}
	} else {

		$chart_data[] = $b['count'];
	}
	
	
}

$total_other_data = array_sum($other_data);

$num_other_records = count($other_data);
if($num_other_records > 0) {

	$chart_data[] = $total_other_data;
}
?>
<div class="block-wrap">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title"><?php esc_html_e('Devices', 'houzez'); ?></h5>
        </div>
        <?php if( !empty($devices)) { ?>
        <div class="d-flex justify-content-start align-items-center gap-4">
          <div class="d-flex justify-content-center">
            <canvas id="devices-doughnut-chart" data-chart='<?php echo json_encode($chart_data); ?>' style="width: 150px; height: 150px;"></canvas>
          </div>
          <ul class="d-flex flex-column flex-grow-1 list-unstyled list-lined" role="list">
            <?php 
            $i = 0;
            foreach( $devices as $device ) { $i++; 

                if($num_other_records > 0) {
                    if($i == 4) break;
                }

                $device_name = $device['name'];
                $device_count = $device['count'];

                if(empty($device_name)) {
                    $device_name = esc_html__('Unknown', 'houzez');
                }

                if($device_count == 1)
                    $count_label = $houzez_local['view_label'];
            ?>
            <li class="mortgage-calculator-data-<?php echo $i; ?> d-flex align-items-center justify-content-between stats-data-<?php echo $i; ?>" role="listitem">
              <div class="list-lined-item w-100 d-flex justify-content-between py-1">	
                <span>
                  <i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong class="text-capitalize"><?php echo esc_attr($device_name); ?></strong> 
                </span>
                <span><?php echo number_format_i18n($device_count); ?> <small><?php echo $count_label; ?></small></span>
              </div>
            </li>
            <?php } ?>
            
            <?php if(!empty($num_other_records)) { 
                $num = '4';
                if($j <= 2) {
                    $num = '3';
                }
                if($total_other_data == 1)
                    $count_label = $houzez_local['view_label'];
            ?>
            <li class="mortgage-calculator-data-<?php echo $num; ?> d-flex align-items-center justify-content-between stats-data-<?php echo $num; ?>" role="listitem">
              <div class="list-lined-item w-100 d-flex justify-content-between py-1">
                <span>
                  <i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong class="text-capitalize"><?php echo esc_html__('Other', 'houzez'); ?></strong> 
                </span>
                <span><?php echo number_format_i18n($total_other_data); ?> <small><?php echo $count_label; ?></small></span>
              </div>
            </li>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>
      </div>
    </div>
