<?php 
global $post, $top_area, $media_tabs; 
$fave_property_images = get_post_meta(get_the_ID(), 'fave_property_images', false);
$tools_position = houzez_option('property_tools_mobile_pos', 'under_banner');
$media_tabs = houzez_get_media_tabs();
$tabs_count = count($media_tabs);
$tabs_count = $tabs_count + 2; //add 2 for mobile
?>
<div class="d-block d-md-none">
    <div class="mobile-top-wrap">
       
        <div class="mobile-property-tools block-wrap">
            <div class="houzez-media-tabs-<?php esc_attr_e($tabs_count);?> d-flex justify-content-between">
                <?php 
                if( !empty($fave_property_images) && $top_area != 'v6' && $top_area != 'v7' ) {?>
                    <?php get_template_part('property-details/partials/banner-nav'); ?>
                <?php } ?>

                <?php 
                if( $tools_position == 'under_banner' ) {
                    get_template_part('property-details/partials/tools'); 
                } ?>  
            </div>
        </div>

        <div class="mobile-property-title block-wrap">
            <div class="d-flex align-items-center mb-3">
                <?php
                get_template_part('property-details/partials/item-labels');?>
            </div>
            <div class="page-title mb-1">
                <div class="page-title-mobile"><?php the_title(); ?></div>
            </div>
            <?php get_template_part('property-details/partials/item-address');?>
            <ul class="item-price-wrap" role="list">
                <?php echo houzez_listing_price_v1(); ?>
            </ul>
            <?php if( $tools_position == 'under_title' ) { ?>
            <div class="mobile-property-tools mobile-property-tools-bottom mt-4">
                <?php get_template_part('property-details/partials/tools'); ?> 
            </div>
            <?php } ?>
        </div>
    </div>
</div>