<?php
$prevPost = get_previous_post(true);
$nextPost = get_next_post(true);

if( $prevPost || $nextPost ) {
?>
<div class="next-prev-block next-prev-blog blog-section clearfix">
    
    <?php if ($prevPost) { ?>
    <div class="prev-box float-start text-start">
        <div class="next-prev-block-content">
            <p><?php esc_html_e('Prev Post', 'houzez'); ?></p>
            <a href="<?php echo get_permalink($prevPost->ID); ?>"><strong><?php echo get_the_title($prevPost->ID); ?></strong></a>
        </div>  
    </div>
    <?php } ?>

    <?php if ($nextPost) { ?>
    <div class="next-box float-end text-end">
        <div class="next-prev-block-content">
            <p><?php esc_html_e('Next Post', 'houzez'); ?></p>
            <a href="<?php echo get_permalink($nextPost->ID); ?>"><strong><?php echo get_the_title($nextPost->ID); ?></strong></a>
        </div>
    </div>
    <?php } ?>
</div>
<?php } ?>