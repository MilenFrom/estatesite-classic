<?php global $is_multi_steps; ?>
<div id="energy-class" class="<?php echo esc_attr($is_multi_steps);?>">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('cls_energy_class', 'Energy Class'); ?></h2>
		</div>
		<div class="block-content-wrap">
			<div class="row">	
				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/energy-class'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/energy-global-index'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/energy-renewable-index'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/energy-building'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/epc-current-rating'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/epc-potential-rating'); ?>
				</div>

				<?php 
				// Add GHG fields and diagnostic date for French/EU mode
				$energy_mode = houzez_option('energy_class_mode', 'standard');
				if ($energy_mode === 'french_eu') : ?>
				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/ghg-emissions-class'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/ghg-emissions-index'); ?>
				</div>

				<div class="col-md-6 col-sm-12">
					<?php get_template_part('template-parts/dashboard/submit/form-fields/diagnostic-date'); ?>
				</div>
				<?php endif; ?>
			</div><!-- row -->	
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- #energy-class -->