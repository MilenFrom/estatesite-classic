<?php
global $houzez_local, $agency_id, $post;
$agency_id = $post->ID;

$service_area = get_post_meta( $agency_id, 'fave_agency_service_area', true );
$agency_properties = Houzez_Query::agency_properties_count( $agency_id );

$agents_properties = 0;
$agency_agents_ids = Houzez_Query::loop_agency_agents_ids($agency_id);

if (!empty($agency_agents_ids)) {
    $agents_properties = Houzez_Query::get_agency_agents_properties_count($agency_agents_ids);
}

$properties = $agency_properties + $agents_properties;
?>
<div class="agency-grid-container">
	<div class="agency-grid-wrap d-flex flex-column align-items-center" role="article" aria-labelledby="agency-name">	
		<div class="agency-grid-image-wrap p-4 text-center d-flex flex-column align-items-center">
			<a class="agency-grid-image d-block mb-3" href="<?php the_permalink($agency_id); ?>">
				<?php get_template_part('template-parts/realtors/agency/image'); ?>
			</a><!-- hover-effect -->
			<h2 id="agency-name" class="mb-3"><a href="<?php the_permalink($agency_id); ?>"><?php echo get_the_title($agency_id); ?></a></h2>
			<?php get_template_part('template-parts/realtors/rating','v2'); ?>
		</div><!-- agency-list-image -->
		<div class="agency-grid-content-wrap text-center p-3">
			<ul class="agency-list-contact list-unstyled" role="list">
				<li role="listitem"><?php echo houzez_option('agency_lb_properties', esc_html__( 'Properties', 'houzez' )); ?>: <strong><?php echo esc_attr($properties); ?></strong></li>
				<?php if( !empty($service_area) ) { ?>
				<li role="listitem"><?php echo houzez_option('agency_lb_service_areas', esc_html__( 'Service Areas', 'houzez' )); ?>: <strong><?php echo esc_attr( $service_area ); ?></strong></li>
				<?php } ?>
			</ul><!-- agency-list-contact -->
			<a class="btn btn-primary-outlined w-100" href="<?php the_permalink($agency_id); ?>"><?php echo houzez_option('agency_view_profile', esc_html__( 'View Profile', 'houzez' )); ?></a>
		</div><!-- agency-list-content -->
	</div><!-- agency-list-wrap -->
</div><!-- agency-grid-container -->