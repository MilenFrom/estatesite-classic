/**
 * Houzez Compatibility Layer
 *
 * This file provides backward compatibility for functions that might be called by
 * plugins or third-party code that depends on the old custom.js structure.
 *
 * Any function that was exposed globally in custom.js should be mapped to its
 * new location in the modular structure.
 */
(function ($) {
    'use strict';

    // Make sure the main objects are available
    window.houzezThemeGlobal = window.houzezThemeGlobal || {};

    // Create compatibility mapping for commonly used functions

    // Utility functions
    window.HouzezDebounce = houzezCore.util.debounce;
    window.parseBool = houzezCore.util.parseBool;
    window.number_format = houzezCore.util.numberFormat;
    window.thousandSeparator = houzezCore.util.thousandSeparator;
    window.currencyFormate = houzezCore.util.currencyFormat;
    window.addCommas = houzezUtility.addCommas;
    window.formatNumber = houzezUtility.formatNumber;

    // UI functions
    window.houzez_sticky_nav = houzezUI.stickyNav;
    window.houzez_sticky_search = houzezUI.stickySearch;
    window.adjustMegaMenu = houzezUI.adjustMegaMenu;
    window.setSectionHeight = houzezUI.setSectionHeight;
    window.houzez_mobile_sticky_nav = houzezUI.stickyMobileNav;
    window.listingViewSwitch = houzezUI.listingViewSwitch;

    // Gallery functions
    window.houzez_lazyload = houzezGallery.initLazyLoad;
    window.houzez_grid_image_gallery = houzezGallery.initGridImageGallery;
    window.houzez_parallax_listings = houzezGallery.initParallaxListings;

    // Favorites and compare functions
    window.houzez_init_add_favorite = houzezFavorites.initAddFavorite;
    window.houzez_init_remove_favorite = houzezFavorites.initRemoveFavorite;
    window.add_to_favorite = houzezFavorites.add_to_favorite;
    window.remove_from_favorite = houzezFavorites.remove_from_favorite;
    window.houzez_check_favourites = houzezFavorites.check_favorites;
    window.compare_for_ajax = houzezCompare.compare_for_ajax;

    // Mortgage calculator functions
    window.calculateMonthlyPayment = houzezMortgage.calculateMonthlyPayment;
    window.parseInput = houzezMortgage.parseInput;
    window.mortgage_calucaltion_section = houzezMortgage.mortgageCalculation;
    window.updateChart = houzezMortgage.updateChart;

    // Search functions
    window.property_status_changed = houzezSearch.statusChangeHandler;
    window.price_range_search = houzezSearch.initPriceRangeSlider;
    window.insertParam = houzezSearch.insertUrlParam;

    // Pagination functions
    window.houzez_loadmore_properties = houzezPagination.loadMoreProperties;
    window.houzez_reinit_functions = houzezPagination.reinitializeFunctions;

    // Login functions
    window.houzez_social_login_panel = houzezLogin.socialLoginPanel;
    window.houzez_process_login = houzezLogin.processLogin;
    window.houzez_process_register = houzezLogin.processRegister;
    window.houzez_process_reset = houzezLogin.processReset;

    // Property functions
    window.property_detail_nav = houzezProperties.propertyDetailNav;
    window.agent_contact_form = houzezProperties.agentContactForm;
    window.schedule_tour = houzezProperties.scheduleTour;
    window.setCalendarCellHeight = houzezProperties.setCalendarCellHeight;

    // Slider and carousel functions
    window.propertyDetailGallery = houzezSliders.propertyDetailGallery;
    window.propertyBannerSlider = houzezSliders.propertyBannerSlider;
    window.lightboxSlider = houzezSliders.lightboxSlider;
    window.variableWidthSlider = houzezSliders.variableWidthSlider;
    window.testimonialsSliders = houzezSliders.testimonialsSliders;
    window.agentsCarousel = houzezSliders.agentsCarousel;
    window.partnersCarousel = houzezSliders.partnersCarousel;

    // Elementor integration functions
    window.houzezProductsTabs = houzezElementor.productsTabsInit;
    window.propertyStatsTabsInit = houzezElementor.propertyStatsTabsInit;
    if (window.houzezThemeGlobal) {
        window.houzezThemeGlobal.houzezEleAddAction = houzezElementor.addAction;
    }

    // Compare properties functions
    window.add_to_compare = houzezCompare.add_to_compare;
    window.remove_from_compare = houzezCompare.remove_from_compare;
    window.compare_for_ajax = houzezCompare.compare_for_ajax;

    // Ensure backward compatibility for plugins that check for the existence of functions
    $(document).ready(function () {
        // Initialize any compatibility-specific code
        console.log('Houzez compatibility layer loaded');
    });

    // Mortgage Calculator compatibility
    if (window.houzezMortgage) {
        // Map mortgage calculator functions
        window.calculateMonthlyPayment =
            window.houzezMortgage.calculateMonthlyPayment;
        window.mortgage_calucaltion_section =
            window.houzezMortgage.mortgageCalculationSection;
    }

    // Search functions compatibility
    if (window.houzezSearch) {
        // Map search functions
        window.property_status_changed =
            window.houzezSearch.propertyStatusChanged;
        window.price_range_search = window.houzezSearch.priceRangeSearch;
        window.insertParam = window.houzezSearch.insertParam;
    }

    // Compare functions compatibility
    if (window.houzezCompare) {
        // Map compare functions with exact function names used in the original code
        window.add_to_compare = window.houzezCompare.add_to_compare;
        window.remove_from_compare = window.houzezCompare.remove_from_compare;
        window.compare_for_ajax = window.houzezCompare.compare_for_ajax;
    }

    // Favorites functions compatibility
    if (window.houzezFavorites) {
        // Map favorites functions
        window.houzez_init_add_favorite =
            window.houzezFavorites.initAddFavorite;
        window.houzez_init_remove_favorite =
            window.houzezFavorites.initRemoveFavorite;
        window.add_to_favorite = window.houzezFavorites.addToFavorite;
        window.houzez_check_favourites = window.houzezFavorites.checkFavorites;
    }

    // Gallery functions compatibility
    if (window.houzezGallery) {
        // Map gallery functions
        window.houzez_grid_image_gallery =
            window.houzezGallery.gridImageGallery;
        window.houzez_listing_lightbox = window.houzezGallery.listingLightbox;
    }

    // Utility functions compatibility
    if (window.houzezUtility) {
        // Map utility functions
        window.houzezGetCookie = window.houzezUtility.getCookie;
        window.houzezSetCookie = window.houzezUtility.setCookie;
        window.fave_processing_modal = window.houzezUtility.processingModal;
        window.fave_processing_modal_close =
            window.houzezUtility.processingModalClose;
    }
})(jQuery);
