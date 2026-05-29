<?php 
global $houzez_local;
$agent_mobile = get_post_meta( get_the_ID(), 'fave_agent_mobile', true );

if(is_author()) {
	global $current_author_meta;
	$agent_mobile = isset($current_author_meta['fave_author_mobile'][0]) ? $current_author_meta['fave_author_mobile'][0] : '';
}

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

$agent_mobile_call = str_replace(array('(',')',' ','-'),'', $agent_mobile);
if( !empty( $agent_mobile ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo $houzez_local['mobile_colon']; ?></strong> 
            <span>
                <a href="tel:<?php echo esc_attr($agent_mobile_call); ?>" class="agent-phone <?php houzez_show_phone(); ?>">
                    <?php echo esc_attr( $agent_mobile ); ?>
                </a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo $houzez_local['mobile_colon']; ?></strong></dt>
            <dd class="agent-phone <?php houzez_show_phone(); ?> mb-0">
                <a href="tel:<?php echo esc_attr($agent_mobile_call); ?>">
                    <?php echo esc_attr( $agent_mobile ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>