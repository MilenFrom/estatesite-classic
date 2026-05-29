<div class="agent-contacts-wrap">
	<h3 class="widget-title mb-4"><?php esc_html_e('Contact', 'houzez'); ?></h3>
	<div class="agent-map">
		<?php 
		if( houzez_option('agent_sidebar_map', 1) ) {
			get_template_part('template-parts/realtors/agent/map'); 
		}?>
		<?php get_template_part('template-parts/realtors/agent/address'); ?>
	</div>
	<ul class="list-unstyled d-flex flex-column">
		<?php 
		if( houzez_option('agent_phone', 1) ) {
			get_template_part('template-parts/realtors/agent/office-phone', null, array('version' => 'v2')); 
		} 

		if( houzez_option('agent_mobile', 1) ) {
			get_template_part('template-parts/realtors/agent/mobile', null, array('version' => 'v2')); 
		}

		if( houzez_option('agent_fax', 1) ) {
			get_template_part('template-parts/realtors/agent/fax', null, array('version' => 'v2')); 
		} 

		if( houzez_option('agent_email', 1) ) {
			get_template_part('template-parts/realtors/agent/email', null, array('version' => 'v2')); 
		}

		if( houzez_option('agent_website', 1) ) {
		 	get_template_part('template-parts/realtors/agent/website'); 
		}
		?>
	</ul>

	<?php 
	if( houzez_option('agent_social', 1) ) { 
		get_template_part('template-parts/realtors/agent/social', 'v2'); 
	} ?>
</div><!-- agent-bio-wrap -->