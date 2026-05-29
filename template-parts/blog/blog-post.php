<?php
global $houzez_local;
$image_size = houzez_get_image_size_for('blog_post');
?>
<div class="article-wrap">
	<article class="post-wrap">

		<?php if( houzez_option('blog_featured_image', 1 ) ) { ?>
		<div class="post-thumbnail-wrap">
			<a href="<?php echo esc_url(get_permalink()); ?>">
				<?php the_post_thumbnail($image_size, array('class' => 'img-fluid')); ?>
			</a>
		</div><!-- post-thumbnail-wrap -->
		<?php } ?>
		
		<div class="post-inner-wrap">
			<div class="post-title-wrap">
				<h2><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h2>
			</div><!-- post-title-wrap -->
			<div class="post-excerpt-wrap">
				<p><?php echo houzez_clean_excerpt( 410, false ); ?></p>
			</div><!-- post-excerpt-wrap -->
		</div><!-- post-inner-wrap -->
		<div class="post-footer-wrap border-top">
			<div class="d-flex flex-column flex-md-row">
				<?php get_template_part('template-parts/blog/meta'); ?>

				<a class="btn btn-primary" href="<?php echo esc_url(get_permalink()); ?>"><?php echo $houzez_local['read_more'];?></a>
			</div><!-- d-flex -->
		</div><!-- post-footer-wrap -->
	</article><!-- post-wrap -->
</div><!-- article-wrap -->