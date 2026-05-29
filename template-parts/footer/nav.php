<?php 
if ( has_nav_menu( 'footer-menu' ) ) : 
	// Pages Menu
	wp_nav_menu( array (
		'theme_location' => 'footer-menu',
		'container' => '',
		'container_class' => '',
		'menu_class' => 'nav',
		'menu_id' => 'footer-menu',
		'depth' => 1,
		'walker' => new houzez_nav_walker()
	));
 endif; ?>