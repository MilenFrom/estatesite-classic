<?php
$blog_date = houzez_option('blog_date');
$blog_author = houzez_option('blog_author');
$image_size = houzez_get_image_size_for('blog_grid');
?>
<div class="blog-post-item blog-post-item-v1 h-100 d-flex flex-column">

	<?php if( houzez_option('blog_featured_image', 1 ) ) { ?>
	<div class="blog-post-thumb">
		<a href="<?php echo esc_url(get_permalink()); ?>" class="hover-effect image-wrap">
			<?php the_post_thumbnail($image_size, array('class' => 'img-fluid')); ?>
		</a>
	</div><!-- blog-post-thumb -->
	<?php } ?>
	
	<div class="blog-post-content-wrap flex-grow-1 d-flex flex-column">
		<div class="blog-post-meta">
			<ul class="list-inline">
				<?php if( $blog_date != 0 ) { ?>
				<li class="list-inline-item d-flex align-items-center">
					<time datetime="<?php esc_attr( the_time( get_option( 'date_format' ) ));?>"><i class="houzez-icon icon-attachment me-1"></i> <?php esc_attr( the_time( get_option( 'date_format' ) ));?></time>
				</li>
				<?php } ?>

				<li class="list-inline-item d-flex align-items-center">
					<i class="houzez-icon icon-tags me-1"></i> <?php the_category(', '); ?>
				</li>
			</ul>
		</div><!-- blog-post-meta -->
		<div class="blog-post-title">
			<h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h3>
		</div><!-- blog-post-title -->
		<div class="blog-post-body">
			<?php echo houzez_clean_excerpt( 95, false ); ?>
		</div><!-- blog-post-body -->
		<div class="blog-post-link mt-auto">
			<a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html__('Continue Reading', 'houzez'); ?></a>
		</div><!-- blog-post-link -->
	</div><!-- blog-post-content-wrap -->

	<?php if( $blog_author != 0 ) { ?>
	<div class="blog-post-author">
		<i class="houzez-icon icon-single-neutral me-1"></i> <?php echo esc_html__('by', 'houzez'); ?> <?php the_author(); ?>
	</div>
	<?php } ?>

</div><!-- blog-post-item -->