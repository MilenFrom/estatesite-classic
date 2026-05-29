<?php
$enquiries_count = Houzez_Enquiry::get_inquiries_stats();

$lastday = $enquiries_count['enquiries_count']['lastday'];
$lasttwo = $enquiries_count['enquiries_count']['lasttwo'];
$lasttwo = $lasttwo - $lastday;

$lastweek = $enquiries_count['enquiries_count']['lastweek'];
$last2week = $enquiries_count['enquiries_count']['last2week'];
$last2week = $last2week - $lastweek;

$lastmonth = $enquiries_count['enquiries_count']['lastmonth'];
$last2month = $enquiries_count['enquiries_count']['last2month'];
$last2month = $last2month - $lastweek;

?>
<div class="property-stats">
	<div class="row">
		<div class="col-md-4 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Last 24 Hours', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($lastday); ?></h3>
					<?php houzez_views_percentage($lasttwo, $lastday); ?>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Last 7 Days', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($enquiries_count['enquiries_count']['lastweek']); ?></h3>
					<?php houzez_views_percentage($last2week, $lastweek); ?>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Last 30 Days', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($enquiries_count['enquiries_count']['lastmonth']); ?></h3>
					<?php houzez_views_percentage($last2month, $lastmonth); ?>
				</div>
			</div>
		</div>
	</div>
</div>
