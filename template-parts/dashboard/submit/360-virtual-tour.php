<?php global $is_multi_steps; ?>
<div id="virtual-tour" class="<?php echo esc_attr($is_multi_steps);?>">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('cls_virtual_tour', '360° Virtual Tour'); ?></h2>
		</div>
		<div class="block-content-wrap">
			<?php get_template_part('template-parts/dashboard/submit/form-fields/virtual-tour'); ?>
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- #virtual-tour -->

