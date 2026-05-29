<?php
global $post;
$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );
$permalink = get_permalink();
$title = get_the_title();
$encoded_permalink = urlencode($permalink);
$encoded_title = urlencode($title);
$query_string = $encoded_title . '&nbsp;' . $encoded_permalink;
?>

<div class="dropdown-item" role="menuitem"><?php echo esc_html__('Share on:', 'houzez'); ?></div>

<a class="dropdown-item" target="_blank" href="https://api.whatsapp.com/send?text=<?php echo $query_string; ?>" role="menuitem" aria-label="Share on WhatsApp">
	<i class="houzez-icon icon-messaging-whatsapp me-1" aria-hidden="true"></i> <?php esc_html_e('WhatsApp', 'houzez'); ?>
</a>

<a class="dropdown-item" href="https://pinterest.com/pin/create/button/?url=<?php echo $encoded_permalink; ?>&amp;media=<?php echo !empty($image[0]) ? urlencode($image[0]) : ''; ?>&amp;description=<?php echo $encoded_title; ?>" onclick="window.open(this.href, 'share_pinterest','left=50,top=50,width=600,height=350,toolbar=0'); return false;" role="menuitem" aria-label="Share on Pinterest">
    <i class="houzez-icon icon-social-pinterest me-1" aria-hidden="true"></i> <?php echo esc_html__('Pinterest', 'houzez'); ?>
</a>

<a class="dropdown-item" href="https://www.facebook.com/sharer.php?u=<?php echo $encoded_permalink; ?>&amp;t=<?php echo $encoded_title; ?>" onclick="window.open(this.href, 'share_facebook','left=50,top=50,width=600,height=350,toolbar=0'); return false;" role="menuitem" aria-label="Share on Facebook">
	<i class="houzez-icon icon-social-media-facebook me-1" aria-hidden="true"></i> <?php esc_html_e('Facebook', 'houzez'); ?>
</a>

<a class="dropdown-item" href="https://twitter.com/intent/tweet?text=<?php echo $encoded_title; ?>&url=<?php echo $encoded_permalink; ?>" onclick="window.open(this.href, 'share_twitter','left=50,top=50,width=600,height=350,toolbar=0'); return false;" role="menuitem" aria-label="Share on X">
	<i class="houzez-icon icon-x-logo-twitter-logo-2 me-1" aria-hidden="true"></i> <?php esc_html_e('X', 'houzez'); ?>
</a>

<a class="dropdown-item" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $encoded_permalink; ?>&title=<?php echo $encoded_title; ?>&source=<?php echo urlencode(home_url('/')); ?>" onclick="window.open(this.href, 'share_linkedin','left=50,top=50,width=600,height=350,toolbar=0'); return false;" role="menuitem" aria-label="Share on LinkedIn">
	<i class="houzez-icon icon-professional-network-linkedin me-1" aria-hidden="true"></i> <?php esc_html_e('LinkedIn', 'houzez'); ?>
</a>

<a class="dropdown-item" href="mailto:?subject=<?php echo $encoded_title; ?>&body=<?php echo $encoded_permalink; ?>" role="menuitem" aria-label="Share via Email">
	<i class="houzez-icon icon-envelope me-1" aria-hidden="true"></i> <?php esc_html_e('Email', 'houzez'); ?>
</a>
