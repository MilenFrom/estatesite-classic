<?php 
global $houzez_local;
$agent_office_num = get_post_meta( get_the_ID(), 'fave_agent_office_num', true );
if(is_author()) {
	global $current_author_meta;
	$agent_office_num = isset($current_author_meta['fave_author_phone'][0]) ? $current_author_meta['fave_author_phone'][0] : '';
}
$agent_office_call = str_replace(array('(',')',' ','-'),'', $agent_office_num);

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty($agent_office_num) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo $houzez_local['office_colon']; ?></strong> 
            <span>
                <a href="tel:<?php echo esc_attr($agent_office_call); ?>" class="agent-phone <?php houzez_show_phone(); ?>">
                    <?php echo esc_attr( $agent_office_num ); ?>
                </a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo $houzez_local['office_colon']; ?></strong></dt>
            <dd class="agent-phone <?php houzez_show_phone(); ?> mb-0">
                <a href="tel:<?php echo esc_attr($agent_office_call); ?>">
                    <?php echo esc_attr( $agent_office_num ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>