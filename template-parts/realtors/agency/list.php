<div class="agent-list-wrap mb-3 p-4">
	<div class="row g-4 align-items-center">
		<div class="col-md-4">
			<figure class="agent-list-image m-0">
				<a href="<?php the_permalink(); ?>">
					<?php get_template_part('template-parts/realtors/agency/image'); ?>
				</a><!-- hover-effect -->
			</figure><!-- agent-list-image -->
		</div><!-- col-md-4 -->
		<div class="col-md-8">
			<div class="agent-list-content">
				<header class="d-flex align-items-center justify-content-between gap-2 mb-1">
					<h2 class="mb-0 d-flex align-items-center gap-2">
						<?php get_template_part('template-parts/realtors/agency/verified'); ?>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> 
					</h2>
					<?php get_template_part('template-parts/realtors/rating'); ?>
				</header><!-- d-flex -->
			</div><!-- d-flex -->
			<?php 
			if( houzez_option('agency_address', 1) ) {
				get_template_part('template-parts/realtors/agency/address'); 
			}?>
			
			<dl class="agent-list-contact list-unstyled list-lined">
				<?php
				if( houzez_option('agency_phone', 1) ) {
					get_template_part('template-parts/realtors/agency/office-phone');
				} 

				if( houzez_option('agency_mobile', 1) ) {
					get_template_part('template-parts/realtors/agency/mobile'); 
				}

				if( houzez_option('agency_fax', 1) ) {
					get_template_part('template-parts/realtors/agency/fax');
				} 

				if( houzez_option('agency_email', 1) ) {
					get_template_part('template-parts/realtors/agency/email'); 
				}
				?>
			</dl><!-- agent-list-contact -->
			<footer class="d-flex align-items-center justify-content-between">
				<nav class="agent-social-media">
					<?php 
					if( houzez_option('agency_social', 1) ) {
						get_template_part('template-parts/realtors/agency/social'); 
					}?>
				</nav>
				<a class="agent-list-link no-wrap" href="<?php the_permalink(); ?>"><strong><?php echo houzez_option( 'agency_view_listings', esc_html__('View My Listings', 'houzez') ); ?></strong></a>
			</footer><!-- d-flex -->
		</div><!-- col-md-8 -->
	</div><!-- row -->
</div><!-- agent-list-wrap -->