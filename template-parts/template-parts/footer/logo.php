<?php $footer_logo = houzez_option( 'footer_logo', false, 'url' ); ?>
<?php if(!empty($footer_logo)) { ?>
<div class="footer_logo logo">
	<img src="<?php echo esc_url($footer_logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
</div><!-- .logo -->
<?php } ?>