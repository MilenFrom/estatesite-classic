<?php
// Check if any social links exist
$agency_id = get_the_ID();
$has_social_links = false;

// Check all social media fields
$social_fields = array(
	'fave_agency_facebook',
	'fave_agency_instagram', 
	'fave_agency_twitter',
	'fave_agency_linkedin',
	'fave_agency_googleplus',
	'fave_agency_youtube',
	'fave_agency_tiktok',
	'fave_agency_pinterest',
	'fave_agency_vimeo',
	'fave_agency_telegram',
	'fave_agency_realtor_com',
	'fave_agency_zillow',
	'fave_agency_line_id',
	'fave_agency_whatsapp'
);

foreach ($social_fields as $field) {
	if (!empty(get_post_meta($agency_id, $field, true))) {
		$has_social_links = true;
		break;
	}
}

// Only display if social links exist
if ($has_social_links) : ?>
	<p><?php printf( esc_html__( 'Find %s on', 'houzez' ) , get_the_title() ); ?>:</p>

	<div class="agent-social-media d-flex gap-2 flex-wrap">
		<?php get_template_part('template-parts/realtors/agency/social'); ?>
	</div>
<?php endif; ?>