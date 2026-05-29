<?php
global $houzez_local;
$blog_date = houzez_option('blog_date');
$blog_author = houzez_option('blog_author');
?>
<ul class="list-unstyled list-inline author-meta flex-grow-1 m-0">
					
	<?php if( $blog_author != 0 && get_the_author_meta( 'fave_author_custom_picture' ) != '') { ?>
	<li class="list-inline-item">
		<img class="img-fluid rounded-circle me-2" src="<?php echo esc_url( get_the_author_meta( 'fave_author_custom_picture' ) );?>" width="40" height="40" alt="<?php echo esc_attr( get_the_author() ); ?>"> <?php echo $houzez_local['by_text']; ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo esc_attr( get_the_author() ); ?></a>
	</li>
	<?php } ?>

	<?php if($blog_date != 0) { ?>
	<li class="list-inline-item">
		<i class="houzez-icon icon-calendar-3 me-1"></i> <?php printf( esc_html__( '%s ago', 'houzez' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
	</li>
	<?php } ?>

	<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && houzez_categorized_blog() ) : ?>
	<li class="list-inline-item">
		<i class="houzez-icon icon-tags me-1"></i> <?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'houzez' ) ); ?>
	</li>
	<?php endif; ?>

	<?php if( comments_open() ) { ?>
	<li class="list-inline-item">
	    <i class="houzez-icon icon-messages-bubble me-1"></i> <?php echo get_comments_number(); ?>
	</li>
	<?php } ?>

</ul><!-- author-meta -->