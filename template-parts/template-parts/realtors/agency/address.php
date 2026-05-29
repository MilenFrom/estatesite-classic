<?php 
$agency_address = get_post_meta( get_the_ID(), 'fave_agency_address', true );
if(!empty($agency_address)) {
	echo '<address class="mb-1"><i class="houzez-icon icon-pin"></i> <span>'.$agency_address.'</span></address>';
}