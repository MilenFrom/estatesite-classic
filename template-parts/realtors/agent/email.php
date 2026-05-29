<?php 
global $post, $houzez_local;
$agent_email = get_post_meta( $post->ID, 'fave_agent_email', true );

if(is_author()) {
	global $author_email;
	$agent_email = $author_email;
}

// Get template part args if available
$template_args = isset($args) ? $args : array();
$version = isset($template_args['version']) ? $template_args['version'] : 'default';

if( !empty( $agent_email ) ) { 
    
    if($version == 'v2') { ?>
        <li class="d-flex align-items-center justify-content-between py-2">
            <strong><?php echo $houzez_local['email']; ?></strong> <span>
                <a href="mailto:<?php echo esc_attr( $agent_email ); ?>"><?php echo esc_attr( $agent_email ); ?></a>
            </span>
        </li>
    <?php 
    } else { ?>
        <div class="d-flex align-items-center justify-content-between py-1 list-lined-item">
            <dt><strong><?php echo $houzez_local['email']; ?></strong></dt>
            <dd class="mb-0"><a href="mailto:<?php echo esc_attr( $agent_email ); ?>"><?php echo esc_attr( $agent_email ); ?></a></dd>
        </div>
    <?php }
} ?>