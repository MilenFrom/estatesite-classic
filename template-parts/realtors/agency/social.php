<?php
/**
 * Agency Social Media Links Template
 *
 * @package Houzez
 * @since Houzez 1.0
 */

// Get agency social media links
$agency_id = get_the_ID();

// Process phone numbers
$agency_mobile = get_post_meta($agency_id, 'fave_agency_mobile', true);
$agency_whatsapp = get_post_meta($agency_id, 'fave_agency_whatsapp', true);
$agency_mobile_call = str_replace(array('(',')',' ','-'), '', $agency_mobile);
$agency_whatsapp_call = str_replace(array('(',')',' ','-'), '', $agency_whatsapp);
$agency_line_id = get_post_meta($agency_id, 'fave_agency_line_id', true);

// Define social media platforms with their properties
$social_networks = array(
    'facebook' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_facebook', true),
        'class' => 'btn-facebook',
        'icon' => 'icon-social-media-facebook',
        'url_callback' => 'esc_url'
    ),
    'instagram' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_instagram', true),
        'class' => 'btn-instagram',
        'icon' => 'icon-social-instagram',
        'url_callback' => 'esc_url'
    ),
    'twitter' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_twitter', true),
        'class' => 'btn-twitter',
        'icon' => 'icon-x-logo-twitter-logo-2',
        'url_callback' => 'esc_url'
    ),
    'linkedin' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_linkedin', true),
        'class' => 'btn-linkedin',
        'icon' => 'icon-professional-network-linkedin',
        'url_callback' => 'esc_url'
    ),
    'googleplus' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_googleplus', true),
        'class' => 'btn-googleplus',
        'icon' => 'icon-social-media-google-plus-1',
        'url_callback' => 'esc_url'
    ),
    'youtube' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_youtube', true),
        'class' => 'btn-youtube',
        'icon' => 'icon-social-video-youtube-clip',
        'url_callback' => 'esc_url'
    ),
    'tiktok' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_tiktok', true),
        'class' => 'btn-tiktok',
        'icon' => 'icon-tiktok-1-logos-24',
        'url_callback' => 'esc_url'
    ),
    'pinterest' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_pinterest', true),
        'class' => 'btn-pinterest',
        'icon' => 'icon-social-pinterest',
        'url_callback' => 'esc_url'
    ),
    'vimeo' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_vimeo', true),
        'class' => 'btn-vimeo',
        'icon' => 'icon-social-video-vimeo',
        'url_callback' => 'esc_url'
    ),
    'telegram' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_telegram', true),
        'class' => 'btn-telegram',
        'icon' => 'icon-telegram-logos-24',
        'url_callback' => 'houzezStandardizeTelegramURL'
    ),
    'realtor_com' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_realtor_com', true),
        'class' => 'btn-realtor-com',
        'icon' => 'icon-realtor-com',
        'url_callback' => 'esc_url'
    ),
    'zillow' => array(
        'value' => get_post_meta($agency_id, 'fave_agency_zillow', true),
        'class' => 'btn-zillow',
        'icon' => 'icon-zillow',
        'url_callback' => 'esc_url'
    )
);

// Special cases with custom URL formats
$special_networks = array(
    'line' => array(
        'value' => $agency_line_id,
        'class' => 'btn-lineapp',
        'icon' => 'icon-lineapp-5',
        'url' => !empty($agency_line_id) ? 'https://line.me/ti/p/~' . esc_attr($agency_line_id) : '',
        'attr_callback' => 'esc_attr'
    ),
    'whatsapp' => array(
        'value' => $agency_whatsapp,
        'class' => 'btn-whatsapp',
        'icon' => 'icon-messaging-whatsapp',
        'url' => !empty($agency_whatsapp) ? 'https://wa.me/' . esc_attr($agency_whatsapp_call) : '',
        'span_class' => 'agent-whatsapp',
        'attr_callback' => 'esc_attr'
    )
);

// Render standard social networks
foreach ($social_networks as $network => $data) {
    if (!empty($data['value'])) {
        $url_function = $data['url_callback'];
        ?>
        <span>
            <a class="<?php echo esc_attr($data['class']); ?>" target="_blank" href="<?php echo $url_function($data['value']); ?>">
                <i class="houzez-icon <?php echo esc_attr($data['icon']); ?> me-2"></i>
            </a>
        </span>
        <?php
    }
}

// Render special networks with custom URL formats
foreach ($special_networks as $network => $data) {
    if (!empty($data['value'])) {
        $span_class = isset($data['span_class']) ? ' class="' . esc_attr($data['span_class']) . '"' : '';
        ?>
        <span<?php echo $span_class; ?>>
            <a class="<?php echo esc_attr($data['class']); ?>" target="_blank" href="<?php echo $data['url']; ?>">
                <i class="houzez-icon <?php echo esc_attr($data['icon']); ?> me-2"></i>
            </a>
        </span>
        <?php
    }
}
?>