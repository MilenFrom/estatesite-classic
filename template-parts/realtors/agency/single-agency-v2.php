<?php
get_header();

global $post, $houzez_local, $properties_ids;

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if( $sticky_sidebar['agency_sidebar'] != 0 ) { 
    $is_sticky = 'houzez_sticky'; 
}

$properties_ids_array = array();
$agency_phone = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
$agency_phone_call = str_replace(array('(',')',' ','-'),'', $agency_phone);

// Set up the default view
$default_view = houzez_option('agency_listings_layout', 'list-view-v1');

// Default arguments for agency listings
$args = array(
    'default_view' => $default_view,
    'layout' => 'no-sidebar', // Agency listings always full width
    'grid_columns' => houzez_option('agency_listings_grid_columns', '2'),
    'show_switch' => true,
);

// Determine if we should show the view switcher based on version
if (in_array($default_view, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $args['show_switch'] = false;
}

// Get view settings
$view_settings = houzez_get_listing_view_settings($args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $args['layout'], $args['grid_columns']);

$active_reviews_tab = $active_agents_tab = '';
$active_reviews_content = $active_agents_content = '';
if( houzez_option( 'agency_listings', 0 ) != 1 && houzez_option( 'agency_agents', 0 ) != 1 && houzez_option( 'agency_review', 0 ) != 0 ) {
    $active_reviews_tab = 'active';
    $active_reviews_content = 'show active';

} elseif( houzez_option( 'agency_listings', 0 ) == 0 && houzez_option( 'agency_agents', 0 ) == 1 ) {
    $active_agents_tab = 'active';
    $active_agents_content = 'show active';

} else {
    $active_listings_tab = 'active';
    $active_listings_content = 'show active';
}

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


global $paged;
if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$agency_id = get_the_ID();

$tax_query = array();
$meta_query = array();

$post_per_page = houzez_option('num_of_agency_listings', 9);

if ( isset( $_GET['tab'] ) && !empty($_GET['tab']) && $_GET['tab'] != "reviews") {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'slug',
        'terms' => $_GET['tab']
    );
}

$args = array(
    'post_type' => 'property',
    'posts_per_page' => $post_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
);

$args = apply_filters( 'houzez_sold_status_filter', $args );

$agents_array = array();
$agency_properties_ids = array();
$agents_properties_ids = array();

$default_lang = function_exists('wpml_get_default_language') ? wpml_get_default_language() : null;
$default_lang_agency_id = apply_filters('wpml_object_id', get_the_ID(), 'houzez_agency', false, $default_lang);

$agency_agents_ids = Houzez_Query::loop_agency_agents_ids($default_lang_agency_id);

$agency_properties_ids = Houzez_Query::get_property_ids_by_agency($default_lang_agency_id);

if (!empty($agency_agents_ids)) {
    $agents_properties_ids = Houzez_Query::get_property_ids_by_agents($agency_agents_ids);
}


$properties_ids = array_merge( $agency_properties_ids, $agents_properties_ids );
$properties_ids = array_unique( $properties_ids );

if (!empty($properties_ids)) {
    $args['post__in'] = $properties_ids;
} else {
    $args['post__in'] = array(-1); // To return no results if no properties are found.
}


$tax_count = count($tax_query);
if($tax_count > 0 ) {
    $args['tax_query'] = $tax_query;
}


$args = houzez_prop_sort($args);

$agency_qry = new WP_Query( $args );
$agency_total_listing = $agency_qry->found_posts;


$agents_query = Houzez_Query::loop_agency_agents($default_lang_agency_id);
?>
<section class="content-wrap agent-detail-page-v2">
    <div class="agent-profile-wrap m-0">
        <div class="container">
            <div class="agent-profile-top-wrap d-flex align-items-start flex-column flex-sm-row gap-4">
                <div class="agency-image">
                    <?php get_template_part('template-parts/realtors/agency/image'); ?>
                </div><!-- agent-image -->
                <div class="agent-profile-header">
                    <h1 class="d-flex align-items-center gap-2 my-2">
                        <?php the_title(); ?> 
                        <?php get_template_part('template-parts/realtors/agency/verified'); ?>
                    </h1>
                    <?php 
                    if( houzez_option( 'agency_review', 0 ) != 0 ) {
                        get_template_part('template-parts/realtors/rating', null, array('is_single_realtor' => true)); 
                    }?> 

                    <div class="agent-profile-address">
                        <?php get_template_part('template-parts/realtors/agency/address'); ?> 
                    </div><!-- agent-profile-address -->
                    <div class="agent-profile-cta mt-3">
                        <ul class="list-inline m-0">
                            <?php if( houzez_option('agency_form_agency_page', 1) ) { ?>
                            <li class="list-inline-item"><a href="#" data-bs-toggle="modal" data-bs-target="#realtor-form"><i class="houzez-icon icon-messages-bubble me-1"></i> <?php echo houzez_option('agency_lb_ask_question', esc_html__('Ask a question', 'houzez')); ?></a></li>
                            <?php } ?>
                            <?php if(!empty($agency_phone)) { ?>
                            <li class="list-inline-item">
                                <a href="tel:<?php echo esc_attr($agency_phone_call); ?>"><i class="houzez-icon icon-phone me-1"></i> <?php echo esc_attr($agency_phone); ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div><!-- agent-profile-header -->
            </div><!-- agent-profile-top-wrap -->
        </div><!-- container -->
    </div><!-- agent-profile-wrap -->

    <div class="agent-nav-wrap">
        <?php if( houzez_option( 'agency_listings', 0 ) != 0 || houzez_option( 'agency_review', 0 ) != 0 || houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
        <div class="container">
            <ul class="nav">
                <?php if( houzez_option( 'agency_listings', 0 ) != 0 ) { ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo esc_attr($active_listings_tab); ?>" href="#tab-properties" data-bs-toggle="pill" role="tab"><?php esc_html_e('Listings', 'houzez'); ?> (<?php echo esc_attr($agency_total_listing); ?>)</a>
                </li>
                <?php } ?>

                <?php if( houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo esc_attr($active_agents_tab); ?>" href="#tab-agents" data-bs-toggle="pill" role="tab"><?php esc_html_e('Agents', 'houzez'); ?> (<?php echo esc_attr($agents_query->found_posts); ?>)</a>
                </li>
                <?php } ?>

                <?php if( houzez_option( 'agency_review', 0 ) != 0 ) { ?>
                <li class="nav-item">
                    <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?>" href="#tab-reviews" data-bs-toggle="pill" role="tab"><?php esc_html_e('Reviews', 'houzez'); ?> (<?php echo houzez_reviews_count('review_agency_id'); ?>)</a>
                </li>
                <?php } ?>
            </ul>
        </div><!-- container -->
        <?php } ?>
    </div><!-- agent-nav-wrap -->

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 bt-content-wrap">
                
                <div class="tab-content" id="tab-content">
                    
                    <?php if( houzez_option( 'agency_listings', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_listings_content); ?>" id="tab-properties" role="tabpanel">
                        <div class="listing-tools-wrap">
                            <div class="d-flex align-items-center">
                                <div class="listing-tabs flex-grow-1">
                                    <?php get_template_part('template-parts/realtors/agency/listing-tabs'); ?> 
                                </div>
                                <?php get_template_part('template-parts/realtors/agency/listing-sort-by'); ?>   
                            </div><!-- d-flex -->
                        </div><!-- listing-tools-wrap -->

                        <div class="<?php echo esc_attr($listing_view_class); ?>" role="list" data-view="<?php echo esc_attr($current_view); ?>">
                            <?php
                            if ( $agency_qry->have_posts() ) :
                                while ( $agency_qry->have_posts() ) : $agency_qry->the_post();

                                    get_template_part('template-parts/listing/item', $current_item_template);

                                endwhile;
                                wp_reset_postdata();
                            else:
                                get_template_part('template-parts/listing/item', 'none');
                            endif;
                            ?> 
                        </div><!-- listing-view -->
                        <?php houzez_pagination( $agency_qry->max_num_pages, $agency_total_listing, $post_per_page ); ?>
                    </div><!-- tab-pane -->
                    <?php } ?>

                    <?php if( houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_agents_content); ?>" id="tab-agents">

                        <div class="agents-list-view" role="list">
                            <?php
                            if ( $agents_query->have_posts() ) :
                                while ( $agents_query->have_posts() ) : $agents_query->the_post();

                                    get_template_part('template-parts/realtors/agent/list');

                                endwhile;
                                wp_reset_postdata();
                            else:
                                get_template_part('template-parts/realtors/agent/none');
                            endif;
                            ?> 
                        </div><!-- listing-view -->
                    </div><!-- tab-pane -->
                    <?php } ?>

                    <?php if( houzez_option( 'agency_review', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews">
                        <?php get_template_part('template-parts/reviews/main'); ?> 
                    </div><!-- tab-pane -->
                    <?php } ?>

                </div><!-- tab-content -->
            </div><!-- bt-content-wrap -->
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap">
                <aside class="sidebar-wrap">
                    <div class="agent-bio-wrap">
                        <h2><?php echo esc_html__('About', 'houzez'); ?> <?php the_title(); ?></h2>
                        <div>
                        <?php
                        // Get the raw post content
                        global $post;
                        $content = $post->post_content;

                        // Process content with auto excerpt if enabled
                        $processed_content = houzez_auto_excerpt_content($content, 'agency');

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

                        <?php get_template_part('template-parts/realtors/agency/languages'); ?>
                    </div><!-- agent-bio-wrap --> 
                    <div class="agent-profile-content">
                        <ul class="list-unstyled m-0">
                            <?php get_template_part('template-parts/realtors/agency/license'); ?>
                            <?php get_template_part('template-parts/realtors/agency/tax-number'); ?>
                            <?php get_template_part('template-parts/realtors/agency/service-area'); ?>
                            <?php get_template_part('template-parts/realtors/agency/specialties'); ?>
                        </ul>
                    </div><!-- agent-profile-content -->
                    <?php get_template_part('template-parts/realtors/agency/agency-contacts'); ?> 
                    <?php do_action('houzez_agency_sidebar') ?> 
                    
                </aside>
            </div><!-- bt-sidebar-wrap -->
        </div><!-- row -->
    </div><!-- container -->

</section><!-- content-wrap -->

<?php get_footer(); ?>