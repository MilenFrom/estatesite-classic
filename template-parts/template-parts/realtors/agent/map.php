<?php
$agent_address = get_post_meta( get_the_ID(), 'fave_agent_address', true );

$address_coordinates = [];
if( ! empty( $agent_address ) ) {
	$address_coordinates = houzez_get_address_coordinates($agent_address);
}

if( ! empty( $address_coordinates ) ) { ?>
	<div id="houzez-agent-sidebar-map" data-lat="<?php echo esc_attr($address_coordinates['lat']); ?>" data-lng="<?php echo esc_attr($address_coordinates['lng']); ?>"></div>
<?php } ?>