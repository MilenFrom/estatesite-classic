<?php 
global $post, $hide_author_date; 
$agent_info = houzez_get_property_agent($post->ID);

$show_author_date = isset($hide_author_date) ? $hide_author_date : houzez_option('disable_agent', 1);

if( $show_author_date && !empty( $agent_info )) { ?>
<div class="item-author">
	<img class="img-fluid" src="<?php echo $agent_info['picture']; ?>" alt="<?php echo esc_attr($agent_info['agent_name'] ?? ''); ?>">
	<?php echo $agent_info['agent_name']; ?>
</div><!-- item-author -->
<?php } ?>