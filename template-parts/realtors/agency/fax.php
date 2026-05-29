<?php 
global $houzez_local;
$agency_fax = get_post_meta( get_the_ID(), 'fave_agency_fax', true );
$agency_fax_call = str_replace(array('(',')',' ','-'),'', $agency_fax);

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty( $agency_fax ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo houzez_option('agency_lb_fax', esc_html__( 'Fax', 'houzez' )); ?>:</strong>
            <span>
                <a href="fax:<?php echo esc_attr($agency_fax_call); ?>">
                    <?php echo esc_attr( $agency_fax ); ?>
                </a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo houzez_option('agency_lb_fax', esc_html__( 'Fax', 'houzez' )); ?></strong></dt>
            <dd class="mb-0">
                <a href="fax:<?php echo esc_attr($agency_fax_call); ?>">
                    <?php echo esc_attr( $agency_fax ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>