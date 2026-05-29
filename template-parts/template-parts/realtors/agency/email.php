<?php 
global $houzez_local;
$agency_email = get_post_meta( get_the_ID(), 'fave_agency_email', true );

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty( $agency_email ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo houzez_option('agency_lb_email', esc_html__( 'Email', 'houzez' )); ?>:</strong>
            <span>
                <a href="mailto:<?php echo esc_attr( $agency_email ); ?>">
                    <?php echo esc_attr( $agency_email ); ?>
                </a>
            </span>
        </li>
    <?php
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo houzez_option('agency_lb_email', esc_html__( 'Email', 'houzez' )); ?></strong></dt>
            <dd class="mb-0">
                <a href="mailto:<?php echo esc_attr( $agency_email ); ?>">
                    <?php echo esc_attr( $agency_email ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>