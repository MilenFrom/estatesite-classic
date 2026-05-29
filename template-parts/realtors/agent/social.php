<?php
/**
 * Agent/Author Social Media Links
 *
 * @package Houzez
 * @since Houzez 1.0
 */

// Get agent social media data
$social_data = array(
	'facebook'    => array(
		'meta_key'  => 'fave_agent_facebook',
		'author_key' => 'fave_author_facebook',
		'icon'      => 'icon-social-media-facebook',
		'label'     => 'Facebook'
	),
	'twitter'     => array(
		'meta_key'  => 'fave_agent_twitter',
		'author_key' => 'fave_author_twitter',
		'icon'      => 'icon-x-logo-twitter-logo-2',
		'label'     => 'Twitter'
	),
	'linkedin'    => array(
		'meta_key'  => 'fave_agent_linkedin',
		'author_key' => 'fave_author_linkedin',
		'icon'      => 'icon-professional-network-linkedin',
		'label'     => 'LinkedIn'
	),
	'googleplus'  => array(
		'meta_key'  => 'fave_agent_googleplus',
		'author_key' => 'fave_author_googleplus',
		'icon'      => 'icon-social-media-google-plus-1',
		'label'     => 'Google Plus'
	),
	'youtube'     => array(
		'meta_key'  => 'fave_agent_youtube',
		'author_key' => 'fave_author_youtube',
		'icon'      => 'icon-social-video-youtube-clip',
		'label'     => 'YouTube'
	),
	'pinterest'   => array(
		'meta_key'  => 'fave_agent_pinterest',
		'author_key' => 'fave_author_pinterest',
		'icon'      => 'icon-social-pinterest',
		'label'     => 'Pinterest'
	),
	'instagram'   => array(
		'meta_key'  => 'fave_agent_instagram',
		'author_key' => 'fave_author_instagram',
		'icon'      => 'icon-social-instagram',
		'label'     => 'Instagram'
	),
	'vimeo'       => array(
		'meta_key'  => 'fave_agent_vimeo',
		'author_key' => 'fave_author_vimeo',
		'icon'      => 'icon-social-video-vimeo',
		'label'     => 'Vimeo'
	),
	'skype'       => array(
		'meta_key'  => 'fave_agent_skype',
		'author_key' => 'fave_author_skype',
		'icon'      => 'icon-video-meeting-skype',
		'label'     => 'Skype',
		'custom_url' => true
	),
	'tiktok'      => array(
		'meta_key'  => 'fave_agent_tiktok',
		'author_key' => 'fave_agent_tiktok',
		'icon'      => 'icon-tiktok-1-logos-24',
		'label'     => 'TikTok'
	),
	'telegram'    => array(
		'meta_key'  => 'fave_agent_telegram',
		'author_key' => 'fave_author_telegram',
		'icon'      => 'icon-telegram-logos-24',
		'label'     => 'Telegram',
		'custom_url' => true
	),
	'line_id'     => array(
		'meta_key'  => 'fave_agent_line_id',
		'author_key' => 'fave_author_line_id',
		'icon'      => 'icon-lineapp-5',
		'label'     => 'Line App',
		'custom_url' => true
	),
	'zillow'      => array(
		'meta_key'  => 'fave_agent_zillow',
		'author_key' => 'fave_author_zillow',
		'icon'      => 'icon-zillow',
		'label'     => 'Zillow'
	),
	'realtor_com' => array(
		'meta_key'  => 'fave_agent_realtor_com',
		'author_key' => 'fave_author_realtor_com',
		'icon'      => 'icon-realtor-com',
		'label'     => 'Realtor.com'
	),
	'whatsapp'    => array(
		'meta_key'  => 'fave_agent_whatsapp',
		'author_key' => 'fave_author_whatsapp',
		'icon'      => 'icon-messaging-whatsapp',
		'label'     => 'WhatsApp',
		'custom_url' => true,
		'custom_class' => 'agent-whatsapp'
	)
);

// Get agent social media values
$social_values = array();

if (is_author()) {
	global $current_author_meta;
	
	foreach ($social_data as $key => $data) {
		$author_key = $data['author_key'];
		$social_values[$key] = $current_author_meta[$author_key][0] ?? '';
	}
} else {
	foreach ($social_data as $key => $data) {
		$meta_key = $data['meta_key'];
		$social_values[$key] = get_post_meta(get_the_ID(), $meta_key, true);
	}
}

// Format phone numbers for WhatsApp and mobile
$social_values['mobile_call'] = str_replace(array('(',')',' ','-'), '', $social_values['mobile'] ?? '');
$social_values['whatsapp_call'] = str_replace(array('(',')',' ','-'), '', $social_values['whatsapp'] ?? '');

// Display social media links
foreach ($social_data as $key => $data) {
	$value = $social_values[$key];
	
	// Skip if empty
	if (empty($value)) {
		continue;
	}
	
	$url = $value;
	$custom_class = isset($data['custom_class']) ? ' class="' . esc_attr($data['custom_class']) . '"' : '';
	
	// Handle special URL formats
	if (isset($data['custom_url']) && $data['custom_url']) {
		if ($key === 'skype') {
			$url = "skype:" . esc_attr($value) . "?chat";
		} elseif ($key === 'telegram') {
			$url = houzezStandardizeTelegramURL($value);
		} elseif ($key === 'line_id') {
			$url = "https://line.me/ti/p/~" . esc_attr($value);
		} elseif ($key === 'whatsapp') {
			$url = "https://wa.me/" . esc_attr($social_values['whatsapp_call']);
		}
	}
	
	// Output the social media link
	?>
	<span<?php echo $custom_class; ?>>
		<a class="btn-<?php echo esc_attr($key); ?>" target="_blank" href="<?php echo $key === 'skype' ? $url : esc_url($url); ?>" aria-label="<?php echo esc_attr($data['label']); ?>">
			<i class="houzez-icon <?php echo esc_attr($data['icon']); ?> me-2"></i>
		</a>
	</span>
	<?php
}
?>