<?php 
global $hide_button, $hide_author_date;

$button_class = '';
// If $hide_author_date is true, show the author and date
// If $hide_author_date doesn't exist, use theme options
$show_author_date = isset($hide_author_date) ? $hide_author_date : (houzez_option('disable_date', 1) || houzez_option('disable_agent', 1));
if( !$show_author_date) {
	$button_class = 'item-no-footer';
}

// If $hide_button is true, show the button
// If $hide_button doesn't exist, use houzez_option('disable_detail_btn', 1)
$show_button = isset($hide_button) ? $hide_button : houzez_option('disable_detail_btn', 1);

if ($show_button) { ?>
<a class="btn btn-primary btn-item d-md-none d-xl-flex <?php echo esc_attr($button_class); ?>" <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>">
	<?php echo houzez_option('glc_detail_btn', 'Details'); ?>
</a><!-- btn-item -->
<?php } ?>