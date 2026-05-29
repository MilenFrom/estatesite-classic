<?php
$title_version = isset($args['item_title']) ? $args['item_title'] : '';
$additional_classes = '';

if($title_version == 'v2') {
    $additional_classes = 'col-12 text-truncate';
}
?>
<h2 class="item-title mb-2 <?php echo esc_attr($additional_classes); ?>">
	<a <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>" role="link"><?php the_title(); ?></a>
</h2><!-- item-title -->