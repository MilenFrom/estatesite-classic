<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Houzez
 * @since Houzez 1.0 
 */
global $houzez_local, $ele_settings;
$blog_date = houzez_option('blog_date');
$blog_author = houzez_option('blog_author');
$show_author = isset($ele_settings['show_author']) ? $ele_settings['show_author'] : $blog_author;
$show_date = isset($ele_settings['show_date']) ? $ele_settings['show_date'] : $blog_date;
$show_cat = isset($ele_settings['show_cat']) ? $ele_settings['show_cat'] : true;
$image_size = houzez_get_image_size_for('blog_grid');
$post_thumb_size = isset($ele_settings['post_thumb_size']) ? $ele_settings['post_thumb_size'] : $image_size;

$content_class = '';
if( ! $show_author ) {
	$content_class = 'with-no-author';
}
?>
<div class="blog-post-item-wrap">
	<div id="post-<?php the_ID(); ?>" <?php post_class('blog-post-item blog-post-item-v2'); ?>>
		
		<?php if(has_post_thumbnail()) { ?>
		<div class="blog-post-thumb">
			<a href="<?php the_permalink(); ?>" class="hover-effect image-wrap">
				<?php the_post_thumbnail($post_thumb_size, array('class' => 'img-fluid')); ?>
			</a>
		</div><!-- blog-post-thumb -->
		<?php } ?>

		<div class="blog-post-content-wrap <?php echo esc_attr($content_class); ?>">
			<div class="blog-post-meta small">
				<ul class="list-inline d-flex align-items-center mb-1">
					<?php if( $show_date ) { ?>
					<li class="list-inline-item">
						<time datetime="<?php esc_attr( the_time( get_option( 'date_format' ) ));?>"><i class="houzez-icon icon-calendar-3 me-1"></i> <?php esc_attr( the_time( get_option( 'date_format' ) ));?></time>
					</li>
					<?php } ?>

					<?php if( $show_cat ) { ?>
					<li class="list-inline-item">
						<i class="houzez-icon icon-tags me-1"></i> <?php the_category(', '); ?>
					</li>
					<?php } ?>
				</ul>
			</div><!-- blog-post-meta -->
			<div class="blog-post-title">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			</div><!-- blog-post-title -->
		</div><!-- blog-post-content-wrap -->
	</div><!-- blog-post-item -->
	<?php if( $show_author ) { ?>
	<div class="blog-post-author">
		<i class="houzez-icon icon-single-neutral me-1"></i> <?php echo $houzez_local['by_text']; ?> <?php the_author(); ?>
	</div>
	<?php } ?>
</div>