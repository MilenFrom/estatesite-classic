<?php 
global $houzez_local;
$agent_fax = get_post_meta( get_the_ID(), 'fave_agent_fax', true );

if(is_author()) {
	global $current_author_meta;
	$agent_fax = isset($current_author_meta['fave_author_fax'][0]) ? $current_author_meta['fave_author_fax'][0] : '';
}

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

$agent_fax_call = str_replace(array('(',')',' ','-'),'', $agent_fax);
if( !empty( $agent_fax ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo $houzez_local['fax_colon']; ?></strong> 
            <span>
                <a href="fax:<?php echo esc_attr($agent_fax_call); ?>">
                    <?php echo esc_attr( $agent_fax ); ?>
                </a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo $houzez_local['fax_colon']; ?></strong></dt>
            <dd class="mb-0">
                <a href="fax:<?php echo esc_attr($agent_fax_call); ?>">
                    <?php echo esc_attr( $agent_fax ); ?>
                </a>
            </dd>
        </div>
    <?php }
} ?>