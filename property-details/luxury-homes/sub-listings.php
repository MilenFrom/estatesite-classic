<?php
global $post, $multi_units_ids;
?>
<div class="fw-property-sub-listings-wrap fw-property-section-wrap" id="property-sub-listings-wrap">
	<div class="container">
		<div class="block-wrap">
			<div class="block-title-wrap">
				<h2><?php echo houzez_option('sps_sub_listings', 'Sub Listings'); ?></h2>
			</div><!-- block-title-wrap -->
			<div class="block-content-wrap">
				<div class="listing-view list-view row g-4">
					<?php
					$ids = explode(',', $multi_units_ids);
					$args = array(
						'post_type' => 'property',
						'post__in' => $ids,
						'posts_per_page' => -1,
					);
					$query = new WP_Query($args);

					if($query->have_posts()): 
						while ($query->have_posts()): $query->the_post(); 
							get_template_part('template-parts/listing/item-list-v1'); 
						endwhile; 
					endif; 
					wp_reset_query();
					?>
				</div><!-- listing-view -->	
			</div><!-- block-content-wrap -->
		</div><!-- block-wrap -->
	</div><!-- container -->
</div><!-- property-address-wrap -->