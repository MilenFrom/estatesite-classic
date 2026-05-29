<?php global $is_multi_steps; ?>
<div id="contact-info" class="<?php echo esc_attr($is_multi_steps);?>">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('cls_contact_info', 'Contact Information'); ?></h2>
		</div>
		<div class="block-content-block">
			<p class="mb-3"><?php echo houzez_option('cl_contact_info_text', 'What information do you want to display in agent data container?'); ?></p>

			<?php get_template_part('template-parts/dashboard/submit/form-fields/contact-info'); ?>

		</div><!-- block-content-block -->
	</div><!-- block-wrap -->
</div><!-- #contact-info -->

