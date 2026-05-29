<?php
global $prop_featured;
$prop_featured = houzez_get_listing_data('featured');

if( $prop_featured == 1 ) {
	echo '<span class="label-featured label me-1" role="status">'.houzez_option('cl_featured_label', esc_html__( 'Featured', 'houzez' )).'</span>';
}