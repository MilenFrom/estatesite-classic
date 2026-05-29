<?php
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) { 

    if( function_exists('fts_single_agent_enabled') && fts_single_agent_enabled() ) {
        do_action('houzez_single_agent');

    } else {   

		$agent_detail_layout = houzez_option('agent-detail-layout', 'v1');

		$valid_layouts = array('v1', 'v2');
		if( isset( $_GET['single-agent-layout'] ) && in_array($_GET['single-agent-layout'], $valid_layouts, true) ) {
			$agent_detail_layout = $_GET['single-agent-layout'];
		}
		get_template_part( 'template-parts/realtors/agent/single-agent', $agent_detail_layout );
		
	} // end fts_single_agent_enabled
}

get_footer();
