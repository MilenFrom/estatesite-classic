<?php 
global $hide_button;
// If $hide_button is true, show the button
// If $hide_button doesn't exist, use houzez_option('disable_detail_btn', 1)
$show_button = isset($hide_button) ? $hide_button : houzez_option('disable_detail_btn', 1);
if($show_button) { ?>
<a class="btn btn-primary btn-item" <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>">
	<?php echo houzez_option('glc_detail_btn', 'Details'); ?>
</a><!-- btn-item -->
<?php } ?>