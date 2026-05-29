<?php 
global $houzez_local, $post; 
$key = '';
$user_id      =   get_current_user_id();
$fav_option = get_user_meta( $user_id, 'houzez_favorites', true );
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}

$icon = '';
if( $key != false || $key != '' ) {
    $icon = 'text-danger';
}

