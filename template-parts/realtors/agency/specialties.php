<?php 
global $houzez_local;
$agency_specialties = get_post_meta( get_the_ID(), 'fave_agency_specialties', true );

if( !empty( $agency_specialties ) ) { ?>
	<li>
		<strong><?php echo $houzez_local['specialties_label']; ?>:</strong> 
		<span><?php echo esc_attr( $agency_specialties ); ?></span>
	</li>
<?php } ?>