<div class="block-wrap mb-4">
  <div class="card-body">
    <div class="text-center mb-4">
      <h2 class="mb-5"><?php esc_html_e('Visits', 'houzez'); ?></h2>
      <div class="d-inline-flex bg-light">
        <div class="nav" role="tablist">
          <button class="btn btn-light btn-sm rounded-0 active" id="tab-24h" data-bs-toggle="tab" data-bs-target="#chart-24h" type="button" role="tab" aria-selected="true">
            <?php esc_html_e('Last 24 Hours', 'houzez'); ?>
          </button>
          <button class="btn btn-light btn-sm rounded-0" id="tab-7days" data-bs-toggle="tab" data-bs-target="#chart-7days" type="button" role="tab" aria-selected="false">
            <?php esc_html_e('Last 7 Days', 'houzez'); ?>
          </button>
          <button class="btn btn-light btn-sm rounded-0" id="tab-30days" data-bs-toggle="tab" data-bs-target="#chart-30days" type="button" role="tab" aria-selected="false">
            <?php esc_html_e('Last 30 Days', 'houzez'); ?>
          </button>
        </div>
      </div>
    </div>
    <div class="tab-content">
		<div class="tab-pane fade show active" id="chart-24h" role="tabpanel" aria-labelledby="tab-24h">
			<?php get_template_part('template-parts/dashboard/insights/24-hours-chart'); ?>
		</div>
		<div class="tab-pane fade" id="chart-7days" role="tabpanel" aria-labelledby="tab-7days">
			<?php get_template_part('template-parts/dashboard/insights/7-days-chart'); ?>
		</div>
		<div class="tab-pane fade" id="chart-30days" role="tabpanel" aria-labelledby="tab-30days">
			<?php get_template_part('template-parts/dashboard/insights/30-days-chart'); ?>
		</div>
	</div><!-- tab-content -->
  </div>
</div>
