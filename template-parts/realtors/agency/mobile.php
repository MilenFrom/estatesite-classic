<?php 
global $houzez_local;
$agency_mobile = get_post_meta( get_the_ID(), 'fave_agency_mobile', true );
$agency_mobile_call = str_replace(array('(',')',' ','-'),'', $agency_mobile);

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty( $agency_mobile ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo houzez_option('agency_lb_mobile', esc_html__( 'Mobile', 'houzez' )); ?>:</strong>
            <span>
                <a href="tel:<?php echo esc_attr($agency_mobile_call); ?>" class="agent-phone <?php houzez_show_phone(); ?>">
                    <?php echo esc_attr( $agency_mobile ); ?>
                </a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo houzez_option('agency_lb_mobile', esc_html__( 'Mobile', 'houzez' )); ?></strong></dt>
            <dd class="agent-phone <?php houzez_show_phone(); ?> mb-0">
                <a href="tel:<?php echo esc_attr($agency_mobile_call); ?>">
                    <?php echo esc_attr( $agency_mobile ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>