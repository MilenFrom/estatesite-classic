<?php 
$website = get_post_meta( get_the_ID(), 'fave_agency_web', true );

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty( $website ) ) { 
    ?>
	<li class="d-flex align-items-center justify-content-between py-2">
		<strong><?php echo houzez_option('agency_lb_website', esc_html__('Website', 'houzez')); ?></strong>
		<span>
			<a target="_blank" href="<?php echo esc_url($website); ?>">
				<?php echo esc_attr( $website ); ?>
			</a>
		</span>
	</li>
 <?php } ?>