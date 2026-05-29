<?php global $is_multi_steps; ?>
<div id="features" class="<?php echo esc_attr($is_multi_steps);?>">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('cls_features', 'Features'); ?></h2>
		</div>
		<div class="block-content-wrap">
			<div class="row g-3">
				<?php get_template_part('template-parts/dashboard/submit/form-fields/features'); ?>
			</div><!-- row -->			
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- #features -->
