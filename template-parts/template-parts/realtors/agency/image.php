<?php
global $agency_id;
$image_size = houzez_get_image_size_for('agency_profile');
if( has_post_thumbnail($agency_id) ) {
	echo get_the_post_thumbnail($agency_id, $image_size, array('class' => 'img-fluid'));
} else {
	houzez_image_placeholder( $image_size );
}
?>