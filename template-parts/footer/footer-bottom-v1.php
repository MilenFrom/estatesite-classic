<div class="footer-bottom-wrap footer-bottom-wrap-v1 pt-4 pb-4">
	<div class="container">
		<div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
			<?php if( houzez_option('copy_rights') != '' ) { ?>
				<div class="footer-copyright mb-3 mb-md-0">
					&copy; <?php echo houzez_option('copy_rights'); ?>
				</div>
			<?php } ?>
		
			<nav class="footer-nav mb-3 mb-md-0">
				<?php get_template_part('template-parts/footer/nav'); ?>
			</nav>
			
			<?php 
			if( houzez_option('social-footer') != '0' ) { 
				echo '<nav class="footer-social">
				<ul class="list-unstyled d-flex mb-0">';
				get_template_part('template-parts/footer/social'); 
				echo '</ul>
				</nav>';
			}?>
		</div>
	</div>
</div>