<?php
global $houzez_local, $post;
$text = get_post_meta($post->ID, 'fave_testi_text', true);
$name = get_post_meta($post->ID, 'fave_testi_name', true);
$position = get_post_meta($post->ID, 'fave_testi_position', true);
$company = get_post_meta($post->ID, 'fave_testi_company', true);
$photo_id = get_post_meta($post->ID, 'fave_testi_photo', true);
$logo_id = get_post_meta($post->ID, 'fave_testi_logo', true);
?>
<div class="testimonial-item testimonial-item-v3">
    <div class="testimonial-body">
        <?php echo wp_kses_post($text); ?>
    </div><!-- testimonial-body -->
    <div class="d-flex align-items-center">
        <?php if (!empty($photo_id)) { ?>
        <div class="testimonial-thumb">
            <?php echo wp_get_attachment_image($photo_id, 'thumbnail', false, array('class' => 'img-fluid rounded-circle')); ?>
        </div><!-- testimonial-thumb -->
        <?php } ?>
        <div class="testimonial-info">
            <strong class="testimonial-name"><?php echo esc_attr($name); ?></strong><br>
            <?php if(!empty($company)){ ?>
            <em class="testimonial-job"><?php echo esc_attr($company); ?></em>
            <?php } else if(!empty($position)) { ?>
            <em class="testimonial-position"><?php echo esc_attr($position); ?></em>
            <?php } ?>
        </div><!-- testimonial-info -->
    </div><!-- d-flex -->
</div><!-- testimonial-item -->