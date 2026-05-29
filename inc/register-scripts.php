<?php
if( !function_exists('houzez_enqueue_styles') ) {
    function houzez_enqueue_styles() {
  
        $minify_css = houzez_option('minify_css');
        $css_minify_prefix = '';
        if ($minify_css != 0) {
            $css_minify_prefix = '.min';
        }

        if( houzez_option('css_all_in_one', 1) ) {
            
            if ( is_rtl() ) { 
                wp_enqueue_style('houzez-all-css', HOUZEZ_CSS_DIR_URI . 'all-rtl-css.css', array(), HOUZEZ_THEME_VERSION);
            } else {
                wp_enqueue_style('houzez-all-css', HOUZEZ_CSS_DIR_URI . 'all-css.css', array(), HOUZEZ_THEME_VERSION);
            }
            
            wp_enqueue_style('fontawesome', HOUZEZ_CSS_DIR_URI . 'font-awesome/css/all.min.css', array(), '6.7.0', 'all');

            if( houzez_is_dashboard() ) {
                // Enqueue dashboard specific styles
                if ( is_rtl() ) {
                    wp_enqueue_style('houzez-dashboard-style', get_template_directory_uri() . '/css/dashboard/style-rtl.css', array(), HOUZEZ_THEME_VERSION);
                    wp_enqueue_style('houzez-dashboard-responsive', get_template_directory_uri() . '/css/dashboard/responsive-rtl.css', array(), HOUZEZ_THEME_VERSION);
                } else {
                    wp_enqueue_style('houzez-dashboard-style', get_template_directory_uri() . '/css/dashboard/style-ltr.css', array(), HOUZEZ_THEME_VERSION);
                    wp_enqueue_style('houzez-dashboard-responsive', get_template_directory_uri() . '/css/dashboard/responsive.css', array(), HOUZEZ_THEME_VERSION);
                }   
            }

        } else {
            
            if ( is_rtl() ) {
                wp_enqueue_style('bootstrap', HOUZEZ_CSS_DIR_URI . 'bootstrap.rtl.min.css', array(), '5.3.3');
            } else {
                wp_enqueue_style('bootstrap', HOUZEZ_CSS_DIR_URI . 'bootstrap.min.css', array(), '5.3.3');
            }
            wp_enqueue_style('bootstrap-select', HOUZEZ_CSS_DIR_URI . 'bootstrap-select.min.css', array(), '1.14.0');
            wp_enqueue_style('houzez-icons', HOUZEZ_CSS_DIR_URI . 'icons'.$css_minify_prefix.'.css', array(), HOUZEZ_THEME_VERSION);

            wp_enqueue_style('slick', HOUZEZ_CSS_DIR_URI . 'slick.min.css', array(), '1.8.1');
            wp_enqueue_style('slick-theme', HOUZEZ_CSS_DIR_URI . 'slick-theme.min.css', array(), '1.8.1');

            if ( is_singular('property') || is_singular('fts_builder') ) {
                wp_enqueue_style('lightslider', HOUZEZ_CSS_DIR_URI . 'lightslider.css', array(), '1.1.3');
                wp_enqueue_style('fancybox', HOUZEZ_JS_DIR_URI . 'vendors/fancybox/fancybox.css', array(), '5.0.36');
            }

            wp_register_style('jquery-ui', HOUZEZ_CSS_DIR_URI . 'jquery-ui.min.css', array(), '1.12.1');

            wp_enqueue_style('bootstrap-datepicker', HOUZEZ_CSS_DIR_URI . 'bootstrap-datepicker.min.css', array(), '1.9.0');

            if ( is_rtl() ) {
                wp_enqueue_style('houzez-rtl', get_template_directory_uri() . '/css/rtl' . $css_minify_prefix . '.css', array(), HOUZEZ_THEME_VERSION, 'all');
            } else {
                wp_enqueue_style('houzez-main', HOUZEZ_CSS_DIR_URI . 'main'.$css_minify_prefix.'.css', array(), HOUZEZ_THEME_VERSION);
            }

            if( houzez_is_dashboard() ) {
                // Enqueue dashboard specific styles
                if ( is_rtl() ) {
                    wp_enqueue_style('houzez-dashboard-style', get_template_directory_uri() . '/css/dashboard/style-rtl.css', array(), HOUZEZ_THEME_VERSION);
                    wp_enqueue_style('houzez-dashboard-responsive', get_template_directory_uri() . '/css/dashboard/responsive-rtl.css', array(), HOUZEZ_THEME_VERSION);
                } else {
                    wp_enqueue_style('houzez-dashboard-style', get_template_directory_uri() . '/css/dashboard/style-ltr.css', array(), HOUZEZ_THEME_VERSION);
                    wp_enqueue_style('houzez-dashboard-responsive', get_template_directory_uri() . '/css/dashboard/responsive.css', array(), HOUZEZ_THEME_VERSION);
                }
            }

            wp_enqueue_style('houzez-styling-options', HOUZEZ_CSS_DIR_URI . 'styling-options'.$css_minify_prefix.'.css', array(), HOUZEZ_THEME_VERSION);
        }

        wp_enqueue_style('houzez-style', get_stylesheet_uri(), array(), HOUZEZ_THEME_VERSION, 'all');
        
    }
}




if( ! function_exists('houzez_admin_scripts') ) {
    function houzez_admin_scripts(){
        global $pagenow, $typenow;

        wp_enqueue_style( 'houzez-admin-styles', HOUZEZ_CSS_DIR_URI. 'admin/admin.min.css', array(), HOUZEZ_THEME_VERSION, 'all' );

        wp_enqueue_script('houzez-admin-ajax', HOUZEZ_JS_DIR_URI .'admin/houzez-admin-ajax.min.js', array('jquery'));
        wp_localize_script('houzez-admin-ajax', 'houzez_admin_vars',
            array(
                'nonce'        => wp_create_nonce( 'houzez-admin-nonce' ),
                'ajaxurl'      => admin_url( 'admin-ajax.php' ),
                'paid_status'  => esc_html__( 'Paid','houzez' ),
                'activate_now' => esc_html__( 'Activate Now', 'houzez' ),
                'activating'   => esc_html__( 'Activating...', 'houzez' ),
                'activated'    => esc_html__( 'Activated!', 'houzez' ),
                'install_now'  => esc_html__( 'Install Now', 'houzez' ),
                'installing'   => esc_html__( 'Installing...', 'houzez' ),
                'installed'    => esc_html__( 'Installed!', 'houzez' ),
                'active'       => esc_html__( 'Active', 'houzez' ),
                'failed'       => esc_html__( 'Failed!', 'houzez' ),
                'update_now'   => esc_html__( 'Update Now', 'houzez' ),
                'updating'     => esc_html__( 'Updating...', 'houzez' ),
                'updated'      => esc_html__( 'Updated!', 'houzez' ),
            )
        );


        if ( isset( $_GET['taxonomy'] ) && ( $_GET['taxonomy'] == 'property_status' || $_GET['taxonomy'] == 'property_type' || $_GET['taxonomy'] == 'property_label' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'houzez_taxonomies', HOUZEZ_JS_DIR_URI.'admin/metaboxes-taxonomies.js', array( 'jquery', 'wp-color-picker' ), 'houzez' );
        }

    }
}

/**
 * 1) Register all your JS scripts (no output yet)
 */
if( ! function_exists('houzez_register_scripts') ) {
    function houzez_register_scripts() {
        $ver    = HOUZEZ_THEME_VERSION;
        $js_uri = HOUZEZ_JS_DIR_URI;

        // — Core bundles & vendors
        wp_register_script( 'houzez-all-in-one',    "{$js_uri}vendors/all-scripts.js",         [ 'jquery' ], $ver, true );
        wp_register_script( 'bootstrap',            "{$js_uri}vendors/bootstrap.bundle.min.js",[ 'jquery' ], '5.3.3', true );
        wp_register_script( 'bootstrap-select',     "{$js_uri}vendors/bootstrap-select.min.js",[ 'jquery' ], '1.14.0',true );
        wp_register_script( 'theia-sticky-sidebar',"{$js_uri}vendors/theia-sticky-sidebar.min.js",[ 'jquery' ], $ver, true );
        wp_register_script( 'slick',                "{$js_uri}vendors/slick.min.js",            [ 'jquery' ], '1.8.1',true );
        wp_register_script( 'chart',                "{$js_uri}vendors/Chart.min.js",            [ 'jquery' ], '2.8.0',true );
        wp_register_script( 'lightslider',          "{$js_uri}vendors/lightslider.min.js",      [ 'jquery' ], '1.1.3',true );
        wp_register_script( 'fancybox',             "{$js_uri}vendors/fancybox/fancybox.umd.js",[ 'jquery' ], '5.0.36',true );
        wp_register_script( 'houzez-instant-page',  "{$js_uri}houzez-instant-page.js",         [], '3.0.0',true );
        wp_register_script( 'bootstrap-datepicker', "{$js_uri}vendors/bootstrap-datepicker.min.js",[ 'jquery' ], '1.9.0',true );
        wp_register_script( 'houzez-invoice', "{$js_uri}houzez-invoice.js",[ 'jquery' ], $ver, true );
        wp_register_script( 'validate', "{$js_uri}vendors/jquery.validate.min.js", [ 'jquery' ], '1.19.5', true); 
        wp_register_script( 'bootbox-min', "{$js_uri}vendors/bootbox.min.js", [ 'jquery' ], '4.4.0', true);
        wp_register_script( 'houzez-property',  "{$js_uri}houzez_property.js", ['jquery', 'plupload', 'jquery-ui-sortable'], $ver, true);
        wp_register_script( 'houzez-user-profile',  "{$js_uri}houzez_user_profile.js", ['jquery', 'plupload'], $ver, true);

        // Datepicker locales: register your chosen language (if any)
        $dp_lang = houzez_option('houzez_date_language','');
        if ( $dp_lang && $dp_lang !== 'xx' ) {
            wp_register_script(
                "bootstrap-datepicker.{$dp_lang}",
                "{$js_uri}vendors/locales/bootstrap-datepicker.{$dp_lang}.min.js",
                [ 'jquery','bootstrap-datepicker' ],
                '1.0',
                true
            );
        }
        if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'en' ) {
            wp_register_script(
                "bootstrap-datepicker." . ICL_LANGUAGE_CODE,
                "{$js_uri}vendors/locales/bootstrap-datepicker." . ICL_LANGUAGE_CODE . ".min.js",
                [ 'jquery','bootstrap-datepicker' ],
                '1.0',
                true
            );
        }

        // Your main "custom.js"
        wp_register_script(
            'houzez-custom',
            "{$js_uri}custom" . houzez_minify_js() . ".js",
            [ 'jquery' ],
            $ver,
            true
        );

        $wp_locale       = get_locale();                            // "en_US"
        $locale_short    = substr( $wp_locale, 0, 2 );              // "en"
        $locale_bcp47    = str_replace( '_', '-', $wp_locale );     // "en-US"

        /**
         * Google Maps API (registered, but not yet enqueued)
         */
        $key    = esc_html( houzez_option('googlemap_api_key','') );
        $libraries = 'places,marker';
        
        // Add inline script to define callback function before API loads
        wp_register_script('houzez-maps-callback', false, array(), false, false);
        wp_enqueue_script('houzez-maps-callback');
        wp_add_inline_script('houzez-maps-callback', '(function(){var orig=Element.prototype.attachShadow;Element.prototype.attachShadow=function(o){if(this.tagName&&this.tagName.toLowerCase()==="gmp-place-autocomplete"){o=Object.assign({},o,{mode:"open"});}return orig.call(this,o);};})();window.houzezMapCallback = function() {
            if (window.jQuery) {
                jQuery(document).ready(function($) {
                    if (window.houzez && window.houzez.Maps) {
                        // Initialize Maps module
                        if ($("#houzez-properties-map").length > 0 || $("input[name=\"search_location\"]").length > 0) {
                            window.houzez.Maps.init();
                        }
                        // Initialize SinglePropertyMap
                        if ( ($("#houzez-single-listing-map").length > 0 || $("#houzez-single-listing-map-address").length > 0 || $("#houzez-single-listing-map-elementor").length > 0) && window.houzez.SinglePropertyMap) {
                            window.houzez.SinglePropertyMap.loadMapFromDOM();
                        }
                        // Initialize SingleAgentMap
                        if ($("#houzez-agent-sidebar-map").length > 0 && window.houzez.SingleAgentMap) {
                            window.houzez.SingleAgentMap.init();
                        }
                        // Initialize SinglePropertyOverviewMap
                        if ($("#houzez-overview-listing-map").length > 0 && window.houzez.SinglePropertyOverviewMap) {
                            window.houzez.SinglePropertyOverviewMap.init();
                        }
                    }
                });
            }
        };');
        
        wp_register_script(
            'houzez-google-map-api',
            "https://maps.google.com/maps/api/js?libraries={$libraries}&language={$wp_locale}&loading=async&key={$key}&callback=houzezMapCallback",
            array('houzez-maps-callback'),
            null,
            true
        );
        
        // Add Advanced Marker Clusterer for Google Maps Advanced Markers
        wp_register_script('googlemap-advanced-marker-clusterer', "{$js_uri}vendors/markeradvancedclusterer.min.js", [ 'houzez-google-map-api' ], '2.5.3', true);
        
        wp_register_script(
            'houzez-google-maps',
            "{$js_uri}houzez-google-maps" . houzez_minify_js() . ".js",
            [ 'jquery', 'houzez-google-map-api', 'googlemap-advanced-marker-clusterer' ],
            $ver,
            true
        );


        /*
        ** Mapbox (registered, but not yet enqueued)
        */
        wp_register_script( 'mapbox-gl', "{$js_uri}vendors/mapbox/mapbox-gl.js", [], '3.11.1', false );
        wp_register_script(
            'mapbox-gl-language',
            "{$js_uri}vendors/mapbox/mapbox-gl-language.js",
            [ 'mapbox-gl' ],
            '3.11.1',
            true
        );
    
        wp_register_style( 'mapbox-gl', "{$js_uri}vendors/mapbox/mapbox-gl.css", [], '3.11.1' );
        
        // Mapbox Geocoder should be registered as a separate global object
        wp_register_script( 'mapbox-geocoder', "{$js_uri}vendors/mapbox/mapbox-gl-geocoder.min.js", ['mapbox-gl'], '5.0.0', false );
        wp_register_style( 'mapbox-geocoder', "{$js_uri}vendors/mapbox/mapbox-gl-geocoder.css", ['mapbox-gl'], '5.0.0' );

        wp_register_script(
            'houzez-mapbox',
            "{$js_uri}houzez-mapbox" . houzez_minify_js() . ".js",
            [ 'jquery','mapbox-gl', 'mapbox-gl-language' ],
            $ver,
            true
        );

        /*
        ** Open Street Map (registered, but not yet enqueued)
        */
        wp_register_script( 'leaflet', "{$js_uri}vendors/leaflet/leaflet.js", [], '1.9.3', false );
        wp_register_style( 'leaflet', "{$js_uri}vendors/leaflet/leaflet.css", [], '1.9.3' );
        wp_register_style('leafletMarkerCluster', "{$js_uri}vendors/leafletCluster/MarkerCluster.css", [], '1.4.0', 'all');
        wp_register_style('leafletMarkerClusterDefault', "{$js_uri}vendors/leafletCluster/MarkerCluster.Default.css", [], '1.4.0', 'all');
        wp_register_script('leafletMarkerCluster', "{$js_uri}vendors/leafletCluster/leaflet.markercluster.js", [ 'leaflet' ], '1.4.0', true);
        wp_register_script('leafletGestureHandling', "{$js_uri}vendors/leaflet/leaflet-gesture-handling.min.js", [ 'leaflet' ], '1.2.0', true);
        wp_register_style('leafletGestureHandling', "{$js_uri}vendors/leaflet/leaflet-gesture-handling.min.css", [], '1.2.0');

        wp_register_script( 
            'houzez-openstreetmap', 
            "{$js_uri}houzez-openstreetmap" . houzez_minify_js() . '.js', 
            [ 'jquery', 'leaflet', 'leafletGestureHandling' ], 
            $ver, 
            true 
        );

    }
}

/**
 * 2) Conditionally enqueue only what's needed
 */
if( ! function_exists('houzez_enqueue_scripts') ) {
    function houzez_enqueue_scripts() {
        global $post;

        global $post;
        $login_redirect = $houzez_date_language = $page_header_type = $woo_checkout_url = $agent_form_redirect = '';
        $userID = get_current_user_id();
        $houzez_local = houzez_get_localization();

        $page_id = isset($post->ID) ? $post->ID : '';

        if(!empty($page_id)) {
            $page_header_type = get_post_meta($page_id, 'fave_header_type', true); 
        }

        $property_gallery_popup_type = houzez_option('property_gallery_popup_type');
        $protocol = is_ssl() ? 'https' : 'http';

        $houzez_logged_in = 'yes';
        if (!is_user_logged_in()) {
            $houzez_logged_in = 'no';
        }

        if (is_rtl()) {
            $houzez_rtl = "yes";
        } else {
            $houzez_rtl = "no";
        }

        $houzez_default_radius = houzez_option('houzez_default_radius');
        if (isset($_GET['radius'])) {
            $houzez_default_radius = $_GET['radius'];
        }

        $geo_country_limit = houzez_option('geo_country_limit');
        $geocomplete_country = '';
        if ($geo_country_limit != 0) {
            $geocomplete_country = houzez_option('geocomplete_country');
        }

        $after_login_redirect = houzez_option('login_redirect');
        if ($after_login_redirect == 'same_page') {

            if (is_tax()) {
                $login_redirect = get_term_link(get_query_var('term'), get_query_var('taxonomy'));
            } else {
                if (is_home() || is_front_page()) {
                    $login_redirect = site_url();
                } else {
                    if (!is_404() && !is_search() && !is_author() && ! empty($page_id) ) {
                        $login_redirect = get_permalink($page_id);
                    }
                }
            }

        } else {
            $login_redirect = houzez_option('login_redirect_link');
        }

        if ( class_exists( 'WooCommerce' ) ) {
            $woo_checkout_url = wc_get_checkout_url();
        } 

        $search_min_price = houzez_option('advanced_search_widget_min_price', 0);
        $search_min_price_range_for_rent = houzez_option('advanced_search_min_price_range_for_rent', 0);

        $search_max_price = houzez_option('advanced_search_widget_max_price', 2500000);
        $search_max_price_range_for_rent = houzez_option('advanced_search_max_price_range_for_rent', 12000);

        if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

            $currency_data = Fcc_get_currency($_COOKIE['houzez_set_current_currency']);
            $currency_position = $currency_data['position'];
            $currency_symbol = $currency_data['symbol'];
            $thousands_separator = $currency_data['thousands_sep'];

            if( function_exists('houzez_get_plain_price') ) {
                $search_min_price = houzez_get_plain_price($search_min_price);
                $search_max_price = houzez_get_plain_price($search_max_price);
                $search_min_price_range_for_rent = houzez_get_plain_price($search_min_price_range_for_rent);
                $search_max_price_range_for_rent = houzez_get_plain_price($search_max_price_range_for_rent);
            }
            

        } else {
            $currency_position   = houzez_option('currency_position', 'before');
            $currency_symbol     = houzez_option('currency_symbol', '$');
            $thousands_separator = houzez_option('thousands_separator', ',');

            if( is_singular('property') ) {
                $s_currency_maker = currency_maker();
                $currency_symbol = $s_currency_maker['currency'];
                $currency_position = $s_currency_maker['currency_position'];
                $thousands_separator = $s_currency_maker['thousands_separator'];
            }
        }

        // Decide "all-in-one" vs individual libs
        if ( houzez_option('js_all_in_one', 1) ) {
            wp_enqueue_script( 'houzez-all-in-one' );
        } else {
            foreach ( [ 'bootstrap','bootstrap-select','theia-sticky-sidebar','slick','chart' ] as $lib ) {
                wp_enqueue_script( $lib );
                // Add async to slick and chart as they're not critical for initial render
                // if ( in_array($lib, ['slick', 'chart']) ) {
                //     wp_script_add_data( $lib, 'async', true );
                // }
            }
        }

        // Instant‐page preloading
        if ( houzez_option('preload_pages', 1) ) {
            wp_enqueue_script( 'houzez-instant-page' );
        }

        // Single‐property & FTS Builder: gallery + charts
        if ( is_singular('property') || is_singular('fts_builder') ) {
            wp_enqueue_script( 'lightslider' );
            //wp_script_add_data( 'lightslider', 'async', true );
            wp_enqueue_script( 'fancybox' );
            //wp_script_add_data( 'fancybox', 'async', true );
        }

        if ( is_page_template('template/user_dashboard_crm.php') 
                || is_page_template('template/user_dashboard_insight.php') 
                || is_page_template('template/user_dashboard_submit.php')
                || is_singular('houzez_agent')
                || is_singular('houzez_agency')
                || is_singular('fts_builder')
                || is_author()
            ) {
            wp_enqueue_script('chart');
        }

        // Date‐picker on single property or dashboard
        if ( is_singular('property') || houzez_is_dashboard() ) {

            $houzez_date_language = houzez_option('houzez_date_language');
            $agent_form_redirect = houzez_option('agent_form_redirect', '');

            if( !empty($agent_form_redirect) ) {

                if (defined('ICL_SITEPRESS_VERSION')) {
                    $agent_form_redirect = houzez_translate_object_id($agent_form_redirect, 'page');
                }
                $agent_form_redirect = get_permalink($agent_form_redirect);
            }

            wp_enqueue_script( 'bootstrap-datepicker' );
            // enqueue your locale file if registered
            if ( $houzez_date_language && $houzez_date_language !== 'xx' ) {
                wp_enqueue_script( "bootstrap-datepicker." . $houzez_date_language );
            }
            if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'en' ) {
                wp_enqueue_script( "bootstrap-datepicker." . ICL_LANGUAGE_CODE );
                $houzez_date_language = ICL_LANGUAGE_CODE;
            }
        }

        // Comment‐reply support
        if ( is_singular('post') && comments_open() && get_option('thread_comments') ) {
            wp_enqueue_script( 'comment-reply' );
        }

        // Mobile touch
        if ( wp_is_mobile() ) {
            wp_enqueue_script( 'jquery-touch-punch' );
        }

        $wp_locale       = get_locale();                            // "en_US"
        $locale_short    = substr( $wp_locale, 0, 2 );              // "en"
        $locale_bcp47    = str_replace( '_', '-', $wp_locale );     // "en-US"

        // Finally your custom script
        wp_enqueue_script( 'houzez-custom' );

        // Pass PHP values to custom.js
        wp_localize_script('houzez-custom', 'houzez_vars',
            array(
                'admin_url' => get_admin_url(),
                'houzez_rtl' => $houzez_rtl,
                'mapboxLocale'     => $locale_bcp47,
                'mapboxLocaleShort'=> $locale_short,
                'user_id' => $userID,
                'primary_color' => houzez_option('houzez_primary_color', '#00aeff'),
                'redirect_type' => $after_login_redirect,
                'login_redirect' => $login_redirect,
                'property_gallery_popup_type' => $property_gallery_popup_type,
                'wp_is_mobile' => wp_is_mobile(),
                // 'default_lat' => houzez_option('map_default_lat', 25.686540),
                // 'default_long' => houzez_option('map_default_long', -80.431345),
                'houzez_is_splash' => houzez_is_splash(),
                'prop_detail_nav' => houzez_option('prop-detail-nav', 'no'),
                'add_to_favorite_login_required' => houzez_option('add_to_favorite', 0),
                'disable_property_gallery' => houzez_option('disable_property_gallery', 1),
                'grid_gallery_behaviour' => houzez_option('gallery_behaviour', 'on_hover'),
                'is_singular_property' => is_singular('property'),
                'search_position' => houzez_get_header_search_position(),
                'login_loading' => esc_html__('Sending user info, please wait...', 'houzez'),
                'not_found' => esc_html__("We didn't find any results", 'houzez'),
                'listings_not_found' => esc_html__("No more listings found", 'houzez'),
                'houzez_map_system' => houzez_get_map_system(),
                'for_rent' => houzez_get_term_slug(houzez_option('search_rent_status'), 'property_status'),
                'for_rent_price_slider' => houzez_get_term_slug(houzez_option('search_rent_status_for_price_range'), 'property_status'),
                'search_min_price_range' => $search_min_price,
                'search_max_price_range' => $search_max_price,
                'search_min_price_range_for_rent' => $search_min_price_range_for_rent,
                'search_max_price_range_for_rent' => $search_max_price_range_for_rent,
                'get_min_price' => isset($_GET['min-price']) ? $_GET['min-price'] : null,
                'get_max_price' => isset($_GET['max-price']) ? $_GET['max-price'] : null,
                'currency_position' => $currency_position,
                'currency_symbol' => $currency_symbol,
                'decimals' => houzez_option('decimals', '2'),
                'decimal_point_separator' => houzez_option('decimal_point_separator', '.'),
                'thousands_separator' => $thousands_separator,
                'is_halfmap' => houzez_is_half_map(),
                'houzez_date_language' => $houzez_date_language,
                'houzez_default_radius' => $houzez_default_radius,
                'houzez_reCaptcha' => houzez_show_google_reCaptcha(),
                'geo_country_limit' => $geo_country_limit,
                'geocomplete_country' => $geocomplete_country,
                'is_edit_property' => houzez_edit_property(),
                'processing_text' => esc_html__('Processing, Please wait...', 'houzez'),
                'halfmap_layout' => houzez_half_map_layout(),
                'prev_text' => esc_html__('Prev', 'houzez'),
                'next_text' => esc_html__('Next', 'houzez'),
                'auto_load_map_listings' => houzez_option('auto_load_map_listings', 1),
                'keyword_search_field' => houzez_option('keyword_field'),
                'keyword_autocomplete' => houzez_option('keyword_autocomplete', 0),
                'autosearch_text' => esc_html__('Searching...', 'houzez'),
                'paypal_connecting' => esc_html__('Connecting to paypal, Please wait... ', 'houzez'),
                'transparent_logo' => houzez_is_transparent_logo(),
                'is_transparent' => houzez_is_transparent(),
                'is_top_header' => houzez_option('top_bar', 0),
                'simple_logo' => houzez_option('custom_logo', '', 'url'),
                'retina_logo' => houzez_option('retina_logo', '', 'url'),
                'mobile_logo' => houzez_option('mobile_logo', '', 'url'),
                'retina_logo_mobile' => houzez_option('mobile_retina_logo', '', 'url'),
                'retina_logo_mobile_splash' => houzez_option('retina_logo_mobile_splash', '', 'url'),
                'custom_logo_splash' => houzez_option('custom_logo_splash', '', 'url'),
                'retina_logo_splash' => houzez_option('retina_logo_splash', '', 'url'),
                'monthly_payment' => esc_html__('Monthly Payment', 'houzez'),
                'weekly_payment' => esc_html__('Weekly Payment', 'houzez'),
                'bi_weekly_payment' => esc_html__('Bi-Weekly Payment', 'houzez'),
                'current_location' => esc_html__('Current Location', 'houzez'),
                'compare_url' => houzez_get_template_link_2('template/template-compare.php'),
                'favorite_url' => houzez_get_template_link_2('template/user_dashboard_favorites.php'),
                'template_thankyou' => houzez_get_template_link('template/template-thankyou.php'),
                'compare_page_not_found' => esc_html__('Please create page using compare properties template', 'houzez'),
                'compare_limit' => esc_html__('Maximum item compare are 4', 'houzez'),
                'compare_add_icon' => '',
                'compare_remove_icon' => '',
                'add_compare_text' => houzez_option('cl_add_compare', 'Add to Compare'),
                'remove_compare_text' => houzez_option('cl_remove_compare', 'Remove from Compare'),
                'map_show_all' => esc_html__('Showing all %s listings', 'houzez'),
                'map_show_some' => esc_html__('Showing %s of %s listings.', 'houzez'),
                'zoom_in_show_more' => esc_html__('Zoom in to see more.', 'houzez'),
                'api_mapbox' => houzez_option('mapbox_api_key'),
                'listing_pagination' => houzez_option('listing_pagination', '_number'),
                'is_marker_cluster' => houzez_option('map_cluster_enable'),
                'g_recaptha_version' => houzez_option( 'recaptha_type', 'v2' ),
                's_country' => isset($_GET['country']) ? $_GET['country'] : '',
                's_state' => isset($_GET['states']) ? $_GET['states'] : '',
                's_city' => isset($_GET['location']) ? $_GET['location'] : '',
                's_areas' => isset($_GET['areas']) ? $_GET['areas'] : '',
                'woo_checkout_url' => esc_url($woo_checkout_url),
                'agent_redirection' => $agent_form_redirect,
                'gesture_text_touch' => esc_html__('Use two fingers to move the map', 'houzez'),
                'gesture_text_scroll' => esc_html__('Use ctrl + scroll to zoom the map', 'houzez'),
                'gesture_text_scrollMac' => esc_html__('Use ⌘ + scroll to zoom the map', 'houzez'),
            )
        ); // end custom script


        if( houzez_is_dashboard() || (is_page_template('template/user_dashboard_submit.php') && ! is_user_logged_in()) ) {

            if( houzez_option('enable_paid_submission') == 'membership') {
                $user_package_id = houzez_get_user_package_id($userID);
                $package_images = get_post_meta( $user_package_id, 'fave_package_images', true );
                $package_unlimited_images = get_post_meta( $user_package_id, 'fave_unlimited_images', true );
                if( $package_unlimited_images != 1 && !empty($package_images)) {
                    $max_prop_images = $package_images;
                } else {
                    $max_prop_images = houzez_option('max_prop_images', '50');
                }
            } else {
                $max_prop_images = houzez_option('max_prop_images', '50');
            }

            wp_enqueue_script('validate');
            wp_enqueue_script('bootbox-min');
            wp_enqueue_script('houzez-property');

            $property_data = array(
                'ajaxURL' => admin_url('admin-ajax.php'),
                'verify_nonce' => wp_create_nonce('verify_gallery_nonce'),
                'verify_file_type' => esc_html__('Valid file formats', 'houzez'),
                'houzez_logged_in' => $houzez_logged_in,
                'msg_digits' => esc_html__('Please enter only digits', 'houzez'),
                'max_prop_images' => $max_prop_images,
                'image_max_file_size' => houzez_option('image_max_file_size'),
                'max_prop_attachments' => houzez_option('max_prop_attachments', '3'),
                'attachment_max_file_size' => houzez_option('attachment_max_file_size', '12000kb'),
                'plan_title_text' => houzez_option('cl_plan_title', 'Plan Title' ),
                'plan_size_text' => houzez_option('cl_plan_size', 'Plan Size' ),
                'plan_bedrooms_text' => houzez_option('cl_plan_bedrooms', 'Bedrooms' ),
                'plan_bathrooms_text' => houzez_option('cl_plan_bathrooms', 'Bathrooms' ),
                'plan_price_text' => houzez_option('cl_plan_price', 'Price' ),
                'plan_price_postfix_text' => houzez_option('cl_plan_price_postfix', 'Price Postfix' ),
                'plan_image_text' => houzez_option('cl_plan_img', 'Plan Image' ),
                'plan_description_text' => houzez_option('cl_plan_des', 'Description'),
                'plan_upload_text' => houzez_option('cl_plan_img_btn', 'Select Image'),
                'plan_upload_size' => houzez_option('cl_plan_img_size', 'Minimum size 800 x 600 px'),

                'mu_title_text' => houzez_option('cl_subl_title', 'Title' ),
                'mu_type_text' => houzez_option('cl_subl_type', 'Property Type' ),
                'mu_beds_text' => houzez_option('cl_subl_bedrooms', 'Bedrooms' ),
                'mu_beds_text' => houzez_option('cl_subl_bedrooms_plac', 'Bedrooms' ),
                'mu_baths_text' => houzez_option('cl_subl_bathrooms', 'Bathrooms' ),
                'mu_baths_text' => houzez_option('cl_subl_bathrooms_plac', 'Bathrooms' ),
                'mu_size_text' => houzez_option('cl_subl_size', 'Property Size' ),
                'mu_size_text' => houzez_option('cl_subl_size_plac', 'Property Size' ),
                'mu_size_postfix_text' => houzez_option('cl_subl_size_postfix', 'Size Postfix' ),
                'mu_price_text' => houzez_option('cl_subl_price', 'Price' ),
                'mu_price_postfix_text' => houzez_option('cl_subl_price_postfix', 'Price Postfix' ),
                'mu_availability_text' => houzez_option('cl_subl_date', 'Availability Date' ),

                'are_you_sure_text' => esc_html__('Are you sure you want to do this?', 'houzez'),
                'delete_btn_text' => esc_html__('Delete', 'houzez'),
                'cancel_btn_text' => esc_html__('Cancel', 'houzez'),
                'confirm_btn_text' => esc_html__('Confirm', 'houzez'),
                'processing_text' => esc_html__('Processing, Please wait...', 'houzez'),
                'add_listing_msg' => esc_html__('Submitting, Please wait...', 'houzez'),
                'confirm_featured' => esc_html__('Are you sure you want to make this a listing featured?', 'houzez'),
                'confirm_featured_remove' => esc_html__('Are you sure you want to remove this listing from featured?', 'houzez'),
                'confirm_relist' => esc_html__('Are you sure you want to relist this property?', 'houzez'),
                'delete_confirmation' => esc_html__('Are you sure you want to delete?', 'houzez'),
                'featured_listings_none' => esc_html__('You have used all the "Featured" listings in your package.', 'houzez'),
                'prop_sent_for_approval' => esc_html__('Sent for Approval', 'houzez'),
                'no_item_selected' => esc_html__('No item selected', 'houzez'),
                'select_action' => esc_html__('Please select an action', 'houzez'),
                'delete_action' => esc_html__('Are you sure you want to delete?', 'houzez'),
                'is_edit_property' => houzez_edit_property(),
                'is_mapbox' => houzez_option('houzez_map_system'),
                'api_mapbox' => houzez_option('mapbox_api_key'),
                'enable_title_limit' => houzez_option('enable_title_limit', 0),
                'property_title_limit' => houzez_option('property_title_limit'),
            );
            wp_localize_script('houzez-property', 'houzezProperty', $property_data);


            if (is_page_template('template/user_dashboard_profile.php') || is_page_template('template/user_dashboard_gdpr.php') || is_page_template('template/user_dashboard_membership.php')) {
                
                wp_enqueue_script('houzez-user-profile');
                $user_profile_data = array(
                    'ajaxURL' => admin_url('admin-ajax.php'),
                    'user_id' => $userID,
                    'houzez_upload_nonce' => wp_create_nonce('houzez_upload_nonce'),
                    'verify_file_type' => esc_html__('Valid file formats', 'houzez'),
                    'houzez_site_url' => home_url(),
                    'gdpr_agree_text' => esc_html__('Please Agree GDPR', 'houzez'),
                );
                wp_localize_script('houzez-user-profile', 'houzezUserProfile', $user_profile_data);

            } // end edit profile

            if ( is_page_template('template/user_dashboard_invoices.php') ) {
                wp_enqueue_script('houzez-invoice', HOUZEZ_JS_DIR_URI . 'houzez-invoice.js', array('jquery'), HOUZEZ_THEME_VERSION, true);
                wp_localize_script('houzez-invoice', 'houzez_vars', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'security' => wp_create_nonce('houzez_invoice_ajax_nonce'),
                    'invoice_title' => esc_html__('Invoice', 'houzez'),
                    'css_url_bootstrap' => HOUZEZ_CSS_DIR_URI . 'bootstrap.min.css',
                    'css_dashboard' => HOUZEZ_CSS_DIR_URI . 'dashboard.css'
                ));
            }

        } // end if( houzez_is_dashboard() )

        if(houzez_map_needed()) { 

            $houzez_map_system = houzez_get_map_system();

            if($houzez_map_system == 'google') {
                wp_enqueue_script('houzez-google-map-api');
                wp_enqueue_script('houzez-google-maps'); 
            } elseif( $houzez_map_system == 'mapbox' ) {

                wp_enqueue_script('mapbox-gl');
                wp_enqueue_style('mapbox-gl');
                wp_enqueue_script('mapbox-geocoder');
                wp_enqueue_style('mapbox-geocoder');
                wp_enqueue_script('houzez-mapbox'); 

            } else {

                wp_enqueue_script('leaflet');
                wp_enqueue_style('leaflet');

                wp_enqueue_script( 'jquery-ui-autocomplete' );
                wp_enqueue_style('jquery-ui');

                if(houzez_option('map_cluster_enable') != 0) { 
                    wp_enqueue_style('leafletMarkerCluster');
                    wp_enqueue_style('leafletMarkerClusterDefault');
                    wp_enqueue_script('leafletMarkerCluster');
                }
                wp_enqueue_script('leafletGestureHandling');
                wp_enqueue_style('leafletGestureHandling');

                wp_enqueue_script('houzez-openstreetmap');
            }

        } // end if(houzez_map_needed())

        // Enqueue Captcha Scripts (Google reCaptcha or Cloudflare Turnstile)
        if ( function_exists('houzez_is_captcha_enabled') && houzez_is_captcha_enabled() ) {
            $provider = houzez_get_captcha_provider();

            if ( $provider === 'recaptcha' ) {
                // Google reCaptcha
                $recaptha_type = houzez_option( 'recaptha_type', 'v2' );

                if ( 'v3' === $recaptha_type ) {
                    $render = houzez_option( 'recaptha_site_key' );
                } else {
                    $render = 'explicit';
                }

                $recaptcha_src = esc_url_raw( add_query_arg( array(
                    'render' => $render,
                    'onload' => 'houzezReCaptchaLoad',
                ), '//www.google.com/recaptcha/api.js' ) );

                wp_enqueue_script(
                    'houzez-google-recaptcha',
                    $recaptcha_src,
                    array(),
                    HOUZEZ_THEME_VERSION,
                    true
                );

            } elseif ( $provider === 'turnstile' ) {
                // Cloudflare Turnstile
                wp_enqueue_script(
                    'houzez-cloudflare-turnstile',
                    'https://challenges.cloudflare.com/turnstile/v0/api.js',
                    array(),
                    HOUZEZ_THEME_VERSION,
                    true
                );
            }

        } elseif ( houzez_show_google_reCaptcha() ) {
            // Fallback for legacy setups
            $recaptha_type = houzez_option( 'recaptha_type', 'v2' );

            if ( 'v3' === $recaptha_type ) {
                $render = houzez_option( 'recaptha_site_key' );
            } else {
                $render = 'explicit';
            }

            $recaptcha_src = esc_url_raw( add_query_arg( array(
                'render' => $render,
                'onload' => 'houzezReCaptchaLoad',
            ), '//www.google.com/recaptcha/api.js' ) );

            wp_enqueue_script(
                'houzez-google-recaptcha',
                $recaptcha_src,
                array(),
                HOUZEZ_THEME_VERSION,
                true
            );
        }

    }
}

// Header custom JS
function houzez_header_scripts(){

    $custom_js_header = houzez_option('custom_js_header');

    if ( $custom_js_header != '' ){
        echo ( $custom_js_header );
    }
}

// Footer custom JS
function houzez_footer_scripts(){
    $custom_js_footer = houzez_option('custom_js_footer');

    if ( $custom_js_footer != '' ){
        echo ( $custom_js_footer );
    }
}


if (is_admin()) {
    add_action('admin_enqueue_scripts', 'houzez_admin_scripts');
}

if( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'houzez_enqueue_styles');
    add_action( 'wp_enqueue_scripts', 'houzez_register_scripts', 1 );
    add_action( 'wp_enqueue_scripts', 'houzez_enqueue_scripts', 20 );
    add_action( 'wp_head', 'houzez_header_scripts');
    add_action( 'wp_footer', 'houzez_footer_scripts', 100 );
}