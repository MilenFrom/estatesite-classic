<?php
$image_size = houzez_get_image_size_for('agent_profile');
if( has_post_thumbnail() && get_the_post_thumbnail() != '' ) {
	the_post_thumbnail($image_size, array('class' => 'img-fluid'));
} else {
	houzez_image_placeholder( $image_size );
}
?>