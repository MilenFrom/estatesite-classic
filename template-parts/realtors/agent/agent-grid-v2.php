<?php
global $post, $houzez_local, $agent_post_id;
$agent_post_id = $post->ID;
$agent_position = get_post_meta( $agent_post_id, 'fave_agent_position', true );
$languages = get_post_meta( $agent_post_id, 'fave_agent_language', true );
$properties = Houzez_Query::agent_properties_count( $agent_post_id );
$agent_company_logo = get_post_meta( $agent_post_id, 'fave_agent_logo', true );
?>
<article class="agent-grid-container" id="<?php echo $agent_post_id; ?>">
	<div class="agent-grid-wrap agent-grid-wrap-v2">	
		<header class="agent-grid-image-wrap text-center">
			<a class="agent-grid-image d-block" href="<?php echo get_the_permalink($agent_post_id); ?>">
				<?php get_template_part('template-parts/realtors/agent/image'); ?>
				<?php get_template_part('template-parts/realtors/agent/verified'); ?>

				<?php if( !empty( $agent_company_logo ) ) {
				$logo_url = wp_get_attachment_url( $agent_company_logo );
				if( !empty($logo_url) ) {
				?>
				<div class="agent-company-logo bottom-0 start-0">
					<img class="img-fluid" src="<?php echo esc_url( $logo_url ); ?>" alt="" loading="lazy">
				</div>
				<?php }
				} ?>
			</a>
			<h2 class="mt-4 mb-1"><a href="<?php echo get_the_permalink($agent_post_id); ?>"><?php echo get_the_title($agent_post_id); ?></a></h2>
			<?php if( $agent_position != '' ) { ?>
			<div class="agent-list-position mb-3" role="text"><?php echo esc_attr($agent_position); ?></div>
			<?php } ?>
		</header>
		<div class="agent-grid-content-wrap text-center p-3">
			<ul class="agent-list-contact list-unstyled">
				<?php if( ! empty($properties) ) { ?>
				<li class="agent-listings-count"><?php echo $houzez_local['properties']?>: <strong><?php echo esc_attr($properties); ?></strong></li>
				<?php } ?>

				<?php if( !empty( $languages ) ) { ?>
				<li class="agent-languages-list"><?php echo $houzez_local['languages']; ?>: <strong><?php echo esc_attr( $languages ); ?></strong></li>
				<?php } ?>
			</ul>
			<a class="btn btn-primary-outlined w-100" href="<?php echo get_the_permalink($agent_post_id); ?>" role="button">
				<?php echo $houzez_local['view_profile']; ?></a>
		</div>
	</div>
</article>