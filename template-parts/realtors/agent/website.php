<?php 
$agent_website = get_post_meta( get_the_ID(), 'fave_agent_website', true );

if(is_author()) {
	global $author_website;
	$agent_website = $author_website;
}

if( !empty( $agent_website ) ) { ?>
	<li class="d-flex align-items-center justify-content-between py-2">
		<strong><?php esc_html_e('Website', 'houzez'); ?></strong> 
		<span><a target="_blank" href="<?php echo esc_url($agent_website); ?>"><?php echo esc_attr( $agent_website ); ?></a></span>
	</li>
<?php } ?>