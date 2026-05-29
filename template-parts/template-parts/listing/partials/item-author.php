<?php 
global $post, $hide_author_date; 
$agent_info = houzez_get_property_agent($post->ID);

$show_author_date = isset($hide_author_date) ? $hide_author_date : houzez_option('disable_agent', 1);

if( $show_author_date && !empty( $agent_info )) { ?>
<div class="item-author d-flex align-items-center gap-1">
	<div class="item-author-image me-2" role="img">
		<img class="rounded-circle" src="<?php echo $agent_info['picture']; ?>" width="32" height="32" alt="<?php echo esc_attr($agent_info['agent_name'] ?? ''); ?>">
	</div>
	<a href="<?php echo $agent_info['link']; ?>" role="link"><?php echo $agent_info['agent_name']; ?></a>
</div><!-- item-author -->
<?php } ?>