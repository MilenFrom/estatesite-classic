<?php 
global $hide_author_date; 

$show_author_date = isset($hide_author_date) ? $hide_author_date : houzez_option('disable_date', 1);

if( $show_author_date ) { ?>
<div class="item-date d-flex align-items-center gap-1">
	<i class="houzez-icon icon-attachment me-1"></i>
	<?php printf( esc_html__( '%s ago', 'houzez' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
</div><!-- item-date -->
<?php } ?>