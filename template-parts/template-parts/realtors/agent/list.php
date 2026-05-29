<?php
global $houzez_local;
$agent_position = get_post_meta( get_the_ID(), 'fave_agent_position', true );
$agent_company = get_post_meta( get_the_ID(), 'fave_agent_company', true );
?>
<article class="agent-list-wrap mb-3 p-4">
	<div class="row g-4 align-items-center">
		<div class="col-md-4">
			<figure class="agent-list-image m-0">
				<a href="<?php the_permalink(); ?>">
					<?php get_template_part('template-parts/realtors/agent/image'); ?>
				</a>	
			</figure>
		</div>

		<div class="col-md-8">
			<div class="agent-list-content">
				<header class="d-flex align-items-center justify-content-between gap-2 mb-1">
					<h2 class="mb-0 d-flex align-items-center gap-2">
						<?php get_template_part('template-parts/realtors/agent/verified'); ?>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> 
					</h2>
					<?php 
					if( houzez_option( 'agent_review', 0 ) != 0 ) { 
						get_template_part('template-parts/realtors/rating'); 
					}?>
				</header>

				<?php get_template_part('template-parts/realtors/agent/position'); ?>

				<dl class="agent-list-contact list-unstyled list-lined">
					<?php
					if( houzez_option('agent_phone', 1) ) {
						get_template_part('template-parts/realtors/agent/office-phone'); 
					} 

					if( houzez_option('agent_mobile', 1) ) {
						get_template_part('template-parts/realtors/agent/mobile'); 
					}

					if( houzez_option('agent_fax', 1) ) {
						get_template_part('template-parts/realtors/agent/fax'); 
					} 

					if( houzez_option('agent_email', 1) ) {
						get_template_part('template-parts/realtors/agent/email'); 
					}
					?>
				</dl>
				<footer class="d-flex align-items-center justify-content-between">
					<nav class="agent-social-media">
						<?php 
						if( houzez_option('agent_social', 1) ) {
							get_template_part('template-parts/realtors/agent/social'); 
						}?>
					</nav>
					<a class="agent-list-link no-wrap" href="<?php the_permalink(); ?>"><strong><?php echo $houzez_local['view_my_prop']; ?></strong></a>
				</footer>

			</div>
		</div>
	</div>
</article>