<div class="footer-bottom-wrap footer-bottom-wrap-v2 pt-4 pb-4 text-center">
	<div class="container">
		<?php 
		if( houzez_option('social-footer') != '0' ) { 
			echo '<nav class="footer-social d-flex justify-content-center flex-wrap">
			<ul class="list-unstyled d-flex flex-wrap justify-content-center m-0">';
			get_template_part('template-parts/footer/social'); 
			echo '</ul>
			</nav>';
		}?>
	
		<div class="footer-logo d-flex justify-content-center">
			<?php get_template_part('template-parts/footer/logo'); ?>
		</div>
		<nav class="footer-nav mb-4 d-flex justify-content-center">
			<?php get_template_part('template-parts/footer/nav'); ?>
		</nav>
		
		<?php if( houzez_option('copy_rights') != '' ) { ?>
			<div class="footer-copyright text-center">
				&copy; <?php echo houzez_option('copy_rights'); ?>
			</div>
		<?php } ?>
	</div><!-- container -->
</div><!-- footer-bottom-wrap -->