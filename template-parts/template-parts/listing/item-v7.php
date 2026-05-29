<?php 
global $post, $random_token, $ele_thumbnail_size, $image_size, $listing_agent_info, $buttonsComposer; 
$listing_agent_info = houzez20_get_property_agent();

$random_token = houzez_random_token();

$defaultButtons = array(
    'enabled' => array(
        'call' => 'Call',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
    )
);

$listingButtonsComposer = houzez_option('listing_buttons_composer', $defaultButtons);

// Ensure that 'enabled' index exists
$buttonsComposer = isset($listingButtonsComposer['enabled']) ? $listingButtonsComposer['enabled'] : [];

// Remove the 'placebo' element
unset($buttonsComposer['placebo']);

// Agent contact information
$agent_mobile = $listing_agent_info['agent_mobile'] ?? '';
$agent_whatsapp = $listing_agent_info['agent_whatsapp'] ?? '';
$agent_telegram = $listing_agent_info['agent_telegram'] ?? '';
$agent_lineapp = $listing_agent_info['agent_lineapp'] ?? '';
$agent_email = $listing_agent_info['agent_email'] ?? '';

$totalButtonsCount = 0;

// Check each button and increment the count if the corresponding agent info is available
foreach ($buttonsComposer as $key => $value) {
    if (($key == 'call' && $agent_mobile != '') ||
        ($key == 'email' && $agent_email != '') ||
        ($key == 'whatsapp' && $agent_whatsapp != '') ||
        ($key == 'telegram' && $agent_telegram != '') ||
        ($key == 'lineapp' && $agent_lineapp != '')
    ) {
        $totalButtonsCount++;
    }
}

// Limit the total buttons to a maximum of 4
$totalButtonsCount = min($totalButtonsCount, 4);
$totalButtonsClass = 'items-btns-count-' . $totalButtonsCount;

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_grid_v7');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap item-wrap-v9 hz-item-gallery-js hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap item-wrap-no-frame h-100">
		<div class="d-flex flex-column align-items-center h-100">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
				<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
				<?php get_template_part('template-parts/listing/partials/item-tools'); ?>
				<?php get_template_part('template-parts/listing/partials/item-image'); ?>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body w-100 flex-fill">
				<ul class="item-amenities item-amenities-with-icons mb-2">
					<?php get_template_part('template-parts/listing/partials/type'); ?>
				</ul>
				<ul class="item-price-wrap d-flex align-items-end gap-3" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				<?php get_template_part('template-parts/listing/partials/item-address'); ?>
				<?php get_template_part('template-parts/listing/partials/item-features-v7'); ?>
			</div>
			<div class="item-footer <?php echo esc_attr($totalButtonsClass); ?>">
				<div class="item-buttons-wrap d-flex justify-content-between gap-2">
					<?php get_template_part('template-parts/listing/partials/item-btns-cew'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php get_template_part('template-parts/listing/partials/modal-phone-number'); ?>
	<?php get_template_part('template-parts/listing/partials/modal-agent-contact-form'); ?>
</div>
