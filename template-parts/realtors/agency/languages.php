<?php 
global $houzez_local;
$languages = get_post_meta( get_the_ID(), 'fave_agency_language', true );

if( !empty( $languages ) ) { ?>
	<p class="mb-0">
		<i class="houzez-icon icon-messages-bubble me-1"></i>
		<strong><?php echo houzez_option('agency_lb_language', esc_html__( 'Language', 'houzez' )); ?>:</strong> 
		<span><?php echo esc_attr( $languages ); ?></span>
	</p>
<?php } ?>