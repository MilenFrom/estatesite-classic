<div class="property-nav-wrap mb-4">
	<div class="container">
		<div class="d-flex justify-content-between" role="navigation" aria-label="Property navigation">
			
			<?php
			// Get previous post
			$prevPost = get_previous_post(false);
			if ($prevPost) {
				// Setup post data directly from the post object
				$prev_permalink = get_permalink($prevPost->ID);
				$prev_title = get_the_title($prevPost->ID);
			?>
			<div class="prev-property d-flex align-items-center gap-4">
				<a href="<?php echo esc_url($prev_permalink); ?>" aria-label="Previous property">
					<?php echo get_the_post_thumbnail($prevPost->ID, array(60, 60), array('class' => 'img-fluid')); ?>
				</a>
				<div>
					<a class="property-nav-link" href="<?php echo esc_url($prev_permalink); ?>" aria-label="Navigate to previous property">
						<?php echo esc_html__('Prev', 'houzez'); ?>
					</a>	
				</div>
			</div><!-- prev-property -->
			<?php
			} else {
				// Empty div to maintain layout when no previous post exists
				echo '<div class="prev-property"></div>';
			}
			
			// Get next post
			$nextPost = get_next_post(false);
			if ($nextPost) {
				// Setup post data directly from the post object
				$next_permalink = get_permalink($nextPost->ID);
				$next_title = get_the_title($nextPost->ID);
			?>
			<div class="next-property d-flex align-items-center gap-4">
				<div>
					<a class="property-nav-link" href="<?php echo esc_url($next_permalink); ?>" aria-label="Navigate to next property">
						<?php echo esc_html__('Next', 'houzez'); ?>
					</a>	
				</div>
				<a href="<?php echo esc_url($next_permalink); ?>" aria-label="Next property">
					<?php echo get_the_post_thumbnail($nextPost->ID, array(60, 60), array('class' => 'img-fluid')); ?>
				</a>
			</div><!-- next-property -->
			<?php
			} else {
				// Empty div to maintain layout when no next post exists
				echo '<div class="next-property"></div>';
			}
			?>

		</div><!-- d-flex -->
	</div><!-- container -->
</div><!-- property-nav-wrap -->