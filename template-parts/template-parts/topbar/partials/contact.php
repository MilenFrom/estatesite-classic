<?php
$top_bar_phone = houzez_option('top_bar_phone');
$top_bar_email = houzez_option('top_bar_email');
?>
<div class="top-bar-contact">
	<?php if( !empty($top_bar_phone)) { ?>
	<!-- Phone Number -->
	<span class="top-bar-contact-info top-bar-contact-phone">
		<a href="tel:<?php echo str_replace(' ', '', $top_bar_phone); ?>">
			<i class="houzez-icon icon-phone me-1"></i>
			<span><?php echo esc_attr($top_bar_phone); ?></span>
		</a>
	</span>
	<?php } ?>

	<?php if( !empty( $top_bar_email ) ) { ?>
	<!-- Email Address -->
	<span class="top-bar-contact-info top-bar-contact-email">
		<a href="mailto:<?php echo esc_attr($top_bar_email); ?>">
			<i class="houzez-icon icon-envelope me-1"></i>
			<span><?php echo esc_attr($top_bar_email); ?></span>
		</a>
	</span>
	<?php } ?>
</div><!-- top-bar-contact -->