<?php
$agent_address = get_post_meta( get_the_ID(), 'fave_agent_address', true );
if(!empty($agent_address)) {
?>
<address>
	<i class="houzez-icon icon-pin"></i> <span><?php echo esc_html($agent_address); ?></span>
</address>
<?php
}
?>