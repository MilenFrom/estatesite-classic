<?php
/**
 * Social Icons Template Part
 *
 * Displays social media icons in the header based on theme options
 *
 * @package Houzez
 * @since Houzez 1.0
 */

// Only display social icons if enabled in theme options
if( houzez_option('social-header') != '0' ):

	// Get social media URLs from theme options
	$social_networks = array(
		'facebook'    => array(
			'url'        => houzez_option('hs-facebook'),
			'icon_class' => 'icon-social-media-facebook',
			'platform'   => 'facebook'
		),
		'twitter'     => array(
			'url'        => houzez_option('hs-twitter'),
			'icon_class' => 'icon-x-logo-twitter-logo-2',
			'platform'   => 'twitter'
		),
		'whatsapp'    => array(
			'url'        => houzez_option('hs-whatsapp'),
			'icon_class' => 'icon-messaging-whatsapp',
			'platform'   => 'whatsapp',
			'custom_url' => true
		),
		'tiktok'      => array(
			'url'        => houzez_option('hs-tiktok'),
			'icon_class' => 'icon-tiktok-1-logos-24',
			'platform'   => 'tiktok'
		),
		'telegram'    => array(
			'url'        => houzez_option('hs-telegram'),
			'icon_class' => 'icon-telegram-logos-24',
			'platform'   => 'telegram',
			'custom_url' => true
		),
		'googleplus'  => array(
			'url'        => houzez_option('hs-googleplus'),
			'icon_class' => 'icon-social-media-google-plus-1',
			'platform'   => 'google-plus'
		),
		'linkedin'    => array(
			'url'        => houzez_option('hs-linkedin'),
			'icon_class' => 'icon-professional-network-linkedin',
			'platform'   => 'linkedin'
		),
		'instagram'   => array(
			'url'        => houzez_option('hs-instagram'),
			'icon_class' => 'icon-social-instagram',
			'platform'   => 'instagram'
		),
		'pinterest'   => array(
			'url'        => houzez_option('hs-pinterest'),
			'icon_class' => 'icon-social-pinterest',
			'platform'   => 'pinterest'
		),
		'youtube'     => array(
			'url'        => houzez_option('hs-youtube'),
			'icon_class' => 'icon-social-video-youtube-clip',
			'platform'   => 'youtube'
		),
		'yelp'        => array(
			'url'        => houzez_option('hs-yelp'),
			'icon_class' => 'icon-social-media-yelp',
			'platform'   => 'yelp'
		),
		'behance'     => array(
			'url'        => houzez_option('hs-behance'),
			'icon_class' => 'icon-designer-community-behance',
			'platform'   => 'behance'
		)
	);
	
	// Format WhatsApp number for URL
	if (!empty($social_networks['whatsapp']['url'])) {
		$social_networks['whatsapp']['formatted_url'] = 'https://wa.me/' . 
			str_replace(array('(',')',' ','-'), '', $social_networks['whatsapp']['url']);
	}
	
	// Format Telegram username for URL
	if (!empty($social_networks['telegram']['url'])) {
		$social_networks['telegram']['formatted_url'] = 'https://telegram.me/' . 
			$social_networks['telegram']['url'];
	}
	
?>
<div class="header-social-icons" role="navigation">
	<ul class="list-inline" role="list">
		<?php
		// Loop through social networks and display icons
		foreach ($social_networks as $network => $data) {
			if (!empty($data['url'])) {
				$url = isset($data['formatted_url']) ? $data['formatted_url'] : $data['url'];
				$platform = $data['platform'];
				$icon_class = $data['icon_class'];
				?>
				<li class="list-inline-item">
					<a target="_blank" class="btn-square btn-<?php echo esc_attr($platform); ?>" 
					   href="<?php echo esc_url($url); ?>" 
					   rel="noopener noreferrer">
						<i class="houzez-icon <?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
					</a>
				</li><!-- .<?php echo esc_attr($platform); ?> -->
				<?php
			}
		}
		?>
	</ul>
</div><!-- .header-social-icons -->
<?php endif; ?>