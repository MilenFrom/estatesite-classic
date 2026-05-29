<?php
global $houzez_local, $post;
$text = get_post_meta($post->ID, 'fave_testi_text', true);
$name = get_post_meta($post->ID, 'fave_testi_name', true);
$position = get_post_meta($post->ID, 'fave_testi_position', true);
$company = get_post_meta($post->ID, 'fave_testi_company', true);
$photo_id = get_post_meta($post->ID, 'fave_testi_photo', true);
$logo_id = get_post_meta($post->ID, 'fave_testi_logo', true);
$testimonials_icon = isset($args['testimonials_icon']) ? $args['testimonials_icon'] : '';
?>
<div class="testimonial-item testimonial-item-v2 mb-4">
	<?php if ($testimonials_icon == 'yes') { ?>	
		<div class="testimonial-icon mb-2">
			<i class="houzez-icon icon-close-quote"></i>
		</div><!-- testimonial-icon -->
	<?php } ?>
	<div class="testimonial-body mb-4">
		<?php echo wp_kses_post($text); ?>
	</div><!-- testimonial-body -->
	<div class="d-flex align-items-center">
		
		<?php if (!empty($photo_id)) { ?>
	        <div class="testimonial-thumb mb-0 me-4">
	            <?php echo wp_get_attachment_image($photo_id, array('70', '70'), false, array('class' => 'img-fluid rounded-circle', 'srcset' => '')); ?>
	        </div>
	    <?php } ?>	

		<div class="testimonial-info">
			<strong class="testimonial-name"><?php echo esc_attr($name); ?></strong><br>
			<em class="testimonial-job"><?php 
				if(!empty($company)){
					echo esc_attr($company);
				} else if(!empty($position)) {
					echo esc_attr($position);
				}
			?></em>
		</div><!-- testimonial-info -->
	</div><!-- d-flex -->
</div><!-- testimonial-item -->