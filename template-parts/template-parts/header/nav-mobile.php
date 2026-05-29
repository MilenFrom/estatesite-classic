<nav class="nav-mobile" role="navigation">
	<div class="main-nav navbar" id="nav-mobile">
		<div class="offcanvas offcanvas-start offcanvas-mobile-menu" tabindex="-1" id="hz-offcanvas-mobile-menu" aria-labelledby="hz-offcanvas-mobile-menu-label">
			<div class="offcanvas-header">
				<div class="offcanvas-title fs-6" id="hz-offcanvas-mobile-menu-label"><?php echo esc_html__( 'Menu', 'houzez' ); ?></div>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas">
				<i class="houzez-icon icon-close"></i>
				</button>
			</div>
			<div class="offcanvas-mobile-menu-body">
				<?php get_template_part('template-parts/header/partials/mobile-nav'); ?>

				<?php
				if( is_active_sidebar( 'hz-mobile-menu' ) ) {
					dynamic_sidebar( 'hz-mobile-menu' );
				}
				?>
			</div>
		</div>
	</div><!-- main-nav -->
	<?php 
	if( is_user_logged_in() ) {
		get_template_part('template-parts/header/partials/logged-in-nav-mobile');

	} else {
		get_template_part('template-parts/header/partials/login-register-mobile');
	}
	?>    
</nav><!-- nav-mobile -->