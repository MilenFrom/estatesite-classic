/**
 * Houzez Custom JavaScript
 * Consolidated version of all Houzez JavaScript modules
 *
 * This file combines the following modules:
 * - Core: Basic functionality and utilities
 * - Login: Authentication functionality
 * - Favorites: Property favorites management
 * - Compare: Property comparison features
 * - Properties: Property-specific functionality
 * - Pagination: AJAX pagination and infinite loading
 * - Elementor: Integration with Elementor page builder
 */
(function ($) {
    'use strict';

    // Main Houzez object that will contain all modules
    var houzez = window.houzez || {};

    /**
     * Core Module
     * Core functionality and utility functions
     */
    houzez.Core = (function () {
        // Cache DOM selectors
        const $document = $(document);
        const $window = $(window);

        // Global variables
        const $win = $(window);
        const $body = $('body');

        // Centralized config from global vars
        const config = {
            // Core config
            ajaxurl: houzez_vars.admin_url + 'admin-ajax.php',
            userID: houzez_vars.user_id,
            primary_color: houzez_vars.primary_color,
            houzez_rtl: houzez_vars.houzez_rtl === 'yes',
            wp_is_mobile: houzez_vars.wp_is_mobile,
            currency_position: houzez_vars.currency_position,
            currency_symbol: houzez_vars.currency_symbol,
            decimals: houzez_vars.decimals,
            decimal_point_separator: houzez_vars.decimal_point_separator,
            thousands_separator: houzez_vars.thousands_separator,
            is_singular_property: houzez_vars.is_singular_property,
            property_detail_nav: houzez_vars.prop_detail_nav,
            processing_text: houzez_vars.processing_text,
            houzez_is_splash: houzez_vars.houzez_is_splash,
            wpadminbar_height: $('#wpadminbar').outerHeight() || 0,
            // Login config
            login_loading: houzez_vars.login_loading,
            login_redirect: houzez_vars.login_redirect,
            redirect_type: houzez_vars.redirect_type,
            houzez_reCaptcha: parseInt(houzez_vars.houzez_reCaptcha),
            g_recaptha_version: houzez_vars.g_recaptha_version,

            // Favorites config
            favorite_url: houzez_vars.favorite_url || '',
            add_to_favorite_login_required: parseInt(
                houzez_vars.add_to_favorite_login_required || 0
            ),

            // Compare config
            compare_url: houzez_vars.compare_url || '',
            compare_add_icon: houzez_vars.compare_add_icon || '',
            compare_remove_icon: houzez_vars.compare_remove_icon || '',
            add_compare_text: houzez_vars.add_compare_text || '',
            remove_compare_text: houzez_vars.remove_compare_text || '',
            compare_limit: houzez_vars.compare_limit || '',
            compare_page_not_found: houzez_vars.compare_page_not_found || '',

            // Sliders config
            prev_text: houzez_vars.prev_text || 'Prev',
            next_text: houzez_vars.next_text || 'Next',
            disable_property_gallery: houzez_vars.disable_property_gallery || 0,
            grid_gallery_behaviour: houzez_vars.grid_gallery_behaviour || '',

            // Properties config
            // ...

            // Pagination config
            // ...

            // New config for half map and fullscreen sections
            is_halfmap: parseInt(houzez_vars.is_halfmap),
        };

        // Utility functions
        const util = {
            /**
             * Debounce function to limit rapid function calls
             * @param {Function} func - Function to debounce
             * @param {number} delay - Delay in milliseconds
             * @return {Function} - Debounced function
             */
            debounce: function (func, delay) {
                let debounceTimer;
                return function () {
                    const context = this;
                    const args = arguments;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(
                        () => func.apply(context, args),
                        delay
                    );
                };
            },

            /**
             * Parse a string to boolean
             * @param {string} str - String to parse
             * @return {boolean} - Parsed boolean
             */
            parseBool: function (str) {
                if (typeof str === 'string') {
                    switch (str.toLowerCase()) {
                        case 'true':
                        case '1':
                        case 'yes':
                        case 'y':
                            return true;
                        case 'false':
                        case '0':
                        case 'no':
                        case 'n':
                            return false;
                        default:
                            return Boolean(str);
                    }
                } else {
                    return Boolean(str);
                }
            },

            /**
             * Format number with thousands separator
             * @param {number} number - Number to format
             * @return {string} - Formatted number
             */
            thousandSeparator: function (n) {
                if (typeof n === 'number') {
                    n += '';
                    const x = n.split('.');
                    const x1 = x[0];
                    const x2 = x.length > 1 ? '.' + x[1] : '';
                    const rgx = /(\d+)(\d{3})/;

                    let result = x1;
                    while (rgx.test(result)) {
                        result = result.replace(
                            rgx,
                            '$1' + config.thousands_separator + '$2'
                        );
                    }
                    return result + x2;
                }
                return n;
            },

            /**
             * Format currency values
             * @param {string|number} price_value - Price to format
             * @return {string} - Formatted price with currency symbol
             */
            currencyFormat: function (price_value) {
                if (!config.currency_position || !config.currency_symbol) {
                    return price_value;
                }

                return config.currency_position === 'after'
                    ? price_value + config.currency_symbol
                    : config.currency_symbol + price_value;
            },

            /**
             * Format numbers with proper separators
             * @param {number} number - Number to format
             * @return {string} - Formatted number
             */
            numberFormat: function (number, custom = false) {
                const dec_point = config.decimal_point_separator;
                const thousands_sep = config.thousands_separator;

                let i, j, kw, kd, km;
                let decimals = config.decimals;

                // Input sanitation & defaults
                if (isNaN(decimals) || custom) {
                    decimals = 2;
                }

                i = parseInt((number = (+number || 0).toFixed(decimals))) + '';
                j = (j = i.length) > 3 ? j % 3 : 0;

                km = j ? i.substr(0, j) + thousands_sep : '';
                kw = i
                    .substr(j)
                    .replace(/(\d{3})(?=\d)/g, '$1' + thousands_sep);

                kd = decimals
                    ? dec_point +
                      Math.abs(number - i)
                          .toFixed(decimals)
                          .replace(/-/, 0)
                          .slice(2)
                    : '';

                return km + kw + kd;
            },

            /**
             * Get cookie by name
             * @param {string} name - Cookie name
             * @return {string} - Cookie value
             */
            getCookie: function (name) {
                const nameEQ = name + '=';
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) {
                        return c.substring(nameEQ.length, c.length);
                    }
                }
                return null;
            },

            /**
             * Set cookie
             * @param {string} name - Cookie name
             * @param {string} value - Cookie value
             * @param {number} days - Days until cookie expires
             */
            setCookie: function (name, value, days) {
                let expires = '';
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                    expires = '; expires=' + date.toUTCString();
                }
                document.cookie = name + '=' + value + expires + '; path=/';
            },

            /**
             * Delete cookie
             * @param {string} name - Cookie name
             */
            deleteCookie: function (name) {
                this.setCookie(name, '', -1);
            },

            /**
             * Add commas to number for display
             * @param {string|number} nStr - Number to format
             * @returns {string} Formatted number
             */
            addCommas: function (nStr) {
                nStr += '';
                const x = nStr.split('.');
                let x1 = x[0];
                const x2 = x.length > 1 ? '.' + x[1] : '';
                const rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            },

            /**
             * Format number with commas
             * @param {number} number - Number to format
             * @returns {string} Formatted number
             */
            formatNumber: function (number) {
                const parts = number.toString().split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
            },

            /**
             * Show the processing modal
             * @param {string} msg - Message to display
             */
            processingModal: function (msg) {
                var process_modal =
                    '<div class="modal fade" id="fave_modal" tabindex="-1" role="dialog" aria-labelledby="faveModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body houzez_messages_modal">' +
                    msg +
                    '</div></div></div></div>';

                jQuery('body').append(process_modal);
                jQuery('#fave_modal').modal('show');
            },

            /**
             * Close the processing modal
             */
            processingModalClose: function () {
                jQuery('#fave_modal').modal('hide');
            },

            /**
             * Generate loader HTML for AJAX requests
             * @returns {string} Loader HTML markup
             */
            getLoaderHtml: function () {
                return '<div class="houzez-overlay-loading"><div class="overlay-placeholder"><div class="loader-ripple spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div></div>';
            },

            /**
             * Generate success alert HTML
             * @param {string} message - Message to display
             * @returns {string} Success alert HTML
             */
            getSuccessHtml: function (message) {
                return (
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<i class="houzez-icon icon-check-circle-1 me-1"></i> ' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>'
                );
            },

            /**
             * Generate error alert HTML
             * @param {string} message - Message to display
             * @returns {string} Error alert HTML
             */
            getErrorHtml: function (message) {
                return (
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="houzez-icon icon-remove-circle me-1"></i> ' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>'
                );
            },

            /**
             * Display success message in target element
             * @param {jQuery|string} target - Target element or selector
             * @param {string} message - Message to display
             */
            showSuccess: function (target, message) {
                const $target = typeof target === 'string' ? $(target) : target;
                $target.empty().append(this.getSuccessHtml(message));
            },

            /**
             * Display error message in target element
             * @param {jQuery|string} target - Target element or selector
             * @param {string} message - Message to display
             */
            showError: function (target, message) {
                const $target = typeof target === 'string' ? $(target) : target;
                $target.empty().append(this.getErrorHtml(message));
            },

            /**
             * Calculate monthly mortgage payment
             * @param {number} principal - Principal loan amount
             * @param {number} annualInterestRate - Annual interest rate (in percentage)
             * @param {number} loanTermInYears - Loan term in years
             * @return {number} - Monthly payment amount
             */
            calculateMonthlyPayment: function (
                principal,
                annualInterestRate,
                loanTermInYears
            ) {
                const monthlyInterestRate = annualInterestRate / 12 / 100;
                const numberOfMonths = loanTermInYears * 12;

                if (monthlyInterestRate === 0) {
                    return principal / numberOfMonths;
                }

                return (
                    (principal *
                        (monthlyInterestRate *
                            Math.pow(
                                1 + monthlyInterestRate,
                                numberOfMonths
                            ))) /
                    (Math.pow(1 + monthlyInterestRate, numberOfMonths) - 1)
                );
            },

            /**
             * Parse and validate input, removing non-numeric characters
             * @param {string} selector - Input selector
             * @return {number} - Parsed value or 0 if invalid/empty
             */
            parseNumberInput: function (selector) {
                const rawValue = $(selector).val();

                if (!rawValue) return 0; // if value is empty or undefined, return 0

                // Remove any non-digit character except the decimal point
                const cleanedValue = rawValue.replace(/[^0-9.]/g, '');

                // Parsing to number
                const parsedValue = parseFloat(cleanedValue);

                // if parsedValue is NaN, return 0, otherwise return the parsed number
                return isNaN(parsedValue) ? 0 : parsedValue;
            },

            /**
             * Update URL with query parameter and return the new URL string
             * @param {string} key - Parameter key
             * @param {string} value - Parameter value
             * @returns {string} The new URL with the updated parameter
             */
            updateUrlParam: function (key, value) {
                var pathname = window.location.pathname;
                var urlParams = new URLSearchParams(window.location.search);
                urlParams.set(key, value);

                // If sorting is being changed, remove pagination to start from page 1
                if (key === 'sortby') {
                    // Remove WordPress permalink-based pagination (e.g., /page/2/)
                    pathname = pathname.replace(/\/page\/\d+\/?/, '/');

                    // Also remove query-based pagination parameters
                    urlParams.delete('paged');
                    urlParams.delete('page');
                }

                var baseUrl = window.location.origin + pathname;
                // Preserve other existing query parameters
                var newUrl = baseUrl + '?' + urlParams.toString();
                return newUrl;
            },

            /**
             * Calculate heights for half map and fullscreen sections
             */
            calculateSectionHeight: function () {
                const windowHeight = $window.height();
                const headerDesktop = $('.header-desktop').outerHeight() || 0;
                const headerMobile = $('.header-mobile').outerHeight() || 0;
                const topBar = $('.top-bar-wrap').outerHeight() || 0;
                const headerElementor =
                    $('#header-hz-elementor').outerHeight() || 0;
                let adminBarHeight = houzez.Core.config.wpadminbar_height;
                let halfMapWrapHeight = 0;

                const mobileSearchNav =
                    $('.mobile-search-nav').outerHeight() || 0;
                const desktopSearchNav =
                    $('.desktop-search-nav').outerHeight() || 0;

                // Use headerElementor as the only header height if present
                let headerHeight;
                if (headerElementor > 0) {
                    headerHeight =
                        headerElementor +
                        (window.innerWidth > 991
                            ? desktopSearchNav + topBar
                            : mobileSearchNav);
                } else {
                    headerHeight =
                        window.innerWidth > 991
                            ? headerDesktop + desktopSearchNav + topBar
                            : headerMobile + mobileSearchNav;
                }

                let halfMapHeight =
                    windowHeight - headerHeight - adminBarHeight;
                halfMapWrapHeight =
                    windowHeight + headerHeight + adminBarHeight;

                if (halfMapHeight < 0) {
                    halfMapHeight = 0;
                }

                if (window.innerWidth > 768) {
                    $('.half-map-wrap').height(halfMapHeight);
                    $('.main-half-map-wrap').css({
                        'max-height': halfMapWrapHeight + 'px',
                        height: 'calc(100vh - ' + adminBarHeight + 'px)',
                    });
                } else {
                    $('.half-map-left-wrap').height(halfMapHeight);
                }

                $('.top-banner-wrap-fullscreen').height(
                    windowHeight - headerHeight
                );
                $('.top-banner-wrap-fullscreen .property-slider').height(
                    windowHeight - headerHeight
                );
                $(
                    '.top-banner-wrap-fullscreen .property-slider-item-wrap'
                ).height(windowHeight - headerHeight);
            },

            /**
             * Reset captcha (supports both Google reCaptcha and Cloudflare Turnstile)
             * @param {jQuery} $form - Form element containing captcha
             */
            resetCaptcha: function($form) {
                // Remove any existing captcha responses
                $form.find('.g-recaptcha-response').remove();
                $form.find('.cf-turnstile-response').remove();

                // Check if reCaptcha is active
                if (config.houzez_reCaptcha == 1) {
                    if (config.g_recaptha_version == 'v3') {
                        // reCaptcha v3 - regenerate token
                        if (typeof houzezReCaptchaLoad === 'function') {
                            houzezReCaptchaLoad();
                        }
                    } else {
                        // reCaptcha v2 - reset widget
                        if (typeof houzezReCaptchaReset === 'function') {
                            houzezReCaptchaReset();
                        }
                    }
                }
                // Check if Turnstile is active (check for widget on page)
                else if ($form.find('.houzez-turnstile').length > 0) {
                    if (typeof houzezTurnstileReset === 'function') {
                        houzezTurnstileReset();
                    } else if (typeof turnstile !== 'undefined') {
                        $form.find('.houzez-turnstile').each(function() {
                            turnstile.reset(this);
                        });
                    }
                }
            },
        };

        /**
         * Check if the device is mobile
         */
        const is_mobile = () => {
            // 1. matchMedia (layout)
            if (window.matchMedia('(max-width: 991px)').matches) {
                return true;
            }

            // 2. modern UAData
            if (navigator.userAgentData?.mobile === true) {
                return true;
            }

            // 3. UA regex fallback
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                navigator.userAgent
            );
        };

        const sortby_mobile = () => {
            if (is_mobile()) {
                $('#ajax_sort_properties').addClass('mobile-sortby');
                $('#mobile-search-form').addClass('apply-mobile-pagination');
            } else {
                $('#ajax_sort_properties').removeClass('mobile-sortby');
                $('#mobile-search-form').removeClass('apply-mobile-pagination');
            }
        };

        /**
         * Initialize tooltips
         */
        const initTooltips = function () {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                // Check if tooltip already exists to prevent duplicates
                const existingTooltip =
                    bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (!existingTooltip) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                }
                return existingTooltip;
            });
        };

        /**
         * Hide all tooltips
         */
        const hideAllTooltips = function () {
            const tooltips = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            );
            tooltips.forEach(function (element) {
                const tooltipInstance = bootstrap.Tooltip.getInstance(element);
                if (tooltipInstance) {
                    tooltipInstance.hide();
                }
            });
        };

        /**
         * Initialize reviews tab from all reviews link
         */
        const initReviewsTab = function () {
            $('.all-reviews').on('click', function (e) {
                e.preventDefault();

                $('.hz-review-tab').trigger('click');

                var target = document.getElementById('review-scroll');
                if (!target) return;

                var headerHeight = $('.header-main-wrap').outerHeight() || 0;
                var targetTop = $(target).offset().top - headerHeight;
                window.scrollTo({
                    top: Math.max(0, targetTop),
                    behavior: 'smooth'
                });
            });
        };

        /**
         * Initialize property sorting
         */
        const initPropertySorting = function () {
            $('#sort_properties').on('change', function () {
                let key = 'sortby';
                let value = $(this).val();
                // Use the new Core utility function and reload
                window.location.href = houzez.Core.util.updateUrlParam(
                    key,
                    value
                );
            });
        };

        /**
         * Initialize modals with dynamic content
         */
        // const initDynamicModals = function () {
        //     $('.schedule-contact-btn').on('click', function (e) {
        //         e.preventDefault();
        //         $('#schedule-contact-modal').modal('show');
        //     });

        //     // Membership selection modals
        //     $('.houzez-open-membership-modal').on('click', function (e) {
        //         e.preventDefault();
        //         $('#modal-membership').modal('show');
        //     });
        // };

        /**
         * Toggle mobile menus and panels
         */
        const mobileToggleMenu = function () {
            // Main navigation
            $('.toggle-mobile-nav').on('click', function (e) {
                e.preventDefault();
                $('#mobile-main-nav').toggleClass('show');
                $('.toggle-mobile-nav').toggleClass('active');
            });

            // Account navigation
            $('.user-icon').on('click', function (e) {
                e.preventDefault();
                if ($('#user-login-dropdown').is(':visible')) {
                    $('#user-login-dropdown').slideUp();
                } else {
                    $('#user-login-dropdown').slideDown();
                }
            });
        };

        /**
         * Handle call and email popup modals from grid items
         */
        const gridCallToAction = function () {
            // Handle call popup clicks
            $('.hz-call-popup-js').on('click', function () {
                var call_model_id = $(this).data('model-id');
                $('#' + call_model_id).appendTo('body');
                $('#' + call_model_id).modal('show');
            });

            // Handle email popup clicks
            $('.hz-email-popup-js').on('click', function () {
                var email_model_id = $(this).data('model-id');
                $('#' + email_model_id).appendTo('body');
                $('#' + email_model_id).modal('show');
            });
        };

        $('.agent-show-onClick').on('click', function (e) {
            $(this).toggleClass('agent-phone-hidden');
        });

        /**
         * Handle scroll to top functionality
         */
        const scrollToTop = function () {
            const scroll_anchor = $('#scroll-top');

            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 500) {
                    scroll_anchor.fadeIn('fast').css('display', 'block');
                    return;
                }
                scroll_anchor.fadeOut('fast');
            });

            scroll_anchor.on('click', function (event) {
                event.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            });
        };

        /**
         * Reinitialize necessary functions after new content is loaded via AJAX
         * Common utility for all modules that load content dynamically
         */
        const reinitializeAjaxFunctions = function () {
            // Reinitialize favorites
            if (houzez.Favorites) {
                houzez.Favorites.initAddFavorite();
            }

            if (houzez.Compare) {
                houzez.Compare.compare_for_ajax();
            }

            // Reinitialize grid image gallery
            if (houzez.Sliders) {
                houzez.Sliders.gridImageGallery();
            }

            if (houzez.PropertyPreview) {
                houzez.PropertyPreview.init();
            }

            // Reinitialize tooltips
            initTooltips();

            // Reinitialize lazy loading
            houzezLazyload();

            // Reinitialize grid call to action
            gridCallToAction();
        };

        /**
         * Handle property view switching (list/grid)
         */
        const listingViewSwitch = function () {
            // Handle view switching with URL update
            $('.listing-switch-view .switch-btn, .switch-btn').on(
                'click',
                function (e) {
                    e.preventDefault();
                    var view = $(this).hasClass('btn-grid') ? 'grid' : 'list';

                    // Update URL and reload page
                    window.location.href = util.updateUrlParam(
                        'listing-view',
                        view
                    );
                }
            );

            // Set initial active state for buttons based on data-view attribute
            var currentView = $('.listing-view').attr('data-view');
            if (currentView) {
                var $btnGrid = $('.btn-grid');
                var $btnList = $('.btn-list');

                if (currentView === 'grid') {
                    $btnGrid.addClass('active').attr('aria-pressed', 'true');
                    $btnList
                        .removeClass('active')
                        .attr('aria-pressed', 'false');
                } else {
                    $btnList.addClass('active').attr('aria-pressed', 'true');
                    $btnGrid
                        .removeClass('active')
                        .attr('aria-pressed', 'false');
                }
            }
        };

        /**
         * Handle lazy loading of images with .houzez-lazyload class
         */
        const houzezLazyload = function () {
            // Check if any .houzez-lazyload elements exist before initializing
            if (document.querySelectorAll('.houzez-lazyload').length === 0) {
                return;
            }

            // core logic: find all .houzez-lazyload images and observe them
            function init() {
                var lazyImages = [].slice.call(
                    document.querySelectorAll('.houzez-lazyload')
                );
                if (!lazyImages.length || !('IntersectionObserver' in window))
                    return;

                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        var img = entry.target;
                        img.src = img.dataset.src;
                        if (img.dataset.srcset) {
                            img.srcset = img.dataset.srcset;
                        }
                        img.classList.remove('houzez-lazyload');
                        observer.unobserve(img);
                    });
                });

                lazyImages.forEach(function (img) {
                    observer.observe(img);
                });
            }

            // if DOM not ready, wait; otherwise run now
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        };

        /**
         * Handle login and register trigger
         */
        const LoginRegisterTrigger = function () {
            // Common function to toggle between login and register forms
            const toggleForms = function (showLogin) {
                // Toggle modal buttons
                $('.modal-toggle-1').toggleClass('active', showLogin);
                $('.modal-toggle-2').toggleClass('active', !showLogin);

                // Toggle form visibility
                $('.login-form-tab').toggleClass('active show', showLogin);
                $('.register-form-tab').toggleClass('active show', !showLogin);
            };

            // Event handlers
            $('.login-link a').on('click', function (e) {
                e.preventDefault();
                toggleForms(true);
            });

            $('.register-link a').on('click', function (e) {
                e.preventDefault();
                toggleForms(false);
            });
        };

        /**
         * Handle sidebar menu toggle
         */
        const sidebarMenuToggle = function () {
            $('.menu-btn').on('click', function () {
                $('body').toggleClass('sidebar-collapsed');
            });

            $('.crose-btn').on('click', function () {
                $('body').removeClass('sidebar-collapsed');
            });
        };

        /**
         * Initialize sticky sidebar functionality
         * Calculates appropriate top margin based on various header elements
         */
        const stickySidebar = function () {
            // Only proceed if sticky elements exist
            if (!$('.houzez_sticky').length) return;

            // Initialize variables for margin calculation
            let houzezStickyTop = 0;
            let adminBarHeight = houzez.Core.config.wpadminbar_height;

            // Get advanced search nav height if sticky
            const advanced_search_nav = $('.advanced-search-nav');
            let advanced_search_nav_height = 0;

            if (advanced_search_nav.data('sticky') === 1) {
                advanced_search_nav_height =
                    advanced_search_nav.innerHeight() || 0;
            }

            // Get header nav height and sticky status
            const header_nav = $('#header-section');
            const houzez_nav_sticky_height = header_nav.innerHeight() || 0;
            let only_nav_sticky = header_nav.data('sticky');

            // Disable sticky nav on property detail pages with property navigation
            if (
                houzez.Core.config.property_detail_nav === 'yes' &&
                houzez.Core.config.is_singular_property
            ) {
                only_nav_sticky = 0;
            }

            // Add header height to sticky top if header is sticky
            if (only_nav_sticky === 1) {
                houzezStickyTop = houzez_nav_sticky_height;
            }

            // Add admin bar height if present
            if (adminBarHeight) {
                houzezStickyTop += adminBarHeight;
            }

            // Get property navigation height if present
            const listing_nav_area_height =
                $('.property-navigation-wrap').innerHeight() || 0;

            // Initialize theiaStickySidebar with calculated margins
            $('.houzez_sticky').theiaStickySidebar({
                additionalMarginTop:
                    houzezStickyTop +
                    advanced_search_nav_height +
                    listing_nav_area_height,
                minWidth: 768,
                updateSidebarHeight: false,
            });
        };

        const checkCollapse = function () {
            var el = document.getElementById('search-expandable-collapse');

            if (!el) return;

            if (window.innerWidth < 992) {
                // Mobile: chiuso
                el.classList.remove('show');
            } else {
                // Desktop: aperto
                el.classList.add('show');
            }
        };

        /**
         * One Page push state
         */
        const onePagePushState = function () {
            $(
                '.houzez-onepage-mode .header-main-wrap .main-nav li.nav-item a.nav-link'
            ).on('click', function (e) {
                var currentUrl = $(this).attr('href');
                window.history.pushState({ houzezTheme: true }, '', currentUrl);
            });
        };

        /**
         * Mobile offcanvas nav — auto-close on same-page hash link click,
         * then smooth-scroll to target. Works with any template whose mobile
         * menu contains in-page anchors (not only template-onepage.php).
         */
        const onePageMobileNav = function () {
            var offcanvasEl = document.getElementById('hz-offcanvas-mobile-menu');
            if (!offcanvasEl) return;

            var savedScrollY = 0;
            var hashLinkClicked = false;
            var targetHash = '';

            offcanvasEl.addEventListener('show.bs.offcanvas', function () {
                savedScrollY = window.scrollY;
                hashLinkClicked = false;
                targetHash = '';
            });

            offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
                if (hashLinkClicked && targetHash) {
                    var target = null;
                    try { target = document.querySelector(targetHash); } catch (err) {}
                    if (target) {
                        var headerHeight = $('.header-main-wrap').outerHeight() || 0;
                        var targetTop = $(target).offset().top - headerHeight;
                        window.scrollTo({ top: targetTop, behavior: 'smooth' });
                        window.history.pushState({ houzezTheme: true }, '', targetHash);
                    }
                } else {
                    window.scrollTo(0, savedScrollY);
                }
            });

            $(offcanvasEl).on(
                'click',
                '#mobile-main-nav .nav-link, #mobile-main-nav .dropdown-item',
                function (e) {
                    var href = $(this).attr('href');
                    if (!href) return;

                    var parsed;
                    try { parsed = new URL(href, window.location.href); }
                    catch (err) { return; }

                    // Only intercept same-page hash links whose target exists
                    if (parsed.origin !== window.location.origin) return;
                    if (parsed.pathname !== window.location.pathname) return;
                    if (!parsed.hash || parsed.hash === '#') return;

                    var target = null;
                    try { target = document.querySelector(parsed.hash); } catch (err) { return; }
                    if (!target) return;

                    e.preventDefault();
                    hashLinkClicked = true;
                    targetHash = parsed.hash;

                    var bsOffcanvas =
                        bootstrap.Offcanvas.getInstance(offcanvasEl) ||
                        bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
            );
        };

        const houzezBannerSearchAutocomplete = function () {
            const input = $('.search-icon input.form-control');
            const dropdown = $('.auto-complete-banner');

            // Only execute if auto-complete-banner exists
            if (dropdown.length > 0 && input.length > 0) {
                const rect = input[0].getBoundingClientRect();
                const offset = 8; // distanza verticale extra

                dropdown.css({
                    position: 'absolute',
                    top: `${window.scrollY + rect.bottom + offset}px`,
                    left: `${window.scrollX + rect.left + rect.width / 2}px`,
                    transform: 'translateX(-50%)',
                    width: `${rect.width}px`,
                    zIndex: '9999',
                });
            }
        };

        // Initialize core module
        const init = function () {
            $(document).ready(function () {
                LoginRegisterTrigger();
                listingViewSwitch();
                initTooltips();
                initPropertySorting();
                initReviewsTab();
                mobileToggleMenu();
                //handlePageSpecificFunctions();
                //initDynamicModals();
                //is_mobile();
                sortby_mobile();
                gridCallToAction();
                sidebarMenuToggle();
                scrollToTop(); // Initialize scroll-to-top
                stickySidebar();
                houzezBannerSearchAutocomplete();
            });

            houzezLazyload();
            onePagePushState();
            onePageMobileNav();

            document.addEventListener('DOMContentLoaded', checkCollapse);
            // window.addEventListener("resize", checkCollapse);

            // Initialize section height calculations
            util.calculateSectionHeight();
            $window.on('load', util.calculateSectionHeight);
            $window.on('resize', util.calculateSectionHeight);
            $window.on('scroll', houzezBannerSearchAutocomplete);
            $window.on('resize', houzezBannerSearchAutocomplete);
            $window.on('resize', checkCollapse);
        };

        // Public API
        return {
            init: init,
            config: config,
            util: util,
            initTooltips: initTooltips,
            hideAllTooltips: hideAllTooltips,
            initReviewsTab: initReviewsTab,
            listingViewSwitch: listingViewSwitch,
            is_mobile: is_mobile,
            //initDynamicModals: initDynamicModals,
            mobileToggleMenu: mobileToggleMenu,
            //handlePageSpecificFunctions: handlePageSpecificFunctions,
            gridCallToAction: gridCallToAction,
            scrollToTop: scrollToTop, // Expose scrollToTop
            stickySidebar: stickySidebar,
            reinitializeAjaxFunctions: reinitializeAjaxFunctions,
        };
    })();

    houzez.PropertiesTabs = (function () {
        // Flag to track if tabs have been initialized
        let tabsInitialized = false;

        /**
         * Initialize products tabs for Elementor
         */
        const propertiesTabsInit = function () {
            // Exit if already initialized to prevent duplicate event handlers
            if (tabsInitialized) {
                return;
            }

            tabsInitialized = true;
            var alreadyProcessed = false;

            $('.houzez-properties-tabs-js').each(function () {
                var $this = $(this);
                var $html_container = $this.find('.houzez-tab-content');
                var $products_cache = [];
                $this.find('ul.property-nav-tabs li').on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var settings = $this.data('json');
                    var data_index = $this.index();

                    // Only check if processing, not if active
                    if (alreadyProcessed) {
                        return;
                    }

                    // Remove active class from all tabs and add to the clicked tab
                    $this.siblings().find('a').removeClass('active');
                    $this.find('a').addClass('active');

                    alreadyProcessed = true;

                    if ($products_cache[data_index]) {
                        setTimeout(function () {
                            $html_container.html(
                                $products_cache[data_index].html
                            );

                            houzez.Core.reinitializeAjaxFunctions();

                            alreadyProcessed = false;
                        }, 300);
                        return;
                    }

                    $.ajax({
                        url: houzez.Core.config.ajaxurl,
                        data: {
                            action: 'houzez_get_properties_tab_content',
                            settings: settings,
                        },
                        dataType: 'json',
                        method: 'POST',
                        beforeSend: function () {
                            $html_container
                                .empty()
                                .append(
                                    '<div id="houzez-map-loading">' +
                                        '<div class="mapPlaceholder">' +
                                        '<div class="loader-ripple spinner">' +
                                        '<div class="bounce1"></div>' +
                                        '<div class="bounce2"></div>' +
                                        '<div class="bounce3"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>'
                                );
                        },
                        success: function (data) {
                            $products_cache[data_index] = data;
                            $html_container.html(data.html);

                            houzez.Core.reinitializeAjaxFunctions();
                        },
                        error: function (xhr, status, error) {
                            var err = eval('(' + xhr.responseText + ')');
                            console.log(err.Message);
                        },
                        complete: function () {
                            alreadyProcessed = false;
                        },
                    });
                });

                // Set first tab as active by default if none are active
                var $firstTab = $this.find(
                    'ul.property-nav-tabs li:first-child'
                );
                if (!$this.find('ul.property-nav-tabs li a.active').length) {
                    $firstTab.find('a').addClass('active');
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            propertiesTabsInit();
        };

        // Public API
        return {
            init: init,
            propertiesTabsInit: propertiesTabsInit,
        };
    })();

    /**
     * Login Module
     * Handles authentication functions
     */
    houzez.Login = (function () {
        /**
         * Process login form submission
         */
        const processLogin = function (e) {
            // Prevent default form submission
            e.preventDefault();

            // Get form data
            const $form = $(this);
            const $messages = $('#hz-login-messages');
            const config = houzez.Core.config;

            // Check if already processing
            if ($form.hasClass('form-submitted')) {
                return;
            }

            // Mark form as submitted
            $form.addClass('form-submitted');

            // Show processing message
            houzez.Core.util.showSuccess($messages, config.login_loading);

            // Send AJAX request
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: config.ajaxurl,
                data: $form.serialize(),
                beforeSend: function () {
                    $form.find('.btn-login').attr('disabled', true);
                    $form
                        .find('.btn-login .houzez-loader-js')
                        .addClass('loader-show');
                },
                success: function (response) {
                    if (response.success) {
                        houzez.Core.util.showSuccess($messages, response.msg);

                        if (config.redirect_type === 'same_page') {
                            window.location.reload();
                        } else {
                            window.location.href = config.login_redirect;
                        }
                    } else {
                        houzez.Core.util.showError($messages, response.msg);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Login error: ' + error);
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing login'
                    );
                },
                complete: function () {
                    $form.removeClass('form-submitted');
                    $form.find('.btn-login').attr('disabled', false);
                    $form
                        .find('.btn-login .houzez-loader-js')
                        .removeClass('loader-show');
                },
            });

            return false;
        };

        /**
         * Process registration form submission
         */
        const processRegister = function (e) {
            // Prevent default form submission
            e.preventDefault();

            // Get form data
            const $form = $(this);
            const $messages = $('#hz-register-messages');
            const config = houzez.Core.config;

            // Check if already processing
            if ($form.hasClass('form-submitted')) {
                return;
            }

            // Mark form as submitted
            $form.addClass('form-submitted');

            // Show processing message
            houzez.Core.util.showSuccess($messages, config.login_loading);

            submitRegistrationForm();

            function submitRegistrationForm() {
                // Send AJAX request
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: config.ajaxurl,
                    data: $form.serialize(),
                    beforeSend: function () {
                        $form.find('.btn-register').attr('disabled', true);
                        $form
                            .find('.btn-register .houzez-loader-js')
                            .addClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            houzez.Core.util.showSuccess(
                                $messages,
                                response.msg
                            );

                            setTimeout(function () {
                                $('#login-tab').tab('show');
                            }, 2000);
                        } else {
                            houzez.Core.util.showError($messages, response.msg);
                        }
                        // Reset captcha (supports both reCaptcha and Turnstile)
                        houzez.Core.util.resetCaptcha($form);
                    },
                    error: function (xhr, status, error) {
                        console.error('Registration error: ' + error);
                        houzez.Core.util.showError(
                            $messages,
                            'Error processing registration'
                        );
                    },
                    complete: function () {
                        $form.removeClass('form-submitted');
                        $form.find('.btn-register').attr('disabled', false);
                        $form
                            .find('.btn-register .houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            }
        };

        /**
         * Process password reset form
         */
        const processForgotPassword = function (e) {
            // Prevent default form submission
            e.preventDefault();

            // Get form data
            const $form = $(this);
            const user_login = $('#user_login').val();
            const security = $('#fave_resetpassword_security').val();
            const $messages = $('#reset_pass_msg');
            const config = houzez.Core.config;

            // Check if already processing
            if ($form.hasClass('form-submitted')) {
                return;
            }

            // Mark form as submitted
            $form.addClass('form-submitted');

            // Show processing message
            houzez.Core.util.showSuccess($messages, config.login_loading);

            // Send AJAX request
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: config.ajaxurl,
                data: {
                    action: 'houzez_forgot_password',
                    user_login: user_login,
                    security: security,
                },
                beforeSend: function () {
                    $form.find('.btn-reset-password').attr('disabled', true);
                    $form
                        .find('.btn-reset-password .houzez-loader-js')
                        .addClass('loader-show');
                },
                success: function (response) {
                    if (response.success) {
                        houzez.Core.util.showSuccess($messages, response.msg);
                    } else {
                        houzez.Core.util.showError($messages, response.msg);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Reset password error: ' + error);
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing request'
                    );
                },
                complete: function () {
                    $form.removeClass('form-submitted');
                    $form.find('.btn-reset-password').attr('disabled', false);
                    $form
                        .find('.btn-reset-password .houzez-loader-js')
                        .removeClass('loader-show');
                },
            });

            return false;
        };

        /**
         * Process reset password from reset password page
         */
        const processResetPassword = function () {
            if ($('#houzez_reset_password').length > 0) {
                $('#houzez_reset_password_form').on('submit', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var rg_login = $('input[name="rp_login"]').val();
                    var rp_key = $('input[name="rp_key"]').val();
                    var pass1 = $('input[name="pass1"]').val();
                    var pass2 = $('input[name="pass2"]').val();
                    var security = $(
                        'input[name="resetpassword_security"]'
                    ).val();
                    var $messages = $('#reset_pass_msg_2');

                    $.ajax({
                        type: 'post',
                        url: houzez.Core.config.ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'houzez_reset_password',
                            rq_login: rg_login,
                            password: pass1,
                            confirm_pass: pass2,
                            rp_key: rp_key,
                            security: security,
                        },
                        beforeSend: function () {
                            $this
                                .find('.houzez-loader-js')
                                .addClass('loader-show');
                        },
                        complete: function () {
                            $this
                                .find('.houzez-loader-js')
                                .removeClass('loader-show');
                        },
                        success: function (response) {
                            if (response.success) {
                                houzez.Core.util.showSuccess(
                                    $messages,
                                    response.msg
                                );
                                jQuery('#oldpass, #newpass, #confirmpass').val(
                                    ''
                                );
                            } else {
                                houzez.Core.util.showError(
                                    $messages,
                                    response.msg
                                );
                            }
                        },
                        error: function (errorThrown) {},
                    });
                });
            }
        };

        // Initialize login module
        const init = function () {
            // Attach event handlers
            $('#houzez_login_form').on('submit', processLogin);
            $('#houzez_register_form').on('submit', processRegister);
            $('#houzez_forgot_password_form').on(
                'submit',
                processForgotPassword
            );

            processResetPassword();
            // Handle popup login
            $('.hz-popup-login').on('click', function (e) {
                e.preventDefault();
                $('#login-register-form').modal('show');
                $('.login-form-tab').show();
                $('.register-form-tab').hide();
            });

            // Handle popup register
            $('.hz-popup-register').on('click', function (e) {
                e.preventDefault();
                $('#login-register-form').modal('show');
                $('.register-form-tab').show();
                $('.login-form-tab').hide();
            });
        };

        // Public API
        return {
            init: init,
            processLogin: processLogin,
            processRegister: processRegister,
            processForgotPassword: processForgotPassword,
            processResetPassword: processResetPassword,
        };
    })();

    /**
     * Favorites Module
     * Handles property favorites functionality
     */
    houzez.Favorites = (function () {
        // Cache DOM selectors for better performance
        const $document = $(document);
        const $favoriteBtn = $('a.favorite-btn');
        const $favCount = $('span.frvt-count');

        // Common selectors
        const selectors = {
            addFavorite: '.add-favorite-js',
            removeFavorite: '.remove_fav',
            loginRegisterForm: '#login-register-form',
            loginFormTab: '.login-form-tab',
            modalToggle1: '.modal-toggle-1.nav-link',
            registerFormTab: '.register-form-tab',
            modalToggle2: '.modal-toggle-2.nav-link',
        };

        /**
         * Initialize adding a property to favorites
         */
        const initAddFavorite = function () {
            // Remove any existing handlers first to prevent duplicates
            $document.off('click.addFavorite', selectors.addFavorite);

            // Add the handler with namespace for better management
            $document.on(
                'click.addFavorite',
                selectors.addFavorite,
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $this = $(this);
                    const listID = $this.data('listid');

                    add_to_favorite(
                        houzez.Core.config.ajaxurl,
                        listID,
                        $this,
                        houzez.Core.config.userID
                    );
                    return false;
                }
            );
        };

        /**
         * Initialize removing a property from favorites
         */
        const initRemoveFavorite = function () {
            // Remove any existing handlers first to prevent duplicates
            $document.off('click.removeFavorite', selectors.removeFavorite);

            // Add the handler with namespace for better management
            $document.on(
                'click.removeFavorite',
                selectors.removeFavorite,
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $this = $(this);
                    const listID = $this.data('listid');

                    add_to_favorite(
                        houzez.Core.config.ajaxurl,
                        listID,
                        $this,
                        houzez.Core.config.userID
                    );

                    // If on the favorites page, remove the row
                    if ($this.parents('tr').length > 0) {
                        $this.parents('tr').remove();
                    }

                    return false;
                }
            );
        };

        /**
         * Initialize favorite dropdown link navigation
         * Fixes the issue where clicking the favorite link in the dropdown menu
         * doesn't navigate because the parent button has a modal trigger
         */
        const initFavoriteDropdownLink = function () {
            // Remove any existing handlers first to prevent duplicates
            $document.off('click.favoriteDropdown', '.dropdown-menu-favorites .favorite-btn.dropdown-item');

            // Add the handler with namespace for better management
            $document.on(
                'click.favoriteDropdown',
                '.dropdown-menu-favorites .favorite-btn.dropdown-item',
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $this = $(this);
                    const href = $this.attr('href');

                    // Navigate to favorites page if href is valid
                    if (href && href !== '#' && href !== '') {
                        window.location.href = href;
                    }

                    return false;
                }
            );
        };

        /**
         * Show the login modal with login tab active
         */
        const showLoginModal = function () {
            $(selectors.loginRegisterForm).modal('show');
            $(selectors.loginFormTab).addClass('active show');
            $(selectors.modalToggle1).addClass('active');
            $(selectors.registerFormTab).removeClass('active show');
            $(selectors.modalToggle2).removeClass('active');
        };

        /**
         * Update favorite button UI
         * @param {object} $element - jQuery object of the element
         * @param {boolean} isActive - Whether the favorite is active or not
         */
        const updateFavoriteUI = function ($element, isActive) {
            if (isActive) {
                $element.children('i').addClass('text-danger');
                if ($element.hasClass('add-favorite-js')) {
                    $element
                        .removeClass('add-favorite-js')
                        .addClass('remove_fav');
                }
                $element.html(
                    '<i class="houzez-icon icon-love-it text-danger"></i>'
                );
            } else {
                $element.children('i').removeClass('text-danger');
                if ($element.hasClass('remove_fav')) {
                    $element
                        .removeClass('remove_fav')
                        .addClass('add-favorite-js');
                }
                $element.html('<i class="houzez-icon icon-love-it"></i>');
            }
        };

        /**
         * Add a property to favorites (works for both adding and removing)
         * @param {string} ajaxurl - Ajax URL
         * @param {number} listID - Property ID
         * @param {object} curnt - jQuery object of the clicked element
         * @param {number} userID - User ID
         */
        const add_to_favorite = function (ajaxurl, listID, curnt, userID) {
            const config = houzez.Core.config;

            // Handle non-logged in users
            if (parseInt(userID, 10) === 0 || userID === undefined) {
                if (config.add_to_favorite_login_required != 0) {
                    // If login is required, show login modal
                    showLoginModal();
                } else {
                    // If login is not required, handle with cookies
                    add_to_favorite_with_cookies(curnt, listID);
                }
                return;
            }

            // Handle logged in users with AJAX
            const $parents = curnt.parents('.item-wrap');
            const preview_loader = $parents.find('.preview_loader');

            $.ajax({
                type: 'post',
                url: houzez.Core.config.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'houzez_add_to_favorite',
                    listing_id: listID,
                },
                beforeSend: function () {
                    if (preview_loader.length) {
                        preview_loader
                            .empty()
                            .append(houzez.Core.util.getLoaderHtml());
                    } else {
                        curnt.html('<i class="houzez-loader-js"></i>');
                        curnt.find('.houzez-loader-js').addClass('loader-show');
                    }
                },
                complete: function () {
                    if (preview_loader.length) {
                        preview_loader.empty();
                    }
                },
                success: function (response) {
                    if (response.success) {
                        const isAdded = response.data.added;
                        updateFavoriteUI(curnt, isAdded);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                },
            });
        };

        /**
         * Update favorite count and URL
         * @param {Array} listings_favorite - Array of favorite listing IDs
         */
        const updateFavoriteCountAndUrl = function (listings_favorite) {
            $favCount.html('');
            $favCount.html(listings_favorite.length);

            $favoriteBtn.attr(
                'href',
                houzez.Core.config.favorite_url +
                    '?ids=' +
                    listings_favorite.join(',')
            );
        };

        /**
         * Add to favorite without login using cookies
         * @param {object} curnt - jQuery object of the clicked element
         * @param {number} listing_id - Listing ID
         */
        const add_to_favorite_with_cookies = function (curnt, listing_id) {
            let listings_favorite = houzez.Core.util.getCookie(
                'houzez_favorite_listings'
            );

            if (listings_favorite && listings_favorite.length) {
                listings_favorite = listings_favorite.split(',');
            } else {
                listings_favorite = [];
            }

            const index = listings_favorite.indexOf(listing_id.toString());
            const isAdding = index === -1;

            if (isAdding) {
                // Add to favorites
                listings_favorite.push(listing_id.toString());
            } else {
                // Remove from favorites
                listings_favorite.splice(index, 1);
            }

            // Save cookie
            houzez.Core.util.setCookie(
                'houzez_favorite_listings',
                listings_favorite.join(','),
                30
            );

            // Update UI
            updateFavoriteCountAndUrl(listings_favorite);
            updateFavoriteUI(curnt, isAdding);
        };

        /**
         * Check favorites for user (matches original implementation)
         * @param {number} userID - User ID
         */
        const check_favorites = function (userID) {
            if (parseInt(userID, 10) === 0 || userID === undefined) {
                let listings_favorite = houzez.Core.util.getCookie(
                    'houzez_favorite_listings'
                );

                $favoriteBtn.attr(
                    'href',
                    houzez.Core.config.favorite_url +
                        '?ids=' +
                        listings_favorite
                );

                if (listings_favorite && listings_favorite.length) {
                    listings_favorite = listings_favorite.split(',');

                    if (listings_favorite.length) {
                        for (let i = 0; i < listings_favorite.length; i++) {
                            $(
                                `.add-favorite-js[data-listid="${listings_favorite[i]}"] i`
                            ).addClass('text-danger');
                            $(
                                `.add-favorite-js[data-listid="${listings_favorite[i]}"]`
                            ).addClass('remove-favorite');
                        }

                        $favCount.html('');
                        $favCount.html(listings_favorite.length);
                    }
                } else {
                    listings_favorite = [];
                }
            }
            // No else block - no AJAX call for logged-in users in original code
        };

        // Initialize favorites module
        const init = function () {
            initAddFavorite();
            initRemoveFavorite();
            initFavoriteDropdownLink();
            check_favorites(houzez.Core.config.userID);
        };

        // Public API
        return {
            init: init,
            initAddFavorite: initAddFavorite,
            initRemoveFavorite: initRemoveFavorite,
            initFavoriteDropdownLink: initFavoriteDropdownLink,
            add_to_favorite: add_to_favorite,
            remove_from_favorite: add_to_favorite, // Use the same function for consistency
            check_favorites: check_favorites,
            showLoginModal: showLoginModal,
        };
    })();

    /**
     * Compare Module
     * Handles property comparison functionality
     */
    houzez.Compare = (function () {
        // Limit for comparison items
        const limit_item_compare = 4;

        /**
         * Add property to compare
         */
        function add_to_compare(
            compare_url,
            compare_add_icon,
            compare_remove_icon,
            add_compare_text,
            remove_compare_text,
            compare_limit,
            listings_compare,
            limit_item_compare
        ) {
            var storedData = localStorage.getItem('houzez_compare_listings');
            var listings_compare = storedData ? JSON.parse(storedData) : [];

            $('a.compare-btn').attr(
                'href',
                compare_url +
                    '?ids=' +
                    listings_compare.map((item) => item.id).join(',')
            );

            if (listings_compare.length > 0) {
                $('.compare-property-label').fadeIn(1000);
            }

            if (listings_compare.length) {
                for (var i = 0; i < listings_compare.length; i++) {
                    $(
                        '.houzez_compare[data-listing_id="' +
                            listings_compare[i].id +
                            '"] i'
                    )
                        .removeClass('icon-add-circle')
                        .addClass('icon-subtract-circle');
                    $(
                        '.houzez_compare[data-listing_id="' +
                            listings_compare[i].id +
                            '"]'
                    ).attr('title', remove_compare_text);
                    $(
                        '.houzez_compare[data-listing_id="' +
                            listings_compare[i].id +
                            '"]'
                    )
                        .tooltip('hide')
                        .attr('data-original-title', remove_compare_text);
                }
                $('.compare-property-label')
                    .find('.compare-count')
                    .html(listings_compare.length);
            }
        }

        /**
         * Handle compare button click
         */
        const handleCompareButtonClick = function (
            compare_url,
            compare_add_icon,
            compare_remove_icon,
            add_compare_text,
            remove_compare_text,
            compare_limit,
            limit_item_compare
        ) {
            $('.houzez_compare')
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();

                    var storedData = localStorage.getItem(
                        'houzez_compare_listings'
                    );
                    var listings_compare = storedData
                        ? JSON.parse(storedData)
                        : [];

                    var listing_id = $(this).data('listing_id');
                    var index = listings_compare.findIndex(
                        (item) => item.id === listing_id.toString()
                    );
                    var image_div = $(this).parents('.item-wrap');
                    var thumb_url = image_div.find('img').attr('src');

                    if (index == -1) {
                        if (listings_compare.length >= limit_item_compare) {
                            alert(compare_limit);
                        } else {
                            $('.compare-wrap').append(
                                '<div class="compare-item remove-' +
                                    listing_id +
                                    '"><a href="" class="remove-compare remove-icon" data-listing_id="' +
                                    listing_id +
                                    '"><i class="houzez-icon icon-remove-circle"></i></a><img class="img-fluid" src="' +
                                    thumb_url +
                                    '" width="200" height="150" alt="Thumb"></div>'
                            );

                            listings_compare.push({
                                id: listing_id.toString(),
                                image: thumb_url,
                            });
                            localStorage.setItem(
                                'houzez_compare_listings',
                                JSON.stringify(listings_compare)
                            );
                            $(this).attr('title', remove_compare_text);
                            $(this)
                                .find('i')
                                .removeClass('icon-add-circle')
                                .addClass('icon-subtract-circle');
                            $('.compare-property-label')
                                .find('.compare-count')
                                .html(listings_compare.length);
                            $('a.compare-btn').attr(
                                'href',
                                compare_url +
                                    '?ids=' +
                                    listings_compare
                                        .map((item) => item.id)
                                        .join(',')
                            );
                            $('.compare-property-label').fadeIn(1000);
                            $(this).toggleClass('active');
                            $('.compare-property-active').addClass(
                                'compare-property-active-push-toleft'
                            );
                            $('#compare-property-panel').addClass(
                                'compare-property-panel-open'
                            );
                            $(this).tooltip('dispose').tooltip('show');
                            remove_from_compare(
                                listings_compare,
                                compare_add_icon,
                                compare_remove_icon,
                                add_compare_text,
                                remove_compare_text
                            );
                        }
                    } else {
                        $('div.remove-' + listing_id).remove();
                        $(this).attr('title', add_compare_text);
                        $(this)
                            .find('i')
                            .removeClass('icon-subtract-circle')
                            .addClass('icon-add-circle');
                        listings_compare.splice(index, 1);
                        localStorage.setItem(
                            'houzez_compare_listings',
                            JSON.stringify(listings_compare)
                        );
                        $('.compare-property-label')
                            .find('.compare-count')
                            .html(listings_compare.length);
                        $('a.compare-btn').attr(
                            'href',
                            compare_url +
                                '?ids=' +
                                listings_compare
                                    .map((item) => item.id)
                                    .join(',')
                        );
                        $(this).tooltip('dispose').tooltip('show');

                        if (listings_compare.length > 0) {
                            $('.compare-property-label').fadeIn(1000);
                            $(this).toggleClass('active');
                            $('.compare-property-active').addClass(
                                'compare-property-active-push-toleft'
                            );
                            $('#compare-property-panel').addClass(
                                'compare-property-panel-open'
                            );
                        } else {
                            $('.compare-property-label').fadeOut(1000);
                        }
                    }
                    return false;
                });
        };

        /**
         * Remove property from compare
         */
        function remove_from_compare(
            listings_compare,
            compare_add_icon,
            compare_remove_icon,
            add_compare_text,
            remove_compare_text
        ) {
            $('.remove-compare')
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();
                    const config = houzez.Core.config;
                    var compare_url = config.compare_url;
                    var storedData = localStorage.getItem(
                        'houzez_compare_listings'
                    );
                    listings_compare = storedData ? JSON.parse(storedData) : [];

                    var listing_id = $(this).data('listing_id');
                    var index = listings_compare.findIndex(
                        (item) => item.id === listing_id.toString()
                    );

                    if (index !== -1) {
                        // Only proceed if the item was found
                        listings_compare.splice(index, 1);
                        localStorage.setItem(
                            'houzez_compare_listings',
                            JSON.stringify(listings_compare)
                        );

                        $('.compare-property-label')
                            .find('.compare-count')
                            .html(listings_compare.length);

                        // Update UI elements if they exist
                        var compareElement = $('.compare-' + listing_id);
                        if (compareElement.length) {
                            compareElement.attr('title', add_compare_text);
                            compareElement
                                .tooltip('hide')
                                .attr('data-original-title', add_compare_text);
                            compareElement
                                .find('i')
                                .removeClass('icon-subtract-circle')
                                .addClass('icon-add-circle');
                        }

                        $(this).parents('.compare-item').remove();

                        // Update the compare URL
                        $('a.compare-btn').attr(
                            'href',
                            compare_url +
                                '?ids=' +
                                listings_compare
                                    .map((item) => item.id)
                                    .join(',')
                        );
                    }
                });
        }

        /**
         * Initialize the compare panel toggle functionality
         */
        const initComparePanel = function () {
            $('.show-compare-panel').on('click', function () {
                $(this).toggleClass('active');
                $('.compare-property-active').addClass(
                    'compare-property-active-push-toleft'
                );
                $('#compare-property-panel').addClass(
                    'compare-property-panel-open'
                );
            });

            $('.close-compare-panel').on('click', function () {
                $(this).toggleClass('active');
                $('.compare-property-active').removeClass(
                    'compare-property-active-push-toleft'
                );
                $('#compare-property-panel').removeClass(
                    'compare-property-panel-open'
                );
            });
        };

        /**
         * Update compare properties after AJAX operations
         * Used for refreshing comparison after content loads via AJAX
         */
        const compare_for_ajax = function () {
            const config = houzez.Core.config;
            var listings_compare = houzez.Core.util.getCookie(
                'houzez_compare_listings'
            );

            add_to_compare(
                config.compare_url,
                config.compare_add_icon,
                config.compare_remove_icon,
                config.add_compare_text,
                config.remove_compare_text,
                config.compare_limit,
                listings_compare,
                limit_item_compare
            );

            // Set up event handlers for compare buttons
            handleCompareButtonClick(
                config.compare_url,
                config.compare_add_icon,
                config.compare_remove_icon,
                config.add_compare_text,
                config.remove_compare_text,
                config.compare_limit,
                limit_item_compare
            );

            remove_from_compare(
                listings_compare,
                config.compare_add_icon,
                config.compare_remove_icon,
                config.add_compare_text,
                config.remove_compare_text
            );
        };

        /**
         * Render stored comparison properties in the compare panel
         */
        const renderComparePropertiesData = function () {
            let compare_listings = JSON.parse(
                localStorage.getItem('houzez_compare_listings')
            );

            // Check if 'compare_listings' is not null and not empty before processing
            if (compare_listings && compare_listings.length > 0) {
                let properties_array = '';
                compare_listings.forEach(function (item) {
                    let img = item.image; // Image URL from the local storage array
                    let listingId = item.id; // Listing ID from the local storage array

                    properties_array +=
                        '<div class="compare-item remove-' +
                        listingId +
                        '">' +
                        '<a href="#" class="remove-compare remove-icon" data-listing_id="' +
                        listingId +
                        '">' +
                        '<i class="houzez-icon icon-remove-circle"></i></a>' +
                        '<img class="img-fluid" src="' +
                        img +
                        '" width="200" height="150" alt="Thumb">' +
                        '</div>';
                });

                $('.compare-wrap').html(properties_array);
            }
        };

        /**
         * Initialize the compare module
         */
        const init = function () {
            initComparePanel();
            compare_for_ajax();
            renderComparePropertiesData();

            // Call remove_from_compare again after items are rendered to ensure event handlers are attached
            const compare_listings =
                JSON.parse(localStorage.getItem('houzez_compare_listings')) ||
                [];
            remove_from_compare(
                compare_listings,
                houzez.Core.config.compare_add_icon,
                houzez.Core.config.compare_remove_icon,
                houzez.Core.config.add_compare_text,
                houzez.Core.config.remove_compare_text
            );
        };

        // Public API
        return {
            init: init,
            add_to_compare: add_to_compare,
            remove_from_compare: remove_from_compare,
            compare_for_ajax: compare_for_ajax,
        };
    })();

    /**
     * Sliders Module
     * Handles all slider and carousel functionality across the theme
     */
    houzez.Sliders = (function () {
        // Cache DOM selectors for better performance
        const $document = $(document);
        const $window = $(window);

        // Common Slick slider settings
        const commonSettings = {
            rtl: houzez.Core.config.houzez_rtl,
            lazyLoad: 'ondemand',
            adaptiveHeight: true,
            speed: 500,
            arrows: true,
        };

        const commonResponsiveSettings = [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
        ];

        const commonArrowButtons = {
            prevArrow: `<button type="button" class="slick-prev btn-primary-outlined">${houzez.Core.config.prev_text}</button>`,
            nextArrow: `<button type="button" class="slick-next btn-primary-outlined">${houzez.Core.config.next_text}</button>`,
        };

        /**
         * Initialize slider with error handling
         * @param {jQuery} $element - jQuery element to initialize slider on
         * @param {Object} settings - Slick slider settings
         */
        const initSlider = function ($element, settings) {
            try {
                if (
                    $element &&
                    $element.length > 0 &&
                    typeof $.fn.slick === 'function'
                ) {
                    // Check if slick is already initialized
                    if ($element.hasClass('slick-initialized')) {
                        $element.slick('unslick');
                    }

                    $element.slick({
                        ...commonSettings,
                        ...settings,
                    });
                } else if (!$element || $element.length === 0) {
                    console.warn('Slider element not found or empty');
                } else if (typeof $.fn.slick !== 'function') {
                    console.warn('Slick slider library not loaded');
                }
            } catch (error) {
                console.error('Error initializing slider:', error);
            }
        };

        /**
         * Initialize custom carousel
         */
        const customCarousel = function () {
            const $customCarousel = $('.custom-carousel');
            if ($customCarousel.length > 0) {
                $customCarousel.each(function () {
                    const $this = $(this);
                    const token = $this.data('token');
                    const carouselSettings = $this.data('carousel');

                    initSlider($('.custom-carousel-js-' + token), {
                        autoplay: carouselSettings.slide_auto === 'true',
                        autoplaySpeed:
                            parseInt(carouselSettings.auto_speed) || 3000,
                        infinite: carouselSettings.slide_infinite === 'true',
                        speed: 500,
                        slidesToShow:
                            parseInt(carouselSettings.slides_to_show) || 3,
                        slidesToScroll:
                            parseInt(carouselSettings.slides_to_scroll) || 1,
                        arrows: carouselSettings.navigation === 'true',
                        dots: carouselSettings.slide_dots === 'true',
                        appendArrows: '.custom-carousel-js-wrap-' + token,
                        prevArrow: $('.slick-prev-js-' + token),
                        nextArrow: $('.slick-next-js-' + token),
                        responsive: commonResponsiveSettings,
                    });
                });
            }
        };

        /**
         * Initialize property detail gallery with optimized loading experience
         * Improved for faster display and better mobile performance
         */
        const propertyDetailGallery = function () {
            var property_detail_gallery = $('#property-gallery-js');

            if (property_detail_gallery.length > 0) {
                // Check if already has a lightSlider instance
                // var existingInstance = property_detail_gallery.data('lightSlider');
                // if (existingInstance && typeof existingInstance.refresh === 'function') {
                //     // Refresh existing instance instead of recreating
                //     existingInstance.refresh();
                //     return;
                // }

                if (!property_detail_gallery.hasClass('gallery-initialized')) {
                    try {
                        // Detect mobile for optimized timing
                        var isMobile = houzez.Core.is_mobile();

                        // Get all images
                        var $images = property_detail_gallery.find(
                            '.houzez-gallery-img'
                        );
                        var totalImages = $images.length;
                        var initialLoadCount = Math.min(3, totalImages); // Load first 3 or less
                        var loadedCount = 0;
                        var galleryInitialized = false;
                        var sliderInitialized = false;

                        // Function to initialize the slider and show gallery
                        var initializeSliderAndGallery = function () {
                            if (sliderInitialized) return;
                            sliderInitialized = true;

                            // Small delay to let browser paint the visible element
                            setTimeout(function () {
                                // Show slider div before initializing (lightSlider needs it visible to measure)
                                property_detail_gallery.css('display', 'block');

                                // Initialize slider on visible element
                                var slider =
                                    property_detail_gallery.lightSlider({
                                        rtl: houzez.Core.config.houzez_rtl,
                                        gallery: true,
                                        item: 1,
                                        thumbItem: 8,
                                        slideMargin: 0,
                                        speed: 500,
                                        adaptiveHeight: false,
                                        auto: false,
                                        loop: false,
                                        prevHtml:
                                            '<button type="button" class="slick-prev slick-arrow"></button>',
                                        nextHtml:
                                            '<button type="button" class="slick-next slick-arrow"></button>',
                                        onSliderLoad: function (el) {
                                            var _this = this;

                                            // Store instance for later reference
                                            property_detail_gallery.data(
                                                'lightSlider',
                                                _this
                                            );

                                            // Add class to parent to show slider and hide placeholder
                                            $(el)
                                                .closest('.top-gallery-section')
                                                .addClass('slider-loaded');

                                            // Hide featured image placeholder now that gallery is ready
                                            // Delay slightly to ensure smooth LCP transition
                                            setTimeout(function () {
                                                var $placeholder = $(
                                                    '#gallery-featured-placeholder'
                                                );
                                                if ($placeholder.length > 0) {
                                                    $placeholder.addClass(
                                                        'hide-placeholder'
                                                    );
                                                }
                                            }, 100);

                                            // Mark gallery as initialized
                                            property_detail_gallery.addClass(
                                                'gallery-initialized'
                                            );
                                            galleryInitialized = true;

                                            // Load remaining images after gallery is visible
                                            if (
                                                totalImages > initialLoadCount
                                            ) {
                                                $images
                                                    .slice(initialLoadCount)
                                                    .each(function (index) {
                                                        var $img = $(this);
                                                        var lazySrc =
                                                            $img.data('lazy');
                                                        if (lazySrc) {
                                                            // Stagger loading to avoid blocking
                                                            setTimeout(
                                                                function () {
                                                                    var remainingImg =
                                                                        new Image();
                                                                    remainingImg.onload =
                                                                        function () {
                                                                            $img.attr(
                                                                                'src',
                                                                                lazySrc
                                                                            );
                                                                        };
                                                                    remainingImg.onerror =
                                                                        function () {
                                                                            // Set src anyway so browser can handle broken image
                                                                            $img.attr(
                                                                                'src',
                                                                                lazySrc
                                                                            );
                                                                        };
                                                                    remainingImg.src =
                                                                        lazySrc;
                                                                },
                                                                index * 50
                                                            );
                                                        }
                                                    });
                                            }
                                        },
                                    });

                                // Store instance reference
                                property_detail_gallery.data(
                                    'lightSlider',
                                    slider
                                );
                            }, 100); // 10ms delay for browser paint
                        };
                        initializeSliderAndGallery();
                    } catch (error) {
                        // Error handling: Show gallery even if initialization fails
                        console.error('Gallery initialization error:', error);
                        property_detail_gallery.removeClass(
                            'gallery-preparing'
                        );
                        property_detail_gallery.addClass('gallery-initialized');
                    }
                }
            }
        };

        /**
         * Initialize lightbox slider
         */
        const lightboxSlider = function () {
            const $lightboxSlider = $('#lightbox-slider-js');
            if ($lightboxSlider.length > 0) {
                initSlider($lightboxSlider, {
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: false,
                    autoplaySpeed: 3000,
                    arrows: true,
                    dots: false,
                    responsive: [],
                });

                $('.houzez-trigger-popup-slider-js').on('click', function (e) {
                    e.preventDefault();

                    let slider_num = parseInt($(this).data('slider-no'));
                    setTimeout(function () {
                        $lightboxSlider.slick('slickGoTo', slider_num - 1);
                    }, 200);
                });
            }
        };

        /**
         * Initialize variable width slider
         */
        const variableWidthSlider = function () {
            const $variableWidthSlider = $('.listing-slider-variable-width');
            if ($variableWidthSlider.length > 0) {
                initSlider($variableWidthSlider, {
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    centerMode: true,
                    variableWidth: true,
                    arrows: true,
                    adaptiveHeight: true,
                });

                // $('.property-detail-v5 #pills-gallery-tab').on('click', function () {
                //     if (!listing_slider_variable_width.hasClass('hz-slick-refreshed')) {
                //         setTimeout(function () {
                //             listing_slider_variable_width.slick('setPosition');
                //             listing_slider_variable_width.slick('refresh');
                //             listing_slider_variable_width.addClass(
                //                 'hz-slick-refreshed'
                //             );
                //         }, 0);
                //     }
                // });
            }
        };

        /**
         * Initialize testimonials sliders
         */
        const testimonialsSliderV1 = function () {
            const $testimonialsSliderV1 = $('.testimonials-slider-wrap-v1');
            if ($testimonialsSliderV1.length > 0) {
                initSlider($testimonialsSliderV1, {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 4000,
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 500,
                    fade: false,
                    cssEase: 'ease',
                    slide: 'div',
                    appendArrows: '.testimonials-slider-wrap-v1-arrows',
                    prevArrow:
                        '<button type="button" class="slick-prev testimonials-slider-v1-prev"></button>',
                    nextArrow:
                        '<button type="button" class="slick-next testimonials-slider-v1-next"></button>',
                });
            }
        };

        const testimonialsSliderV2 = function () {
            const $testimonialsSliderV2 = $('.testimonials-slider-wrap-v2');
            if ($testimonialsSliderV2.length > 0) {
                initSlider($testimonialsSliderV2, {
                    infinite: true,
                    speed: 500,
                    slidesToShow: 3,
                    arrows: true,
                    dots: true,
                    appendArrows: '.testimonials-slider-wrap-v2-arrows',
                    prevArrow:
                        '<button type="button" class="slick-prev testimonials-slider-v2-prev"></button>',
                    nextArrow:
                        '<button type="button" class="slick-next testimonials-slider-v2-next"></button>',
                    responsive: commonResponsiveSettings,
                });
            }
        };

        const testimonialsSliderV3 = function () {
            const $testimonialsSliderV3 = $('.testimonials-slider-wrap-v3');
            if ($testimonialsSliderV3.length > 0) {
                initSlider($testimonialsSliderV3, {
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    adaptiveHeight: true,
                    arrows: true,
                    dots: false,
                    appendArrows: '.testimonials-slider-wrap-v3-arrows',
                    prevArrow:
                        '<button type="button" class="slick-prev btn-primary testimonials-slider-v3-prev"></button>',
                    nextArrow:
                        '<button type="button" class="slick-next btn-primary testimonials-slider-v3-next"></button>',
                });
            }
        };

        /**
         * Initialize agents carousel
         */
        const agentsCarousel = function () {
            const $agentsCarousel = $('.agents-carousel');
            if ($agentsCarousel.length > 0) {
                initSlider($agentsCarousel, {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    speed: 500,
                    autoplaySpeed: 3000,
                    arrows: true,
                    dots: false,
                    responsive: commonResponsiveSettings,
                });
            }
        };

        /**
         * Initialize partners carousel
         */
        const partnersCarousel = function () {
            const $partnersCarousel = $('.partners-slider-wrap');
            if ($partnersCarousel.length > 0) {
                initSlider($partnersCarousel, {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    arrows: true,
                    dots: true,
                    appendArrows: '.partners-module-slider',
                    prevArrow: $('.partner-prev-js'),
                    nextArrow: $('.partner-next-js'),
                    responsive: commonResponsiveSettings,
                });
            }
        };

        /**
         * Initialize partners carousel
         */
        const splashSlider = function () {
            const $splashSlider = $('.splash-slider-wrap');
            if ($splashSlider.length > 0) {
                initSlider($splashSlider, {
                    lazyLoad: 'ondemand',
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    infinite: true,
                    autoplaySpeed: 3000,
                    arrows: false,
                    dots: false,
                });
            }
        };

        const propertyScheduleTourDayFormSlide = function () {
            const $propertyScheduleTourDayFormSlide = $(
                '.property-schedule-tour-day-form-slide'
            );
            if ($propertyScheduleTourDayFormSlide.length > 0) {
                initSlider($propertyScheduleTourDayFormSlide, {
                    infinite: true,
                    lazyLoad: 'ondemand',
                    speed: 500,
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    arrows: true,
                    adaptiveHeight: false,
                    dots: false,
                    prevArrow:
                        '<div class="tour-day-form-slide-arrow prev" role="button" aria-label="Previous dates"><i class="houzez-icon icon-arrow-left-1" aria-hidden="true"></i></div>',
                    nextArrow:
                        '<div class="tour-day-form-slide-arrow next" role="button" aria-label="Next dates"><i class="houzez-icon icon-arrow-right-1" aria-hidden="true"></i></div>',
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                            },
                        },
                        {
                            breakpoint: 769,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                            },
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                            },
                        },
                    ],
                });
            }
        };

        /**
         * Initialize featured property widget
         */
        const featuredPropertyWidget = function () {
            const $featuredPropertyWidget = $(
                '.widget-featured-property-slider'
            );
            if ($featuredPropertyWidget.length > 0) {
                initSlider($featuredPropertyWidget, {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    arrows: true,
                    dots: false,
                    responsive: [],
                });
            }
        };

        /**
         * Initialize property banner slider
         */
        const propertyBannerSlider = function () {
            const $propertyBannerSlider = $('.property-banner-slider');
            if ($propertyBannerSlider.length > 0) {
                initSlider($propertyBannerSlider, {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 4000,
                    arrows: true,
                    dots: false,
                    responsive: [],
                });
            }
        };

        /**
         * Initialize splash page slider
         */
        const splashPageSlider = function () {
            const $splashPageSlider = $('.splash-slider');
            if ($splashPageSlider.length > 0) {
                initSlider($splashPageSlider, {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    arrows: false,
                    dots: true,
                    responsive: [],
                });
            }
        };

        /**
         * Initialize grid image gallery for property listings
         */
        const gridImageGallery = function () {
            var is_listing_gallery_slider = false;
            if (houzez_vars.disable_property_gallery == 1) {
                let gallery_behaviour = houzez_vars.grid_gallery_behaviour;

                $('.hz-item-gallery-js').each(function () {
                    let $this = $(this);

                    if (
                        !$this.hasClass('houzez-gallery-loaded') &&
                        $this.data('images')
                    ) {
                        // First check if URL was previously stored
                        var href = $this.data('property-url');
                        if (!href) {
                            // Try to find the link
                            href = $this
                                .find('a.listing-featured-thumb')
                                .attr('href');
                            // Store it for future re-initializations
                            if (href) {
                                $this.data('property-url', href);
                            }
                        }

                        // Similarly for link_target
                        var link_target = $this.data('property-target');
                        if (!link_target) {
                            link_target = $this
                                .find('a.listing-featured-thumb')
                                .attr('target');
                            if (link_target) {
                                $this.data('property-target', link_target);
                            }
                        }
                        var images = $this.data('images');

                        // Create document fragment for better performance
                        var fragment = document.createDocumentFragment();
                        var galleryWrap = document.createElement('div');
                        galleryWrap.className =
                            'listing-gallery-wrap ' + gallery_behaviour;

                        var carouselDiv = document.createElement('div');
                        carouselDiv.className = 'houzez-listing-carousel';

                        // Clear existing content before adding new elements
                        carouselDiv.innerHTML = '';

                        images.forEach((image) => {
                            const itemDiv = document.createElement('div');
                            itemDiv.className = 'slide-img';

                            const anchor = document.createElement('a');
                            anchor.className = 'hover-effect'; // Added wf class from your edit
                            anchor.href = href;
                            anchor.target = link_target;
                            anchor.setAttribute('role', 'link');

                            const img = document.createElement('img');
                            img.src = image.image;
                            img.alt = image.alt || ''; // Ensure alt is not undefined
                            img.width = image.width;
                            img.height = image.height;
                            img.className = 'img-fluid';

                            // Add srcset and sizes attributes if available
                            if (image.srcset) {
                                img.srcset = image.srcset;
                            }
                            if (image.sizes) {
                                img.sizes = image.sizes;
                            }

                            anchor.appendChild(img);
                            itemDiv.appendChild(anchor);
                            carouselDiv.appendChild(itemDiv);
                        });

                        galleryWrap.appendChild(carouselDiv);
                        fragment.appendChild(galleryWrap);

                        $this.find('.listing-image-wrap').html(fragment);

                        $('.item-wrap-v6 a').removeClass('hover-effect');

                        var listing_slider = $this.find(
                            '.houzez-listing-carousel'
                        );

                        listing_slider.slick({
                            rtl: houzez.Core.config.houzez_rtl,
                            autoplay: false,
                            lazyLoad: 'ondemand',
                            infinite: false,
                            speed: 500,
                            slidesToShow: 1,
                            arrows: true,
                            prevArrow:
                                '<button type="button" class="slick-prev slick-arrow"></button>',
                            nextArrow:
                                '<button type="button" class="slick-next slick-arrow"></button>',
                            adaptiveHeight: true,
                        });

                        is_listing_gallery_slider = true;
                        $this.addClass('houzez-gallery-loaded');
                    }
                });
            }
        };

        /**
         * Initialize the module
         */
        const init = function () {
            // Initialize functions that need DOM ready
            $(document).ready(function () {
                // Initialize grid gallery on document ready for better reliability
                gridImageGallery();

                // Initialize other components that work well with DOM ready
                customCarousel();
                partnersCarousel();
                featuredPropertyWidget();
            });

            // Initialize functions that need all resources loaded
            $(window).on('load', function () {
                propertyDetailGallery();
                lightboxSlider();
                variableWidthSlider();
                testimonialsSliderV1();
                testimonialsSliderV2();
                testimonialsSliderV3();
                agentsCarousel();
                customCarousel();
                partnersCarousel();
                propertyBannerSlider();
                featuredPropertyWidget();
                splashPageSlider();
                gridImageGallery(); // Keep here too as fallback
                splashSlider();
                propertyScheduleTourDayFormSlide();
            });

            // Fallback initialization for cached pages
            setTimeout(function () {
                // Check if galleries exist but aren't initialized
                if (
                    $('.hz-item-gallery-js').length > 0 &&
                    $('.hz-item-gallery-js:not(.houzez-gallery-loaded)')
                        .length > 0
                ) {
                    gridImageGallery();
                }
            }, 500);
        };

        // Public API
        return {
            init: init,
            initSlider: initSlider,
            customCarousel: customCarousel,
            propertyBannerSlider: propertyBannerSlider,
            propertyDetailGallery: propertyDetailGallery,
            lightboxSlider: lightboxSlider,
            variableWidthSlider: variableWidthSlider,
            testimonialsSliderV1: testimonialsSliderV1,
            agentsCarousel: agentsCarousel,
            partnersCarousel: partnersCarousel,
            //partnersSlider: partnersSlider,
            featuredPropertyWidget: featuredPropertyWidget,
            splashPageSlider: splashPageSlider,
            gridImageGallery: gridImageGallery,
            testimonialsSliderV2: testimonialsSliderV2,
            testimonialsSliderV3: testimonialsSliderV3,
            propertyScheduleTourDayFormSlide: propertyScheduleTourDayFormSlide,
        };
    })();

    /**
     * Pagination Module
     * Handles AJAX pagination and infinite loading for property listings
     */
    houzez.Pagination = (function () {
        // Variables for tracking pagination state
        let moduleLoading = false;
        let listingsLoading = false;
        let moduleObserver = null;
        let listingsObserver = null;
        let loadMoreButton = $('#properties_module_section .fave-load-more a');
        let paginationType =
            loadMoreButton.data('pagination_type') || 'loadmore';

        /**
         * Load more properties with AJAX
         * Used in property modules section with load more button
         * @param {Event} e - Click event
         */
        const loadMoreModuleProperties = function (e) {
            if (e) e.preventDefault();
            if (moduleLoading) {
                return;
            }

            const $this = $(this);
            const $wrap = $this
                .closest('#properties_module_section')
                .find('#module_properties');

            // Initialize the data object with the action
            let data = {
                action: 'houzez_loadmore_properties',
            };

            // Dynamically collect all data attributes
            const allData = $this.data();

            // Merge all data attributes into the data object
            $.each(allData, function (key, value) {
                data[key] = value;
            });

            moduleLoading = true;
            $.ajax({
                type: 'POST',
                url: houzez.Core.config.ajaxurl,
                dataType: 'json',
                data: data,
                beforeSend: function () {
                    $this.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    $this.find('.houzez-loader-js').removeClass('loader-show');
                    moduleLoading = false;
                },
                success: function (data) {
                    if (data.html == 'no_result') {
                        $this
                            .closest('#properties_module_section')
                            .find('.fave-load-more')
                            .fadeOut('fast')
                            .remove();
                        return;
                    }

                    $wrap.append(data.html);
                    $this.data('paged', parseInt($this.data('paged')) + 1);
                    $this.find('i').remove();

                    if (!data.has_more_posts) {
                        $this
                            .closest('#properties_module_section')
                            .find('.fave-load-more')
                            .fadeOut('fast')
                            .remove();
                    } else if (paginationType === 'infinite_scroll') {
                        // Re-observe the button after new content is loaded
                        observeModuleLoadMoreButton();
                    }

                    // Use the Core function instead of local one
                    houzez.Core.reinitializeAjaxFunctions();
                },
                error: function (xhr, status, error) {
                    moduleLoading = false;
                    console.error('AJAX error:', error);
                },
            });
        };

        const observeModuleLoadMoreButton = function () {
            if (moduleObserver) {
                moduleObserver.disconnect();
            }

            const loadMoreButton = document.querySelector(
                '#properties_module_section .fave-load-more a'
            );

            if (!loadMoreButton) return;

            moduleObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting && !moduleLoading) {
                            loadMoreModuleProperties.call(entry.target);
                        }
                    });
                },
                {
                    root: null,
                    rootMargin: '0px 0px 300px 0px', // Increased bottom margin for earlier detection
                    threshold: 0.1,
                }
            );

            moduleObserver.observe(loadMoreButton);
        };

        /**
         * Load more listings for main listings view
         */
        const loadMoreListings = function () {
            if (listingsLoading || this.noMoreListings) {
                return;
            }

            listingsLoading = true;
            const $loadMoreBtn = $('.houzez-infinite-load');
            const nextPageUrl = $loadMoreBtn.attr('href');

            $.ajax({
                url: nextPageUrl,
                type: 'GET',
                dataType: 'html',
                beforeSend: function () {
                    $loadMoreBtn
                        .find('.houzez-loader-js')
                        .addClass('loader-show');
                },
                complete: function () {
                    $loadMoreBtn
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                },
                success: function (response) {
                    const $html = $(response);
                    const $listingView = $('.listing-view');
                    const $paginationContainer = $('#fave-pagination-loadmore');
                    const $newListings = $html.find(
                        '.listing-view .item-listing-wrap'
                    );

                    if ($newListings.length > 0) {
                        $listingView.append($newListings);
                        this.currentPage++;

                        const nextPageLink = $html
                            .find('.houzez-infinite-load')
                            .attr('href');

                        if (
                            nextPageLink &&
                            this.currentPage * this.perPage < this.totalListings
                        ) {
                            $loadMoreBtn.attr('href', nextPageLink);
                            $loadMoreBtn.attr(
                                'data-page',
                                this.currentPage + 1
                            );
                            updateBrowserUrl(nextPageUrl);
                            $loadMoreBtn.show(); // Ensure button is visible
                        } else {
                            this.noMoreListings = true;
                            $paginationContainer.html(
                                '<p class="no-more-listings">' +
                                    houzez_vars.listings_not_found +
                                    '</p>'
                            );
                            $loadMoreBtn.hide(); // Hide button when no more listings
                        }

                        // Use the Core function instead of local one
                        houzez.Core.reinitializeAjaxFunctions();
                        if ($('.houzez-parallax').length > 0) {
                            houzez.Parallax.init();
                        }
                    } else {
                        this.noMoreListings = true;
                        $paginationContainer.html(
                            '<p class="no-more-listings">' +
                                houzez_vars.listings_not_found +
                                '</p>'
                        );
                        $loadMoreBtn.hide(); // Hide button when no more listings
                    }

                    listingsLoading = false;
                }.bind(this),
                error: function (xhr, status, error) {
                    listingsLoading = false;
                },
            });
        };

        /**
         * Update browser URL for history state
         */
        const updateBrowserUrl = function (url) {
            if (history.pushState) {
                const newUrl = new URL(url);
                window.history.pushState(
                    { path: newUrl.href },
                    '',
                    newUrl.href
                );
            }
        };

        /**
         * Initialize the module pagination functionality
         */
        const initPropertiesModulePagination = function () {
            const properties_module =
                $('#properties_module_section').length > 0;

            if (!properties_module) {
                return;
            }

            // Load More button click event
            $('body').on(
                'click',
                '#properties_module_section .fave-load-more a',
                loadMoreModuleProperties
            );

            // Initialize Infinite Scroll if that's the pagination type
            if (paginationType === 'infinite_scroll') {
                observeModuleLoadMoreButton();
            }
        };

        /**
         * Handle main listings infinite load pagination
         */
        const initListingsInfiniteScroll = function () {
            const $listingView = $('.listing-view');
            const $loadMoreBtn = $('.houzez-infinite-load');

            // Skip if we're in properties module context or there's no listing view
            const inModuleContext = $('#properties_module_section').length > 0;

            if (inModuleContext || !$listingView.length || !$loadMoreBtn.length)
                return;

            // Set up context with pagination state
            const context = {
                currentPage: 1,
                noMoreListings: false,
                totalListings: parseInt($loadMoreBtn.data('total'), 10) || 0,
                perPage: parseInt($loadMoreBtn.data('per-page'), 10) || 10,
            };

            const pagi_type = $loadMoreBtn.data('pagi-type') || '_number';

            // Bind loadMoreListings to the context
            const boundLoadMoreListings = loadMoreListings.bind(context);

            // Check if we need to show the Load More button initially
            if (context.totalListings <= context.perPage) {
                $loadMoreBtn.hide();
            }

            // Set up infinite scroll
            if (pagi_type == '_infinite' && $loadMoreBtn.length > 0) {
                if (listingsObserver) {
                    listingsObserver.disconnect();
                }

                listingsObserver = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (
                                entry.isIntersecting &&
                                !listingsLoading &&
                                !context.noMoreListings
                            ) {
                                boundLoadMoreListings();
                            }
                        });
                    },
                    {
                        root: null,
                        rootMargin: '0px 0px 300px 0px', // Increased bottom margin
                        threshold: 0.1,
                    }
                );

                // Start observing the load more button
                listingsObserver.observe($loadMoreBtn[0]);
            }

            // Keep the click event for browsers that don't support IntersectionObserver
            $loadMoreBtn.on('click', function (e) {
                e.preventDefault();
                if (!listingsLoading && !context.noMoreListings) {
                    boundLoadMoreListings();
                }
            });

            // Handle browser back/forward buttons
            $(window).on('popstate', function (e) {
                if (e.originalEvent.state !== null) {
                    window.location.reload();
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            initPropertiesModulePagination();
            initListingsInfiniteScroll();
        };

        // Public API
        return {
            init: init,
            loadMoreModuleProperties: loadMoreModuleProperties,
            loadMoreListings: loadMoreListings,
        };
    })();

    /**
     * Mortgage Module
     * Handles mortgage calculator functionality
     */
    houzez.Mortgage = (function () {
        /**
         * Calculation handler for mortgage calculator section
         */
        const mortgageCalculation = function () {
            const homePrice = houzez.Core.util.parseNumberInput('#homePrice');
            const downPaymentPercentage = houzez.Core.util.parseNumberInput(
                '#downPaymentPercentage'
            );
            const annualInterestRate = houzez.Core.util.parseNumberInput(
                '#annualInterestRate'
            );
            const loanTermInYears =
                houzez.Core.util.parseNumberInput('#loanTermInYears');
            const annualPropertyTaxRate = houzez.Core.util.parseNumberInput(
                '#annualPropertyTaxRate'
            );
            const annualHomeInsurance = houzez.Core.util.parseNumberInput(
                '#annualHomeInsurance'
            );
            const monthlyHOAFees =
                houzez.Core.util.parseNumberInput('#monthlyHOAFees');
            const pmi = houzez.Core.util.parseNumberInput('#pmi');

            const downPayment = homePrice * (downPaymentPercentage / 100);
            const principal = homePrice - downPayment;
            const monthlyPayment = houzez.Core.util.calculateMonthlyPayment(
                principal,
                annualInterestRate,
                loanTermInYears
            );
            const monthlyPropertyTax =
                (homePrice * (annualPropertyTaxRate / 100)) / 12;
            const monthlyHomeInsurance = annualHomeInsurance / 12;

            const pmiRequired = downPayment / homePrice < 0.2;
            const monthlyPMI = pmiRequired ? (principal * (pmi / 100)) / 12 : 0;

            const totalMonthlyPayment =
                monthlyPayment +
                monthlyPropertyTax +
                monthlyHomeInsurance +
                monthlyHOAFees +
                monthlyPMI;

            const loanAmount = homePrice - downPayment;

            // Use numberFormat from Core utility
            const numberFormat = houzez.Core.util.numberFormat;

            const formattedDownPayment = numberFormat(downPayment, true);
            const formattedLoanAmount = numberFormat(loanAmount, true);
            const formattedMonthlyPayment = numberFormat(monthlyPayment, true);
            const formattedPropertyTax = numberFormat(monthlyPropertyTax, true);
            const formattedHomeInsurance = numberFormat(
                monthlyHomeInsurance,
                true
            );
            const formattedPMI = pmiRequired
                ? numberFormat(monthlyPMI, true)
                : '';
            const formattedHOAFees = numberFormat(monthlyHOAFees, true);
            const formattedTotalMonthlyPayment = numberFormat(
                totalMonthlyPayment,
                true
            );

            // Update UI elements with results
            $('#downPaymentResult').html(
                houzez.Core.util.currencyFormat(formattedDownPayment)
            );
            $('#loadAmountResult').html(
                houzez.Core.util.currencyFormat(formattedLoanAmount)
            );
            $('#monthlyMortgagePaymentResult').html(
                houzez.Core.util.currencyFormat(formattedMonthlyPayment)
            );
            $('#monthlyPropertyTaxResult').html(
                houzez.Core.util.currencyFormat(formattedPropertyTax)
            );
            $('#monthlyHomeInsuranceResult').html(
                houzez.Core.util.currencyFormat(formattedHomeInsurance)
            );

            if (pmiRequired) {
                $('.rslt-pmi').show();
                $('#monthlyPMIResult').html(
                    houzez.Core.util.currencyFormat(formattedPMI)
                );
            } else {
                $('.rslt-pmi').hide();
            }

            $('#monthlyHOAResult').html(
                houzez.Core.util.currencyFormat(formattedHOAFees)
            );
            $('#m_monthly_val').html(
                houzez.Core.util.currencyFormat(formattedTotalMonthlyPayment)
            );

            // Update chart data for visualization
            const chartData = [
                {
                    label: 'Monthly Mortgage Payment',
                    value: monthlyPayment,
                    color: '#ff6384',
                },
                {
                    label: 'Property Tax',
                    value: monthlyPropertyTax,
                    color: '#36a2eb',
                },
                {
                    label: 'Home Insurance',
                    value: monthlyHomeInsurance,
                    color: '#ffce56',
                },
                {
                    label: 'HOA',
                    value: monthlyHOAFees,
                    color: '#c2d500',
                },
            ];

            if (pmiRequired) {
                chartData.push({
                    label: 'PMI',
                    value: monthlyPMI,
                    color: '#4bc0c0',
                });
            }

            updateChart(chartData);
        };

        /**
         * Update the mortgage donut chart
         * @param {Array} chartData - Chart data for visualization
         */
        const updateChart = function (chartData) {
            const ctx = $('#mortgage-calculator-chart')[0].getContext('2d');

            if (window.myChart) {
                window.myChart.destroy();
            }

            window.myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [
                        {
                            data: chartData.map((item) => item.value),
                            backgroundColor: chartData.map(
                                (item) => item.color
                            ),
                        },
                    ],
                },
                options: {
                    cutoutPercentage: 85,
                    responsive: false,
                    tooltips: false,
                },
            });
        };

        /**
         * Initialize the mortgage calculator
         */
        const initMortgageCalculator = function () {
            // If the calculator form exists, set up event handlers
            if ($('#houzez-calculator-form').length > 0) {
                $('#houzez-calculator-form input').on(
                    'input',
                    mortgageCalculation
                );

                // Calculate on page load
                mortgageCalculation();
            }
        };

        /**
         * Initialize the mortgage module
         */
        const init = function () {
            initMortgageCalculator();
        };

        // Public API
        return {
            init: init,
            mortgageCalculation: mortgageCalculation,
            updateChart: updateChart,
        };
    })();

    /**
     * Property Contact Forms Module
     * Handles property contact forms functionality
     */
    houzez.ContactForms = (function () {
        /**
         * Handle agent contact form submittal
         */
        const propertyAgentContactForm = function () {
            $('.houzez_agent_property_form').on('click', function (e) {
                e.preventDefault();

                var $this = $(this);
                var $form = $this.parents('form');
                var $form_wrap = $this.parents('.property-form-wrap');
                var $result = $form_wrap.find('.form_messages');
                var $is_bottom = $('.is_bottom').val();

                if ($is_bottom == 'bottom') {
                    $result = $form.find('.form_messages');
                }
                $result.empty();

                $.ajax({
                    type: 'post',
                    url: houzez.Core.config.ajaxurl,
                    data: $form.serialize(),
                    dataType: 'JSON',
                    beforeSend: function () {
                        $this.attr('disabled', true);
                        $this.find('.houzez-loader-js').addClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            // Clear form fields
                            $form
                                .find(
                                    'input[name="name"], input[name="mobile"], input[name="email"]'
                                )
                                .val('');
                            $form.find('textarea').val('');

                            // Show success message
                            houzez.Core.util.showSuccess($result, response.msg);
                        } else {
                            // Show error message
                            houzez.Core.util.showError($result, response.msg);
                        }

                        // Reset captcha (supports both reCaptcha and Turnstile)
                        houzez.Core.util.resetCaptcha($form);

                        // Handle redirection if needed
                        if (
                            houzez_vars.agent_redirection != '' &&
                            response.success
                        ) {
                            setTimeout(function () {
                                window.location.replace(
                                    houzez_vars.agent_redirection
                                );
                            }, 500);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this.attr('disabled', false);
                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            });
        };

        /**
         * Handle property schedule tour form submission
         */
        const scheduleTour = function () {
            $('.schedule_contact_form').on('click', function (e) {
                e.preventDefault();

                const $this = $(this);
                const $form = $this.parents('form');
                const $messages = $form.find('.form_messages');

                $.ajax({
                    method: $form.attr('method'),
                    dataType: 'JSON',
                    url: houzez.Core.config.ajaxurl,
                    data: $form.serialize(),
                    beforeSend: function () {
                        $this.attr('disabled', true);
                        $this.find('.houzez-loader-js').addClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            houzez.Core.util.showSuccess(
                                $messages,
                                response.msg
                            );
                        } else {
                            houzez.Core.util.showError($messages, response.msg);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this.attr('disabled', false);
                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            });

            /* ------------------------------------------------------------------------ */
            /*  Schedule tour v2
            /* ------------------------------------------------------------------------ */

            // $('.tour-day-form-slide-arrow.next').click(function (e) {
            //     $('.property-schedule-tour-day-form-slide').addClass('end');
            //     $('.property-schedule-tour-day-form-slide').removeClass(
            //         'start'
            //     );
            // });
            // $('.tour-day-form-slide-arrow.prev').click(function (e) {
            //     $('.property-schedule-tour-day-form-slide').addClass('start');
            //     $('.property-schedule-tour-day-form-slide').removeClass('end');
            // });
        };

        /**
         * Handle contact agent form submission
         */
        const contactRealtor = function () {
            $(document).on('click', '.contact-realtor-btn', function (e) {
                e.preventDefault();
                var current_element = $(this);
                var $this = $(this);
                var $form = $this.parents('form');
                var $result = $form.find('.form_messages');

                jQuery.ajax({
                    type: 'post',
                    url: houzez.Core.config.ajaxurl,
                    data: $form.serialize(),
                    method: $form.attr('method'),
                    dataType: 'JSON',

                    beforeSend: function () {
                        $this.find('.houzez-loader-js').addClass('loader-show');
                    },
                    success: function (res) {
                        if (res.success) {
                            houzez.Core.util.showSuccess($result, res.msg);
                        } else {
                            // Show error message
                            houzez.Core.util.showError($result, res.msg);
                        }

                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                        // Reset captcha (supports both reCaptcha and Turnstile)
                        houzez.Core.util.resetCaptcha($form);
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        houzez.Core.util.showError($result, err.Message);
                    },
                    complete: function () {
                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            propertyAgentContactForm();
            contactRealtor();
            if (houzez.Core.config.is_singular_property) {
                scheduleTour();
            }
        };

        // Public API
        return {
            init: init,
            propertyAgentContactForm: propertyAgentContactForm,
            scheduleTour: scheduleTour,
            contactRealtor: contactRealtor,
        };
    })();

    /**
     * Single Property Module
     * Handles single property functionality
     */
    houzez.SingleProperty = (function () {
        /**
         * Refresh the media tabs
         */
        const refreshMediaTabs = () => {
            // Track if tour has been loaded
            let tourLoaded = false;

            // Use class selector instead of ID
            let tourTabs = document.querySelectorAll(
                '.houzez-360-virtual-media-tab'
            );

            // Check if elements exist before adding event listeners
            if (tourTabs.length > 0) {
                tourTabs.forEach((tourTab) => {
                    tourTab.addEventListener('shown.bs.tab', () => {
                        // Only load if not already loaded
                        if (!tourLoaded) {
                            // Show loading indicator
                            $('.loader-360').show();

                            // Properly reload the content
                            $('#pills-360tour').load(
                                window.location.href +
                                    ' #virtual-tour-iframe-container',
                                function () {
                                    // Hide loading indicator when done
                                    $('.loader-360').hide();
                                    // Mark as loaded
                                    tourLoaded = true;
                                }
                            );
                        }
                    });
                });
            }

            // Handle gallery tab switch - refresh slider and load images
            var galleryTab = document.getElementById('pills-gallery-tab');
            if (galleryTab) {
                galleryTab.addEventListener('shown.bs.tab', function () {
                    // Handle LightSlider for standard gallery
                    var slider = $('#property-gallery-js');
                    if (slider.length && slider.data('lightSlider')) {
                        // Force refresh the slider
                        slider.data('lightSlider').refresh();

                        // Trigger lazy image loading for visible slides
                        slider
                            .find('.houzez-gallery-img[data-lazy]')
                            .each(function () {
                                var $img = $(this);
                                var lazySrc = $img.data('lazy');
                                if (
                                    lazySrc &&
                                    (!$img.attr('src') ||
                                        !$img.attr('src').includes(lazySrc))
                                ) {
                                    $img.attr('src', lazySrc);
                                }
                            });
                    }

                    // Also handle Slick slider for v5 layouts
                    var slickSlider = $('.listing-slider-variable-width');
                    if (
                        slickSlider.length &&
                        slickSlider.hasClass('slick-initialized')
                    ) {
                        slickSlider.slick('setPosition');
                        slickSlider.slick('refresh');
                    }
                });
            }
        };

        /**
         * Initialize the sticky property navigation
         */
        const propertyDetailNav = function () {
            let houzez_listing_nav = $('.property-navigation-wrap');
            if (
                houzez.Core.config.property_detail_nav === 'yes' &&
                houzez_listing_nav.length > 0
            ) {
                let header_area = $('.header-main-wrap');

                if (!header_area.length) {
                    header_area = $('#header-hz-elementor');
                }

                const header_area_height = header_area.innerHeight();
                const get_header_search = $('.advanced-search-header');
                const get_header_search_height =
                    get_header_search.innerHeight();
                let scroll_nav_height;

                if (get_header_search.length) {
                    scroll_nav_height =
                        header_area_height + get_header_search_height;
                } else {
                    scroll_nav_height = header_area_height;
                }

                if ($(window).width() >= 992) {
                    const contentTop = $('section.property-wrap').offset().top;

                    $(window).scroll(function () {
                        const scroll = $(window).scrollTop();
                        const admin_nav = houzez.Core.config.wpadminbar_height;

                        let top_position = 0;
                        if (admin_nav > 0) {
                            top_position = admin_nav;
                        }

                        if (scroll > contentTop) {
                            $('.property-navigation-wrap').css({
                                top: top_position,
                            });
                            $('.property-navigation-wrap').fadeIn();
                            $('.property-navigation-wrap').addClass(
                                'nav-fixed'
                            );
                        } else if (scroll == 0) {
                            $('.property-navigation-wrap').removeAttr('style');
                            $('.property-navigation-wrap').removeClass(
                                'nav-fixed'
                            );
                            $('.property-navigation-wrap').fadeOut();
                        }
                    });

                    $(window).on('scroll', function () {
                        $('.property-section-wrap').each(function () {
                            if (
                                $(window).scrollTop() >=
                                $(this).offset().top - 86
                            ) {
                                var id = $(this).attr('id');
                                $('.target').removeClass('active');
                                $('.target[href="#' + id + '"]').addClass(
                                    'active'
                                );
                            } else if ($(window).scrollTop() <= 0) {
                                $('.target').removeClass('active');
                            }
                        });
                    });

                    $('.property-navigation-item a.target').on(
                        'click',
                        function (e) {
                            e.preventDefault(); // Prevent the default jump-to-anchor behavior

                            // Grab the href (e.g. "#section1") and find its offset from the top
                            let targetId = $(this).attr('href');
                            let $target = $(targetId);
                            if ($target.length) {
                                let offsetTop =
                                    $target.offset().top - header_area_height;

                                // Animate the scroll to that position (600ms duration)
                                $('html, body').animate(
                                    { scrollTop: offsetTop },
                                    100
                                );
                            }
                        }
                    );
                }
            }
        };

        const propertyFancyboxGallery = function () {
            Fancybox.bind(
                'a[data-fancybox][data-houzez-fancybox], .listing-slider-variable-width .slick-slide:not(.slick-cloned) a[data-fancybox="gallery-variable-width"]',
                {
                    Hash: false,
                    Thumbs: {
                        type: 'modern', // or 'classic'
                    },
                }
            );
        };

        const propertyDetailLightbox = function () {
            $('.btn-expand').on('click', function () {
                $('.lightbox-gallery-wrap').toggleClass(
                    'lightbox-gallery-full-wrap'
                );
                $('.lightbox-slider').slick('setPosition');
            });

            $('.btn-email').on('click', function () {
                $('.lightbox-form-wrap').toggleClass('lightbox-form-wrap-show');
            });
        };

        /**
         * Handle print property feature
         */
        const printProperty = function () {
            $('.houzez-print').on('click', function (e) {
                e.preventDefault();
                const propID = $(this).attr('data-propid');
                const printWindow = window.open(
                    '',
                    'Print Me',
                    'width=800, height=842'
                );

                $.ajax({
                    type: 'post',
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_create_print',
                        propid: propID,
                    },
                    success: function (data) {
                        printWindow.document.write(data);
                        printWindow.document.close();
                        printWindow.focus();
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                });
                return false;
            });
        };

        const adjustTabContentHeight = function (forceAdjust = false) {
            // Get the tab content element
            const $tabContent = $('.hs-property-gallery-wrap .tab-content');
            if (!$tabContent.length) return;

            $('.mobile-property-tools a.nav-link').on('click', function () {
                const tabHeight = $('.tab-content').outerHeight(true);
                // Now set the fixed height
                $tabContent.css({
                    height: tabHeight + 'px',
                    transition: 'height 0.3s ease',
                });
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            if (houzez.Core.config.is_singular_property) {
                propertyDetailNav();
                propertyFancyboxGallery();
                propertyDetailLightbox();
                printProperty();
                refreshMediaTabs();

                // Initialize Bootstrap tooltips for media tab buttons
                var tooltipTriggerList = [].slice.call(
                    document.querySelectorAll(
                        '#pills-tab [data-bs-tooltip="tooltip"]'
                    )
                );
                tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl, {
                        placement: 'bottom',
                        trigger: 'hover',
                    });
                });

                if (houzez.Core.is_mobile) {
                    adjustTabContentHeight();
                }
            }
        };

        // Public API
        return {
            init: init,
            propertyDetailNav: propertyDetailNav,
            propertyFancyboxGallery: propertyFancyboxGallery,
            propertyDetailLightbox: propertyDetailLightbox,
            printProperty: printProperty,
            refreshMediaTabs: refreshMediaTabs,
            adjustTabContentHeight: adjustTabContentHeight,
        };
    })();

    /**
     * Realtor Stats Module
     * Handles realtor stats functionality
     */
    houzez.RealtorStats = (function () {
        /* ------------------------------------------------------------------------ */
        /*  Types chart for agent and agency
     /* ------------------------------------------------------------------------ */
        const init = function () {
            $('.houzez-realtor-stats-js[id^="stats-property-"]').each(
                function () {
                    var $div = $(this);
                    var token = $div.data('token');

                    var statsID = 'stats-property-' + token;
                    if ($('#' + statsID).length > 0) {
                        var chartData = $('#' + statsID).data('chart');
                        var ctx = document
                            .getElementById(statsID)
                            .getContext('2d');
                        var myDoughnutChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                datasets: [
                                    {
                                        data: chartData,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.5)',
                                            'rgba(54, 162, 235, 0.5)',
                                            'rgba(255, 206, 86, 0.5)',
                                            'rgba(75, 192, 192, 0.5)',
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                        ],
                                        borderWidth: 1,
                                    },
                                ],
                            },
                            options: {
                                cutoutPercentage: 60,
                                responsive: false,
                                tooltips: false,
                            },
                        });
                    }
                }
            );
        };

        return {
            init: init,
        };
    })();

    /**
     * Logo Module
     * Handles logo functionality
     */
    houzez.Logo = (function () {
        /**
         * Handle retina logo display
         */
        const retinaLogo = function () {
            // Get retina logo paths from global vars
            const retina_logo = houzez_vars.retina_logo || '';
            const retina_logo_splash = houzez_vars.retina_logo_splash || '';
            const retina_logo_mobile = houzez_vars.retina_logo_mobile || '';
            const retina_logo_mobile_splash =
                houzez_vars.retina_logo_mobile_splash || '';
            const transparent_logo = houzez_vars.transparent_logo || false;

            if (
                retina_logo !== '' ||
                retina_logo_splash != '' ||
                retina_logo_mobile != '' ||
                retina_logo_mobile_splash != ''
            ) {
                if (window.devicePixelRatio == 2) {
                    if (retina_logo != '') {
                        $('.logo-desktop img').attr('src', retina_logo);
                    }

                    if (retina_logo_splash != '') {
                        $('.logo-splash img').attr('src', retina_logo_splash);
                    }

                    if (retina_logo_mobile != '') {
                        $('.logo-mobile img').attr('src', retina_logo_mobile);
                    }
                }
            }

            // Special handling for transparent logo in retina screens
            if (window.devicePixelRatio == 2 && transparent_logo) {
                if (retina_logo_splash != '') {
                    houzez_vars.custom_logo_splash = retina_logo_splash;
                }
                if (retina_logo != '') {
                    houzez_vars.simple_logo = retina_logo;
                }
            }
        };

        const init = function () {
            retinaLogo();
        };

        return {
            init: init,
            retinaLogo: retinaLogo,
        };
    })();

    /**
     * Sticky Module
     * Handles sticky functionality
     */
    houzez.Sticky = (function () {
        // Cache DOM elements
        const $win = $(window);
        const $header_area = $('.header-main-wrap');
        const $header_nav = $('#header-section');
        const $header_mobile = $('#header-mobile');
        const $desktop_search_nav = $('.desktop-search-nav');
        const $mobile_search_nav = $('.mobile-search-nav');
        const $top_banner_wrap = $('.top-banner-wrap');

        // Configuration variables
        let houzezStickyTop = 0;
        let adminBarHeight = houzez.Core.config.wpadminbar_height;
        let header_area_height = $header_area.innerHeight() || 0;
        let desktop_search_nav_height = $desktop_search_nav.innerHeight() || 0;
        let mobile_search_nav_height = $mobile_search_nav.innerHeight() || 0;
        let houzez_nav_sticky_height = $header_nav.innerHeight() || 0;
        let top_banner_wrap_height = 0;

        let only_nav_sticky = $header_nav.data('sticky') || 0;
        let mobile_menu_sticky = $header_mobile.data('sticky') || 0;

        if (
            houzez.Core.config.property_detail_nav == 'yes' &&
            houzez.Core.config.is_singular_property
        ) {
            only_nav_sticky = 0;
        }

        /**
         * Calculate sticky heights and positions
         */
        const calculateStickyHeights = function () {
            houzezStickyTop = houzez_nav_sticky_height;

            if (adminBarHeight > 0) {
                houzezStickyTop += adminBarHeight;
            }

            // Update top banner height
            top_banner_wrap_height = $top_banner_wrap.innerHeight() || 0;

            // Search position calculations
            let make_search_sticky_position =
                header_area_height + desktop_search_nav_height;
            let searchStickyPlus = 100;
            let search_under_navigation = true;

            if (houzez.Core.is_mobile()) {
                make_search_sticky_position += 40;
            }

            if (houzez_vars.search_position === 'under_banner') {
                make_search_sticky_position =
                    header_area_height + top_banner_wrap_height;
                searchStickyPlus = 0;

                if (houzez_vars.is_transparent) {
                    searchStickyPlus = 60;
                }

                search_under_navigation = false;
            }

            return {
                make_search_sticky_position,
                searchStickyPlus,
                search_under_navigation,
            };
        };

        /**
         * Handle sticky navigation
         */
        const stickyNav = function () {
            $win.scroll(function () {
                const scroll = $win.scrollTop();
                let top_banner_margin = 0;
                const thisHeight = $header_nav.outerHeight();
                top_banner_margin = thisHeight;
                if (houzez_vars.is_top_header != 0) {
                    const top_bar_wrap = $('.top-bar-wrap').height();
                    top_banner_margin = top_banner_margin + top_bar_wrap;
                }

                // Check if sticky is disabled
                if (only_nav_sticky === 0) {
                    return;
                }
                // Update logo if transparent
                if (houzez_vars.transparent_logo) {
                    $('.logo-splash img').attr('src', houzez_vars.simple_logo);
                }

                if (scroll > header_area_height) {
                    $header_nav.addClass('sticky-nav-area');
                    $header_nav.css('top', adminBarHeight);

                    if (houzez_vars.transparent_logo) {
                        $header_area.removeClass('header-transparent-wrap');
                        $('.top-banner-wrap').css(
                            'margin-top',
                            '-' + top_banner_margin + 'px'
                        );
                        $('body.houzez-header-elementor .content-wrap').css(
                            'margin-top',
                            '-' + top_banner_margin + 'px'
                        );
                        $('#main-wrap > div.elementor').css(
                            'margin-top',
                            '-' + top_banner_margin + 'px'
                        );
                    }

                    if (scroll >= header_area_height + 20) {
                        $header_nav.addClass('houzez-in-view');
                        $('#main-wrap').css('margin-top', thisHeight);
                    }
                } else {
                    $header_nav.removeClass('sticky-nav-area');
                    $header_nav.removeAttr('style');

                    if (houzez_vars.transparent_logo) {
                        $header_area.addClass('header-transparent-wrap');
                        $('.top-banner-wrap').css('margin-top', 0);
                        $('body.houzez-header-elementor .content-wrap').css(
                            'margin-top',
                            0
                        );
                        $('#main-wrap > div.elementor').css('margin-top', 0);
                        $('.logo-splash img').attr(
                            'src',
                            houzez_vars.custom_logo_splash
                        );
                    }

                    if (scroll <= header_area_height + 20) {
                        $header_nav.removeClass('houzez-in-view');
                    }

                    $('#main-wrap').css('margin-top', 0);
                }
            });
        };

        const stickyMobileNav = function () {
            let thisHeight = $header_mobile.outerHeight();
            $win.scroll(function () {
                let scroll = $win.scrollTop();

                if (mobile_menu_sticky === 0 || !houzez.Core.is_mobile()) {
                    return;
                }

                if (scroll > header_area_height + 50) {
                    $header_mobile.addClass('sticky-nav-area');
                    //$header_mobile.css('top', adminBarHeight);

                    if (scroll >= header_area_height + 60) {
                        $header_mobile.addClass('houzez-in-view');
                        $('#main-wrap').css('margin-top', thisHeight);
                    }
                } else {
                    $header_mobile.removeClass('sticky-nav-area');
                    $header_mobile.removeAttr('style');

                    if (scroll <= header_area_height + 60) {
                        $header_mobile.removeClass('houzez-in-view');
                    }

                    $('#main-wrap').css('margin-top', 0);
                }
            });
        };

        /**
         * Handle sticky search
         */
        const stickySearch = function () {
            const { make_search_sticky_position, searchStickyPlus } =
                calculateStickyHeights();

            $win.scroll(function () {
                const scroll = $win.scrollTop();
                const thisHeight = $desktop_search_nav.outerHeight();
                const hidden_data = $desktop_search_nav.data('hidden');

                if (scroll >= make_search_sticky_position) {
                    $desktop_search_nav.addClass('sticky-search-area');

                    if (hidden_data) {
                        $desktop_search_nav.removeClass('search-hidden');
                    }

                    if (!houzez.Core.is_mobile()) {
                        $desktop_search_nav.css('top', adminBarHeight);
                    }

                    if (
                        scroll >=
                        make_search_sticky_position + searchStickyPlus
                    ) {
                        $desktop_search_nav.addClass('houzez-in-view');

                        if (!hidden_data) {
                            $('#main-wrap').css('padding-top', thisHeight);
                        }
                    }
                } else {
                    $desktop_search_nav.removeClass('sticky-search-area');
                    $desktop_search_nav.removeAttr('style');

                    if (scroll <= make_search_sticky_position + 20) {
                        $desktop_search_nav.removeClass('houzez-in-view');
                    }

                    if (hidden_data) {
                        $desktop_search_nav.addClass('search-hidden');
                    }

                    if (!hidden_data) {
                        $('#main-wrap').css('padding-top', 0);
                    }
                }
            });
        };

        const stickyMobileSearch = function () {
            let thisHeight = $mobile_search_nav.outerHeight();
            let { make_search_sticky_position, searchStickyPlus } =
                calculateStickyHeights();

            let make_mobile_search_sticky_position = thisHeight;
            let searchInView = thisHeight;
            if (adminBarHeight > 0) {
                make_mobile_search_sticky_position += adminBarHeight;
                searchInView += adminBarHeight + 46;
            }

            $win.scroll(function () {
                const scroll = $win.scrollTop();

                if (scroll >= make_mobile_search_sticky_position) {
                    $mobile_search_nav.addClass('sticky-search-area');
                    $mobile_search_nav.css('top', adminBarHeight);

                    if (scroll >= searchInView) {
                        $mobile_search_nav.addClass('houzez-in-view');

                        $('#main-wrap').css('padding-top', thisHeight);
                    }
                } else {
                    $mobile_search_nav.removeClass('sticky-search-area');
                    $mobile_search_nav.removeAttr('style');

                    if (scroll <= searchInView + 20) {
                        $mobile_search_nav.removeClass('houzez-in-view');
                    }

                    $('#main-wrap').css('padding-top', 0);
                }
            });
        };

        /**
         * Initialize all functionality
         */
        const init = function () {
            // Calculate heights and initialize variables
            calculateStickyHeights();

            // Initialize functionality based on configuration
            const only_nav_sticky = $header_nav.data('sticky');
            const desktop_header_search_enabled =
                $desktop_search_nav.data('sticky');
            const mobile_header_search_enabled =
                $mobile_search_nav.data('sticky');

            // Initialize sticky navigation if enabled
            if (only_nav_sticky && !houzez.Core.is_mobile()) {
                // apply new mobile
                stickyNav();

                // Special handling for compare table if needed
                if ($('.compare-table').length > 0) {
                    var compare_top_margin = $desktop_search_nav.outerHeight();
                    $('.compare-table thead th').css(
                        'top',
                        compare_top_margin + 25
                    );
                }
            }

            stickyMobileNav();

            if (mobile_header_search_enabled) {
                stickyMobileSearch();
            }

            // Initialize sticky search if enabled
            if (desktop_header_search_enabled && !houzez.Core.is_mobile()) {
                stickySearch();
            }
        };

        // Public API
        return {
            init: init,
            stickyNav: stickyNav,
            stickySearch: stickySearch,
        };
    })();

    /**
     * Search Module
     * Handles search functionality
     */
    houzez.Search = (function () {
        /**
         * Initialize mobile search overlay
         */
        const mobileSearchOverlay = function () {
            // Calculate admin bar height and set overlay height
            const setOverlayHeight = function () {
                const $adminBar = houzez.Core.config.wpadminbar_height;
                const $searchOverlay = $('#overlay-search-advanced-module');
                const windowHeight = $(window).height();

                if ($adminBar > 0 && $searchOverlay.length) {
                    const adminBarHeight = $adminBar;
                    $searchOverlay.css({
                        height: 'calc(100vh)',
                        zIndex: '999',
                        top: adminBarHeight,
                    });
                } else if ($searchOverlay.length) {
                    $searchOverlay.css({
                        height: '100vh',
                        top: '0',
                    });
                }
            };

            // Set initial height
            setOverlayHeight();

            // Update height on window resize
            $(window).on('resize', function () {
                setOverlayHeight();
            });

            // Toggle overlay
            $(
                '.mobile-search-nav, .overlay-search-module-close, .overly_is_halfmap .half-map-search-js-btn'
            ).click(function () {
                $('#overlay-search-advanced-module').toggleClass('open');
            });
        };

        /**
         * Initialize search status tabs
         */
        const initSearchTabs = function () {
            $('.houzez-status-tabs li a').on('click', function (e) {
                e.preventDefault();
                let $this = $(this);
                let status = $this.data('val');

                $('#search-tabs').val(status);

                const $form = $('.houzez-search-form-js');
                statusChangeHandler(status, $form);
            });
        };

        /**
         * Changes price dropdown options based on property status
         * @param {string} propertyStatus - Selected property status
         * @param {object} $form - jQuery form object
         * @returns {boolean} - Returns true if successful, false otherwise
         */
        const statusChangeHandler = function (propertyStatus, $form) {
            // Validate parameters
            if (!propertyStatus || !$form || !$form.length) {
                return false;
            }

            const isForRent = propertyStatus == houzez_vars.for_rent;
            const rentSelector = '.prices-only-for-rent';
            const allSelector = '.prices-for-all';

            // Toggle rent-specific price options
            if (isForRent) {
                $form.find(allSelector).addClass('hide');
                $form
                    .find(`${allSelector} select`)
                    .attr('disabled', 'disabled');
                $form.find(rentSelector).removeClass('hide');
                $form.find(`${rentSelector} select`).removeAttr('disabled');
                $form.find(`${rentSelector} select`).selectpicker('refresh');
            } else {
                $form.find(rentSelector).addClass('hide');
                $form
                    .find(`${rentSelector} select`)
                    .attr('disabled', 'disabled');
                $form.find(allSelector).removeClass('hide');
                $form.find(`${allSelector} select`).removeAttr('disabled');
                $form.find(`${allSelector} select`).selectpicker('refresh');
            }

            return true;
        };

        /**
         * Initialize property status change event handlers
         * Sets up event listeners for status changes and initializes the UI
         */
        const initStatusChangeHandlers = function () {
            // Status dropdown change event
            $('select.status-js').on('change', function (e) {
                // Prevent event from bubbling up to the wrapper div
                e.stopPropagation();

                const selected_status = $(this).val();
                const $form = $(this).parents('form');
                statusChangeHandler(selected_status, $form);
            });

            // Status tab click event
            $('.status-tab-js').on('click', function () {
                const tab_selected_status = $(this).data('val');
                const $form = $(this).parents('form');
                statusChangeHandler(tab_selected_status, $form);
            });

            // Initialize on page load based on current status
            const $searchForm = $('.houzez-search-form-js');
            const $builderForm = $('.houzez-search-builder-form-js');

            /* On page load*/
            const selected_status = $('select.status-js').val();
            if (selected_status == houzez_vars.for_rent) {
                statusChangeHandler(selected_status, $searchForm);
            } else {
                statusChangeHandler('dummy', $searchForm);
            }

            /* On page load status tab */
            const selected_status_tab = $('.status-tab-js').val();
            if (selected_status_tab == houzez_vars.for_rent) {
                statusChangeHandler(selected_status_tab, $builderForm);
            } else {
                statusChangeHandler('dummy', $builderForm);
            }
        };

        /**
         * Fill the distance range slider with gradient background
         */
        const fillDistanceRangeSlider = function (slider) {
            if (!slider) return;

            const value =
                ((slider.value - slider.min) / (slider.max - slider.min)) * 100;
            const isRTL = document.documentElement.dir === 'rtl';
            const direction = isRTL ? 'to left' : 'to right';
            slider.style.background = `linear-gradient(
                ${direction},
                ${houzez_vars.primary_color} 0%,
                ${houzez_vars.primary_color} ${value}%,
                #dce0e0 ${value}%,
                #dce0e0 100%
            )`;
        };

        /**
         * Initialize the distance range slider functionality
         */
        const initDistanceRangeSlider = function () {
            const rangeInput = document.getElementById('radius-range-text');
            const rangeSlider = document.getElementById('radius-range-slider');
            const radiusValue = document.getElementById('radius-range-value');
            const radiusCheckbox = document.getElementById('use_radius');
            const distanceRange = document.querySelector('.distance-range');

            if (!rangeInput || !rangeSlider || !radiusValue) return;

            // Sync input and slider
            rangeInput.addEventListener('input', (e) => {
                const value = e.target.value;
                rangeSlider.value = value;
                radiusValue.value = value;
                if (distanceRange) {
                    distanceRange.value = value;
                }
                fillDistanceRangeSlider(rangeSlider);
            });

            rangeSlider.addEventListener('input', (e) => {
                const value = e.target.value;
                rangeInput.value = value;
                radiusValue.value = value;
                if (distanceRange) {
                    distanceRange.value = value;
                }
                fillDistanceRangeSlider(rangeSlider);
            });

            // Set initial values
            const defaultRadius = rangeSlider.value;
            rangeInput.value = defaultRadius;
            radiusValue.value = defaultRadius;
            if (distanceRange) {
                distanceRange.value = defaultRadius;
            }

            // Initial fill
            fillDistanceRangeSlider(rangeSlider);

            // Enable/disable based on checkbox
            if (radiusCheckbox) {
                radiusCheckbox.addEventListener('change', (e) => {
                    const isEnabled = e.target.checked;
                    rangeInput.disabled = !isEnabled;
                    rangeSlider.disabled = !isEnabled;
                    if (isEnabled) {
                        radiusValue.value = rangeSlider.value;
                        if (distanceRange) {
                            distanceRange.value = rangeSlider.value;
                        }
                    } else {
                        radiusValue.value = '';
                        if (distanceRange) {
                            distanceRange.value = '';
                        }
                    }
                    fillDistanceRangeSlider(rangeSlider);
                });

                // Set initial state
                const isChecked = radiusCheckbox.checked;
                rangeInput.disabled = !isChecked;
                rangeSlider.disabled = !isChecked;
                if (isChecked) {
                    radiusValue.value = defaultRadius;
                    if (distanceRange) {
                        distanceRange.value = defaultRadius;
                    }
                } else {
                    radiusValue.value = '';
                    if (distanceRange) {
                        distanceRange.value = '';
                    }
                }
            }
        };

        /**
         * Initialize the price range slider
         * @param {number|string} min_price - Minimum price
         * @param {number|string} max_price - Maximum price
         * @param {string} unique_id - Unique identifier for this price range slider
         * @param {number|null} selectedMinPrice - Selected minimum price
         * @param {number|null} selectedMaxPrice - Selected maximum price
         */
        const initPriceRangeSlider = function (
            min_price,
            max_price,
            unique_id = 'default',
            selectedMinPrice = null,
            selectedMaxPrice = null
        ) {
            const fromSlider = document.getElementById(
                'fromSlider_' + unique_id
            );
            const toSlider = document.getElementById('toSlider_' + unique_id);
            const rangeWrap = document.querySelector(
                `.range-wrap[data-price-range-id="${unique_id}"]`
            );

            if (!rangeWrap) {
                return;
            }

            const minPriceDisplay = rangeWrap.querySelector('.min-price-range');
            const maxPriceDisplay = rangeWrap.querySelector('.max-price-range');

            if (
                !fromSlider ||
                !toSlider ||
                !minPriceDisplay ||
                !maxPriceDisplay
            ) {
                return;
            }

            // Ensure min_price and max_price are numbers
            min_price = parseInt(min_price, 10);
            max_price = parseInt(max_price, 10);

            // Set min and max attributes for both sliders
            fromSlider.min = min_price;
            fromSlider.max = max_price;
            toSlider.min = min_price;
            toSlider.max = max_price;

            // Set initial values based on selected values or defaults
            if (selectedMinPrice !== null && selectedMaxPrice !== null) {
                fromSlider.value = selectedMinPrice;
                toSlider.value = selectedMaxPrice;
            } else {
                fromSlider.value = min_price;
                toSlider.value = max_price;
            }

            const formatCurrency = (value) => {
                // Get separators from theme options
                const thousandSeparator =
                    houzez_vars.thousands_separator || ',';
                const decimalSeparator =
                    houzez_vars.decimal_point_separator || '.';

                // Format number with proper separators
                let formattedValue = value.toString();

                // Split number at decimal point
                const parts = formattedValue.split('.');

                // Format the whole number part with thousand separators
                parts[0] = parts[0].replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    thousandSeparator
                );

                // Join back with decimal part if exists
                formattedValue = parts.join(decimalSeparator);

                // Add currency symbol based on position
                if (houzez_vars.currency_position === 'after') {
                    return formattedValue + houzez_vars.currency_symbol;
                } else {
                    return houzez_vars.currency_symbol + formattedValue;
                }
            };

            // Creates the gradient background effect for the slider
            const fillSlider = (
                from,
                to,
                sliderColor,
                rangeColor,
                controlSlider
            ) => {
                const rangeDistance = to.max - to.min;
                const fromPosition = from.value - to.min;
                const toPosition = to.value - to.min;
                const isRTL = document.documentElement.dir === 'rtl';
                const direction = isRTL ? 'to left' : 'to right';
                controlSlider.style.background = `linear-gradient(
                    ${direction},
                    ${sliderColor} 0%,
                    ${sliderColor} ${(fromPosition / rangeDistance) * 100}%,
                    ${rangeColor} ${(fromPosition / rangeDistance) * 100}%,
                    ${rangeColor} ${(toPosition / rangeDistance) * 100}%,
                    ${sliderColor} ${(toPosition / rangeDistance) * 100}%,
                    ${sliderColor} 100%)`;
            };

            // Track which slider was last interacted with
            let lastActiveSlider = null;

            // Adjusts z-index of sliders for better handle overlap behavior
            const setToggleAccessible = (activeSlider = null) => {
                const from = parseInt(fromSlider.value, 10);
                const to = parseInt(toSlider.value, 10);
                const range =
                    parseInt(toSlider.max, 10) - parseInt(toSlider.min, 10);
                const threshold = range * 0.05; // 5% of range as overlap threshold

                // Check if sliders are overlapping or very close
                const isOverlapping = Math.abs(to - from) <= threshold;

                if (isOverlapping && activeSlider) {
                    // When overlapping, the active slider gets higher z-index
                    if (activeSlider === 'from') {
                        fromSlider.style.zIndex = 2;
                        toSlider.style.zIndex = 0;
                    } else {
                        fromSlider.style.zIndex = 0;
                        toSlider.style.zIndex = 2;
                    }
                } else {
                    // Default behavior when not overlapping
                    // Min slider has higher z-index when at same position
                    if (from === to) {
                        fromSlider.style.zIndex = 2;
                        toSlider.style.zIndex = 0;
                    } else {
                        fromSlider.style.zIndex = 1;
                        toSlider.style.zIndex = 0;
                    }
                }
            };

            const controlFromSlider = () => {
                const from = parseInt(fromSlider.value, 10);
                const to = parseInt(toSlider.value, 10);

                fillSlider(
                    fromSlider,
                    toSlider,
                    '#dce0e0',
                    houzez_vars.primary_color,
                    toSlider
                );

                if (from > to) {
                    fromSlider.value = to;
                } else {
                    fromSlider.value = from;
                }

                // Set this as the active slider
                lastActiveSlider = 'from';
                setToggleAccessible('from');

                // Update display values
                minPriceDisplay.textContent = formatCurrency(fromSlider.value);
                maxPriceDisplay.textContent = formatCurrency(toSlider.value);
            };

            const controlToSlider = () => {
                const from = parseInt(fromSlider.value, 10);
                const to = parseInt(toSlider.value, 10);

                fillSlider(
                    fromSlider,
                    toSlider,
                    '#dce0e0',
                    houzez_vars.primary_color,
                    toSlider
                );

                if (from <= to) {
                    toSlider.value = to;
                } else {
                    toSlider.value = from;
                }

                // Set this as the active slider
                lastActiveSlider = 'to';
                setToggleAccessible('to');

                // Update display values
                minPriceDisplay.textContent = formatCurrency(fromSlider.value);
                maxPriceDisplay.textContent = formatCurrency(toSlider.value);
            };

            // Add event listeners
            fromSlider.addEventListener('input', controlFromSlider);
            toSlider.addEventListener('input', controlToSlider);

            // Initialize slider appearance
            fillSlider(
                fromSlider,
                toSlider,
                '#dce0e0',
                houzez_vars.primary_color,
                toSlider
            );
            setToggleAccessible();

            // Initialize display values
            minPriceDisplay.textContent = formatCurrency(fromSlider.value);
            maxPriceDisplay.textContent = formatCurrency(toSlider.value);
        };

        /**
         * Initialize price range sliders with appropriate values based on property status
         */
        const initPriceRangeSliders = function () {
            // Check if any price range sliders exist
            if (!$('.range-wrap[data-price-range-id]').length) {
                return;
            }

            // Get min and max values from theme options
            const min_price_rent =
                parseInt(houzez_vars.search_min_price_range_for_rent, 10) || 0;
            const max_price_rent =
                parseInt(houzez_vars.search_max_price_range_for_rent, 10) ||
                1000000;
            const min_price_sale =
                parseInt(houzez_vars.search_min_price_range, 10) || 0;
            const max_price_sale =
                parseInt(houzez_vars.search_max_price_range, 10) || 1000000;

            // Check if we have values from search results
            let selectedMinPrice = null;
            let selectedMaxPrice = null;

            if (houzez_vars.get_min_price && houzez_vars.get_max_price) {
                selectedMinPrice = parseInt(houzez_vars.get_min_price, 10);
                selectedMaxPrice = parseInt(houzez_vars.get_max_price, 10);
            }

            // Initialize all price range sliders
            $('.range-wrap[data-price-range-id]').each(function () {
                const unique_id = $(this).data('price-range-id');
                const selected_status_adv_search = $('select.status-js').val();

                // Update slider min/max values based on property status
                if (
                    selected_status_adv_search ==
                    houzez_vars.for_rent_price_slider
                ) {
                    initPriceRangeSlider(
                        min_price_rent,
                        max_price_rent,
                        unique_id,
                        selectedMinPrice,
                        selectedMaxPrice
                    );
                } else {
                    initPriceRangeSlider(
                        min_price_sale,
                        max_price_sale,
                        unique_id,
                        selectedMinPrice,
                        selectedMaxPrice
                    );
                }
            });

            // Handle price slider changes based on status
            $('select.status-js').on('change', function () {
                let search_status = $(this).val();

                // Update all price range sliders
                $('.range-wrap[data-price-range-id]').each(function () {
                    const unique_id = $(this).data('price-range-id');

                    if (search_status == houzez_vars.for_rent_price_slider) {
                        initPriceRangeSlider(
                            min_price_rent,
                            max_price_rent,
                            unique_id
                        );
                    } else {
                        initPriceRangeSlider(
                            min_price_sale,
                            max_price_sale,
                            unique_id
                        );
                    }
                });
            });

            // Handle price slider changes based on status tab
            $('.status-tab-js').on('click', function () {
                let tab_status = $(this).data('val');

                // Update all price range sliders
                $('.range-wrap[data-price-range-id]').each(function () {
                    const unique_id = $(this).data('price-range-id');

                    if (tab_status == houzez_vars.for_rent_price_slider) {
                        initPriceRangeSlider(
                            min_price_rent,
                            max_price_rent,
                            unique_id
                        );
                    } else {
                        initPriceRangeSlider(
                            min_price_sale,
                            max_price_sale,
                            unique_id
                        );
                    }
                });
            });
        };

        /**
         * Initialize auto-complete for keyword search
         */
        const initKeywordAutocomplete = function () {
            if (houzez_vars.keyword_autocomplete != 0) {
                let ajaxCount = 0;
                let $dataType = '';

                $('body').on(
                    'keyup',
                    '.houzez-keyword-autocomplete',
                    houzez.Core.util.debounce(function () {
                        let $this = $(this);
                        $dataType = $this.data('type');
                        let $form = $this.parents('form');

                        let auto_complete_container =
                            $form.find('.auto-complete');

                        if ($dataType == 'banner') {
                            auto_complete_container = $(
                                '.auto-complete-banner'
                            );
                        }

                        let keyword = $this.val();

                        keyword = $.trim(keyword);
                        const currentLength = keyword.length;

                        if (currentLength < 2) {
                            auto_complete_container.hide();
                            return;
                        }

                        auto_complete_container.fadeIn();

                        $.ajax({
                            type: 'POST',
                            url: houzez.Core.config.ajaxurl,
                            data: {
                                action: 'houzez_get_auto_complete_search',
                                key: keyword,
                            },
                            beforeSend: function () {
                                ajaxCount++;
                                if (ajaxCount == 1) {
                                    auto_complete_container.html(
                                        '<ul class="list-group"><li class="list-group-item"><i class="fa fa-spinner fa-spin fa-fw"></i> ' +
                                            houzez_vars.autosearch_text +
                                            '</li></ul>'
                                    );
                                }
                            },
                            success: function (data) {
                                ajaxCount--;
                                if (ajaxCount == 0) {
                                    auto_complete_container.show();
                                    if (data != '') {
                                        auto_complete_container
                                            .empty()
                                            .html(data);
                                    }
                                }
                            },
                            error: function (xhr, status, error) {
                                ajaxCount--;
                                if (ajaxCount == 0) {
                                    auto_complete_container.html(
                                        '<ul class="list-group"><li class="list-group-item"><i class="fa fa-spinner fa-spin fa-fw"></i> ' +
                                            error +
                                            '</li></ul>'
                                    );
                                }
                            },
                        });
                    }, 300)
                );

                // Handle mousedown on autocomplete items to update input value
                // before click events fire (fixes half-map search reading old value)
                $(document).on('mousedown', '.auto-complete li', function () {
                    let $this = $(this);
                    let $input;
                    let $container = $this.closest('.auto-complete');

                    $input = $container.siblings(
                        '.houzez-keyword-autocomplete'
                    );

                    if ($dataType == 'banner') {
                        $input = $('.houzez-keyword-autocomplete.is-banner');
                    }

                    $input.val($this.data('text'));
                });

                // Handle click on autocomplete items to hide dropdown
                $(document).on('click', '.auto-complete li', function () {
                    let $this = $(this);
                    let $container = $this.closest('.auto-complete');
                    $container.fadeOut();
                });

                // Handle click on "View All Results" link
                $(document).on('click', '.search-result-view', function () {
                    $('.auto-complete').fadeOut();
                });
            }
        };

        /**
         * Handle reset button click to clear all search fields
         */
        const handleResetSearch = function () {
            $('.reset-search-btn').on('click', function (e) {
                e.preventDefault();

                const $form = $(this).closest('form');

                // Reset text inputs and selects
                $form
                    .find('input[type="text"], input[type="number"], select')
                    .each(function () {
                        $(this).val('');
                    });

                // Reset selects with Select2
                $form.find('select.selectpicker').each(function () {
                    $(this).val('').selectpicker('refresh');
                });

                // Reset checkboxes and radio buttons, except use_radius
                $form
                    .find('input[type="checkbox"], input[type="radio"]')
                    .not('[name="use_radius"]')
                    .prop('checked', false);

                // Ensure use_radius remains checked
                $form.find('input[name="use_radius"]').prop('checked', true);

                // Reset price range sliders
                $form.find('.range-slider').each(function () {
                    const $rangeWrap = $(this).closest('.range-wrap');
                    const fromSlider = $rangeWrap.find(
                        '.min-price-range-slider'
                    )[0];
                    const toSlider = $rangeWrap.find(
                        '.max-price-range-slider'
                    )[0];

                    if (fromSlider && toSlider) {
                        fromSlider.value = fromSlider.min;
                        toSlider.value = toSlider.max;

                        // Trigger input event to update UI
                        $(fromSlider).trigger('input');
                        $(toSlider).trigger('input');
                    }
                });

                // Reset distance slider
                const distanceSlider = $form.find('#radius-range-slider')[0];
                const distanceInput = $form.find('#radius-range-value')[0];
                const distanceText = $form.find('#radius-range-text')[0];

                if (distanceSlider && distanceInput) {
                    // Get default radius from radius-range-value input which has the data-default attribute
                    const defaultRadius =
                        $(distanceInput).data('default') || 50;

                    // Reset the slider value
                    distanceSlider.value = defaultRadius;

                    // Reset the hidden input value (radius-range-value)
                    distanceInput.value = defaultRadius;

                    // Also reset any direct input field with name="radius"
                    $form.find('input[name="radius"]').val(defaultRadius);

                    // Reset the text display if it exists
                    if (distanceText) {
                        distanceText.value = defaultRadius;
                    }

                    // Update fill
                    if (typeof fillDistanceRangeSlider === 'function') {
                        fillDistanceRangeSlider(distanceSlider);
                    } else {
                        // If the function is not directly accessible, use it from the current context
                        const rangeDistance =
                            distanceSlider.max - distanceSlider.min;
                        const position = defaultRadius - distanceSlider.min;

                        // Apply the gradient fill manually
                        const isRTL = document.documentElement.dir === 'rtl';
                        const direction = isRTL ? 'to left' : 'to right';
                        distanceSlider.style.background = `linear-gradient(
                            ${direction},
                            #dce0e0 0%,
                            #dce0e0 ${(position / rangeDistance) * 100}%,
                            ${houzez_vars.primary_color} ${
                            (position / rangeDistance) * 100
                        }%,
                            ${houzez_vars.primary_color} 100%
                        )`;
                    }
                } else {
                    // If slider elements aren't found, still try to reset radius input field
                    const defaultRadius = 50;
                    $form.find('input[name="radius"]').val(defaultRadius);
                }

                // Reset hidden fields
                $form
                    .find('input[type="hidden"]')
                    .not(
                        '[name="_wp_http_referer"], [name="search-location"], [name="radius"]'
                    )
                    .val('');

                // Clear location fields
                $form.find('.location-search input[type="text"]').val('');

                // Clear keyword fields
                $form.find('.keyword-search input').val('');

                // Hide any open dropdowns
                $('.search-dropdown-wrap').removeClass('show');

                // Hide & clear the keyword autocomplete dropdown
                $form.find('.auto-complete').empty().hide();

                // If using any custom sliders, reset them here

                // If this is a half map search, submit the form after reset
                const isHalfMapSearch = $('.main-half-map-wrap').length > 0;

                if (isHalfMapSearch) {
                    // Submit the form after a small delay to ensure all values are properly reset
                    setTimeout(function () {
                        $form.find('.btn-search').trigger('click');
                    }, 100);
                    return false;
                }

                // Don't submit the form - let user click search if they want to
                return false;
            });
        };

        /**
         * Initialize beds and baths counter functionality
         */
        const initBedsAndBaths = function () {
            /* ------------------------------------------------------------------------ */
            /* Beds and baths
            /* ------------------------------------------------------------------------ */
            const beds_baths = function (btn_action, btn_count, btn_val) {
                $('.' + btn_action).on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var current_val = parseInt($('.' + btn_val).val()) || 0;

                    if (
                        btn_action == 'btn_count_plus' ||
                        btn_action == 'btn_beds_plus'
                    ) {
                        current_val++;
                    } else {
                        if (current_val == 0) return;
                        current_val--;
                    }

                    $('.' + btn_count).text(current_val);
                    $('.' + btn_val).val(current_val);
                });
            };

            // Initialize beds and baths buttons
            beds_baths('btn_count_plus', 'baths_count', 'bathrooms');
            beds_baths('btn_count_minus', 'baths_count', 'bathrooms');
            beds_baths('btn_beds_plus', 'beds_count', 'bedrooms');
            beds_baths('btn_beds_minus', 'beds_count', 'bedrooms');
            beds_baths('btn_rooms_plus', 'rooms_count', 'rooms');
            beds_baths('btn_rooms_minus', 'rooms_count', 'rooms');

            // Apply button handler
            $('.btn-apply').on('click', function (e) {
                e.preventDefault();
                $('.advanced-search-v3 .btn-group .dropdown-menu').removeClass(
                    'show'
                );
            });

            // Clear buttons handlers
            $('.clear-baths').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.baths_count').text('0');
                $('.bathrooms').val('');
            });

            $('.clear-beds').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.beds_count').text('0');
                $('.bedrooms').val('');
            });

            $('.clear-rooms').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.rooms_count').text('0');
                $('.rooms').val('');
            });

            $('.clear-checkboxes').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this)
                    .parents('.btn-group')
                    .find('input[type="checkbox"]')
                    .prop('checked', false)
                    .attr('checked', false);
            });
        };

        /**
         * Initialize the save search functionality
         */
        const initSaveSearch = function () {
            $('.save_search_click').on('click', function (e) {
                e.preventDefault();

                var $this = $(this);
                let $parent = $this.parents('.save-search-form-wrap');

                var search_args = $parent
                    .find('input[name="search_args"]')
                    .val();
                var security = $parent
                    .find('input[name="houzez_save_search_ajax"]')
                    .val();
                var search_URI = $parent.find('input[name="search_URI"]').val();

                if (parseInt(houzez.Core.config.userID, 10) === 0) {
                    houzez.Favorites.showLoginModal();
                } else {
                    $.ajax({
                        url: houzez.Core.config.ajaxurl,
                        data: {
                            action: 'houzez_save_search',
                            search_args: search_args,
                            search_URI: search_URI,
                            houzez_save_search_ajax: security,
                        },
                        method: 'POST',
                        dataType: 'JSON',

                        beforeSend: function () {
                            $this
                                .find('.houzez-loader-js')
                                .addClass('loader-show');
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.save_search_click').attr('disabled', true);
                            }
                        },
                        error: function (xhr, status, error) {
                            var err = eval('(' + xhr.responseText + ')');
                            console.log(err.Message);
                        },
                        complete: function () {
                            $this
                                .find('.houzez-loader-js')
                                .removeClass('loader-show');
                        },
                    });
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            mobileSearchOverlay();
            initSearchTabs();
            initStatusChangeHandlers();
            initPriceRangeSliders();
            initKeywordAutocomplete();
            handleResetSearch();
            initBedsAndBaths();
            initDistanceRangeSlider();
            initSaveSearch();
        };

        // Public API
        return {
            init: init,
            mobileSearchOverlay: mobileSearchOverlay,
            statusChangeHandler: statusChangeHandler,
            initPriceRangeSlider: initPriceRangeSlider,
            initSearchTabs: initSearchTabs,
            initKeywordAutocomplete: initKeywordAutocomplete,
            handleResetSearch: handleResetSearch,
            initBedsAndBaths: initBedsAndBaths,
            initDistanceRangeSlider: initDistanceRangeSlider,
            initSaveSearch: initSaveSearch,
        };
    })();

    /* ------------------------------------------------------------------------ */
    /* BLOG
    /* ------------------------------------------------------------------------ */
    houzez.Blog = (function () {
        const resizeMasonryItem = function (item) {
            /* Get the grid object, its row-gap, and the size of its implicit rows */
            var grid = document.getElementsByClassName('masonry')[0],
                rowGap = parseInt(
                    window
                        .getComputedStyle(grid)
                        .getPropertyValue('grid-row-gap')
                ),
                rowHeight = parseInt(
                    window
                        .getComputedStyle(grid)
                        .getPropertyValue('grid-auto-rows')
                );

            /*
             * Spanning for any brick = S
             * Grid's row-gap = G
             * Size of grid's implicitly create row-track = R
             * Height of item content = H
             * Net height of the item = H1 = H + G
             * Net height of the implicit row-track = T = G + R
             * S = H1 / T
             */
            var rowSpan = Math.ceil(
                (item.querySelector('.masonry-content').getBoundingClientRect()
                    .height +
                    rowGap) /
                    (rowHeight + rowGap)
            );

            /* Set the spanning as calculated above (S) */
            item.style.gridRowEnd = 'span ' + rowSpan;
        };

        const resizeAllMasonryItems = function () {
            // Get all item class objects in one list
            var allItems = document.getElementsByClassName('masonry-brick');

            /*
             * Loop through the above list and execute the spanning function to
             * each list-item (i.e. each masonry item)
             */
            for (var i = 0; i < allItems.length; i++) {
                resizeMasonryItem(allItems[i]);
            }
        };

        const waitForImages = function () {
            var allItems = document.getElementsByClassName('masonry-brick');
            for (var i = 0; i < allItems.length; i++) {
                var images = allItems[i].getElementsByTagName('img');
                for (var j = 0; j < images.length; j++) {
                    images[j].addEventListener('load', function () {
                        resizeMasonryItem(this.closest('.masonry-brick'));
                    });
                    if (images[j].complete) {
                        resizeMasonryItem(images[j].closest('.masonry-brick'));
                    }
                }
            }
        };

        const initMasonry = function () {
            /* Resize all the grid items on the load and resize events */
            var masonryEvents = ['load', 'resize'];
            masonryEvents.forEach(function (event) {
                window.addEventListener(event, resizeAllMasonryItems);
            });

            /* Do a resize once more when all the images finish loading */
            waitForImages();
        };

        const init = function () {
            initMasonry();
        };

        return {
            init: init,
            initMasonry: initMasonry,
        };
    })();

    /**
     * LocationFilter Module
     * Handles location filtering functionality
     */
    houzez.LocationFilter = (function () {
        /**
         * Handle select filter functionality
         * @param {jQuery} $this - jQuery element
         * @param {string} dataRef - Data reference
         * @param {string} list_num - List number
         * @param {string} is_on_load - Whether function is called on load
         */
        const houzezSelectFilter = function (
            $this,
            dataRef,
            list_num = '',
            is_on_load = ''
        ) {
            let e = $this.data('target');
            let i = $this.find(':selected').data('ref');

            if (is_on_load == 'yes') {
                e = list_num;
                i = dataRef;
            }

            $('select.' + e).val('');

            if (i == null) {
                $('select.' + e)
                    .find('option')
                    .each(function () {
                        $(this).removeAttr('disabled hidden');
                    });
            } else {
                $('select.' + e)
                    .find('option')
                    .each(function () {
                        const e = $(this).data('belong');
                        const t = $(this).val();

                        if (i != e && t != '') {
                            $(this).prop('disabled', true);
                            $(this).prop('hidden', true);
                        } else {
                            $(this).prop('disabled', false);
                            $(this).prop('hidden', false);
                        }
                    });
            }
            $('select.' + e).selectpicker('refresh');
        };

        /**
         * Initialize location filters on page load
         */
        const initLocationFilters = function () {
            if (
                $('.houzez-search-form-js').length > 0 ||
                $('#location').length > 0
            ) {
                let countryRef, stateRef, cityRef, areasRef;

                if (
                    typeof is_edit_property !== 'undefined' &&
                    is_edit_property
                ) {
                    countryRef = $('#country').data('country');
                    stateRef = $('#countyState').data('state');
                    cityRef = $('#city').data('city');
                    areasRef = $('#neighborhood').data('area');
                } else {
                    countryRef = houzez_vars.s_country;
                    stateRef = houzez_vars.s_state;
                    cityRef = houzez_vars.s_city;
                    areasRef = houzez_vars.s_areas;
                }

                // Handle country filter
                if (
                    ($('.houzez-country-js').length > 0 ||
                        $('#country').length > 0) &&
                    countryRef != ''
                ) {
                    const countryFilter = $('.houzezCountryFilter');
                    houzezSelectFilter(
                        countryFilter,
                        countryRef,
                        'houzezSecondList',
                        'yes'
                    );

                    $(window).on('load', function () {
                        $('.houzezSecondList').val(stateRef);
                        $('select.houzezSecondList').selectpicker('refresh');
                    });
                }

                // Handle state filter
                if (
                    ($('.houzez-state-js').length > 0 ||
                        $('#countyState').length > 0) &&
                    stateRef != ''
                ) {
                    const stateFilter = $('.houzezStateFilter');
                    houzezSelectFilter(
                        stateFilter,
                        stateRef,
                        'houzezThirdList',
                        'yes'
                    );

                    $(window).on('load', function () {
                        $('.houzezThirdList').val(cityRef);
                        $('select.houzezThirdList').selectpicker('refresh');
                    });
                }

                // Handle city filter
                if (
                    ($('.houzez-city-js').length > 0 ||
                        $('#city').length > 0) &&
                    cityRef != ''
                ) {
                    const cityFilter = $('.houzezCityFilter');
                    houzezSelectFilter(
                        cityFilter,
                        cityRef,
                        'houzezFourthList',
                        'yes'
                    );

                    $(window).on('load', function () {
                        $('.houzezFourthList').val(areasRef);
                        $('select.houzezFourthList').selectpicker('refresh');
                    });
                }
            }
        };

        /**
         * Initialize the module
         */
        const init = function () {
            // Attach change event handler
            $('.houzezSelectFilter').on('change', function () {
                houzezSelectFilter($(this));
            });

            // Initialize location filters
            initLocationFilters();
        };

        // Public API
        return {
            init: init,
            houzezSelectFilter: houzezSelectFilter,
        };
    })();

    /**
     * CurrencySwitcher Module
     * Handles currency switching functionality
     */
    houzez.CurrencySwitcher = (function () {
        /**
         * Process currency switch
         * @param {string} selectedCurrencyCode - The selected currency code
         */
        const processCurrencySwitch = function (selectedCurrencyCode) {
            if (!selectedCurrencyCode) {
                console.warn('No currency code provided');
                return;
            }

            // Update hidden input value
            $('#houzez-switch-to-currency').val(selectedCurrencyCode);

            // Show processing modal
            houzez.Core.util.processingModal(
                houzez.Core.config.processing_text
            );

            // Make AJAX request
            $.ajax({
                url: houzez.Core.config.ajaxurl,
                dataType: 'JSON',
                method: 'POST',
                data: {
                    action: 'houzez_currency_converter',
                    currency_converter: selectedCurrencyCode,
                },
                success: function (response) {
                    if (response.success) {
                        window.location.reload(true);
                    } else {
                        houzez.Core.util.processingModalClose();
                        console.warn('Currency conversion failed:', response);

                        // Show error message if provided
                        if (response.msg) {
                            const $messages = $(
                                '.hz-currency-switcher-messages'
                            );
                            houzez.Core.util.showError($messages, response.msg);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    houzez.Core.util.processingModalClose();
                    console.error('Currency switch error:', error);

                    // Show generic error message
                    const $messages = $('.hz-currency-switcher-messages');
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing currency switch'
                    );
                },
            });
        };

        /**
         * Initialize currency switcher functionality
         */
        const initCurrencySwitcher = function () {
            const $currencySwitcherList = $('#hz-currency-switcher-list');

            if ($currencySwitcherList.length === 0) {
                return;
            }

            // Handle currency selection
            $currencySwitcherList.on('click', 'li', function (e) {
                e.preventDefault();
                const selectedCurrencyCode = $(this).data('currency-code');

                if (selectedCurrencyCode) {
                    processCurrencySwitch(selectedCurrencyCode);
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            initCurrencySwitcher();
        };

        // Public API
        return {
            init: init,
            processCurrencySwitch: processCurrencySwitch,
        };
    })();

    /**
     * AreaSwitcher Module
     * Handles area unit switching functionality
     */
    houzez.AreaSwitcher = (function () {
        /**
         * Process area unit switch
         * @param {string} selectedAreaCode - The selected area unit code
         */
        const processAreaSwitch = function (selectedAreaCode) {
            if (!selectedAreaCode) {
                console.warn('No area code provided');
                return;
            }

            // Update hidden input value
            $('#houzez-switch-to-area').val(selectedAreaCode);

            // Show processing modal
            houzez.Core.util.processingModal(
                houzez.Core.config.processing_text
            );

            // Make AJAX request
            $.ajax({
                url: houzez.Core.config.ajaxurl,
                dataType: 'JSON',
                method: 'POST',
                data: {
                    action: 'houzez_switch_area',
                    switch_to_area: selectedAreaCode,
                },
                success: function (response) {
                    if (response.success) {
                        window.location.reload(true);
                    } else {
                        houzez.Core.util.processingModalClose();
                        console.warn('Area unit conversion failed:', response);

                        // Show error message if provided
                        if (response.msg) {
                            const $messages = $('.hz-area-switcher-messages');
                            houzez.Core.util.showError($messages, response.msg);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    houzez.Core.util.processingModalClose();
                    console.error('Area unit switch error:', error);

                    // Show generic error message
                    const $messages = $('.hz-area-switcher-messages');
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing area unit switch'
                    );
                },
            });
        };

        /**
         * Initialize area switcher functionality
         */
        const initAreaSwitcher = function () {
            const $areaSwitcherList = $('#area-switcher-list-js');

            if ($areaSwitcherList.length === 0) {
                return;
            }

            // Handle area unit selection
            $areaSwitcherList.on('click', 'li', function (e) {
                e.preventDefault();
                const selectedAreaCode = $(this).data('area-code');

                if (selectedAreaCode) {
                    processAreaSwitch(selectedAreaCode);
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            initAreaSwitcher();
        };

        // Public API
        return {
            init: init,
            processAreaSwitch: processAreaSwitch,
        };
    })();

    /**
     * Houzez Elementor Mobile Menu Module
     * Handles Elementor mobile menu functionality
     */
    houzez.ElementorMobileMenu = (function () {
        const $win = $(window);

        /* ------------------------------------------------------------------------ */
        /*  Elementor Mobile menu trigger
        /* ------------------------------------------------------------------------ */
        const mobileMenuTrigger = function () {
            $('.houzez-nav-menu-main-mobile-wrap .houzez-menu-toggle').on(
                'click',
                function (e) {
                    $(
                        '.houzez-nav-menu-main-mobile-wrap .navbar-nav, .houzez-nav-menu-main-mobile-wrap .houzez-menu-toggle'
                    ).toggleClass('houzez-nav-menu-active');
                }
            );
        };
        /**
         * Adjust mega menu positioning
         */
        const adjustMegaMenu = function (
            megaMenuSelector,
            containerSelector,
            fullwidthClass,
            customWidthClass,
            customWidthPx
        ) {
            jQuery(megaMenuSelector).each(function () {
                var $megamenu = jQuery(this);
                var windowWidth = jQuery(window).width();
                var isRTL = jQuery('html').attr('dir') === 'rtl';

                // Cache common selections
                var $fullwidthContainer = $megamenu.closest(fullwidthClass);
                var $customWidthContainer = $megamenu.closest(customWidthClass);
                var $container = $megamenu.closest(containerSelector);
                var $navItem = $megamenu.closest('.nav-item');

                // Reset inline styles
                $megamenu.css({ left: '', right: '', width: '' });

                // Case 1: Full viewport width
                if ($fullwidthContainer.length > 0) {
                    $megamenu.css('width', '100vw');

                    // Store current position
                    var originalPosition = $megamenu.css('position');

                    // Temporarily set to absolute to get correct offset
                    if (originalPosition === 'fixed') {
                        $megamenu.css('position', 'absolute');
                    }

                    var offset = $megamenu.offset();

                    // Remove inline position style to let CSS take over
                    if (originalPosition === 'fixed') {
                        $megamenu.css('position', '');
                    }

                    if (offset && typeof offset.left === 'number') {
                        var position = offset.left * -1;
                        if (isRTL) {
                            $megamenu.css('right', -position + 'px');
                        } else {
                            $megamenu.css('left', position + 'px');
                        }
                    }
                }
                // Case 2: Custom width
                else if ($customWidthContainer.length > 0) {
                    $megamenu.css('width', customWidthPx + 'px');
                    if ($navItem.length > 0) {
                        var menuItemOffset = $navItem.offset().left;
                        var parentOffset = $megamenu.parent().offset();
                        parentOffset = parentOffset ? parentOffset.left : 0;
                        var position = menuItemOffset - parentOffset;

                        // Adjust if the dropdown goes outside the window
                        if (menuItemOffset + customWidthPx > windowWidth) {
                            position -= customWidthPx - $navItem.width();
                        }

                        if (isRTL) {
                            $megamenu.css('right', -position + 'px');
                        } else {
                            $megamenu.css('left', position + 'px');
                        }
                    }
                }
                // Case 3: Use the nearest container's width
                else if ($container.length > 0) {
                    var containerWidth = $container.width();
                    $megamenu.css('width', containerWidth);

                    var containerOffsetObj = $container.offset();
                    var navItemOffsetObj = $navItem.offset();

                    if (containerOffsetObj && navItemOffsetObj) {
                        var containerOffset = containerOffsetObj.left;
                        var megamenuOffset = navItemOffsetObj.left;
                        var position = containerOffset - megamenuOffset;

                        if (isRTL) {
                            $megamenu.css('right', -position + 'px');
                        } else {
                            $megamenu.css('left', position + 'px');
                        }
                    }
                }
            });
        };

        /**
         * Set full width menu element
         */
        const setMenuFullWidthEle = function () {
            if ($('#houzez_toggle').length > 0) {
                var leftDistance = $('#houzez_toggle').offset().left;
                $('.houzez-nav-mobile-menu-fullwidth .main-mobile-nav').css(
                    'left',
                    'calc(50vw - ' + leftDistance + 'px)'
                );
            }
        };

        /**
         * Initialize all UI-related functionality
         */
        const init = function () {
            // Initialize menu adjustments
            $win.on('load', function () {
                adjustMegaMenu(
                    '.houzez-elementor-menu .dropdown-menu.megamenu',
                    '.e-con-inner',
                    '.menu-item-design-full-width',
                    '.menu-item-design-custom-size',
                    200
                );
                setTimeout(setMenuFullWidthEle, 500);
            });

            // Window resize events
            $win.resize(function () {
                adjustMegaMenu(
                    '.houzez-elementor-menu .dropdown-menu.megamenu',
                    '.e-con-inner',
                    '.menu-item-design-full-width',
                    '.menu-item-design-custom-size',
                    200
                );
                setMenuFullWidthEle();
            });

            mobileMenuTrigger();
        };

        // Public API
        return {
            init: init,
            adjustMegaMenu: adjustMegaMenu,
            mobileMenuTrigger: mobileMenuTrigger,
        };
    })();

    /**
     * Property Preview Module
     * Handles property preview functionality
     */
    houzez.PropertyPreview = (function () {
        const init = function () {
            // Remove any existing handlers first to prevent duplicates
            $(document).off('click.houzezPreview', '.hz-show-lightbox-js');

            // Use event delegation with namespaced event for proper cleanup
            $(document).on(
                'click.houzezPreview',
                '.hz-show-lightbox-js',
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Hide all tooltips when preview is clicked
                    houzez.Core.hideAllTooltips();

                    var listing_id = $(this).data('listid');
                    var $parents = $(this).parents('.item-wrap');
                    var preview_loader = $parents.find('.preview_loader');

                    $.ajax({
                        type: 'post',
                        url: houzez.Core.config.ajaxurl,
                        data: {
                            action: 'load_lightbox_content',
                            listing_id: listing_id,
                        },
                        beforeSend: function () {
                            preview_loader
                                .empty()
                                .append(
                                    '' +
                                        '<div class="houzez-overlay-loading">' +
                                        '<div class="overlay-placeholder">' +
                                        '<div class="loader-ripple spinner">' +
                                        '<div class="bounce1"></div>' +
                                        '<div class="bounce2"></div>' +
                                        '<div class="bounce3"></div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>'
                                );
                        },
                        complete: function () {
                            preview_loader.empty();
                        },
                        success: function (response) {
                            $('#hz-listing-model-content').html(response);

                            // Bootstrap 5 modal initialization
                            var modalElement = document.getElementById(
                                'houzez-listing-lightbox'
                            );
                            if (modalElement) {
                                // Check if modal instance already exists
                                var existingModal =
                                    bootstrap.Modal.getInstance(modalElement);
                                if (existingModal) {
                                    existingModal.show();
                                } else {
                                    // Create new modal instance with proper options
                                    var myModal = new bootstrap.Modal(
                                        modalElement,
                                        {
                                            backdrop: true,
                                            keyboard: true,
                                            focus: true,
                                        }
                                    );
                                    myModal.show();
                                }
                            }

                            //$('.lightbox-slider').slick('unslick');

                            $('.lightbox-slider')
                                .not('.slick-initialized')
                                .slick({
                                    rtl: houzez.Core.config.houzez_rtl,
                                    lazyLoad: 'ondemand',
                                    infinite: true,
                                    speed: 300,
                                    slidesToShow: 1,
                                    arrows: true,
                                    adaptiveHeight: true,
                                });

                            $('#houzez-listing-lightbox').on(
                                'shown.bs.modal',
                                function (e) {
                                    $('.lightbox-slider').slick('setPosition');
                                }
                            );

                            $('#houzez-listing-lightbox').on(
                                'hide.bs.modal',
                                function (e) {
                                    $('.lightbox-slider').slick('unslick');
                                }
                            );

                            $('.btn-expand').on('click', function () {
                                $('.lightbox-gallery-wrap').toggleClass(
                                    'lightbox-gallery-full-wrap'
                                );
                                $('.lightbox-slider').slick('setPosition');
                            });

                            $('.btn-email').on('click', function () {
                                $('.lightbox-form-wrap').toggleClass(
                                    'lightbox-form-wrap-show'
                                );
                            });

                            houzez.Core.reinitializeAjaxFunctions();
                        },
                        error: function (xhr, status, error) {
                            console.log(error);
                        },
                    });
                }
            );
        };

        // Public API
        return {
            init: init,
        };
    })();

    /* ------------------------------------------------------------------------ */
    /*  Mobile Navigation
    /* ------------------------------------------------------------------------ */
    houzez.MobileNav = (function () {
        const initMobileNav = function () {};

        const setupToggleButtons = function () {};

        const init = function () {
            initMobileNav();
            setupToggleButtons();
        };

        return {
            init: init,
        };
    })();

    /* ------------------------------------------------------------------------ */
    /*  Top Banner Full Screen
    /* ------------------------------------------------------------------------ */
    houzez.TopBannerFullScreen = (function () {
        const getWindowHeight = function () {
            return $(window).innerHeight();
        };

        const setTopBannerFullScreen = function () {
            var totalTopBarsHeight = 0;
            var searchH = 0;
            var topBarH = 0;
            var totalBannerHeight = 0;
            var window_height = $(window).innerHeight();
            var admin_bar_height = houzez.Core.config.wpadminbar_height;
            var topBarB = $('.top-bar-wrap');
            var header_area = $('.header-main-wrap');
            if (!header_area.length) {
                header_area = $('#header-hz-elementor');
            }
            var header_area_height = header_area.outerHeight();
            var advanced_search_nav = $('.advanced-search-nav');
            var advanced_search_nav_height = advanced_search_nav.outerHeight();
            searchH = window_height - header_area_height;

            if (header_area.hasClass('header-transparent-wrap')) {
                if (topBarB.length) {
                    topBarH = topBarB.outerHeight();
                }
                totalBannerHeight =
                    getWindowHeight() - (topBarH + admin_bar_height);
            } else {
                if (
                    header_area.length &&
                    advanced_search_nav.length &&
                    !advanced_search_nav.hasClass('search-hidden')
                ) {
                    totalTopBarsHeight =
                        parseInt(header_area_height) +
                        parseInt(advanced_search_nav_height);
                } else if (header_area.length) {
                    totalTopBarsHeight = parseInt(header_area_height);
                }
                totalBannerHeight =
                    getWindowHeight() - (totalTopBarsHeight + admin_bar_height);
            }

            $('.top-banner-wrap-fullscreen').css('height', totalBannerHeight);
        };

        const init = function () {
            if (
                !houzez.Core.config.houzez_is_splash &&
                $('.top-banner-wrap-fullscreen').length > 0
            ) {
                setTopBannerFullScreen();

                $(window).on('resize', function () {
                    setTopBannerFullScreen();
                });
            }
        };

        return {
            init: init,
        };
    })();

    /**
     * Reviews Module
     * Handles property reviews functionality
     */
    houzez.Reviews = (function () {
        // Like/dislike handler for reviews
        const review_likes = function () {
            $(document).on('click', '.hz-like-dislike-js', function (e) {
                e.preventDefault();
                var $this = $(this);
                var $parent = $this.parents('.likes-container-js');

                if ($this.hasClass('already-voted')) {
                    $parent.find('.vote-msg').text($this.data('msg')).show();
                    setTimeout(function () {
                        $parent.find('.vote-msg').hide();
                    }, 3000);
                } else {
                    var review_id = $this.data('id');
                    var type = $this.data('type');

                    $.ajax({
                        type: 'post',
                        url: houzez.Core.config.ajaxurl,
                        dataType: 'JSON',
                        data: {
                            action: 'reviews_likes_dislikes',
                            type: type,
                            review_id: review_id,
                        },
                        beforeSend: function () {
                            $parent.find('.vote-msg').empty();
                            $parent
                                .find('.houzez-loader-js')
                                .addClass('loader-show');
                            if (type == 'likes') {
                                $('.review-dislike-button a').removeClass(
                                    'already-voted'
                                );
                            } else if (type == 'dislikes') {
                                $('.review-like-button a').removeClass(
                                    'already-voted'
                                );
                            }
                        },
                        success: function (response) {
                            if (response.success) {
                                $parent
                                    .find('.likes-count')
                                    .text(response.data.likes);
                                $parent
                                    .find('.dislikes-count')
                                    .text(response.data.dislikes);
                                $parent
                                    .find('.vote-msg')
                                    .text(response.data.message)
                                    .show();
                            } else {
                                $parent
                                    .find('.vote-msg')
                                    .text(response.data.message)
                                    .show();
                            }
                            setTimeout(function () {
                                $parent.find('.vote-msg').hide();
                            }, 3000);
                            $this.addClass('already-voted');
                        },
                        error: function (xhr, status, error) {
                            var err = eval('(' + xhr.responseText + ')');
                            console.log(err.Message);
                        },
                        complete: function () {
                            $parent
                                .find('.houzez-loader-js')
                                .removeClass('loader-show');
                        },
                    });
                }
            });
        };

        // AJAX load reviews
        const listing_review_ajax = function (sortby, listing_id, paged) {
            var review_container = $('#houzez_reviews_container');
            var review_post_type = $('input[name="review_post_type"]').val();

            $.ajax({
                type: 'post',
                url: houzez.Core.config.ajaxurl,
                data: {
                    action: 'houzez_ajax_review',
                    sortby: sortby,
                    listing_id: listing_id,
                    review_post_type: review_post_type,
                    paged: paged,
                },
                beforeSend: function () {
                    review_container
                        .empty()
                        .append(
                            '<div id="houzez-map-loading">' +
                                '<div class="mapPlaceholder">' +
                                '<div class="loader-ripple spinner">' +
                                '<div class="bounce1"></div>' +
                                '<div class="bounce2"></div>' +
                                '<div class="bounce3"></div>' +
                                '</div>' +
                                '</div>' +
                                '</div>'
                        );
                    $('html, body').animate(
                        {
                            scrollTop:
                                $('#property-review-wrap').offset().top - 50,
                        },
                        'slow'
                    );
                },
                success: function (data) {
                    review_container.empty();
                    review_container.html(data);
                    review_likes();
                },
                error: function (xhr, status, error) {
                    var err = eval('(' + xhr.responseText + ')');
                    console.log(err.Message);
                },
                complete: function () {},
            });
        };

        // Submit review handler
        const submitReview = function () {
            $(document).on('click', '#submit-review', function (e) {
                e.preventDefault();
                var $this = $(this);
                var $form = $this.parents('form');
                var $messages = $form.find('.form_messages');
                var $is_bottom = $('.is_bottom').val();

                if ($is_bottom == 'bottom') {
                    $messages = $form.find('.form_messages');
                }
                $messages.empty();

                $.ajax({
                    type: 'post',
                    url: houzez.Core.config.ajaxurl,
                    data: $form.serialize(),
                    dataType: 'JSON',
                    beforeSend: function () {
                        $this.attr('disabled', true);
                        $this.find('.houzez-loader-js').addClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            // Clear form fields
                            $form
                                .find(
                                    'input[name="name"], input[name="mobile"], input[name="email"]'
                                )
                                .val('');
                            $form.find('textarea').val('');

                            // Show success message
                            houzez.Core.util.showSuccess(
                                $messages,
                                response.data.message
                            );
                        } else {
                            // Show error message
                            houzez.Core.util.showError(
                                $messages,
                                response.data.message
                            );
                        }

                        // Reset captcha (supports both reCaptcha and Turnstile)
                        houzez.Core.util.resetCaptcha($form);

                        // Handle redirection if needed
                        if (
                            houzez_vars.agent_redirection != '' &&
                            response.success
                        ) {
                            setTimeout(function () {
                                window.location.replace(
                                    houzez_vars.agent_redirection
                                );
                            }, 500);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this.attr('disabled', false);
                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            });
        };

        // Sort and pagination handlers
        const sortAndPaginate = function () {
            $(document).on('change', '#sort_review', function () {
                var sortby = $(this).val();
                var listing_id = $('#review_listing_id').val();
                var paged = 1;
                $('#review_paged').val(paged);
                $('#review_prev').attr('disabled', true);
                $('#review_next').attr('disabled', false);
                listing_review_ajax(sortby, listing_id, paged);
            });

            $(document).on('click', '#review_next', function (e) {
                e.preventDefault();
                $('#review_prev').removeAttr('disabled');
                var sortby = $('#sort_review').val();
                var total_pages = $('#total_pages').val();
                var listing_id = $('#review_listing_id').val();
                var paged = $('#review_paged').val();
                paged = Number(paged) + 1;
                $('#review_paged').val(paged);
                if (paged == total_pages) {
                    $(this).attr('disabled', true);
                }
                listing_review_ajax(sortby, listing_id, paged);
            });

            $(document).on('click', '#review_prev', function (e) {
                e.preventDefault();
                $('#review_next').removeAttr('disabled');
                var sortby = $('#sort_review').val();
                var listing_id = $('#review_listing_id').val();
                var paged = $('#review_paged').val();
                paged = Number(paged) - 1;
                $('#review_paged').val(paged);
                if (paged <= 1) {
                    $(this).attr('disabled', true);
                }
                listing_review_ajax(sortby, listing_id, paged);
            });
        };

        // Initialize reviews module
        const init = function () {
            review_likes();
            submitReview();
            sortAndPaginate();
        };

        // Public API
        return {
            init: init,
            review_likes: review_likes,
            listing_review_ajax: listing_review_ajax,
        };
    })();

    houzez.Parallax = (function () {
        // closure vars
        let $els;
        const speedDefault = 0.5;
        let ticking = false;

        const init = function () {
            $els = $('.houzez-parallax');
            if ($els.length === 0) {
                return;
            }

            _setBackgrounds();
            _bindEvents();
            _update(); // position backgrounds on load
        };

        const _setBackgrounds = function () {
            $els.each(function () {
                const $el = $(this);
                const src = $el.data('parallax-bg-image');
                if (src) {
                    $el.css({
                        'background-image': `url("${src}")`,
                        'background-size': 'cover',
                        'background-position': 'center center',
                        'background-repeat': 'no-repeat',
                    });
                }
            });
        };

        const _bindEvents = function () {
            $(window).on('scroll', _onScroll);
        };

        const _onScroll = function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    _update();
                    ticking = false;
                });
                ticking = true;
            }
        };

        const _update = function () {
            const scrollY = $(window).scrollTop();

            $els.each(function () {
                const $el = $(this);
                const speed =
                    parseFloat($el.data('parallax-speed')) || speedDefault;
                const top = $el.offset().top;
                const offset = scrollY - top;

                $el.css('background-position', `center ${offset * speed}px`);
            });
        };

        // Public API
        return {
            init: init,
        };
    })();

    // Social login module
    houzez.SocialLogin = (function () {
        const initFacebookLogin = function () {
            $('.hz-facebook-login').on('click', function () {
                var current = $(this);
                loginViaFacebook(current);
            });
        };

        const loginViaFacebook = function (current) {
            var $messages = $('.hz-social-messages');
            const config = houzez.Core.config;

            $.ajax({
                type: 'POST',
                url: config.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'houzez_facebook_login_oauth',
                },
                beforeSend: function () {
                    houzez.Core.util.showSuccess(
                        $messages,
                        config.login_loading
                    );
                    current.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    current
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                },
                success: function (response) {
                    if (response.success) {
                        window.location.replace(response.url);
                    } else {
                        houzez.Core.util.showError($messages, response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Facebook login error: ' + error);
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing login'
                    );
                },
            });
        };

        const initGoogleLogin = function () {
            $('.hz-google-login').on('click', function () {
                var current = $(this);
                loginViaGoogle(current);
            });
        };

        const loginViaGoogle = function (current) {
            var $messages = $('#hz-login-messages');
            const config = houzez.Core.config;

            $.ajax({
                type: 'POST',
                url: config.ajaxurl,
                data: {
                    action: 'houzez_google_login_oauth',
                },
                beforeSend: function () {
                    houzez.Core.util.showSuccess(
                        $messages,
                        config.login_loading
                    );
                    current.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    current
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                },
                success: function (data) {
                    window.location.replace(data);
                },
                error: function (xhr, status, error) {
                    console.error('Google login error: ' + error);
                    houzez.Core.util.showError($messages, error);
                },
            });
        };

        const initLinkAccount = function () {
            $('#houzez-link-account').on('click', function (e) {
                e.preventDefault();
                var current = $(this);
                linkAccount(current);
            });
        };

        const linkAccount = function (current) {
            var $form = current.parents('form');
            var $messages = $('#hz-link-messages');
            const config = houzez.Core.config;

            $.ajax({
                type: 'post',
                url: config.ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    current.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    current
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                },
                success: function (response) {
                    if (response.success) {
                        houzez.Core.util.showSuccess($messages, response.msg);
                        window.location.replace(response.redirect_to);
                    } else {
                        houzez.Core.util.showError($messages, response.msg);
                    }

                    // Reset captcha (supports both reCaptcha and Turnstile)
                    houzez.Core.util.resetCaptcha($form);
                },
                error: function (xhr, status, error) {
                    console.error('Link account error: ' + error);
                    houzez.Core.util.showError(
                        $messages,
                        'Error processing account link'
                    );
                },
            });
        };

        const initCreateAccount = function () {
            $('#houzez-create-account-btn').on('click', function (e) {
                e.preventDefault();
                var current = $(this);
                createAccount(current);
            });
        };

        const createAccount = function (current) {
            var $form = current.parents('form');
            var $messages = $('#hz-create-messages');
            const config = houzez.Core.config;

            $.ajax({
                type: 'post',
                url: config.ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function () {
                    current.find('.houzez-loader-js').addClass('loader-show');
                },
                complete: function () {
                    current
                        .find('.houzez-loader-js')
                        .removeClass('loader-show');
                },
                success: function (response) {
                    if (response.success) {
                        houzez.Core.util.showSuccess($messages, response.msg);
                        window.location.replace(response.redirect_to);
                    } else {
                        houzez.Core.util.showError($messages, response.msg);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Create account error: ' + error);
                    houzez.Core.util.showError(
                        $messages,
                        'Error creating account'
                    );
                },
            });
        };

        const initSocialLoginPanel = function () {
            const $mainStepWrap = $('.main-step-wrap');
            const $newAccountWrap = $('.new-account-wrap');
            const $linkAccountWrap = $('.link-account-wrap');

            const toggleVisibility = function (hide1, hide2, show) {
                hide1.hide();
                hide2.hide();
                show.show();
            };

            $(document).on('click', '.btn-link-account', function (event) {
                event.preventDefault();
                toggleVisibility(
                    $mainStepWrap,
                    $newAccountWrap,
                    $linkAccountWrap
                );
            });

            $(document).on('click', '.btn-create-account', function (event) {
                event.preventDefault();
                toggleVisibility(
                    $mainStepWrap,
                    $linkAccountWrap,
                    $newAccountWrap
                );
            });

            $(document).on('click', '.hz-fb-cancel', function (event) {
                event.preventDefault();
                toggleVisibility(
                    $linkAccountWrap,
                    $newAccountWrap,
                    $mainStepWrap
                );
            });
        };

        const init = function () {
            initFacebookLogin();
            initGoogleLogin();
            initLinkAccount();
            initCreateAccount();
            initSocialLoginPanel();
        };

        return {
            init: init,
            loginViaFacebook: loginViaFacebook,
            loginViaGoogle: loginViaGoogle,
            linkAccount: linkAccount,
            createAccount: createAccount,
        };
    })();

    // Payments module
    houzez.Payments = (function () {
        /**
         * Handle changing listing fee for featured properties
         */
        const changeListingFeeForFeatured = function () {
            $('.prop_featured').on('change', function () {
                var currency_symbol = houzez_vars.currency_symbol;
                var currency_position = houzez_vars.currency_position;
                var total_price,
                    total_price_with_currency,
                    price_regular_with_currency,
                    price_regular_with_tax_currency;

                var price_regular = parseFloat($('#submission_price').text());
                var price_featured = parseFloat(
                    $('#submission_featured_price').text()
                );

                // Get tax information
                var submission_tax_percent =
                    parseFloat($('#submission_tax_percent').text()) || 0;
                var featured_tax_percent =
                    parseFloat($('#featured_tax_percent').text()) || 0;
                var submission_tax_amount =
                    parseFloat($('#submission_tax_amount').text()) || 0;
                var featured_tax_amount =
                    parseFloat($('#featured_tax_amount').text()) || 0;

                // Calculate total with taxes
                var price_regular_with_tax =
                    price_regular + submission_tax_amount;
                var price_featured_with_tax =
                    price_featured + featured_tax_amount;

                total_price = price_regular_with_tax + price_featured_with_tax;

                if (currency_position === 'after') {
                    price_regular_with_currency =
                        price_regular + '' + currency_symbol;
                    price_regular_with_tax_currency =
                        price_regular_with_tax + '' + currency_symbol;
                    total_price_with_currency =
                        total_price + '' + currency_symbol;
                } else {
                    price_regular_with_currency =
                        currency_symbol + '' + price_regular;
                    price_regular_with_tax_currency =
                        currency_symbol + '' + price_regular_with_tax;
                    total_price_with_currency =
                        currency_symbol + '' + total_price;
                }

                if ($(this).is(':checked')) {
                    $('#submission_total_price').text(
                        total_price_with_currency
                    );
                    $('#featured_pay').val(1);
                    $('#houzez_listing_price').val(total_price);
                    $('input[name="pay_ammout"]').val(total_price * 100);
                } else {
                    $('#submission_total_price').text(
                        price_regular_with_tax_currency
                    );
                    $('#featured_pay').val(0);
                    $('input[name="pay_ammout"]').val(
                        price_regular_with_tax * 100
                    );
                    $('#houzez_listing_price').val(price_regular_with_tax);
                }
                return false;
            });
        };

        /**
         * Handle Paypal payment for per listing
         * @param {number} property_id - Property ID
         * @param {number} is_prop_featured - Is property featured
         * @param {number} is_prop_upgrade - Is property upgrade
         * @param {string} relist_mode - Relist mode
         */
        const paypalPerListingPayment = function (
            property_id,
            is_prop_featured,
            is_prop_upgrade,
            relist_mode
        ) {
            $.ajax({
                type: 'post',
                url: houzez.Core.config.ajaxurl,
                data: {
                    action: 'houzez_property_paypal_payment',
                    prop_id: property_id,
                    is_prop_featured: is_prop_featured,
                    is_prop_upgrade: is_prop_upgrade,
                    relist_mode: relist_mode,
                },
                success: function (response) {
                    window.location.href = response;
                },
                error: function (xhr, status, error) {
                    var err = eval('(' + xhr.responseText + ')');
                    console.log(err.Message);
                },
            });
        };

        /**
         * Handle Stripe payment for per listing
         * @param {number} property_id - Property ID
         * @param {number} is_prop_featured - Is property featured
         * @param {number} is_prop_upgrade - Is property upgrade
         * @param {string} relist_mode - Relist mode
         */
        const stripePerListingPayment = function (
            property_id,
            is_prop_featured,
            is_prop_upgrade,
            relist_mode
        ) {
            $.ajax({
                type: 'post',
                url: houzez.Core.config.ajaxurl,
                dataType: 'JSON',
                data: {
                    action: 'houzez_property_stripe_payment',
                    prop_id: property_id,
                    is_prop_featured: is_prop_featured,
                    is_prop_upgrade: is_prop_upgrade,
                    relist_mode: relist_mode,
                },
                success: function (response) {
                    if (response.status) {
                        window.location.href = response.paymeny_link;
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    var err = eval('(' + xhr.responseText + ')');
                    console.log(err.Message);
                },
            });
        };

        /**
         * Handle bank transfer per listing payment
         * @param {number} prop_id - Property ID
         * @param {number} listing_price - Listing price
         */
        const bankTransferPerListing = function (prop_id, listing_price) {
            var is_featured = $('input[name="featured_pay"]').val();
            var is_upgrade = $('input[name="is_upgrade"]').val();

            jQuery.ajax({
                type: 'POST',
                url: houzez.Core.config.ajaxurl,
                data: {
                    action: 'houzez_direct_pay_per_listing',
                    prop_id: prop_id,
                    is_featured: is_featured,
                    is_upgrade: is_upgrade,
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function (errorThrown) {},
            });
        };

        /**
         * Handle WooCommerce payment for per listing
         * @param {number} listID - Listing ID
         * @param {number} is_featured - Is property featured
         */
        const wooCommercePerListingPayment = function (listID, is_featured) {
            $.ajax({
                type: 'POST',
                url: houzez.Core.config.ajaxurl,
                data: {
                    action: 'houzez_perlist_woo_pay',
                    listing_id: listID,
                    is_featured: is_featured,
                },
                success: function (data) {
                    if (data.success != false) {
                        window.location.href = houzez_vars.woo_checkout_url;
                    } else {
                        houzez.Core.util.processingModalClose();
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                    houzez.Core.util.processingModalClose();
                },
            });
        };

        /**
         * Handle WooCommerce payment for package
         * @param {number} packId - Package ID
         */
        const wooCommercePackagePayment = function (packId) {
            $.ajax({
                type: 'POST',
                url: houzez.Core.config.ajaxurl,
                data: {
                    action: 'houzez_woo_pay_package',
                    package_id: packId,
                },
                success: function (data) {
                    if (data.success != false) {
                        window.location.href = houzez_vars.woo_checkout_url;
                    } else {
                        houzez.Core.util.processingModalClose();
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                    houzez.Core.util.processingModalClose();
                },
            });
        };

        /**
         * Initialize payment event listeners
         */
        const initPaymentEvents = function () {
            $('#houzez_complete_order').on('click', function (e) {
                e.preventDefault();
                var payment_gateway,
                    is_prop_featured,
                    is_prop_upgrade,
                    relist_mode;
                var property_id, houzez_listing_price;

                payment_gateway = $(
                    "input[name='houzez_payment_type']:checked"
                ).val();
                is_prop_featured = $("input[name='featured_pay']").val();
                is_prop_upgrade = $("input[name='is_upgrade']").val();
                relist_mode = $("input[name='relist_mode']").val();

                property_id = $('#houzez_property_id').val();
                houzez_listing_price = $('#houzez_listing_price').val();

                if (payment_gateway == 'paypal') {
                    houzez.Core.util.processingModal(
                        houzez_vars.paypal_connecting
                    );
                    paypalPerListingPayment(
                        property_id,
                        is_prop_featured,
                        is_prop_upgrade,
                        relist_mode
                    );
                } else if (payment_gateway == 'stripe') {
                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    stripePerListingPayment(
                        property_id,
                        is_prop_featured,
                        is_prop_upgrade,
                        relist_mode
                    );
                } else if (payment_gateway == 'direct_pay') {
                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    bankTransferPerListing(property_id, houzez_listing_price);
                }
                return;
            });

            // WooCommerce Per Listing Payment
            $('.houzez-woocommerce-pay').on('click', function (e) {
                e.preventDefault();

                const listID = $(this).data('listid');
                const is_featured = $(this).data('featured');

                houzez.Core.util.processingModal(
                    houzez.Core.config.processing_text
                );
                wooCommercePerListingPayment(listID, is_featured);
            });

            // WooCommerce Package Payment
            $('.houzez-woocommerce-package').on('click', function (e) {
                e.preventDefault();

                if (
                    parseInt(houzez.Core.config.userID, 10) === 0 ||
                    houzez.Core.config.userID === undefined
                ) {
                    $('#login-register-form').modal('show');
                    $('.login-form-tab').addClass('active show');
                    $('.modal-toggle-1.nav-link').addClass('active');
                } else {
                    const packId = $(this).data('packid');

                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    wooCommercePackagePayment(packId);
                }
            });
        };

        /**
         * Initialize payment method options and UI
         */
        const initPaymentOptions = function () {
            // Get payment method selector
            const $paymentMethodSelect = $('.payment-method-select');

            if ($paymentMethodSelect.length === 0) {
                return; // Exit if element doesn't exist on the page
            }

            // Initialize UI based on current payment type
            const initPaymentUI = function (paymentType) {
                // Hide all payment sections first
                $(
                    '.recurring-payment-paypal, .recurring-payment-stripe'
                ).hide();

                // Show appropriate section based on payment type
                if (paymentType === 'paypal') {
                    $('.recurring-payment-stripe').hide();
                    $('.recurring-payment-paypal').fadeIn(300);
                } else if (paymentType === 'stripe') {
                    $('.recurring-payment-paypal').hide();
                    $('.recurring-payment-stripe').fadeIn(300);
                }
            };

            // Set initial state based on default selected payment method
            let initialPaymentType = $paymentMethodSelect.val();
            initPaymentUI(initialPaymentType);

            // Handle payment method change event
            $paymentMethodSelect.on('change', function () {
                const paymentType = $(this).val();
                initPaymentUI(paymentType);
            });

            // Handle recurring payment checkbox for Stripe
            $('#stripe_package_recurring').on('change', function () {
                if ($(this).is(':checked')) {
                    if ($('#houzez_stripe_recurring').length === 0) {
                        $('.houzez_payment_form').append(
                            '<input type="hidden" name="houzez_stripe_recurring" id="houzez_stripe_recurring" value="1">'
                        );
                    }
                } else {
                    $('#houzez_stripe_recurring').remove();
                }
            });

            // Initialize recurring checkbox to match its current state
            if (
                $('#stripe_package_recurring').is(':checked') &&
                $('#houzez_stripe_recurring').length === 0
            ) {
                $('.houzez_payment_form').append(
                    '<input type="hidden" name="houzez_stripe_recurring" id="houzez_stripe_recurring" value="1">'
                );
            }
        };

        /**
         * Initialize membership payment functionality
         */
        const initMembershipPayment = function () {
            /**
             * Handle Stripe package payment
             * @param {number} houzez_package_id - Package ID
             * @param {boolean} is_stripe_recurring - Whether the payment is recurring
             */
            const stripePackagePayment = function (
                houzez_package_id,
                is_stripe_recurring
            ) {
                $.ajax({
                    type: 'POST',
                    url: houzez.Core.config.ajaxurl,
                    dataType: 'JSON',
                    data: {
                        action: 'houzez_stripe_package_payment',
                        package_id: houzez_package_id,
                        is_stripe_recurring: is_stripe_recurring,
                    },
                    success: function (response) {
                        if (response.status) {
                            window.location.href = response.paymeny_link;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                });
            };

            /**
             * Handle PayPal package payment
             * @param {number} houzez_package_price - Package price
             * @param {string} houzez_package_name - Package name
             * @param {number} houzez_package_id - Package ID
             */
            const paypalPackagePayment = function (
                houzez_package_price,
                houzez_package_name,
                houzez_package_id
            ) {
                $.ajax({
                    type: 'POST',
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_paypal_package_payment',
                        houzez_package_price: houzez_package_price,
                        houzez_package_name: houzez_package_name,
                        houzez_package_id: houzez_package_id,
                    },
                    success: function (data) {
                        window.location.href = data;
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                });
            };

            /**
             * Handle recurring PayPal package payment
             * @param {number} houzez_package_price - Package price
             * @param {string} houzez_package_name - Package name
             * @param {number} houzez_package_id - Package ID
             */
            const recuringPaypalPackagePayment = function (
                houzez_package_price,
                houzez_package_name,
                houzez_package_id
            ) {
                jQuery.ajax({
                    type: 'POST',
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_recuring_paypal_package_payment',
                        houzez_package_name: houzez_package_name,
                        houzez_package_id: houzez_package_id,
                        houzez_package_price: houzez_package_price,
                    },
                    success: function (data) {
                        window.location.href = data;
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                });
            };

            /**
             * Handle direct bank transfer package payment
             * @param {number} houzez_package_id - Package ID
             * @param {number} houzez_package_price - Package price
             * @param {string} houzez_package_name - Package name
             */
            const directBankTransferPackage = function (
                houzez_package_id,
                houzez_package_price,
                houzez_package_name
            ) {
                jQuery.ajax({
                    type: 'POST',
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_direct_pay_package',
                        selected_package: houzez_package_id,
                    },
                    success: function (data) {
                        window.location.href = data;
                    },
                    error: function (errorThrown) {},
                });
            };

            /**
             * Handle free membership package
             * @param {number} houzez_package_id - Package ID
             */
            const freeMembershipPackage = function (houzez_package_id) {
                jQuery.ajax({
                    type: 'POST',
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_free_membership_package',
                        selected_package: houzez_package_id,
                    },
                    success: function (data) {
                        window.location.href = data;
                    },
                    error: function (errorThrown) {},
                });
            };

            /**
             * Process membership data based on payment method
             * @param {jQuery} currnt - Current jQuery element
             * @returns {boolean} - Always returns false
             */
            const membershipData = function (currnt) {
                var payment_gateway = $(
                    "input[name='houzez_payment_type']:checked"
                ).val();
                var houzez_package_price = $(
                    "input[name='houzez_package_price']"
                ).val();
                var houzez_package_id = $(
                    "input[name='houzez_package_id']"
                ).val();
                var houzez_package_name = $('#houzez_package_name').text();

                if (payment_gateway == 'paypal') {
                    houzez.Core.util.processingModal(
                        houzez_vars.paypal_connecting
                    );
                    if ($('#paypal_package_recurring').is(':checked')) {
                        recuringPaypalPackagePayment(
                            houzez_package_price,
                            houzez_package_name,
                            houzez_package_id
                        );
                    } else {
                        paypalPackagePayment(
                            houzez_package_price,
                            houzez_package_name,
                            houzez_package_id
                        );
                    }
                } else if (payment_gateway == 'stripe') {
                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    var is_stripe_recurring = $('#houzez_stripe_recurring').is(
                        ':checked'
                    );
                    stripePackagePayment(
                        houzez_package_id,
                        is_stripe_recurring
                    );
                } else if (payment_gateway == 'direct_pay') {
                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    directBankTransferPackage(
                        houzez_package_id,
                        houzez_package_price,
                        houzez_package_name
                    );
                } else {
                    houzez.Core.util.processingModal(
                        houzez.Core.config.processing_text
                    );
                    freeMembershipPackage(houzez_package_id);
                }

                return false;
            };

            /**
             * Register user with membership
             * @param {jQuery} currnt - Current jQuery element
             */
            const registerUserWithMembership = function (currnt) {
                var $form = currnt.parents('form');
                var $messages = $('#packmem-msgs');

                $.ajax({
                    type: 'post',
                    url: houzez.Core.config.ajaxurl,
                    dataType: 'json',
                    data: $form.serialize(),
                    beforeSend: function () {
                        currnt
                            .find('.houzez-loader-js')
                            .addClass('loader-show');
                    },
                    complete: function () {
                        currnt
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            membershipData(currnt);
                        } else {
                            $('html, body').animate(
                                {
                                    scrollTop: $(
                                        '.frontend-submission-page'
                                    ).offset().top,
                                },
                                'slow'
                            );
                            houzez.Core.util.showError($messages, response.msg);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                        houzez.Core.util.showError(
                            $messages,
                            err.Message || 'An error occurred'
                        );
                    },
                });
            };

            // Handle membership payment submission
            $('#houzez_complete_membership').on('click', function (e) {
                e.preventDefault();
                var currnt = $(this);
                if (
                    parseInt(houzez.Core.config.userID, 10) === 0 ||
                    houzez.Core.config.userID === undefined
                ) {
                    registerUserWithMembership(currnt);
                    return;
                }
                membershipData(currnt);
            });

            // Expose functions for external use
            return {
                stripePackagePayment: stripePackagePayment,
                paypalPackagePayment: paypalPackagePayment,
                recuringPaypalPackagePayment: recuringPaypalPackagePayment,
                directBankTransferPackage: directBankTransferPackage,
                freeMembershipPackage: freeMembershipPackage,
                membershipData: membershipData,
                registerUserWithMembership: registerUserWithMembership,
            };
        };

        const init = function () {
            changeListingFeeForFeatured();
            initPaymentEvents();
            initPaymentOptions();

            // Initialize membership payment
            const membershipFunctions = initMembershipPayment();

            // Expose membership functions to the module
            Object.assign(this, membershipFunctions);
        };

        return {
            init: init,
            changeListingFeeForFeatured: changeListingFeeForFeatured,
            paypalPerListingPayment: paypalPerListingPayment,
            stripePerListingPayment: stripePerListingPayment,
            bankTransferPerListing: bankTransferPerListing,
            wooCommercePerListingPayment: wooCommercePerListingPayment,
            wooCommercePackagePayment: wooCommercePackagePayment,
            initPaymentOptions: initPaymentOptions,
            initMembershipPayment: initMembershipPayment,
        };
    })();

    houzez.DirectMessage = (function () {
        /**
         * Initialize direct message functionality
         */
        const initDirectMessage = function () {
            $('.msg-login-required').on('click', function () {
                $('.modal-toggle-1').addClass('active');
                jQuery('.login-form-tab').addClass('active show');
            });

            $('.houzez-send-message').on('click', function (e) {
                e.preventDefault();

                var $result;
                var $this = $(this);
                var $form = $this.parents('form');
                var $form_wrap = $this.parents('.property-form-wrap');
                $result = $form_wrap.find('.form_messages');
                var $is_bottom = $('.is_bottom').val();
                if ($is_bottom == 'bottom') {
                    $result = $form.find('.form_messages');
                }
                $result.empty();

                var property_id = $('input[name="listing_id"]').val();
                var message = $form.find('.hz-form-message').val();
                var security = $(
                    'input[name="property_agent_contact_security"]'
                ).val();

                $.ajax({
                    url: houzez.Core.config.ajaxurl,
                    data: {
                        action: 'houzez_start_thread',
                        property_id: property_id,
                        message: message,
                        start_thread_form_ajax: security,
                    },
                    method: $form.attr('method'),
                    dataType: 'JSON',

                    beforeSend: function () {
                        $this.find('.houzez-loader-js').addClass('loader-show');
                    },
                    success: function (response) {
                        if (response.success) {
                            $form
                                .find(
                                    'input[name="name"], input[name="mobile"], input[name="email"]'
                                )
                                .val('');
                            $form.find('textarea').val('');
                            if ($is_bottom == 'bottom') {
                                houzez.Core.util.showSuccess(
                                    $result,
                                    response.msg
                                );
                            } else {
                                $result
                                    .empty()
                                    .append(
                                        '<p class="success text-success"><i class="fa fa-check"></i> ' +
                                            response.msg +
                                            '</p>'
                                    );
                            }
                        } else {
                            if ($is_bottom == 'bottom') {
                                houzez.Core.util.showError(
                                    $result,
                                    response.msg
                                );
                            } else {
                                $result
                                    .empty()
                                    .append(
                                        '<p class="error text-danger"><i class="fas fa-times"></i> ' +
                                            response.msg +
                                            '</p>'
                                    );
                            }
                        }

                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');

                        // Reset captcha (supports both reCaptcha and Turnstile)
                        houzez.Core.util.resetCaptcha($form);

                        if (houzez_vars.agent_redirection != '') {
                            setTimeout(function () {
                                window.location.replace(
                                    houzez_vars.agent_redirection
                                );
                            }, 500);
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = eval('(' + xhr.responseText + ')');
                        console.log(err.Message);
                    },
                    complete: function () {
                        $this
                            .find('.houzez-loader-js')
                            .removeClass('loader-show');
                    },
                });
            });
        };

        const init = function () {
            // Only initialize if we're on a property page and the message form exists
            if (
                houzez.Core.config.is_singular_property &&
                $('.houzez-send-message').length > 0
            ) {
                initDirectMessage();
            }
        };

        return {
            init: init,
            initDirectMessage: initDirectMessage,
        };
    })();
    // For backward compatibility
    window.houzezGetCookie = houzez.Core.util.getCookie;
    window.houzezSetCookie = houzez.Core.util.setCookie;
    // window.houzezAddCommas = houzez.Core.util.addCommas;
    // window.houzezFormatNumber = houzez.Core.util.formatNumber;
    // window.houzezGetLoaderHtml = houzez.Core.util.getLoaderHtml;
    // window.houzezProcessingModal = houzez.Core.util.processingModal;
    // window.houzezProcessingModalClose = houzez.Core.util.processingModalClose;
    // window.houzezGetSuccessHtml = houzez.Core.util.getSuccessHtml;
    // window.houzezGetErrorHtml = houzez.Core.util.getErrorHtml;
    // window.houzezShowSuccess = houzez.Core.util.showSuccess;
    // window.houzezShowError = houzez.Core.util.showError;
    window.HouzezDebounce = houzez.Core.util.debounce;
    // window.parseBool = houzez.Core.util.parseBool;
    // window.number_format = houzez.Core.util.numberFormat;
    // window.thousandSeparator = houzez.Core.util.thousandSeparator;
    // window.currencyFormate = houzez.Core.util.currencyFormat;

    // Login module backward compatibility
    window.houzez_process_login = houzez.Login.processLogin;
    window.houzez_process_register = houzez.Login.processRegister;
    window.houzez_process_reset = houzez.Login.processForgotPassword;
    window.houzez_social_login_panel = houzez.Login.socialLoginPanel;

    // Favorites module backward compatibility
    window.houzez_init_add_favorite = houzez.Favorites.initAddFavorite;
    window.houzez_init_remove_favorite = houzez.Favorites.initRemoveFavorite;
    window.add_to_favorite = houzez.Favorites.add_to_favorite;
    window.remove_from_favorite = houzez.Favorites.remove_from_favorite;
    window.houzez_check_favourites = houzez.Favorites.check_favorites;

    // Compare module backward compatibility
    window.add_to_compare = houzez.Compare.add_to_compare;
    window.remove_from_compare = houzez.Compare.remove_from_compare;
    window.compare_for_ajax = houzez.Compare.compare_for_ajax;
    window.compare_for_ajax_map = houzez.Compare.compare_for_ajax;

    // Sliders module backward compatibility
    // window.propertyDetailGallery = houzez.Sliders.propertyDetailGallery;
    // window.propertyBannerSlider = houzez.Sliders.propertyBannerSlider;
    // window.lightboxSlider = houzez.Sliders.lightboxSlider;
    // window.variableWidthSlider = houzez.Sliders.variableWidthSlider;
    // window.testimonialsSliderV1 = houzez.Sliders.testimonialsSliderV1;
    // window.agentsCarousel = houzez.Sliders.agentsCarousel;
    // window.partnersCarousel = houzez.Sliders.partnersCarousel;

    // Mortgage module backward compatibility
    window.calculateMonthlyPayment = houzez.Core.util.calculateMonthlyPayment;
    window.mortgage_calucaltion_section = houzez.Mortgage.mortgageCalculation;
    window.updateChart = houzez.Mortgage.updateChart;

    // Property preview backward compatibility
    window.houzez_listing_lightbox = function () {
        houzez.PropertyPreview.init();
    };

    // Add backward compatibility for grid gallery
    window.houzez_grid_image_gallery = function () {
        houzez.Sliders.gridImageGallery();
    };

    window.houzez_grid_call_to_action = function () {
        houzez.Core.gridCallToAction();
    };

    /**
     * Initialize all modules when DOM is ready
     */
    $(document).ready(function () {
        // Initialize core first
        houzez.Core.init();
        houzez.Logo.init();
        houzez.ElementorMobileMenu.init();
        houzez.MobileNav.init();
        houzez.TopBannerFullScreen.init();
        houzez.Sticky.init();
        houzez.Search.init();
        houzez.PropertyPreview.init();
        houzez.LocationFilter.init();

        // Then initialize other modules
        houzez.Blog.init();
        houzez.Login.init();
        houzez.Favorites.init();
        houzez.Compare.init();
        //houzez.Properties.init();
        houzez.Sliders.init();
        houzez.Pagination.init();
        houzez.Mortgage.init();
        houzez.ContactForms.init();
        houzez.PropertiesTabs.init();
        houzez.SingleProperty.init();
        houzez.RealtorStats.init();
        houzez.CurrencySwitcher.init();
        houzez.AreaSwitcher.init();
        houzez.Payments.init();
        houzez.DirectMessage.init();

        if (
            $('.hz-facebook-login').length > 0 ||
            $('.hz-google-login').length > 0 ||
            $('.hz-fb-main-wrap').length > 0
        ) {
            houzez.SocialLogin.init();
        }

        // Initialize Reviews module only if the submit button exists
        if ($('#submit-review').length > 0) {
            houzez.Reviews.init();
        }
        if ($('.houzez-parallax').length > 0) {
            houzez.Parallax.init();
        }
    });

    // Make houzez object available globally
    window.houzez = houzez;
})(jQuery);
