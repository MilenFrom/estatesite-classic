<div class="footer-bottom-wrap footer-bottom-wrap-v4 pt-4 pb-4">
	<div class="container">
		<div class="d-flex justify-content-between flex-column flex-md-row align-items-center">
			<?php if( houzez_option('copy_rights') != '' ) { ?>
				<div class="footer-copyright mb-sm-2">
					<p>&copy; <?php echo houzez_option('copy_rights'); ?></p>
				</div>
			<?php } ?>

			<div class="footer-logo d-flex justify-content-center mb-sm-4 mt-sm-3">
				<?php get_template_part('template-parts/footer/logo'); ?>
			</div>

			<?php 
			if( houzez_option('social-footer') != '0' ) { 
				echo '<nav class="footer-social">
				<ul class="list-unstyled d-flex mb-0">';
				get_template_part('template-parts/footer/social'); 
				echo '</ul>
				</nav>';
			}?>
		</div><!-- d-flex -->
	</div><!-- container -->
</div><!-- footer-top-wrap -->