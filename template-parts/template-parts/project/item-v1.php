<?php
global $post, $ele_thumbnail_size, $image_size; 
$image_size = 'houzez-item-image-6';
?>
<div class="item-listing-wrap item-listing-wrap-v6 project-item-listing-wrap project-item-listing-wrap-v6 card">
	<div class="item-wrap item-wrap-v6 project-item-wrap-v6 h-100">
		<div class="d-flex align-items-center h-100">
			<div class="item-header">
				<?php get_template_part('template-parts/project/item', 'featured-label'); ?>
				<div class="listing-image-wrap">
					<div class="listing-thumb">
						<a class="listing-featured-thumb" <?php houzez_project_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>">
						<?php
						$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;

					    if( has_post_thumbnail( $post->ID ) && get_the_post_thumbnail($post->ID) != '' ) {
					        the_post_thumbnail( $thumbnail_size, array('class' => 'img-fluid') );
					    }else{
					        houzez_image_placeholder( $thumbnail_size );
					    }
					    ?>
						</a>
					</div>
				</div>
				<?php get_template_part('template-parts/project/item', 'tools'); ?>
			</div><!-- item-header -->	
			<div class="item-body flex-grow-1">
				<div class="project-title-label-wrap">
					<?php get_template_part('template-parts/project/item', 'labels'); ?>
					<?php get_template_part('template-parts/project/item', 'title'); ?>	
				</div>
				<div class="d-flex justify-content-between align-items-center amenities-price-wrap">
					<ul class="item-price-wrap">
						<?php echo houzez_project_price_v1(); ?>
					</ul>
				</div><!-- d-flex -->
				<?php get_template_part('template-parts/project/item', 'address'); ?>
				<dl class="project-type-wrap list-inline">
					<dt class="project-developer-label">Developer:</dt>
					<dd>Company Name</dd>
				</dl>
				<dl class="project-type-wrap list-inline">
					<dt class="project-developer-label">Type:</dt>
					<dd>Apartments</dd>
				</dl>
				<div class="project-delivery-date-wrap">
					<span class="project-delivery-date-label label">
						Delivery Date: Q1 2028
					</span>
				</div>
			</div><!-- item-body -->
		</div><!-- d-flex -->
	</div><!-- item-wrap -->
</div><!-- item-listing-wrap -->