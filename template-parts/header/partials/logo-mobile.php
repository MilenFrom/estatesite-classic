<?php
$mobile_logo = houzez_option( 'mobile_logo', false, 'url' );
$logo_height = houzez_option('retina_mobilelogo_height');
$logo_width = houzez_option('retina_mobilelogo_width'); 
?>
<div class="logo logo-mobile">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
	    <?php if( !empty( $mobile_logo ) ) { ?>
	       <img src="<?php echo esc_url( $mobile_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>" width="<?php echo esc_attr($logo_width); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
	    <?php } ?>
	</a>
</div>