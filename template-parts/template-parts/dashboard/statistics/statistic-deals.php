<?php
$total_deals = Houzez_Deals::get_total_deals_by_group('all');
$active_deals = Houzez_Deals::get_total_deals_by_group('active');
$won_deals = Houzez_Deals::get_total_deals_by_group('won');
$lost_deals = Houzez_Deals::get_total_deals_by_group('lost');
?>
<div class="property-stats">
	<div class="row">
		<div class="col-md-3 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Total', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($total_deals); ?></h3>
				</div>
			</div>
		</div>	
		<div class="col-md-3 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Active', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($active_deals); ?></h3>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Won', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($won_deals); ?></h3>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-12">
			<div class="stats-box">
				<div class="media">
					<p>
						<strong><?php esc_html_e('Lost', 'houzez'); ?></strong>
					</p>
				</div>
				<div class="d-flex align-items-baseline gap-2">
					<h3><?php echo number_format_i18n($lost_deals); ?></h3>
				</div>
			</div>
		</div>
	</div>
</div>
