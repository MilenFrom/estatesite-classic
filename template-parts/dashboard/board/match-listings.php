<?php 
global $matched_query, $enquiry, $properties_query, $lead; 
$properties_query = $matched_query;
?>
<div class="inquiry-data">
    <?php if(empty($matched_query) || !$matched_query->have_posts()): ?>
    <div class="alert alert-info mb-4" role="alert">
        <i class="houzez-icon icon-info-circle me-2"></i>
        <?php esc_html_e('No data found', 'houzez'); ?>
    </div>
    <?php else: ?>
    <div class="inquiry-listing d-flex align-items-center justify-content-between mb-4">
        <p class="ps-3"><strong><?php echo esc_html($matched_query->found_posts); ?> <?php esc_html_e('Listings', 'houzez'); ?></strong> <?php esc_html_e('found on file', 'houzez'); ?></p>
        <a id="inquiry-send-email" href="#" class="btn btn-primary"><i class="houzez-icon icon-email-action-reply me-2"></i> <?php esc_html_e('Send Via Email', 'houzez'); ?></a>
        <input type="hidden" id="lead_email" value="<?php echo esc_attr($lead->email); ?>">
    </div>

    <div class="houzez-data-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                            <label class="control control--checkbox">
                                <input type="checkbox" id="listings_select_all" name="listings_multicheck">
                                <span class="control__indicator"></span>
                            </label>
                        </th>
                        <th data-label="<?php esc_html_e('ID', 'houzez'); ?>"><?php esc_html_e('ID', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Type', 'houzez'); ?>"><?php esc_html_e('Type', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Price', 'houzez'); ?>"><?php esc_html_e('Price', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Bedrooms', 'houzez'); ?>"><?php esc_html_e('Bedrooms', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Bathrooms', 'houzez'); ?>"><?php esc_html_e('Bathrooms', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Area Size', 'houzez'); ?>"><?php esc_html_e('Area Size', 'houzez'); ?></th>
                        <th data-label="<?php esc_html_e('Location', 'houzez'); ?>"><?php esc_html_e('Location', 'houzez'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if($matched_query->have_posts()):
                    while ($matched_query->have_posts()): $matched_query->the_post(); 
                        $prop_id = houzez_get_listing_data('property_id');
                ?>
                    <tr>
                        <td data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="listing_multi_id" name="listing_multi_id[]" value="<?php echo intval(get_the_ID()); ?>">
                                <span class="control__indicator"></span>
                            </label>
                        </td>
                        <td data-label="<?php esc_html_e('ID', 'houzez'); ?>">
                            <?php echo houzez_propperty_id_prefix($prop_id); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Type', 'houzez'); ?>">
                            <?php echo houzez_taxonomy_simple('property_type'); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Price', 'houzez'); ?>">
                            <?php echo houzez_property_price_crm(); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Bedrooms', 'houzez'); ?>">
                            <?php echo houzez_get_listing_data('property_bedrooms'); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Bathrooms', 'houzez'); ?>">
                            <?php echo houzez_get_listing_data('property_bathrooms'); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Area Size', 'houzez'); ?>">
                            <?php echo houzez_get_listing_data('property_size'); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Location', 'houzez'); ?>">
                            <?php echo houzez_taxonomy_simple('property_city'); ?>
                        </td>
                        <td data-label="">
                            <a target="_blank" href="<?php echo get_permalink(get_the_ID()); ?>"><?php esc_html_e('View', 'houzez'); ?></a>
                        </td>
                    </tr>
                <?php
                    endwhile;
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php get_template_part('template-parts/dashboard/property/pagination'); ?>
</div>