<div class="footer-bottom-wrap footer-bottom-wrap-v3 pt-4 pb-4 text-center">
	<div class="container">
		<div class="footer-logo d-flex justify-content-center mb-4 mt-4">
			<?php get_template_part('template-parts/footer/logo'); ?>
		</div>
		<nav class="footer-nav mb-4 d-flex justify-content-center">
			<?php get_template_part('template-parts/footer/nav'); ?>
		</nav>

		<?php 
		if( houzez_option('social-footer') != '0' ) { 
			echo '<nav class="footer-social d-flex justify-content-center flex-wrap">
			<ul class="social-links-list list-unstyled d-flex mb-0">';
			get_template_part('template-parts/footer/social'); 
			echo '</ul>
			</nav>';
		}?>
		
		<?php if( houzez_option('copy_rights') != '' ) { ?>
			<div class="footer-copyright mb-sm-2">
				<p>&copy; <?php echo houzez_option('copy_rights'); ?></p>
			</div>
		<?php } ?>
	</div>
</div>