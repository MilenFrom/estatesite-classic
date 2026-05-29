<?php
get_header();
global $post, $houzez_local, $paged, $author_id, $agent_total_listings, $agent_listing_ids, $current_author_meta, $author_website, $author_email;

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if( isset($sticky_sidebar['agent_sidebar']) && $sticky_sidebar['agent_sidebar'] != 0 ) { 
    $is_sticky = 'houzez_sticky'; 
}
 
// Set up the default view
$listing_view = houzez_option('agent_listings_layout', 'list-view-v1');
$card_deck = 'card-deck';

// Default arguments for author listings
$view_args = array(
    'default_view' => $listing_view,
    'layout' => (houzez_option('agent_sidebar', 0) == 0) ? 'full-width' : 'with-sidebar', 
    'grid_columns' => houzez_option('agent_listings_grid_columns', '2'),
    'show_switch' => true,
);

// Determine if we should show the view switcher based on view version
if (in_array($listing_view, array('grid-view-v3', 'grid-view-v4', 'grid-view-v5', 'grid-view-v6', 'list-view-v7'))) {
    $view_args['show_switch'] = false;
}

// Check if the layout is list-view-v7, which needs special handling for card_deck
if ($listing_view == 'list-view-v7') {
    $card_deck = '';
}

// Get view settings
$view_settings = houzez_get_listing_view_settings($view_args['default_view']);
$current_view = $view_settings['current_view'];
$current_item_template = $view_settings['current_item_template'];
$item_version = $view_settings['item_version'];

// Get listing view class
$listing_view_class = houzez_get_listing_view_class($current_view, $item_version, $view_args['layout'], $view_args['grid_columns']);

$active_listings_tab = 'active';
$active_listings_content = 'show active';
$active_reviews_tab = '';
$active_reviews_content = '';
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

$current_author = $wp_query->get_queried_object();
$author_id = $current_author->ID;
$current_author_meta = get_user_meta( $current_author->ID );
$agent_name = $current_author->display_name;
$author_email = $current_author->user_email;
$author_website = $current_author->user_url;
$agent_bio = $current_author->description;
$agent_number = $current_author_meta['fave_author_mobile'][0] ?? '';
$agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number );

if( empty($agent_number) ) {
    $agent_number = $current_author_meta['fave_author_phone'][0] ?? '';
    $agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number );
}

$fave_author_title = $current_author_meta['fave_author_title'][0] ?? '';


$agent_listing_ids = Houzez_Query::loop_get_author_properties_ids($author_id);
$tax_query = array();

if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}
$agent_listing_args = array(
	'post_type' => 'property',
	'posts_per_page' => houzez_option('num_of_agent_listings', 10),
	'author' => $current_author->ID,
	'paged' => $paged,
	'post_status' => 'publish'
);

if ( isset( $_GET['tab'] ) && !empty($_GET['tab']) && $_GET['tab'] != "reviews") {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'slug',
        'terms' => $_GET['tab']
    );
}

$agent_listing_args['tax_query'] = $tax_query;

$agent_listing_args = houzez_prop_sort($agent_listing_args);

$agent_query = new WP_Query( $agent_listing_args );
$agent_total_listings = Houzez_Query::author_properties_count($author_id);

$content_classes = 'col-lg-8 col-md-12 bt-content-wrap';
if( houzez_option( 'agent_sidebar', 0 ) == 0 ) { 
    $content_classes = 'col-lg-12 col-md-12';
}
?>

<section class="content-wrap agent-detail-page-v1">
    <div class="container">

        <div class="agent-profile-wrap">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="agent-image">
                        <?php
						if( isset($current_author_meta['fave_author_custom_picture'][0]) && !empty( $current_author_meta['fave_author_custom_picture'][0] ) ) {
							echo '<img class="img-fluid" src="'.esc_url( $current_author_meta['fave_author_custom_picture'][0] ).'" width="300" height="300">';
						}else{
                            houzez_image_placeholder( 'medium' );
						}
						?>
                    </div><!-- agent-image -->
                </div><!-- col-lg-4 col-md-4 col-sm-12 -->

                <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="agent-profile-top-wrap mb-4 pb-3">
                        <div class="agent-profile-header">
                            <?php if( !empty( $agent_name ) ) { ?>
								<h1 class="d-flex align-items-baseline gap-2"><?php echo esc_attr( $agent_name ); ?></h1>
							<?php } ?>
                            
                            <?php 
                            if( houzez_option( 'agent_review', 0 ) != 0 ) {
                                get_template_part('template-parts/realtors/rating', null, array('is_single_realtor' => true)); 
                            }?>

                        </div><!-- agent-profile-content -->
                        
						<p class="agent-list-position"> <?php echo esc_attr( $fave_author_title ); ?>
							<?php if( isset($current_author_meta['fave_author_company'][0]) && !empty($current_author_meta['fave_author_company'][0])) { ?>
								
								<?php echo $houzez_local['at']; ?>
								<a>
									<?php echo esc_attr( $current_author_meta['fave_author_company'][0] ); ?>		
								</a>

							<?php } ?>
						</p>

                    </div><!-- agent-profile-header -->

                    <div class="agent-profile-content">
                        <ul class="list-unstyled">
                            
                            <?php 
                            if( isset($current_author_meta['fave_author_license'][0]) && !empty($current_author_meta['fave_author_license'][0]) ) { ?>
								<li>
									<strong><?php echo $houzez_local['agent_license']; ?>:</strong> 
									<?php echo esc_attr( $current_author_meta['fave_author_license'][0] ); ?>
								</li>
							<?php 
							} ?>

							<?php 
                            if( isset($current_author_meta['fave_author_tax_no'][0]) && !empty($current_author_meta['fave_author_tax_no'][0]) ) { ?>
								<li>
									<strong><?php echo $houzez_local['tax_number']; ?>:</strong> 
									<?php echo esc_attr( $current_author_meta['fave_author_tax_no'][0] ); ?>
								</li>
							<?php 
							} ?>

							<?php 
                            if( isset($current_author_meta['fave_author_service_areas'][0]) && !empty($current_author_meta['fave_author_service_areas'][0]) ) { ?>
								<li>
									<strong><?php echo $houzez_local['service_area']; ?>:</strong> 
									<?php echo esc_attr( $current_author_meta['fave_author_service_areas'][0] ); ?>
								</li>
							<?php 
							} ?>

							<?php 
                            if( isset($current_author_meta['fave_author_specialties'][0]) && !empty($current_author_meta['fave_author_specialties'][0]) ) { ?>
								<li>
									<strong><?php echo $houzez_local['specialties_label']; ?>:</strong> 
									<?php echo esc_attr( $current_author_meta['fave_author_specialties'][0] ); ?>
								</li>
							<?php 
							} ?>

                        </ul>
                    </div><!-- agent-profile-content -->

                    <div class="agent-profile-buttons d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#realtor-form">
                            <?php echo esc_html__('Send Email', 'houzez'); ?>  
                        </button>
                        
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
                    <h2 class="mb-3"><?php echo esc_html__('About', 'houzez'); ?> <?php echo esc_attr( $agent_name ); ?></h2>

                    <?php echo wp_kses_post( wpautop( wptexturize( $agent_bio ) ) ); ?>

                    <?php 
                    if( isset($current_author_meta['fave_author_language'][0]) && !empty( $current_author_meta['fave_author_language'][0] ) ) { ?>
						<p>
							<i class="houzez-icon icon-messages-bubble me-1"></i>
							<strong><?php echo $houzez_local['languages']; ?>:</strong> 
							<?php echo esc_attr( $current_author_meta['fave_author_language'][0] ); ?>
						</p>
					<?php 
					} ?>
                </div>
                <?php } ?>
                
                <?php if( houzez_option( 'agent_listings', 0 ) != 0 || houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                <div id="review-scroll" class="agent-nav-wrap">
                    <ul class="nav nav-pills nav-justified gap-2" role="tablist">
                        <?php if( houzez_option( 'agent_listings', 0 ) != 0 ) { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo esc_attr($active_listings_tab); ?> py-3" href="#tab-properties" data-bs-toggle="pill" role="tab">
                                <?php esc_html_e('Listings', 'houzez'); ?> (<?php echo esc_attr($agent_total_listings); ?>)
                            </a>
                        </li>
                        <?php } ?>

                        <?php if( houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?> py-3" href="#tab-reviews" data-bs-toggle="pill" role="tab">
                                <?php esc_html_e('Reviews', 'houzez'); ?> (<?php echo houzez_reviews_count('review_author_id'); ?>)
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                
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
                            if ( $agent_query->have_posts() ) :
                                while ( $agent_query->have_posts() ) : $agent_query->the_post();

                                    get_template_part('template-parts/listing/item', $current_item_template);

                                endwhile;
                                wp_reset_postdata();
                            else:
                                get_template_part('template-parts/listing/item', 'none');
                            endif;
                            ?> 
                        </div><!-- listing-view -->

                        <?php houzez_pagination( $agent_query->max_num_pages ); ?>
                    </div>
                    <?php } ?>

                    <?php if( houzez_option( 'agent_review', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews" role="tabpanel">
                        <?php get_template_part('template-parts/reviews/main'); ?> 
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

            </div>

            <?php if( houzez_option( 'agent_sidebar', 0 ) != 0 ) { ?>
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo esc_attr($is_sticky); ?>">
                <aside class="sidebar-wrap">
                    
                    <div class="agent-contacts-wrap">
                        <h3 class="widget-title"><?php esc_html_e('Contacts', 'houzez'); ?></h3>
                        <div class="agent-map">
                            <?php
                            if( isset($current_author_meta['fave_author_custom_picture'][0]) && !empty( $current_author_meta['fave_author_custom_picture'][0] ) ) {
                                echo '<img class="img-fluid" src="'.esc_url( $current_author_meta['fave_author_custom_picture'][0] ).'" width="300" height="300">';
                            }else{
                                houzez_image_placeholder( 'medium' );
                            }
                            
                            if( isset($current_author_meta['fave_author_address'][0]) && !empty($current_author_meta['fave_author_address'][0])) {
                                echo '<address><i class="houzez-icon icon-pin"></i> '.$current_author_meta['fave_author_address'][0].'</address>';
                            }
                            ?>
                        </div>
                        <ul class="list-unstyled">
                            <?php get_template_part('template-parts/realtors/agent/office-phone'); ?>
                            <?php get_template_part('template-parts/realtors/agent/mobile'); ?>
                            <?php get_template_part('template-parts/realtors/agent/fax'); ?>
                            <?php get_template_part('template-parts/realtors/agent/email'); ?>
                            <?php get_template_part('template-parts/realtors/agent/website'); ?>
                        </ul>
                        <p><?php printf( esc_html__( 'Find %s on', 'houzez' ) , $agent_name ); ?>:</p>
                        <div class="agent-social-media">
                            <?php get_template_part('template-parts/realtors/agent/social'); ?>
                        </div><!-- agent-social-media -->
                    </div><!-- agent-bio-wrap -->

                    <?php 
                    if (is_active_sidebar('agent-sidebar')) {
                        dynamic_sidebar('agent-sidebar');
                    }
                    ?>
                </aside>
            </div>
            <?php } ?>

        </div><!-- row -->

        
    </div><!-- container -->
</section><!-- listing-wrap -->
</main><!-- .main-wrap -->  
<?php get_template_part('template-parts/realtors/contact', 'form'); ?>
<?php get_footer(); ?>