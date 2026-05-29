<?php 
global $post, $random_token, $ele_thumbnail_size, $image_size, $listing_agent_info, $buttonsComposer, $hide_date, $hide_author; 
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

$show_date = isset($hide_date) ? $hide_date : houzez_option('disable_date', 1);
$show_author = isset($hide_author) ? $hide_author : houzez_option('disable_agent', 1);

// Get the dynamically assigned image size for this layout
$image_size = houzez_get_image_size_for('listing_list_v7');
$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>

<div class="item-listing-wrap item-wrap-v8 hz-item-gallery-js hz-map-trigger" data-hz-id="<?php echo esc_attr($post->ID); ?>" <?php houzez_property_gallery($image_size); ?>>
	<div class="item-wrap">
		<div class="d-flex align-items-center flex-column flex-md-row">
			<div class="item-header">
				<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
				<?php get_template_part('template-parts/listing/partials/item-labels'); ?>
				<?php get_template_part('template-parts/listing/partials/item', 'tools'); ?>
				<?php get_template_part('template-parts/listing/partials/item-image'); ?>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body">
				<ul class="item-amenities mb-2">
					<?php get_template_part('template-parts/listing/partials/type'); ?>
				</ul>
				
				<ul class="item-price-wrap d-flex gap-2 align-items-end mb-4" role="list">
					<?php echo houzez_listing_price_v1(); ?>
				</ul>
				<div class="d-flex">
					<?php get_template_part('template-parts/listing/partials/item-title'); ?>
				</div>
				<?php get_template_part('template-parts/listing/partials/item-address'); ?>
				<?php get_template_part('template-parts/listing/partials/item-features-v7'); ?>
			</div>
		</div>
		<div class="item-footer d-flex justify-content-sm-end justify-content-md-between align-items-center">
			<div class="item-footer-left-wrap d-flex gap-3 align-items-center d-none d-md-flex">
				<?php 
				if($show_author) {
					get_template_part('template-parts/listing/partials/item-author'); 
				}
				if($show_date) {
					get_template_part('template-parts/listing/partials/item-date'); 
				}
				?>
			</div>
			<div class="item-footer-right-wrap d-flex">
				<?php get_template_part('template-parts/listing/partials/item-btn-v7'); ?>
			</div>
		</div>
	</div>
	<?php get_template_part('template-parts/listing/partials/modal-phone-number'); ?>
	<?php get_template_part('template-parts/listing/partials/modal-agent-contact-form'); ?>
</div>