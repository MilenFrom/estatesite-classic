<?php 
$text_facebook = $text_twitter = $text_instagram = $text_linkedin = $text_googleplus = $text_youtube = $text_pinterest = $text_yelp = $text_behance = $text_tiktok = $text_whatsapp = $text_telegram = '';
$show_text = false;
$icon_spacing = 'me-2';
$aria_hidden = 'aria-hidden="true"';
$wrapper_tag = 'span';

$agent_whatsapp = houzez_option('fs-whatsapp');
$agent_whatsapp_call = str_replace(array('(',')',' ','-'),'', $agent_whatsapp);

// Determine which version to display based on ft-bottom option
$footer_style = houzez_option('ft-bottom');

// Set up styling based on footer style
if($footer_style == 'v1' || $footer_style == 'v4') {
	// Version 1 & 4: Icons only with me-2 spacing
	$show_text = false;
	$icon_spacing = 'me-2';
	$aria_hidden = 'aria-hidden="true"';
	$wrapper_tag = 'li';
	$wrapper_class = 'mx-2';
} else if($footer_style == 'v2') {
	// Version 2: Icons with text and me-2 spacing
	$show_text = true;
	$icon_spacing = 'me-2';
	$aria_hidden = '';
	$wrapper_tag = 'li';
	$wrapper_class = 'mx-2 mb-2 mb-lg-0';
	
	$text_facebook = esc_html__('Facebook', 'houzez'); 
	$text_twitter = esc_html__('Twitter', 'houzez');
	$text_instagram = esc_html__('Instagram', 'houzez'); 
	$text_linkedin = esc_html__('Linkedin', 'houzez');
	$text_googleplus = esc_html__('Google +', 'houzez');
	$text_youtube = esc_html__('Youtube', 'houzez');
	$text_pinterest = esc_html__('Pinterest', 'houzez');
	$text_yelp = esc_html__('Yelp', 'houzez');
	$text_behance = esc_html__('Behance', 'houzez');
	$text_tiktok = esc_html__('TikTok', 'houzez');
	$text_whatsapp = esc_html__('WhatsApp', 'houzez');
	$text_telegram = esc_html__('Telegram', 'houzez');
} else if($footer_style == 'v3') {
	// Version 3: Icons only with mx-1 spacing and no me-2 class
	$show_text = false;
	$icon_spacing = '';
	$aria_hidden = '';
	$wrapper_tag = 'li';
	$wrapper_class = 'mx-1';
}
?>

<?php if( houzez_option('fs-facebook') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-facebook" target="_blank" href="<?php echo esc_url(houzez_option('fs-facebook')); ?>" aria-label="Facebook">
		<i class="houzez-icon icon-social-media-facebook <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_facebook : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-twitter') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-twitter" target="_blank" href="<?php echo esc_url(houzez_option('fs-twitter')); ?>" aria-label="Twitter">
		<i class="houzez-icon icon-x-logo-twitter-logo-2 <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_twitter : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-instagram') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-instagram" target="_blank" href="<?php echo esc_url(houzez_option('fs-instagram')); ?>" aria-label="Instagram">
		<i class="houzez-icon icon-social-instagram <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_instagram : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-linkedin') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-linkedin" target="_blank" href="<?php echo esc_url(houzez_option('fs-linkedin')); ?>" aria-label="LinkedIn">
		<i class="houzez-icon icon-professional-network-linkedin <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_linkedin : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-googleplus') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-googleplus" target="_blank" href="<?php echo esc_url(houzez_option('fs-googleplus')); ?>" aria-label="Google Plus">
		<i class="houzez-icon icon-social-media-google-plus-1 <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_googleplus : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-youtube') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-youtube" target="_blank" href="<?php echo esc_url(houzez_option('fs-youtube')); ?>" aria-label="YouTube">
		<i class="houzez-icon icon-social-video-youtube-clip <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_youtube : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-pinterest') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-pinterest" target="_blank" href="<?php echo esc_url(houzez_option('fs-pinterest')); ?>" aria-label="Pinterest">
		<i class="houzez-icon icon-social-pinterest <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_pinterest : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-yelp') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-yelp" target="_blank" href="<?php echo esc_url(houzez_option('fs-yelp')); ?>" aria-label="Yelp">
		<i class="houzez-icon icon-social-media-yelp <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_yelp : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-behance') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-behance" target="_blank" href="<?php echo esc_url(houzez_option('fs-behance')); ?>" aria-label="Behance">
		<i class="houzez-icon icon-designer-community-behance <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_behance : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-vimeo') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a class="btn-vimeo" target="_blank" href="<?php echo esc_url(houzez_option('fs-vimeo')); ?>" aria-label="Vimeo">
		<i class="houzez-icon icon-social-video-vimeo <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.esc_html__('Vimeo', 'houzez') : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( $agent_whatsapp != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a target="_blank" class="btn-whatsapp" href="https://wa.me/<?php echo esc_attr( $agent_whatsapp_call ); ?>" aria-label="WhatsApp">
		<i class="houzez-icon icon-messaging-whatsapp <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_whatsapp : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-tiktok') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a target="_blank" class="btn-tiktok" href="<?php echo esc_url(houzez_option('fs-tiktok')); ?>" aria-label="TikTok">
		<i class="houzez-icon icon-tiktok-1-logos-24 <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_tiktok : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>

<?php if( houzez_option('fs-telegram') != '' ){ ?>
<<?php echo $wrapper_tag; ?><?php echo $wrapper_tag == 'li' ? ' class="'.$wrapper_class.'"' : ''; ?>>
	<a target="_blank" class="btn-telegram" href="https://telegram.me/<?php echo esc_attr(houzez_option('fs-telegram')); ?>" aria-label="Telegram">
		<i class="houzez-icon icon-telegram-logos-24 <?php echo esc_attr($icon_spacing); ?>" <?php echo $aria_hidden; ?>></i><?php echo $show_text ? ' '.$text_telegram : ''; ?>
	</a>
</<?php echo $wrapper_tag; ?>>
<?php } ?>