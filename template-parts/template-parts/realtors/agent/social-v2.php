<?php
// Check if any social links exist
$has_social_links = false;

// Define social fields to check
$social_fields = array(
	'facebook' => array('meta' => 'fave_agent_facebook', 'author' => 'fave_author_facebook'),
	'twitter' => array('meta' => 'fave_agent_twitter', 'author' => 'fave_author_twitter'),
	'linkedin' => array('meta' => 'fave_agent_linkedin', 'author' => 'fave_author_linkedin'),
	'googleplus' => array('meta' => 'fave_agent_googleplus', 'author' => 'fave_author_googleplus'),
	'youtube' => array('meta' => 'fave_agent_youtube', 'author' => 'fave_author_youtube'),
	'pinterest' => array('meta' => 'fave_agent_pinterest', 'author' => 'fave_author_pinterest'),
	'instagram' => array('meta' => 'fave_agent_instagram', 'author' => 'fave_author_instagram'),
	'vimeo' => array('meta' => 'fave_agent_vimeo', 'author' => 'fave_author_vimeo'),
	'skype' => array('meta' => 'fave_agent_skype', 'author' => 'fave_author_skype'),
	'tiktok' => array('meta' => 'fave_agent_tiktok', 'author' => 'fave_agent_tiktok'),
	'telegram' => array('meta' => 'fave_agent_telegram', 'author' => 'fave_author_telegram'),
	'line_id' => array('meta' => 'fave_agent_line_id', 'author' => 'fave_author_line_id'),
	'zillow' => array('meta' => 'fave_agent_zillow', 'author' => 'fave_author_zillow'),
	'realtor_com' => array('meta' => 'fave_agent_realtor_com', 'author' => 'fave_author_realtor_com'),
	'whatsapp' => array('meta' => 'fave_agent_whatsapp', 'author' => 'fave_author_whatsapp')
);

// Check if this is an author page or agent post
if (is_author()) {
	global $current_author_meta;
	
	foreach ($social_fields as $field) {
		$author_key = $field['author'];
		if (!empty($current_author_meta[$author_key][0])) {
			$has_social_links = true;
			break;
		}
	}
} else {
	foreach ($social_fields as $field) {
		$meta_key = $field['meta'];
		if (!empty(get_post_meta(get_the_ID(), $meta_key, true))) {
			$has_social_links = true;
			break;
		}
	}
}

// Only display if social links exist
if ($has_social_links) : ?>
	<p><?php printf( esc_html__( 'Find %s on', 'houzez' ) , get_the_title() ); ?>:</p>

	<div class="agent-social-media d-flex gap-2 flex-wrap">
		<?php get_template_part('template-parts/realtors/agent/social'); ?>
	</div>
<?php endif; ?>