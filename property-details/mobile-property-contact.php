<?php
$agent_display = houzez_get_listing_data('agent_display_option');

if ($agent_display != 'none') { 

$agent_array = houzez20_get_property_agent();
$agent_array = $agent_array['agent_info'][0] ?? '';

$agent_name = isset($agent_array['agent_name']) ? $agent_array['agent_name'] : '';
$agent_mobile_call = isset($agent_array['agent_mobile_call']) ? $agent_array['agent_mobile_call'] : '';
$agent_whatsapp_call = isset($agent_array['agent_whatsapp_call']) ? $agent_array['agent_whatsapp_call'] : '';
$agent_number_call = isset($agent_array['agent_mobile_call']) ? $agent_array['agent_mobile_call'] : '';
$agent_picture = $agent_array['picture'] ?? '';
if( empty($agent_number_call) ) {
	$agent_number_call = isset($agent_array['agent_phone_call']) ? $agent_array['agent_phone_call'] : '';
}

?>
<div class="mobile-property-contact w-100 d-block d-lg-none" role="complementary">
	<div class="d-flex justify-content-between">
		<div class="agent-details flex-grow-1">
			<div class="d-flex align-items-center gap-3">
				<div class="agent-image">
				<img class="rounded" src="<?php echo esc_url($agent_picture); ?>" width="50" height="50" alt="<?php echo esc_attr($agent_name); ?>">
				</div>
				<ul class="agent-information list-unstyled m-0" role="list">
					<li class="agent-name" role="listitem">
						<?php echo esc_attr($agent_name); ?>
					</li>
				</ul>
			</div><!-- d-flex -->
		</div><!-- agent-details -->
		<button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#mobile-property-form">
			<i class="houzez-icon icon-envelope" aria-hidden="true"></i>
		</button>
		<?php if( !empty( $agent_whatsapp_call ) && houzez_option('agent_whatsapp_num', 1) ) { ?>
		<a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $agent_whatsapp_call ); ?>&text=<?php echo houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink(); ?> " class="btn btn-secondary-outlined">
			<i class="houzez-icon icon-messaging-whatsapp" aria-hidden="true"></i>
		</a>
		<?php } ?>
		<?php if( ! empty($agent_number_call) && houzez_option('agent_mobile_num', 1) ) { ?>
		<a href="tel:<?php echo esc_attr($agent_number_call); ?>" class="btn btn-secondary-outlined">
			<i class="houzez-icon icon-phone" aria-hidden="true"></i>
		</a>
		<?php } ?>
	</div><!-- d-flex -->
</div><!-- mobile-property-contact -->

<div class="modal fade mobile-property-form" id="mobile-property-form" role="dialog" aria-labelledby="propertyFormTitle" aria-modal="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h5 class="modal-title" id="phoneNumberModalLabel"><?php esc_html_e('Contact me', 'houzez'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div><!-- modal-header -->
			<div class="modal-body">
				<?php get_template_part('property-details/agent-form'); ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>