<?php
get_header();
global $post, $houzez_local, $paged, $agent_listing_ids;

// Setup sidebar sticky class
$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if( isset($sticky_sidebar['agent_sidebar']) && $sticky_sidebar['agent_sidebar'] != 0 ) { 
    $is_sticky = 'houzez_sticky'; 
}

// Setup agent details
$agent_company_logo = get_post_meta( get_the_ID(), 'fave_agent_logo', true );
$agent_number = get_post_meta( get_the_ID(), 'fave_agent_mobile', true );
$agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
if( empty($agent_number) ) {
    $agent_number = get_post_meta( get_the_ID(), 'fave_agent_office_num', true );
    $agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
}

// Set up the default view
$default_view_option = houzez_option('agent_listings_layout', 'list-view-v1');

// Determine if we should show the view switcher based on version
$show_switch = true;
if (in_array($default_view_option, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $show_switch = false;
}

// Default arguments for agent listings
$args = array(
    'default_view' => $default_view_option,
    'layout' => 'no-sidebar', // Agent listings always full width
    'grid_columns' => houzez_option('agent_listings_grid_columns', '2'),
    'show_switch' => $show_switch,
);

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

if(isset($_GET['tab']) || $paged > 0) {
    if(isset($_GET['tab']) && $_GET['tab'] == 'reviews') {
        $active_reviews_tab = 'active';
        $active_reviews_content = 'show active';
        $active_listings_tab = '';
        $active_listings_content = '';
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('html, body').animate({
                scrollTop: $(".agent-nav-wrap").offset().top
            }, 'slow');
        });
    </script>
    <?php
}

$post_per_page = houzez_option('num_of_agent_listings', 10);

$the_query = Houzez_Query::loop_agent_properties();
$agent_total_listings = $the_query->found_posts;

if( houzez_option('agent_stats', 0) != 0 ) {
 $default_lang = function_exists('wpml_get_default_language') ? wpml_get_default_language() : null;
 $default_lang_agent_id = apply_filters('wpml_object_id', get_the_ID(), 'houzez_agent', false, $default_lang);
 $agent_listing_ids = Houzez_Query::get_agent_properties_ids_by_agent_id($default_lang_agent_id);
}

$active_reviews_tab = '';
$active_reviews_content = '';
$active_listings_content = '';
$active_listings_tab = '';
if( houzez_option( 'agent_listings', 0 ) != 1 && houzez_option( 'agent_review', 0 ) != 0 ) {
    $active_reviews_tab = 'active';
    $active_reviews_content = 'show active';

} else if ( $agent_total_listings == 0 ) {
    $active_reviews_tab = 'active';
    $active_reviews_content = 'show active';
} else {
    $active_listings_tab = 'active';
    $active_listings_content = 'show active';
}

$content_classes = 'col-lg-8 col-md-12 bt-content-wrap';
if( houzez_option( 'agent_sidebar', 0 ) == 0 ) { 
    $content_classes = 'col-lg-12 col-md-12';
}
?>
<section class="content-wrap agent-detail-page-v1">
    <div class="container">

        <div class="agent-profile-wrap">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="agent-image">
                        <?php if( !empty( $agent_company_logo ) ) {
                        $logo_url = wp_get_attachment_url( $agent_company_logo );
                        if( !empty($logo_url) ) {
                        ?>
                        <div class="agent-company-logo bottom-0 start-0">
                            <img class="img-fluid" src="<?php echo esc_url( $logo_url ); ?>" alt="">
                        </div>
                        <?php }
                        } ?>
                        <?php get_template_part('template-parts/realtors/agent/image'); ?>
                    </div><!-- agent-image -->
                </div><!-- col-lg-4 col-md-4 col-sm-12 -->

                <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="agent-profile-top-wrap mb-4 pb-3">
                        <div class="agent-profile-header">
                            <h1 class="d-flex align-items-baseline gap-2">
                                <?php the_title(); ?>
                                <?php get_template_part('template-parts/realtors/agent/verified'); ?>
                            </h1>
                            
                            <?php 
                            if( houzez_option( 'agent_review', 0 ) != 0 ) {

                                get_template_part('template-parts/realtors/rating', null, array('is_single_realtor' => true)); 
                            }?>

                        </div><!-- agent-profile-content -->
                        <?php get_template_part('template-parts/realtors/agent/position'); ?>
                    </div><!-- agent-profile-header -->

                    <div class="agent-profile-content">
                        <ul class="list-unstyled">
                            
                            <?php get_template_part('template-parts/realtors/agent/license'); ?>

                            <?php get_template_part('template-parts/realtors/agent/tax-number'); ?>

                            <?php get_template_part('template-parts/realtors/agent/service-area'); ?>

                            <?php get_template_part('template-parts/realtors/agent/specialties'); ?>

                        </ul>
                    </div><!-- agent-profile-content -->

                    <div class="agent-profile-buttons d-flex gap-2">
                        <?php if( houzez_option('agent_form_agent_page', 1) ) { ?>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#realtor-form">
                            <?php echo esc_html__('Send Email', 'houzez'); ?>  
                        </button>
                        <?php } ?>
                        
                        <?php if(!empty($agent_number)) { ?>
                        <a href="tel:<?php echo esc_attr($agent_number_call); ?>" class="btn btn-call">
                            <span class="hide-on-click"><?php echo esc_html__('Call', 'houzez'); ?></span>
                            <span class="show-on-click"><?php echo esc_attr($agent_number); ?></span>
                        </a>
                        <?php } ?>
                    </div><!-- agent-profile-buttons -->
                </div><!-- col-lg-8 col-md-8 col-sm-12 -->
            </div><!-- row -->
        </div><!-- agent-profile-wrap -->

        <?php if( !empty($agent_listing_ids) && houzez_option('agent_stats', 0) != 0 ) { ?>
        <div class="agent-stats-wrap">
            <div class="row g-4">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agent/stats-property-types'); ?> 
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agent/stats-property-status'); ?> 
                </div>

                <?php if(taxonomy_exists('property_city')) { ?>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agent/stats-property-cities'); ?> 
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="row">
            <div class="<?php echo esc_attr($content_classes); ?>">

                <?php if( houzez_option('agent_bio', 0) != 0 ) { ?>
                <div class="agent-bio-wrap">
                    <h2 class="mb-3"><?php echo esc_html__('About', 'houzez'); ?> <?php the_title(); ?></h2>
                    <div>
                    <?php
                    // Get the raw post content
                    global $post;
                    $content = $post->post_content;

                    // Process content with auto excerpt if enabled
                    $processed_content = houzez_auto_excerpt_content($content, 'agent');

                    if( $processed_content['has_more'] ) {
                        // Apply content filters to both parts
                        $content_before_more = apply_filters( 'the_content', $processed_content['content_before'] );
                        $content_after_more = apply_filters( 'the_content', $processed_content['content_after'] );

                        // Get the read more text from settings or use default
                        $more_link_text = houzez_option('read_more_text', __( 'Read More', 'houzez' ));
                        $more_link = '<p><a href="#" class="houzez-read-more-link" onclick="this.style.display=\'none\'; this.parentNode.nextElementSibling.style.display=\'block\'; return false;">' . $more_link_text . '</a></p>';

                        // Output the content with read more functionality
                        echo $content_before_more;
                        echo $more_link;
                        echo '<div class="houzez-more-content" style="display: none;">' . $content_after_more . '</div>';
                    } else {
                        // No more tag needed, just display the content normally
                        echo apply_filters( 'the_content', $processed_content['content'] );
                    }
                    ?>
                    </div>

                    <?php get_template_part('template-parts/realtors/agent/languages'); ?> 
                </div><!-- agent-bio-wrap --> 
                <?php } ?>
                
                <?php if( houzez_option( 'agent_listings', 0 ) != 0 || houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                <div id="review-scroll" class="agent-nav-wrap">
                    <ul class="nav nav-pills nav-justified gap-2" role="tablist">
                        
                        <?php if( houzez_option( 'agent_listings', 0 ) != 0 && $agent_total_listings > 0 ) { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo esc_attr($active_listings_tab); ?> py-3" href="#tab-properties" data-bs-toggle="pill" role="tab">
                                <?php esc_html_e('Listings', 'houzez'); ?> (<?php echo esc_attr($agent_total_listings); ?>)
                            </a>
                        </li>
                        <?php } ?>

                        <?php if( houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?> py-3" href="#tab-reviews" data-bs-toggle="pill" role="tab">
                                <?php esc_html_e('Reviews', 'houzez'); ?> (<?php echo houzez_reviews_count('review_agent_id'); ?>)
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div><!-- agent-nav-wrap -->
                
                <div class="tab-content" id="tab-content">
                    
                    <?php if( houzez_option( 'agent_listings', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_listings_content); ?>" id="tab-properties" role="tabpanel">
                        <div class="listing-tools-wrap">
                            <div class="d-flex align-items-center">
                                <div class="listing-tabs flex-grow-1">
                                    <?php get_template_part('template-parts/realtors/agent/listing-tabs'); ?> 
                                </div>
                                <?php get_template_part('template-parts/listing/listing-sort-by'); ?>  
                            </div><!-- d-flex -->
                        </div><!-- listing-tools-wrap -->

                        <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                            <?php
                            if ( $the_query->have_posts() ) :
                                while ( $the_query->have_posts() ) : $the_query->the_post();

                                    $agent_listing_ids[] = get_the_ID(); 
                                    get_template_part('template-parts/listing/item', $current_item_template);

                                endwhile;
                                wp_reset_postdata();
                            else:
                                get_template_part('template-parts/listing/item', 'none');
                            endif;
                            ?> 
                        </div><!-- listing-view -->

                        <?php
                        // Only show pagination if there are actual pages to paginate
                        if ($the_query->max_num_pages > 1) {
                            houzez_pagination( $the_query->max_num_pages, $agent_total_listings, $post_per_page );
                        }
                        ?>
                    </div><!-- tab-pane -->
                    <?php } ?>

                    <?php if( houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews">
                        <?php get_template_part('template-parts/reviews/main'); ?> 
                    </div><!-- tab-pane -->
                    <?php } ?>
                </div><!-- tab-content -->
                <?php } ?>

            </div><!-- bt-content-wrap -->

            <?php if( houzez_option( 'agent_sidebar', 0 ) != 0 ) { ?>
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo esc_attr($is_sticky); ?>">
                <aside class="sidebar-wrap">
                    <?php get_template_part('template-parts/realtors/agent/agent-contacts') ;?> 
                    <?php 
                    if (is_active_sidebar('agent-sidebar')) {
                        dynamic_sidebar('agent-sidebar');
                    }
                    ?>
                </aside>
            </div><!-- bt-sidebar-wrap -->
            <?php } ?>
        </div><!-- row -->
    </div><!-- container -->
</section><!-- listing-wrap -->

<?php get_footer(); ?>
