<?php
/**
 * Similar Properties
 * Date: 06/03/25
 */

$show_similer = houzez_option('houzez_similer_properties');
$similer_criteria = houzez_option('houzez_similer_properties_type', array('property_type', 'property_city'));
$similer_count = houzez_option('houzez_similer_properties_count');
$sort_by = houzez_option('similar_order', 'd_date');

if(!$show_similer) {
    return;
}

// Set up the default view
$default_view_option = houzez_option('houzez_similer_properties_view', 'grid-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = false; // Generally not needed for similar properties

// Default arguments
$args = array(
    'default_view' => $default_view_option,
    'layout' => 'no-sidebar', // Similar properties are always full width
    'grid_columns' => houzez_option('houzez_similer_properties_grid_columns', '2'), // Default to 3 columns for grid
);

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

// Get similar properties
$similar_query = houzez_get_similar_properties(null, $similer_criteria, $similer_count, $sort_by);

if ($similar_query->have_posts()) : ?>
    <div id="similar-listings-wrap" class="similar-property-wrap property-section-wrap listing-<?php echo esc_attr($item_version); ?>" data-nosnippet>
        <div class="block-title-wrap">
            <h2><?php echo houzez_option('sps_similar_listings', 'Similar Listings'); ?></h2>
        </div><!-- block-title-wrap -->
        
        <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
            <?php
            while ($similar_query->have_posts()) : $similar_query->the_post();
                get_template_part('template-parts/listing/item', $current_item_template);
            endwhile;
            wp_reset_postdata();
            ?> 
        </div><!-- listing-view -->
    </div><!-- similar-property-wrap -->
<?php
endif;
?>