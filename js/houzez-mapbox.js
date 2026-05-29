/*
 * Show properties for header map and half map
 */
(function ($) {
    'use strict';

    // Main Houzez object that will contain all modules
    var houzez = window.houzez || {};

    /**
     * Maps Utils Module
     * Contains utility functions for map operations
     */
    houzez.MapboxUtils = (function () {
        // Variables needed for utility functions
        let thousands_separator = '';
        let ajaxurl = '';
        let userID = '';
        let not_found = '';
        let markers = new Array();
        let current_marker = 0; // Initialize current marker index
        let current_marker_id = 0; // Track current marker by property ID
        let activePopup = null;
        let mapLanguage = 'en';

        // Initialize variables from houzez_vars
        if (typeof houzez_vars !== 'undefined') {
            ajaxurl = houzez_vars.admin_url + 'admin-ajax.php';
            userID = houzez_vars.user_id;
            not_found = houzez_vars.not_found;
            thousands_separator = houzez_vars.thousands_separator;
            mapLanguage = houzez_vars.mapboxLocaleShort;
        }
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

        /**
         * Format numbers with commas
         */
        const addCommas = function (nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        };

        /**
         * Format numbers with thousand separators
         */
        const thousandSeparator = (n) => {
            if (typeof n === 'number') {
                n += '';
                var x = n.split('.');
                var x1 = x[0];
                var x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + thousands_separator + '$2');
                }
                return x1 + x2;
            } else {
                return n;
            }
        };

        /**
         * Handle special characters in map data
         */
        const processSpecialChars = function () {
            return {
                '&amp;': '&',
                '&quot;': '"',
                '&#039;': "'",
                '&#8217;': '\u2019',
                '&#038;': '&',
                '&lt;': '<',
                '&gt;': '>',
                '&#8216;': '\u2018',
                '&#8230;': '\u2026',
                '&#8221;': '\u201D',
                '&#8211;': 'â€"',
                '&#8212;': 'â€"',
            };
        };

        /**
         * Remove map loader after map loads
         */
        const remove_map_loader = function (map) {
            map.on('load', function () {
                jQuery('.houzez-map-loading').hide();
            });
        };

        /**
         * Reload map markers
         */
        const reloadMarkers = function (map) {
            // Remove existing markers
            if (map.getLayer('markers')) {
                map.removeLayer('markers');
            }
            if (map.getLayer('clusters')) {
                map.removeLayer('clusters');
            }
            if (map.getLayer('cluster-count')) {
                map.removeLayer('cluster-count');
            }
            if (map.getSource('properties')) {
                map.removeSource('properties');
            }

            // For Mapbox markers, we need to remove each DOM element manually
            for (let i = 0; i < markers.length; i++) {
                if (markers[i]) {
                    markers[i].remove();
                }
            }
            // Reset the markers array
            markers = [];
            // Sync with MapboxUtils
            houzez.MapboxUtils.clearMarkers();
        };

        /**
         * Clear marker clusterer
         */
        const clearClusterer = function (map) {
            reloadMarkers(map);
        };

        /**
         * Fit map bounds to markers
         */
        const map_bounds = function (map) {
            if (markers.length > 0) {
                const bounds = new mapboxgl.LngLatBounds();
                markers.forEach(function (marker) {
                    bounds.extend(marker.getLngLat());
                });
                map.fitBounds(bounds, { padding: 50 });
            }
        };

        // Fit map to all markers, then enforce a minimum zoom
        const houzez_map_bounds = (mapboxMap) => {
            if (!markers.length) return;

            let default_zoom = houzez.Mapbox.getDefaultZoom();

            const bounds = markers.reduce((b, marker) => {
                return b.extend(marker.getLngLat());
            }, new mapboxgl.LngLatBounds());

            // Check if bounds are valid and have width/height
            // This is how we check for a single point in Mapbox GL
            const isEmpty = bounds.isEmpty();
            const isPoint =
                !isEmpty &&
                (Math.abs(bounds.getNorth() - bounds.getSouth()) < 0.000001 ||
                    Math.abs(bounds.getEast() - bounds.getWest()) < 0.000001);

            if (isEmpty || isPoint) {
                // Single point or empty bounds - use default zoom and center on the point
                let center;

                if (isEmpty) {
                    // If bounds are empty, use the first marker or current center
                    center = markers.length
                        ? markers[0].getLngLat()
                        : mapboxMap.getCenter();
                } else {
                    // For a point (or very small area), calculate the center
                    center = [
                        (bounds.getWest() + bounds.getEast()) / 2,
                        (bounds.getNorth() + bounds.getSouth()) / 2,
                    ];
                    // Convert to LngLat object if needed
                    if (!center.lng) {
                        center = new mapboxgl.LngLat(center[0], center[1]);
                    }
                }

                mapboxMap.setCenter(center);
                mapboxMap.setZoom(default_zoom);
                return;
            }

            // First fit bounds without animation to get the zoom level
            mapboxMap.fitBounds(bounds, {
                padding: 30,
                animate: false,
                maxZoom: default_zoom + 2, // Allow slightly more zoom for better visibility
            });

            // Then check if we need to adjust the zoom level
            const currentZoom = mapboxMap.getZoom();
            if (currentZoom > default_zoom) {
                // If the current zoom is greater than default_zoom,
                // set it back to default_zoom
                mapboxMap.setZoom(default_zoom);
            }
        };

        // Add a marker to the markers array
        const addMarker = function (marker) {
            markers.push(marker);
        };

        // Clear all markers
        const clearMarkers = function () {
            markers = [];
        };

        // Change map type (streets, satellite, etc)
        const change_map_type = function (map_type) {
            if (!houzezMap) return false;

            if (map_type === 'streets') {
                houzezMap.setStyle('mapbox://styles/mapbox/streets-v12');
            } else if (map_type === 'satellite') {
                houzezMap.setStyle('mapbox://styles/mapbox/satellite-v9');
            } else if (map_type === 'hybrid') {
                houzezMap.setStyle(
                    'mapbox://styles/mapbox/satellite-streets-v12'
                );
            } else if (map_type === 'terrain') {
                houzezMap.setStyle('mapbox://styles/mapbox/outdoors-v12');
            }
            return false;
        };

        // Close all open info windows
        const hideInfoWindows = function () {
            // First try to close any active popup we're tracking
            if (activePopup) {
                try {
                    activePopup.remove();
                } catch (e) {
                    console.error('Error closing active popup:', e);
                }
                activePopup = null;
            }

            // Then close any other open popups on markers
            markers.forEach(function (marker) {
                if (marker && marker._popup && marker._popup.isOpen()) {
                    try {
                        marker._popup.remove();
                    } catch (e) {
                        console.error('Error closing marker popup:', e);
                    }
                }
            });
        };

        // Find a marker by property ID
        const findMarkerByPropertyId = function (propertyId) {
            for (let i = 0; i < markers.length; i++) {
                if (
                    markers[i].propertyId &&
                    markers[i].propertyId == propertyId
                ) {
                    return markers[i];
                }
            }
            return null;
        };

        // Open popup for a marker
        const openPopup = function (propertyId, panToMarker = true) {
            if (!propertyId) return;

            // Make sure we're working with a clean ID (removing any "hz-" prefix)
            if (
                typeof propertyId === 'string' &&
                propertyId.indexOf('hz-') === 0
            ) {
                propertyId = propertyId.replace('hz-', '');
            }

            // First close any open popups
            hideInfoWindows();

            // Find the marker with the matching property ID
            for (let i = 0; i < markers.length; i++) {
                if (markers[i].propertyId == propertyId) {
                    // Update the current marker index
                    current_marker = i + 1;
                    current_marker_id = propertyId;

                    // Only pan to marker if requested (for clicks, but not for hovers)
                    if (panToMarker && window.houzezMap) {
                        // Get current zoom level
                        const currentZoom = window.houzezMap.getZoom();

                        // Pan to the marker position without changing zoom
                        window.houzezMap.easeTo({
                            center: markers[i].getLngLat(),
                            zoom: currentZoom,
                            duration: 500,
                        });
                    }

                    // Open the popup for this marker
                    if (markers[i]._popup) {
                        markers[i].togglePopup();
                        // Track the active popup
                        activePopup = markers[i]._popup;
                    }

                    break;
                }
            }
        };

        // Open popup by property ID
        const openPopupById = function (propertyId, panToMarker = true) {
            // First close any open popups
            hideInfoWindows();

            // Find the marker with this property ID
            const marker = findMarkerByPropertyId(propertyId);

            if (marker) {
                // Use the openPopup function with the panToMarker parameter
                openPopup(propertyId, panToMarker);
            }
        };

        // Close popup for a specific property
        const closePopup = function (propertyId) {
            if (!propertyId) return;

            // Make sure we're working with a clean ID (removing any "hz-" prefix)
            if (
                typeof propertyId === 'string' &&
                propertyId.indexOf('hz-') === 0
            ) {
                propertyId = propertyId.replace('hz-', '');
            }

            // Find the marker with the matching property ID
            for (let i = 0; i < markers.length; i++) {
                if (
                    markers[i].propertyId == propertyId &&
                    markers[i]._popup &&
                    markers[i]._popup.isOpen()
                ) {
                    // Close the popup
                    markers[i].togglePopup();
                    activePopup = null;
                    break;
                }
            }
        };

        // Set URL state for browser history
        const setPushState = function (pageUrl) {
            window.history.pushState({ houzezTheme: true }, '', pageUrl);
        };

        // Set URL based on search form parameters
        const set_url = function (currentForm) {
            var $form =
                currentForm || $('form.houzez-search-filters-js').first();

            var url = $form.attr('action');

            if (url == undefined) {
                return true;
            }

            var formData = $form
                .find(':input')
                .filter(function (index, element) {
                    if (
                        !$(element).prop('disabled') &&
                        $(element).val() != '' &&
                        $(element).attr('name') != 'search_geolocation' &&
                        $(element).attr('name') != 'search_URI' &&
                        $(element).attr('name') != 'search_args' &&
                        $(element).attr('name') != 'houzez_save_search_ajax'
                    ) {
                        return true;
                    }
                })
                .serialize();

            if (url == undefined) {
                url = '';
            } else if (url.indexOf('?') != -1) {
                url = url + '?' + formData;
            } else {
                url = url + '?' + formData;
            }

            setPushState(url);
        };

        // Initialize map view toggle for mobile devices
        const initMapViewToggle = function () {
            $('#houzez-btn-map-view').on('click', function (e) {
                e.preventDefault();
                $('#half-map-listing-area, .listing-wrap').hide();
                $('#map-view-wrap').show();
                $('#mobile-search-form').addClass(
                    'hz-mobile-overlay-search-js'
                );

                // Ensure map renders correctly after changing visibility
                window.setTimeout(function () {
                    window.dispatchEvent(new Event('resize'));
                    if (window.houzezMap) {
                        window.houzezMap.resize();
                    }
                }, 100);
            });

            $('#houzez-btn-listing-view').on('click', function (e) {
                e.preventDefault();
                $('#map-view-wrap').hide();
                $('#half-map-listing-area, .listing-wrap').show();
                $('#mobile-search-form').removeClass(
                    'hz-mobile-overlay-search-js'
                );

                $('.hz-item-gallery-js').removeClass('houzez-gallery-loaded');

                // Reinitialize all AJAX-dependent functions including grid image gallery
                // This ensures galleries work properly when switching from map to listing view
                window.setTimeout(function () {
                    if (
                        houzez.Core &&
                        typeof houzez.Core.reinitializeAjaxFunctions ===
                            'function'
                    ) {
                        houzez.Core.reinitializeAjaxFunctions();
                    }
                }, 100);
            });
        };

        // Initialize toggle for fullscreen map view
        const initializeFullScreenToggle = function () {
            // Add event listener for both IDs to ensure compatibility
            $('#houzez-gmap-full, #houzez-gmap-full-osm').on(
                'click',
                function () {
                    var $this = $(this);
                    if ($this.hasClass('active')) {
                        $this.removeClass('active');
                        $this
                            .parents('.map-wrap')
                            .removeClass('houzez-fullscreen-map');
                    } else {
                        $this
                            .parents('.map-wrap')
                            .addClass('houzez-fullscreen-map');
                        $this.addClass('active');
                    }

                    // Trigger resize event to ensure map renders correctly
                    window.setTimeout(function () {
                        window.dispatchEvent(new Event('resize'));
                        if (window.houzezMap) {
                            window.houzezMap.resize();

                            // Re-center the map to ensure proper display
                            if (window.houzezMap.getCenter) {
                                try {
                                    window.houzezMap.panTo(
                                        window.houzezMap.getCenter()
                                    );
                                } catch (e) {
                                    console.error('Error re-centering map:', e);
                                }
                            }
                        }
                    }, 100);
                }
            );
        };

        // Initialize infobox trigger for property listing hover events
        const initInfoboxTrigger = function () {
            if (is_mobile()) return;

            // Use a generic class name that works for all map types
            const listings = document.querySelectorAll(
                '#half-map-listing-area .hz-map-trigger'
            );

            if (!listings.length) {
                console.log(
                    'No property listings found with class .hz-map-trigger'
                );
                return;
            }

            console.log(
                'Found ' +
                    listings.length +
                    ' property listings with map hover trigger'
            );

            listings.forEach(function (listing) {
                // Store property ID in a variable
                const propertyId = listing.getAttribute('data-hz-id');

                if (propertyId) {
                    // Mouse enter
                    listing.addEventListener('mouseenter', function () {
                        openPopup(propertyId, false); // Don't pan map on hover
                    });

                    // Mouse leave
                    listing.addEventListener('mouseleave', function () {
                        closePopup(propertyId);
                    });
                }
            });
        };

        // Navigate to next map marker
        const map_next = function () {
            current_marker++;
            if (current_marker > markers.length) {
                current_marker = 1;
            }

            if (markers[current_marker - 1]) {
                const marker = markers[current_marker - 1];
                if (marker.propertyId) {
                    openPopup(marker.propertyId, false);
                }
            }
        };

        // Navigate to previous map marker
        const map_prev = function () {
            current_marker--;
            if (current_marker < 1) {
                current_marker = markers.length;
            }

            if (markers[current_marker - 1]) {
                const marker = markers[current_marker - 1];
                if (marker.propertyId) {
                    openPopup(marker.propertyId, false);
                }
            }
        };

        // Initialize map search handlers
        const initMapSearchHandlers = function () {
            // Setup pagination
            const setupPagination = () => {
                $('.houzez_ajax_pagination a').on('click', function (e) {
                    e.preventDefault();
                    const page = $(this).data('houzepagi') || 0;
                    let $form = $('#desktop-search-form');
                    if (
                        $('#mobile-search-form.apply-mobile-pagination')
                            .length > 0
                    ) {
                        $form = $('#mobile-search-form');
                    }

                    houzez.Mapbox.searchOnChange(null, $form, page);
                });
            };

            // Initialize pagination
            setupPagination();

            // Sorting handler
            $('#ajax_sort_properties').on('change', function () {
                let $form = $('#desktop-search-form');
                let mobile_sortby = $(this).hasClass('mobile-sortby');
                if (mobile_sortby) {
                    $form = $('#mobile-search-form');
                }

                houzez.Mapbox.searchOnChange(null, $form);
            });

            // Search fields change handler
            $('select.houzez_search_ajax, input.houzez_search_ajax').on(
                'change',
                function () {
                    let $form = $(this).closest('form');
                    houzez.Mapbox.searchOnChange(null, $form);
                }
            );

            // Search button click handler for half map
            if ($('.half-map-wrap').length > 0) {
                $(
                    '.btn-apply, .half-map-search-js-btn, #auto_complete_ajax'
                ).on('click', function (e) {
                    e.preventDefault();
                    let $form = $(this).closest('form');
                    houzez.Mapbox.searchOnChange(null, $form);
                });
            }
        };

        // Add markers to the map
        const addMarkers = function (map_properties, preservePosition = false) {
            const special_chars = processSpecialChars();

            // Clear existing markers before adding new ones
            clearMarkers();
            // Create markers for each property
            for (var i = 0; i < map_properties.length; i++) {
                if (map_properties[i].latitude && map_properties[i].longitude) {
                    let marker_color = map_properties[i].marker_color;
                    let markerElement = document.createElement('div');
                    markerElement.className = 'mapbox-marker';

                    if (houzez.Mapbox.getMarkerPricePins() == 'yes') {
                        markerElement.className += ' map-marker-label';
                        markerElement.innerHTML = map_properties[i].pricePin;

                        // Add background and border color if marker_color exists
                        if (marker_color) {
                            markerElement.style.backgroundColor = marker_color;
                            markerElement.style.borderColor = marker_color;
                            markerElement.style.color = '#ffffff';

                            // Update triangle color to match marker color
                            markerElement.style.setProperty(
                                '--triangle-color',
                                marker_color
                            );
                        } else {
                            markerElement.style.setProperty(
                                '--triangle-color',
                                '#1DABE3'
                            );
                        }
                    } else {
                        markerElement.style.backgroundImage =
                            'url(' + (map_properties[i].marker || '') + ')';
                        markerElement.style.width = '44px';
                        markerElement.style.height = '56px';
                        markerElement.style.backgroundSize = 'contain';
                        markerElement.style.backgroundRepeat = 'no-repeat';
                    }

                    // Create popup content
                    let popupContent = document.createElement('div');
                    popupContent.className = 'property-info-window';
                    let innerHTML = '';

                    if (map_properties[i].thumbnail) {
                        innerHTML +=
                            '<div class="info-window-image">' +
                            map_properties[i].featured_label +
                            '<a target="' +
                            map_properties[i].link_target +
                            '" href="' +
                            map_properties[i].url +
                            '">' +
                            '<img class="img-fluid listing-thumbnail" src="' +
                            houzez.Mapbox.getInfoWindowPlac() +
                            '" data-src="' +
                            map_properties[i].thumbnail +
                            '" alt="' +
                            map_properties[i].title +
                            '"/>' +
                            '</a>' +
                            '</div>';
                    } else {
                        innerHTML +=
                            '<div class="info-window-image">' +
                            map_properties[i].featured_label +
                            '<a target="' +
                            map_properties[i].link_target +
                            '" href="' +
                            map_properties[i].url +
                            '">' +
                            '<img class="img-fluid listing-thumbnail" src="' +
                            houzez.Mapbox.getInfoWindowPlac() +
                            '" alt="' +
                            map_properties[i].title +
                            '"/>' +
                            '</a>' +
                            '</div>';
                    }

                    innerHTML +=
                        '<div class="info-content" style="padding:10px;">';

                    if (map_properties[i].price) {
                        innerHTML +=
                            '<div class="info-window-price">' +
                            map_properties[i].price +
                            '</div>';
                    }

                    innerHTML += map_properties[i].meta;

                    if (map_properties[i].property_type) {
                        innerHTML +=
                            '<div class="info-window-property-type">' +
                            map_properties[i].property_type +
                            '</div>';
                    }

                    innerHTML += map_properties[i].address;
                    innerHTML += '</div>';

                    popupContent.innerHTML = innerHTML;

                    // Get the property ID
                    const propertyId = map_properties[i].property_id || '';

                    // Create popup directly following the Mapbox example
                    const popup = new mapboxgl.Popup({
                        offset: 25,
                        closeButton: true,
                        closeOnClick: false,
                        maxWidth: '320px',
                    }).setDOMContent(popupContent);

                    // Set up lazy loading when popup opens
                    popup.on('open', function () {
                        console.log('Popup opened');
                        let infoWindowImage =
                            popupContent.getElementsByClassName(
                                'listing-thumbnail'
                            );
                        if (
                            infoWindowImage.length &&
                            infoWindowImage[0].dataset.src &&
                            infoWindowImage[0].dataset.src !== 'undefined'
                        ) {
                            infoWindowImage[0].src =
                                infoWindowImage[0].dataset.src;
                        }
                        activePopup = popup;
                    });

                    // Listen for popup close to clean up activePopup reference
                    popup.on('close', function () {
                        if (activePopup === popup) {
                            activePopup = null;
                        }
                    });

                    // Create marker and directly attach popup following the Mapbox example
                    const marker = new mapboxgl.Marker({
                        element: markerElement,
                        clickTolerance: 10, // Increase click tolerance to make clicking easier
                    })
                        .setLngLat([
                            parseFloat(map_properties[i].longitude),
                            parseFloat(map_properties[i].latitude),
                        ])
                        .setPopup(popup) // sets a popup on this marker
                        .addTo(window.houzezMap);

                    // Store property ID on the marker for identification
                    marker.propertyId = propertyId;

                    // Add explicit click handling using the DOM element
                    marker.getElement().addEventListener('click', function (e) {
                        e.stopPropagation(); // Prevent event bubbling

                        // Close any open popups first
                        hideInfoWindows();

                        // Update current marker
                        current_marker = i + 1;
                        current_marker_id = propertyId;

                        // Use openPopup without panning (don't change map position)
                        openPopup(propertyId, false);
                    });

                    // Add the marker to our array
                    addMarker(marker);
                }
            }

            // Fit map to bounds
            if (!preservePosition) {
                houzez_map_bounds(window.houzezMap);
            }
        };

        const init = function () {};

        // Public API
        return {
            init: init,
            addCommas: addCommas,
            thousandSeparator: thousandSeparator,
            processSpecialChars: processSpecialChars,
            remove_map_loader: remove_map_loader,
            reloadMarkers: reloadMarkers,
            clearClusterer: clearClusterer,
            map_bounds: map_bounds,
            change_map_type: change_map_type,
            map_next: map_next,
            map_prev: map_prev,
            openPopup: openPopup,
            openPopupById: openPopupById,
            closePopup: closePopup,
            hideInfoWindows: hideInfoWindows,
            findMarkerByPropertyId: findMarkerByPropertyId,
            addMarkers: addMarkers,
            setPushState: setPushState,
            set_url: set_url,
            initMapViewToggle: initMapViewToggle,
            initializeFullScreenToggle: initializeFullScreenToggle,
            initInfoboxTrigger: initInfoboxTrigger,
            getAjaxUrl: () => ajaxurl,
            getUserId: () => userID,
            getMapLanguage: () => mapLanguage,
            getNotFound: () => not_found,
            getThousandsSeparator: () => thousands_separator,
            getMarkers: () => markers,
            getCurrentMarker: () => current_marker,
            getCurrentMarkerId: () => current_marker_id,
            addMarker: addMarker,
            clearMarkers: clearMarkers,
            initMapSearchHandlers: initMapSearchHandlers,
            is_mobile: is_mobile,
        };
    })();

    /**
     * Maps Module
     * Handles Mapbox functionality for property maps
     */
    houzez.Mapbox = (function () {
        // Map variables
        let houzezMap;
        let mapBounds;
        let hideInfoWindows;
        let markers = new Array();
        let activePopup = null;
        let clusterIcon = '';
        let map_cluster_enable = 1;
        let clusterer_zoom = 12;
        let closeIcon = '';
        let infoWindowPlac = '';
        let lastClickedMarker;
        let markerPricePins = 'no';
        let mapbox_style = 'mapbox://styles/mapbox/streets-v12';
        let is_halfmap = 0;
        let default_lat = 0;
        let default_lng = 0;
        let houzez_default_radius = 0;
        let mapbox_access_token = '';
        let current_page = 0; // Add back current_page variable
        let isSearchInProgress = false; // Flag to prevent recursive map searches
        let pendingSearch = false; // Coalesce flag: queue one re-run if input changes during AJAX
        let pagination_only = false;
        let viewport_search = false;
        let auto_load_map_listings = 0;
        let map_message_timeout = null;
        let default_zoom = 12;
        let max_zoom = 18;

        // Ajax and other variables
        if (typeof houzez_vars !== 'undefined') {
            is_halfmap = parseInt(houzez_vars.is_halfmap);
            houzez_default_radius = parseInt(houzez_vars.houzez_default_radius);
            auto_load_map_listings = parseInt(
                houzez_vars.auto_load_map_listings
            );
            mapbox_access_token = houzez_vars.api_mapbox;
        }

        // Update references to use MapboxUtils functions
        const clearClusterer = () =>
            houzez.MapboxUtils.clearClusterer(houzezMap);
        const reloadMarkers = () => houzez.MapboxUtils.reloadMarkers(houzezMap);
        const houzez_map_bounds = () =>
            houzez.MapboxUtils.map_bounds(houzezMap);

        /**
         * Zoom in on the map with smooth animation
         */
        const map_zoomin = function (hMap) {
            document
                .getElementById('listing-mapzoomin')
                .addEventListener('click', function () {
                    hMap.zoomIn();

                    updateViewportCoordinates();
                });
        };

        /**
         * Zoom out on the map with smooth animation
         */
        const map_zoomout = function (hMap) {
            document
                .getElementById('listing-mapzoomout')
                .addEventListener('click', function () {
                    hMap.zoomOut();

                    updateViewportCoordinates();
                });
        };

        /**
         * Initialize map event listeners for map controls
         */
        const initMapControls = function () {
            $('.houzezMapType').on('click', function (e) {
                e.preventDefault();
                var maptype = $(this).data('maptype');
                houzez.MapboxUtils.change_map_type(maptype);
            });

            map_zoomin(window.houzezMap);
            map_zoomout(window.houzezMap);

            $('#houzez-gmap-next').on('click', function () {
                houzez.MapboxUtils.map_next();
            });

            $('#houzez-gmap-prev').on('click', function () {
                houzez.MapboxUtils.map_prev();
            });
        };

        /**
         * Display map message showing visible properties count
         */
        const showMapMessage = function (visibleCount, totalCount) {
            if (auto_load_map_listings !== 1) {
                return;
            }

            // Clear any existing timeout
            if (map_message_timeout) {
                clearTimeout(map_message_timeout);
                map_message_timeout = null;
            }

            const $messageContainer = $('#houzez-map-message');
            const $messageContent = $messageContainer.find('.map-info-message');

            if (visibleCount < totalCount) {
                // Use translatable strings with placeholders
                const countMessage = houzez_vars.map_show_some
                    .replace('%s', visibleCount)
                    .replace('%s', totalCount);

                const zoomMessage = houzez_vars.zoom_in_show_more;
                $messageContent.html(countMessage + '<br>' + zoomMessage);
                $messageContainer.addClass('show');
            } else {
                // Use translatable string with placeholder
                const message = houzez_vars.map_show_all.replace(
                    '%s',
                    totalCount
                );
                $messageContent.html(message);
                $messageContainer.addClass('show');
            }

            // Hide message after 5 seconds
            map_message_timeout = setTimeout(function () {
                $messageContainer.removeClass('show');
            }, 3000);
        };

        // Preserve current center+zoom (e.g. after a reload or re-render)
        const preserveViewport = (houzezMap) => {
            const currentCenter = houzezMap.getCenter();
            const currentZoom = houzezMap.getZoom();
            if (currentCenter && currentZoom != null && viewport_search) {
                houzezMap.setCenter(currentCenter);
                houzezMap.setZoom(currentZoom);
            }
        };

        /**
         * AJAX search for half map view
         */
        const houzez_half_map_listings = function (current_page, current_form) {
            var ajax_container = $('#houzez_ajax_container');
            var $halfmapBtn = current_form.find('.half-map-search-js-btn');
            var total_results = $('#half-map-listing-area .page-title span');
            var sortby = $('#ajax_sort_properties').val();
            var item_layout = $('.listing-view').data('layout');
            var layout_css = $('.listing-view').data('css');
            var layout_view = $('.listing-view').data('view');

            // Create data object from form serialization
            const formData = current_form.serialize();

            // Prepare AJAX data
            const ajaxData = {
                action: 'houzez_half_map_listings',
                paged: current_page,
                sortby: sortby,
                item_layout: item_layout,
                layout_css: layout_css,
                layout_view: layout_view,
            };

            ajaxData.is_pagination_request = pagination_only;

            // Merge form data with our ajax parameters
            const serializedData = formData + '&' + $.param(ajaxData);

            // Add viewport data if this is a viewport-based search
            let finalData = serializedData;

            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: houzez.MapboxUtils.getAjaxUrl(),
                data: finalData,
                beforeSend: function () {
                    $halfmapBtn.prop('disabled', true);
                    $('.houzez-map-loading').show();
                    ajax_container
                        .empty()
                        .append(
                            '' +
                                '<div id="houzez-map-loading" class="houzez-map-loading">' +
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
                    if (data.query != '') {
                        $('input[name="search_args"]').val(data.query);
                    }
                    if (data.search_uri != '') {
                        $('input[name="search_URI"]').val(data.search_uri);
                    }
                    $('.map-notfound').remove();
                    $('.search-no-results-found').remove();

                    $('.houzez-map-loading').hide();

                    if (data.total_results > 0) {
                        if (data.getProperties === true) {
                            clearClusterer();
                            reloadMarkers(houzezMap);

                            // Check if map is already loaded
                            if (houzezMap.loaded()) {
                                // Map already loaded, add markers directly
                                houzez.MapboxUtils.addMarkers(
                                    data.properties,
                                    viewport_search
                                );
                                //houzez.MapboxUtils.map_bounds(houzezMap);
                                preserveViewport(houzezMap);
                            } else {
                                // Wait for map to load
                                houzezMap.on('load', function () {
                                    houzez.MapboxUtils.addMarkers(
                                        data.properties,
                                        viewport_search
                                    );
                                    //houzez.MapboxUtils.map_bounds(houzezMap);
                                    preserveViewport(houzezMap);
                                });
                            }

                            // Show map message with property counts
                            showMapMessage(
                                data.properties.length,
                                data.total_results
                            );
                        } // End of if (data.getProperties === true)

                        ajax_container.empty().html(data.propHtml);
                        total_results.empty().html(data.total_results);
                        map_ajax_pagination();

                        houzez_listing_lightbox();
                        houzez_grid_image_gallery();
                        houzez_grid_call_to_action();
                        compare_for_ajax();

                        // Only initialize infobox trigger if we're not on mobile and have properties
                        if (!houzez.MapboxUtils.is_mobile()) {
                            houzez.MapboxUtils.initInfoboxTrigger();
                        }

                        $('[data-bs-toggle="tooltip"]').tooltip();
                    } else {
                        clearClusterer();
                        reloadMarkers(houzezMap);

                        // Preserve current center+zoom
                        preserveViewport(houzezMap);

                        $('#houzez-properties-map').append(
                            '<div class="map-notfound">' +
                                houzez.MapboxUtils.getNotFound() +
                                '</div>'
                        );
                        ajax_container.empty().html(data.propHtml);
                        total_results.empty().html(data.total_results);
                    }
                    return false;
                },
                complete: function () {
                    pagination_only = false;
                    viewport_search = false;
                    isSearchInProgress = false;

                    // If user changed inputs or clicked search during AJAX,
                    // re-run with latest form state instead of dropping it
                    if (pendingSearch) {
                        pendingSearch = false;
                        houzez_search_on_change(null, current_form);
                    } else {
                        $halfmapBtn.prop('disabled', false);
                    }

                    // Remove icon-spin class from location trigger when AJAX completes
                    $('.location-trigger')
                        .find('.icon-location-target')
                        .removeClass('icon-spin');
                },
                error: function (xhr, status, error) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(error);
                },
            });
            return false;
        };

        // Trigger map search when slider interaction ends
        const triggerDistanceRangeSearch = function () {
            if ($('#houzez-properties-map').length > 0) {
                let distanceRange = $('#radius-range-slider');
                let $form = $('#desktop-search-form');
                distanceRange.on('change', (e) => {
                    $('input[name="use_radius"]').prop('checked', true);
                    $('input[name="ne_lat"]').val('');
                    $('input[name="ne_lng"]').val('');
                    $('input[name="sw_lat"]').val('');
                    $('input[name="sw_lng"]').val('');
                    $('input[name="zoom"]').val('');
                    houzez_search_on_change(null, $form);
                });
            }
        };

        const triggerPriceRangeSearch = function () {
            if ($('#houzez-properties-map').length > 0) {
                let fromPrice = $('#fromSlider_price_range_halfmap');
                let toPrice = $('#toSlider_price_range_halfmap');
                let $form = $('#desktop-search-form');
                fromPrice.on('change', (e) => {
                    houzez_search_on_change(null, $form);
                });

                toPrice.on('change', (e) => {
                    houzez_search_on_change(null, $form);
                });
            }
        };

        const triggerPriceRangeSearchMobile = function () {
            if ($('#houzez-properties-map').length > 0) {
                let fromPrice = $('#fromSlider_price_range_mobile');
                let toPrice = $('#toSlider_price_range_mobile');
                let $form = $('#mobile-search-form');
                fromPrice.on('change', (e) => {
                    houzez_search_on_change(null, $form);
                });

                toPrice.on('change', (e) => {
                    houzez_search_on_change(null, $form);
                });
            }
        };

        /**
         * Handle search changes and updating results
         */
        const houzez_search_on_change = function (
            $this = null,
            currentForm = null,
            current_page = 0
        ) {
            // If a search is already in progress, queue a single re-run
            // so a click/change during AJAX is honored, not silently dropped
            if (isSearchInProgress) {
                pendingSearch = true;
                return;
            }

            let $form = currentForm;

            // If currentForm is null, try to get the form from $this
            if (!$form && $this) {
                $form = $this.parents('form');
            }

            // Safety check to ensure we have a form
            if (!$form || !$form.length) {
                console.warn('No valid form found for search');
                return;
            }

            isSearchInProgress = true;
            $('.hz-halfmap-paged').val(current_page);
            houzez.MapboxUtils.set_url($form);
            houzez_half_map_listings(current_page, $form);
        };

        /**
         * Update viewport coordinates
         */
        const updateViewportCoordinates = () => {
            if (!auto_load_map_listings) return;
            viewport_search = true;
            let $form = $('#desktop-search-form');

            const bounds = houzezMap.getBounds();
            if (!bounds) return;

            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();
            const zoom = houzezMap.getZoom();

            let overlayform = $(
                '#mobile-search-form.hz-mobile-overlay-search-js'
            );

            if (overlayform.length > 0) {
                $form = $('#mobile-search-form');
            }

            $('input[name="ne_lat"]').val(ne.lat);
            $('input[name="ne_lng"]').val(ne.lng);
            $('input[name="sw_lat"]').val(sw.lat);
            $('input[name="sw_lng"]').val(sw.lng);
            $('input[name="zoom"]').val(zoom);
            $('input[name="use_radius"]').prop('checked', false);

            houzez_search_on_change(null, $form);
        };

        // Initialize drag/zoom listeners to trigger viewport search
        const initPropertiesInViewport = () => {
            let isDragging = false;
            let lastDragTime = 0;

            // Remove any previous handlers
            houzezMap.off('dragstart');
            houzezMap.off('dragend');
            houzezMap.off('zoomend');

            houzezMap.on('dragstart', () => {
                isDragging = true;
                console.log('drag start');
            });

            houzezMap.on('dragend', () => {
                if (!isSearchInProgress && isDragging) {
                    updateViewportCoordinates();
                    lastDragTime = Date.now();
                }
                isDragging = false;
            });
        };

        /**
         * Setup Ajax pagination for map results
         */
        const map_ajax_pagination = function () {
            $('.houzez_ajax_pagination a').on('click', function (e) {
                e.preventDefault();
                if (auto_load_map_listings) {
                    pagination_only = true;
                }
                current_page = $(this).data('houzepagi');

                let $form = $('#desktop-search-form');

                if (
                    $('#mobile-search-form.apply-mobile-pagination').length > 0
                ) {
                    $form = $('#mobile-search-form');
                }

                houzez_search_on_change(null, $form, current_page);
            });
            return false;
        };

        /**
         * Setup autocomplete for location field
         */
        const initAutocomplete = function () {
            if ($('.hz-map-field-js').length > 0) {
                var geo_country_limit = houzez_vars.geo_country_limit;
                var geocomplete_country = houzez_vars.geocomplete_country;

                // Use function construction to store map & DOM elements separately for each instance
                var MapField = function ($container) {
                    this.$container = $container;
                };

                // Use prototype for better performance
                MapField.prototype = {
                    // Initialize everything
                    init: function () {
                        this.initDomElements();
                        this.autocomplete();
                    },
                    // Initialize DOM elements
                    initDomElements: function () {
                        this.addressField =
                            this.$container.data('address-field');
                    },

                    // Autocomplete address using Mapbox Geocoding API
                    autocomplete: function () {
                        var that = this,
                            $address = this.addressField;

                        if (null === $address) {
                            return;
                        }

                        var inputField = document.getElementById($address);
                        var $inputParent = $(inputField).parent();

                        // Get references to the existing hidden lat/lng fields
                        var $latField = $inputParent.find('input[name="lat"]');
                        var $lngField = $inputParent.find('input[name="lng"]');

                        // Set country restrictions if enabled
                        let countryRestrictions = undefined;
                        if (
                            geo_country_limit != undefined &&
                            geocomplete_country != undefined
                        ) {
                            // Handle special case for UAE
                            if (geocomplete_country == 'UAE') {
                                geocomplete_country = 'AE';
                            }
                            countryRestrictions =
                                geocomplete_country.toLowerCase();
                        }

                        if (typeof MapboxGeocoder === 'undefined') {
                            console.error(
                                'MapboxGeocoder is not defined! Make sure the script is loaded correctly.'
                            );
                            return;
                        }

                        // Set up event handler for location input
                        $(inputField).on('input', function () {
                            var query = $(this).val();
                            if (query.length < 3) return; // Don't search for very short queries

                            // Call Mapbox Geocoding API directly
                            $.ajax({
                                url:
                                    'https://api.mapbox.com/geocoding/v5/mapbox.places/' +
                                    encodeURIComponent(query) +
                                    '.json',
                                data: {
                                    access_token: mapbox_access_token,
                                    autocomplete: true,
                                    language: houzez_vars.mapboxLocale,
                                    types: 'address,poi,place',
                                    country: countryRestrictions,
                                },
                                success: function (data) {
                                    // Create suggestions dropdown
                                    var $suggestions = $inputParent.find(
                                        '.location-suggestions'
                                    );
                                    if ($suggestions.length === 0) {
                                        $suggestions = $(
                                            '<div class="location-suggestions" style="position:absolute;z-index:1000;background:white;width:100%;border:1px solid #ccc;max-height:200px;overflow-y:auto;display:none;"></div>'
                                        );
                                        $inputParent.css(
                                            'position',
                                            'relative'
                                        );
                                        $inputParent.append($suggestions);
                                    }

                                    // Clear previous suggestions
                                    $suggestions.empty();

                                    if (
                                        data.features &&
                                        data.features.length > 0
                                    ) {
                                        // Add suggestions to dropdown
                                        data.features.forEach(function (
                                            feature
                                        ) {
                                            var $item = $(
                                                '<div class="suggestion-item" style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #eee;"></div>'
                                            );
                                            $item.text(feature.place_name);
                                            $item.data(
                                                'coordinates',
                                                feature.center
                                            );
                                            $item.on('click', function () {
                                                // Set input value
                                                $(inputField).val(
                                                    feature.place_name
                                                );

                                                // Update coordinates
                                                var coordinates =
                                                    feature.center;
                                                $lngField.val(coordinates[0]); // longitude is first in Mapbox coordinates
                                                $latField.val(coordinates[1]); // latitude is second
                                                $('input[name="ne_lat"]').val(
                                                    ''
                                                );
                                                $('input[name="ne_lng"]').val(
                                                    ''
                                                );
                                                $('input[name="sw_lat"]').val(
                                                    ''
                                                );
                                                $('input[name="sw_lng"]').val(
                                                    ''
                                                );
                                                $('input[name="zoom"]').val('');
                                                $(
                                                    'input[name="use_radius"]'
                                                ).prop('checked', true);

                                                // Hide suggestions
                                                $suggestions.hide();

                                                // Trigger search if in half map
                                                if (is_halfmap) {
                                                    let $this = $(this);
                                                    houzez_search_on_change(
                                                        $this
                                                    );
                                                }
                                            });
                                            $suggestions.append($item);
                                        });

                                        // Show suggestions
                                        $suggestions.show();
                                    } else {
                                        $suggestions.hide();
                                    }
                                },
                            });
                        });

                        // Handle clicks outside the suggestions
                        $(document).on('click', function (e) {
                            if (
                                !$(e.target).closest('.location-search').length
                            ) {
                                $('.location-suggestions').hide();
                            }
                        });

                        // Handle use of location button
                        $inputParent
                            .find('.location-trigger')
                            .on('click', function (e) {
                                e.preventDefault();
                                let $this = $(this);

                                $this
                                    .find('.icon-location-target')
                                    .addClass('icon-spin');

                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(
                                        function (position) {
                                            // Get location name from coordinates
                                            $.ajax({
                                                url:
                                                    'https://api.mapbox.com/geocoding/v5/mapbox.places/' +
                                                    position.coords.longitude +
                                                    ',' +
                                                    position.coords.latitude +
                                                    '.json',
                                                data: {
                                                    access_token:
                                                        mapbox_access_token,
                                                    types: 'address',
                                                    limit: 1,
                                                    language:
                                                        houzez_vars.mapbox_language ||
                                                        'en',
                                                },
                                                success: function (data) {
                                                    if (
                                                        data.features &&
                                                        data.features.length > 0
                                                    ) {
                                                        $(inputField).val(
                                                            data.features[0]
                                                                .place_name
                                                        );
                                                    } else {
                                                        $(inputField).val(
                                                            houzez_vars.current_location
                                                        );
                                                    }

                                                    // Update coordinates
                                                    $lngField.val(
                                                        position.coords
                                                            .longitude
                                                    );
                                                    $latField.val(
                                                        position.coords.latitude
                                                    );

                                                    // Trigger search if in half map
                                                    if (is_halfmap) {
                                                        houzez_search_on_change(
                                                            $this
                                                        );
                                                    } else {
                                                        $this
                                                            .find(
                                                                '.icon-location-target'
                                                            )
                                                            .removeClass(
                                                                'icon-spin'
                                                            );
                                                    }
                                                },
                                                error: function () {
                                                    // In case of error, still provide coordinates but use fallback text
                                                    $(inputField).val(
                                                        houzez_vars.current_location
                                                    );

                                                    // Update coordinates
                                                    $lngField.val(
                                                        position.coords
                                                            .longitude
                                                    );
                                                    $latField.val(
                                                        position.coords.latitude
                                                    );

                                                    // Trigger search if in half map
                                                    if (is_halfmap) {
                                                        let $this = $(this);
                                                        houzez_search_on_change(
                                                            $this
                                                        );
                                                    }
                                                },
                                            });
                                        }
                                    );
                                }
                            });
                    },

                    // Update coordinate to input field
                    updateCoordinate: function (coordinates) {
                        $('input[name="lng"]').val(coordinates[0]);
                        $('input[name="lat"]').val(coordinates[1]);
                        $('input[name="ne_lat"]').val('');
                        $('input[name="ne_lng"]').val('');
                        $('input[name="sw_lat"]').val('');
                        $('input[name="sw_lng"]').val('');
                        $('input[name="zoom"]').val('');
                        $('input[name="use_radius"]').prop('checked', true);
                    },
                };

                var initGeoField = function () {
                    var $this = $(this);
                    var controller;
                    controller = new MapField($this);
                    controller.init();
                };

                var init = function (e) {
                    $('.hz-map-field-js').each(initGeoField);
                };
                init();
            }
        };

        /**
         * Initialize the map
         */
        const initializeMap = function () {
            if ($('#houzez-properties-map').length > 0) {
                // Set Mapbox access token

                // Add custom CSS for Mapbox popups and markers
                // Add custom CSS for Mapbox popups and markers
                if (!document.getElementById('houzez-mapbox-custom-css')) {
                    const mapboxCSS = `
                        .mapboxgl-popup {
                            z-index: 1000;
                        }
                        .mapboxgl-popup-content {
                            background: none;
                            padding: 0px;
                            border-radius: 0px;
                            box-shadow: none;
                        }
                        .mapboxgl-popup-close-button {
                            color: #f1ecec;
                            background: #000;
                            height: 20px;
                            padding: 0px 0px 2px 0px;
                            width: 20px;
                            line-height: 1px;
                            right: -20px;
                            border-radius: 0;
                            font-size: 20px;
                        }
                        .mapboxgl-popup-close-button:hover {
                            background: rgba(0,0,0,0.5);
                            color: #fff;
                        }
                        .mapbox-marker {
                            cursor: pointer;
                        }
                    `;

                    const styleEl = document.createElement('style');
                    styleEl.id = 'houzez-mapbox-custom-css';
                    styleEl.innerHTML = mapboxCSS;
                    document.head.appendChild(styleEl);
                }

                // Get map data from data attributes
                let mapElement = $('#houzez-properties-map');
                let mapDataJSON = mapElement.data('map');
                let mapOptionsJSON = mapElement.data('options');
                let mapData, mapOptions;
                let initialLat = 0;
                let initialLng = 0;
                let mapbox_style = 'mapbox://styles/mapbox/streets-v12';

                // Get search parameters from URL or form
                const searchParams = new URLSearchParams(
                    window.location.search
                );
                let searchLat = searchParams.get('lat');
                let searchLng = searchParams.get('lng');

                // Also check hidden form fields which might have coordinates from previous search
                if (!searchLat || !searchLng) {
                    searchLat = $('input[name="lat"]').val();
                    searchLng = $('input[name="lng"]').val();
                }

                if (!mapDataJSON) {
                    return;
                }

                try {
                    // If it's already an object (jQuery may have parsed it)
                    if (typeof mapDataJSON === 'object') {
                        mapData = mapDataJSON;
                    } else {
                        // Otherwise parse the JSON string
                        mapData = JSON.parse(mapDataJSON);
                    }

                    // Get options from data-options attribute
                    if (typeof mapOptionsJSON === 'object') {
                        mapOptions = mapOptionsJSON;
                    } else if (mapOptionsJSON) {
                        mapOptions = JSON.parse(mapOptionsJSON);
                    }
                } catch (e) {
                    console.error('Error parsing map data:', e);
                    return;
                }

                // Initialize map options from the data
                if (mapOptions) {
                    clusterIcon = mapOptions.clusterIcon;
                    map_cluster_enable = mapOptions.map_cluster_enable;
                    clusterer_zoom = mapOptions.clusterer_zoom;
                    closeIcon = mapOptions.closeIcon;
                    infoWindowPlac = mapOptions.infoWindowPlac;
                    markerPricePins = mapOptions.markerPricePins;
                    default_lat = parseFloat(mapOptions.default_lat);
                    default_lng = parseFloat(mapOptions.default_lng);
                    default_zoom = parseFloat(mapOptions.default_zoom);
                    max_zoom = parseFloat(mapOptions.max_zoom);
                    mapbox_style = mapOptions.mapbox_style;
                    mapbox_access_token = mapOptions.mapbox_access_token;

                    // If we have valid search coordinates, use them instead of defaults
                    initialLat = default_lat;
                    initialLng = default_lng;

                    if (
                        searchLat &&
                        searchLng &&
                        !isNaN(parseFloat(searchLat)) &&
                        !isNaN(parseFloat(searchLng))
                    ) {
                        initialLat = parseFloat(searchLat);
                        initialLng = parseFloat(searchLng);
                    }

                    // Override default coordinates with taxonomy coordinates if available
                    if (mapOptions.center_lat && mapOptions.center_lng) {
                        initialLat = parseFloat(mapOptions.center_lat);
                        initialLng = parseFloat(mapOptions.center_lng);
                        // Give taxonomy coordinates priority over search coordinates
                        searchLat = null;
                        searchLng = null;
                    }
                }

                if (!mapbox_access_token) {
                    console.error('Mapbox Access Token is missing.');
                    $(mapElement).html(
                        '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Mapbox API key is missing.</p>'
                    );
                    return;
                }

                mapboxgl.accessToken = mapbox_access_token;

                // Initialize Mapbox map with search coordinates if available
                houzezMap = new mapboxgl.Map({
                    container: 'houzez-properties-map',
                    style: mapbox_style,
                    center: [initialLng, initialLat], // Use search coordinates or defaults
                    zoom: default_zoom,
                    maxZoom: max_zoom,
                    cooperativeGestures: true,
                    scrollZoom: true, // Enable scroll zoom to work with cooperative gestures
                });

                const language = new MapboxLanguage({
                    defaultLanguage: houzez.MapboxUtils.getMapLanguage(),
                });
                houzezMap.addControl(language);

                houzezMap.on('error', (e) => {
                    // Mapbox GL JS surfaces HTTP 401s as errors with status 401
                    if (e && e.error && e.error.status === 401) {
                        console.error('Invalid Mapbox token:', e.error.message);
                        $(mapElement).html(
                            '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Invalid Mapbox token – check your API key.</p>'
                        );
                    }
                    return;
                });

                // Make map instance available globally for MapboxUtils
                window.houzezMap = houzezMap;

                houzez.MapboxUtils.remove_map_loader(houzezMap);

                // Use the map bounds to center on the properties when they load
                houzezMap.on('load', function () {
                    // Only add markers and fit bounds if properties exist
                    if (mapData.properties && mapData.properties.length > 0) {
                        houzez.MapboxUtils.addMarkers(mapData.properties);

                        let total_results =
                            $('#total-results').data('total-results');

                        // Show initial map message
                        if (total_results > 0) {
                            showMapMessage(
                                mapData.properties.length,
                                total_results
                            );
                        }

                        // Fit the map to the property bounds if we have properties
                        // but don't override specific search coordinates
                        if (
                            (!searchLat || !searchLng) &&
                            mapData.properties.length > 1
                        ) {
                            houzez.MapboxUtils.map_bounds(houzezMap);
                        }

                        // Initialize infobox trigger if we have properties and not on mobile
                        if (!houzez.MapboxUtils.is_mobile()) {
                            houzez.MapboxUtils.initInfoboxTrigger();
                        }
                    } else {
                        // No properties found message
                        $('#houzez-properties-map').append(
                            '<div class="map-notfound">' +
                                houzez.MapboxUtils.getNotFound() +
                                '</div>'
                        );
                    }

                    // Initialize map controls after map is loaded
                    initMapControls();

                    if (auto_load_map_listings && is_halfmap) {
                        initPropertiesInViewport();
                    }
                });
            }
        };

        /**
         * Initialize the maps module
         */
        const init = function () {
            if (typeof mapboxgl === 'object') {
                initializeMap();
                houzez.MapboxUtils.initMapSearchHandlers();
                houzez.MapboxUtils.initMapViewToggle();
                // Only initialize autocomplete after map is loaded
                initAutocomplete();
            }

            triggerPriceRangeSearch();
            triggerPriceRangeSearchMobile();
            triggerDistanceRangeSearch();
            houzez.MapboxUtils.initializeFullScreenToggle();
        };

        // Public API
        return {
            init: init,
            halfMapAjax: houzez_half_map_listings,
            searchOnChange: houzez_search_on_change,
            getDefaultZoom: () => default_zoom,
            getMarkerPricePins: () => markerPricePins,
            getInfoWindowPlac: () => infoWindowPlac,
            triggerPriceRangeSearch: triggerPriceRangeSearch,
            triggerPriceRangeSearchMobile: triggerPriceRangeSearchMobile,
            triggerDistanceRangeSearch: triggerDistanceRangeSearch,
        };
    })();

    /**
     * SingleAgentMap Module
     * Handles Mapbox functionality for single agent page
     */
    houzez.SingleAgentMapbox = (function () {
        let agentMap = null;
        const mapContainerId = 'houzez-agent-sidebar-map';

        // Default configuration
        const defaultConfig = {
            single_map_zoom: 15,
            mapbox_style: 'mapbox://styles/mapbox/streets-v11',
        };

        /**
         * Initialize the agent sidebar map
         */
        const init = () => {
            if ($('#' + mapContainerId).length <= 0) {
                return;
            }

            let mapZoom = defaultConfig.single_map_zoom;
            const mapbox_style = defaultConfig.mapbox_style;
            const api_mapbox = houzez_vars.api_mapbox;

            // Check if Mapbox GL JS is loaded
            if (typeof mapboxgl === 'undefined') {
                console.error('Mapbox GL JS is not loaded.');
                $('#' + mapContainerId).html(
                    '<p class="text-danger p-4">Mapbox GL JS library not loaded.</p>'
                );
                return;
            }

            // Set Mapbox Access Token
            if (!api_mapbox) {
                console.error('Mapbox Access Token is missing.');
                $('#' + mapContainerId).html(
                    '<p class="text-danger p-4">Mapbox access token is missing.</p>'
                );
                return;
            }
            mapboxgl.accessToken = api_mapbox;

            // Get agent coordinates from data attributes
            const agent_lat = $('#' + mapContainerId).data('lat');
            const agent_lng = $('#' + mapContainerId).data('lng');

            if (
                agent_lat != '' &&
                agent_lng != '' &&
                !isNaN(parseFloat(agent_lat)) &&
                !isNaN(parseFloat(agent_lng))
            ) {
                const agentLngLat = [
                    parseFloat(agent_lng),
                    parseFloat(agent_lat),
                ];

                try {
                    // Initialize the map
                    agentMap = new mapboxgl.Map({
                        container: mapContainerId,
                        style: mapbox_style,
                        center: agentLngLat,
                        zoom: mapZoom,
                        cooperativeGestures: true,
                        scrollZoom: true, // Enable scroll zoom to work with cooperative gestures
                    });

                    const language = new MapboxLanguage({
                        defaultLanguage: houzez.MapboxUtils.getMapLanguage(),
                    });
                    agentMap.addControl(language);

                    agentMap.on('error', (e) => {
                        // Mapbox GL JS surfaces HTTP 401s as errors with status 401
                        if (e && e.error && e.error.status === 401) {
                            console.error(
                                'Invalid Mapbox token:',
                                e.error.message
                            );
                            $('#' + mapContainerId).html(
                                '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Invalid Mapbox token – check your API key.</p>'
                            );
                        }
                        return;
                    });

                    // Add navigation controls
                    agentMap.addControl(
                        new mapboxgl.NavigationControl(),
                        'top-right'
                    );

                    // Add marker at agent's location
                    new mapboxgl.Marker()
                        .setLngLat(agentLngLat)
                        .addTo(agentMap);

                    // Remove map loader if MapUtils is available
                    if (
                        houzez.MapUtils &&
                        typeof houzez.MapUtils.remove_map_loader === 'function'
                    ) {
                        houzez.MapUtils.remove_map_loader(agentMap);
                    }
                } catch (e) {
                    console.error('Error creating Mapbox map for agent:', e);
                    $('#' + mapContainerId).html(
                        '<p class="text-danger p-4">Error initializing map.</p>'
                    );
                }
            } else {
                console.warn('Agent map coordinates are missing or invalid.');
                $('#' + mapContainerId).html(
                    '<p class="text-warning p-4">Agent location not available.</p>'
                );
            }
        };

        // Public API
        return {
            init: init,
        };
    })();

    /**
     * SinglePropertyOverviewMap Module
     * Handles Mapbox functionality for single property overview map
     */
    houzez.SinglePropertyOverviewMapbox = (function () {
        let houzezMap;
        const mapContainerId = 'houzez-overview-listing-map';

        // Default configuration
        const defaultConfig = {
            single_map_zoom: 15,
            map_pin_type: 'marker',
            markerPricePins: 'no',
        };

        let mapbox_style = 'mapbox://styles/mapbox/streets-v12';

        /**
         * Remove map loader after map loads
         */
        const remove_map_loader = function (map) {
            map.on('load', function () {
                jQuery('.houzez-map-loading').hide();
            });
        };

        /**
         * Add custom CSS for Mapbox
         */
        const addCustomCSS = function () {
            if (!document.getElementById('houzez-mapbox-custom-css')) {
                const mapboxCSS = `
                        .mapboxgl-popup {
                            z-index: 1000;
                        }
                        .mapboxgl-popup-content {
                            background: none;
                            padding: 0px;
                            border-radius: 0px;
                            box-shadow: none;
                        }
                        .mapboxgl-popup-close-button {
                            color: #f1ecec;
                            background: #000;
                            height: 20px;
                            padding: 0px 0px 2px 0px;
                            width: 20px;
                            line-height: 1px;
                            right: -20px;
                            border-radius: 0;
                            font-size: 20px;
                        }
                        .mapboxgl-popup-close-button:hover {
                            background: rgba(0,0,0,0.5);
                            color: #fff;
                        }
                        .mapbox-marker {
                            cursor: pointer;
                        }
                    `;

                const styleEl = document.createElement('style');
                styleEl.id = 'houzez-mapbox-custom-css';
                styleEl.innerHTML = mapboxCSS;
                document.head.appendChild(styleEl);
            }
        };

        /**
         * Initialize the property overview map
         */
        const init = function () {
            if ($('#' + mapContainerId).length <= 0) {
                return;
            }

            const mapElement = $('#' + mapContainerId);
            let mapDataJSON = mapElement.data('map');
            let mapOptionsJSON = mapElement.data('options');

            if (!mapDataJSON) {
                return;
            }

            // Get Mapbox API key
            const mapbox_api_key = mapOptionsJSON.mapbox_access_token;

            // If no API key available, display error and exit
            if (!mapbox_api_key) {
                console.error('Mapbox API key not available');
                mapElement.html(
                    '<div class="error-wrap"><p>Mapbox API key is missing. Please add it in the theme options.</p></div>'
                );
                return;
            }

            try {
                // Parse map data
                if (typeof mapDataJSON === 'object') {
                    var propertyMap = mapDataJSON;
                } else {
                    var propertyMap = JSON.parse(mapDataJSON);
                }

                // Parse map options
                if (typeof mapOptionsJSON === 'object') {
                    var mapOptions = mapOptionsJSON;
                } else if (mapOptionsJSON) {
                    var mapOptions = JSON.parse(mapOptionsJSON);
                }
            } catch (e) {
                console.error('Error parsing map data:', e);
                return;
            }

            // Initialize map options from the data
            const showCircle = mapOptions?.map_pin_type === 'circle';
            const markerPricePins =
                mapOptions?.markerPricePins || defaultConfig.markerPricePins;
            const mapZoom =
                parseInt(mapOptions?.single_map_zoom) ||
                defaultConfig.single_map_zoom;

            // Set map style based on map type
            let mapStyle = defaultConfig.mapbox_style;

            if (!propertyMap.latitude || !propertyMap.longitude) {
                console.error('Property coordinates not provided');
                mapElement.html(
                    '<div class="error-wrap"><p>Property coordinates not found</p></div>'
                );
                return;
            }

            // Configure Mapbox
            mapboxgl.accessToken = mapbox_api_key;
            mapStyle = mapOptions.mapbox_style || mapStyle;

            // Create the map
            houzezMap = new mapboxgl.Map({
                container: mapContainerId,
                style: mapStyle,
                center: [propertyMap.longitude, propertyMap.latitude],
                zoom: mapZoom,
                cooperativeGestures: true,
                scrollZoom: true, // Enable scroll zoom to work with cooperative gestures
            });

            const language = new MapboxLanguage({
                defaultLanguage: houzez.MapboxUtils.getMapLanguage(),
            });
            houzezMap.addControl(language);

            // Add loading indicator
            remove_map_loader(houzezMap);

            houzezMap.on('error', (e) => {
                // Mapbox GL JS surfaces HTTP 401s as errors with status 401
                if (e && e.error && e.error.status === 401) {
                    console.error('Invalid Mapbox token:', e.error.message);
                    mapElement.html(
                        '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Invalid Mapbox token – check your API key.</p>'
                    );
                }
                return;
            });

            // Add custom CSS
            addCustomCSS();

            // Add marker after map loads
            houzezMap.on('load', function () {
                if (showCircle) {
                    // Add circle layer to the map
                    houzezMap.addSource('circle-source', {
                        type: 'geojson',
                        data: {
                            type: 'Feature',
                            geometry: {
                                type: 'Point',
                                coordinates: [
                                    propertyMap.longitude,
                                    propertyMap.latitude,
                                ],
                            },
                        },
                    });

                    houzezMap.addLayer({
                        id: 'circle-layer',
                        type: 'circle',
                        source: 'circle-source',
                        paint: {
                            'circle-radius': 20,
                            'circle-color': '#4f5962',
                            'circle-opacity': 0.35,
                            'circle-stroke-width': 2,
                            'circle-stroke-color': '#4f5962',
                            'circle-stroke-opacity': 0.8,
                        },
                    });
                } else {
                    // Create marker element
                    const markerElement = document.createElement('div');
                    markerElement.className = 'mapbox-custom-marker';

                    if (markerPricePins === 'yes' && propertyMap.pricePin) {
                        // Create price pin
                        markerElement.className += ' map-marker-label';

                        // Create price pin content
                        const pricePinInner = document.createElement('div');
                        pricePinInner.className = 'gm-marker-price';
                        pricePinInner.innerHTML = propertyMap.pricePin;
                        markerElement.appendChild(pricePinInner);

                        // Add background and border color if marker_color exists
                        if (propertyMap.marker_color) {
                            markerElement.style.backgroundColor =
                                propertyMap.marker_color;
                            markerElement.style.borderColor =
                                propertyMap.marker_color;
                            markerElement.style.color = '#ffffff';

                            // Update triangle color to match marker color
                            markerElement.style.setProperty(
                                '--triangle-color',
                                propertyMap.marker_color
                            );
                        } else {
                            markerElement.style.setProperty(
                                '--triangle-color',
                                '#1DABE3'
                            );
                        }
                    } else {
                        // Use standard marker with custom icon
                        markerElement.style.backgroundImage =
                            'url(' + propertyMap.marker + ')';
                        markerElement.style.width = '44px';
                        markerElement.style.height = '56px';
                        markerElement.style.backgroundSize = 'contain';
                        markerElement.style.backgroundRepeat = 'no-repeat';
                    }

                    new mapboxgl.Marker({
                        element: markerElement,
                    })
                        .setLngLat([
                            propertyMap.longitude,
                            propertyMap.latitude,
                        ])
                        .addTo(houzezMap);
                }
            });
        };

        // Public API
        return {
            init: init,
        };
    })();

    /**
     * SinglePropertyMap Module
     * Handles Mapbox functionality for single property pages
     */
    houzez.SinglePropertyMapbox = (function () {
        // Private variables
        let houzezMap;
        let mapContainerId = 'houzez-single-listing-map'; // Changed from const to let
        const mapInstances = {}; // Store all map instances by container ID

        // Default configuration
        const defaultConfig = {
            single_map_zoom: 15,
            map_pin_type: 'marker',
            markerPricePins: 'no',
        };

        let mapStyle = 'mapbox://styles/mapbox/streets-v12';

        // Configuration object
        const config = {
            mapContainerId: 'houzez-single-listing-map',
            streetViewContainerId: 'pills-street-view',
            zoomInBtnId: 'listing-mapzoomin',
            zoomOutBtnId: 'listing-mapzoomout',
            mapTypeSelector: '.houzezMapType',
            mapLoaderSelector: '.houzez-map-loading',
            streetViewTabSelector: 'a[href="#pills-street-view"]',
        };

        /**
         * Initialize map options from data
         */
        const initializeMapOptions = function (singleMapData, mapOptionsData) {
            if (!mapboxgl) {
                console.error('Mapbox GL JS is not loaded.');
                return false;
            }

            if (
                !singleMapData ||
                !singleMapData.latitude ||
                !singleMapData.longitude
            ) {
                console.error(
                    'Single property map data (latitude/longitude) is missing.'
                );
                return false;
            }

            // Set map style based on map
            mapStyle = mapOptionsData.mapbox_style || mapStyle;

            if (!mapOptionsData.mapbox_access_token) {
                $('#' + mapContainerId).html(
                    '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Mapbox API key is missing.</p>'
                );
                return;
            }

            // Configure Mapbox
            mapboxgl.accessToken = mapOptionsData.mapbox_access_token;

            // Create the map
            try {
                houzezMap = new mapboxgl.Map({
                    container: mapContainerId,
                    style: mapStyle,
                    cooperativeGestures: true,
                    center: [
                        parseFloat(singleMapData.longitude),
                        parseFloat(singleMapData.latitude),
                    ],
                    zoom:
                        parseInt(mapOptionsData?.single_map_zoom) ||
                        defaultConfig.single_map_zoom,
                    scrollZoom: true, // Enable scroll zoom to work with cooperative gestures
                });

                const language = new MapboxLanguage({
                    defaultLanguage: houzez.MapboxUtils.getMapLanguage(),
                });
                houzezMap.addControl(language);

                houzezMap.on('error', (e) => {
                    // Mapbox GL JS surfaces HTTP 401s as errors with status 401
                    if (e && e.error && e.error.status === 401) {
                        console.error('Invalid Mapbox token:', e.error.message);
                        $('#' + mapContainerId).html(
                            '<p class="text-danger p-4 flex-grow-1 text-center mx-4">Invalid Mapbox token – check your API key.</p>'
                        );
                    }
                    return;
                });

                // Add navigation controls
                houzezMap.addControl(
                    new mapboxgl.NavigationControl(),
                    'top-right'
                );

                // Store the map instance
                mapInstances[mapContainerId] = houzezMap;

                return true;
            } catch (e) {
                console.error('Error creating Mapbox map:', e);
                return false;
            }
        };

        /**
         * Add marker or circle to the map
         */
        const addMarkerOrCircle = function (singleMapData, mapOptionsData) {
            if (!houzezMap) return;

            const showCircle = mapOptionsData?.map_pin_type === 'circle';
            const markerPricePins =
                mapOptionsData?.markerPricePins ||
                defaultConfig.markerPricePins;
            const coordinates = [
                parseFloat(singleMapData.longitude),
                parseFloat(singleMapData.latitude),
            ];

            if (showCircle) {
                // Add circle layer
                houzezMap.on('load', () => {
                    houzezMap.addSource('property-circle-source', {
                        type: 'geojson',
                        data: {
                            type: 'Feature',
                            geometry: {
                                type: 'Point',
                                coordinates: coordinates,
                            },
                        },
                    });

                    houzezMap.addLayer({
                        id: 'property-circle-layer',
                        type: 'circle',
                        source: 'property-circle-source',
                        paint: {
                            'circle-radius': 25,
                            'circle-color': '#4f5962',
                            'circle-opacity': 0.35,
                            'circle-stroke-width': 2,
                            'circle-stroke-color': '#4f5962',
                            'circle-stroke-opacity': 0.8,
                        },
                    });
                });
            } else {
                // Add marker
                let markerElement = document.createElement('div');
                markerElement.className = 'mapbox-marker';

                if (markerPricePins === 'yes' && singleMapData.pricePin) {
                    // Create price pin
                    markerElement.className += ' map-marker-label';

                    // Create price pin content
                    const pricePinInner = document.createElement('div');
                    pricePinInner.className = 'gm-marker-price';
                    pricePinInner.innerHTML = singleMapData.pricePin;
                    markerElement.appendChild(pricePinInner);

                    // Add background and border color if marker_color exists
                    if (singleMapData.marker_color) {
                        markerElement.style.backgroundColor =
                            singleMapData.marker_color;
                        markerElement.style.borderColor =
                            singleMapData.marker_color;
                        markerElement.style.color = '#ffffff';

                        // Update triangle color to match marker color
                        markerElement.style.setProperty(
                            '--triangle-color',
                            singleMapData.marker_color
                        );
                    } else {
                        markerElement.style.setProperty(
                            '--triangle-color',
                            '#1DABE3'
                        );
                    }
                } else {
                    // Use standard marker with custom icon
                    markerElement.style.backgroundImage = `url(${singleMapData.marker})`;
                    markerElement.style.width = '44px';
                    markerElement.style.height = '56px';
                    markerElement.style.backgroundSize = 'contain';
                    markerElement.style.backgroundRepeat = 'no-repeat';
                }

                const marker = new mapboxgl.Marker({ element: markerElement })
                    .setLngLat(coordinates)
                    .addTo(houzezMap);

                // Setup popup if needed
                if (
                    singleMapData.title ||
                    singleMapData.price ||
                    singleMapData.property_type
                ) {
                    // TODO: Implement popup functionality
                    // setupPopup(marker, singleMapData);
                }
            }
        };

        /**
         * Setup map controls
         */
        const setupMapControls = function (currentMapId) {
            // Store the current map ID for this set of controls
            const mapInstance = mapInstances[currentMapId] || houzezMap;

            // Zoom controls - use event delegation to avoid multiple bindings
            $(document)
                .off('click.mapzoom-' + currentMapId)
                .on(
                    'click.mapzoom-' + currentMapId,
                    '#' + config.zoomInBtnId,
                    function () {
                        mapInstance && mapInstance.zoomIn();
                    }
                );

            $(document)
                .off('click.mapzoomout-' + currentMapId)
                .on(
                    'click.mapzoomout-' + currentMapId,
                    '#' + config.zoomOutBtnId,
                    function () {
                        mapInstance && mapInstance.zoomOut();
                    }
                );

            // Map type controls
            $(document)
                .off('click.maptype-' + currentMapId)
                .on(
                    'click.maptype-' + currentMapId,
                    config.mapTypeSelector,
                    function (e) {
                        e.preventDefault();
                        const maptype = $(this).data('maptype');
                        // Change map type for all instances
                        Object.values(mapInstances).forEach((map) => {
                            if (map && map.setStyle) {
                                houzez.MapboxUtils.change_map_type(maptype);
                            }
                        });
                    }
                );
        };

        /**
         * Setup street view placeholder
         */
        const setupStreetView = function () {
            $(config.streetViewTabSelector).on('shown.bs.tab', function () {
                const streetViewContainer = $(
                    '#' + config.streetViewContainerId
                );
                streetViewContainer.html(
                    '<p class="text-center p-5">Street View not available for Mapbox in this version.</p>'
                );
            });
        };

        /**
         * Setup tab resize handler
         */
        const setupTabResizeHandler = function () {
            $('.map-media-tab').on('shown.bs.tab', function () {
                // Find which map container is visible in the shown tab
                const $tabPane = $($(this).attr('href'));
                const $mapContainer = $tabPane.find(
                    '[id^="houzez-single-listing-map"]'
                );

                if ($mapContainer.length > 0) {
                    const containerId = $mapContainer.attr('id');
                    const mapInstance = mapInstances[containerId];

                    if (mapInstance) {
                        setTimeout(() => {
                            mapInstance.resize();
                        }, 50);
                    }
                } else {
                    // If we can't find specific container, resize all maps
                    Object.values(mapInstances).forEach((mapInstance) => {
                        if (mapInstance) {
                            setTimeout(() => {
                                mapInstance.resize();
                            }, 50);
                        }
                    });
                }
            });
        };

        /**
         * Initialize the module
         */
        const init = function () {
            // Check for multiple possible map container IDs
            const mapIds = [
                'houzez-single-listing-map',
                'houzez-single-listing-map-address',
                'houzez-single-listing-map-elementor',
            ];

            mapIds.forEach((currentMapId) => {
                const mapContainer = document.getElementById(currentMapId);
                if (!mapContainer) {
                    return;
                }

                const mapDataAttr = mapContainer.getAttribute('data-map');
                const mapOptionsAttr =
                    mapContainer.getAttribute('data-options');

                if (!mapDataAttr || !mapOptionsAttr) {
                    console.log(
                        `Map container ${currentMapId} found, but missing data attributes.`
                    );
                    $(config.mapLoaderSelector).hide();
                    return;
                }

                try {
                    const singleMapData = JSON.parse(mapDataAttr);
                    const mapOptionsData = JSON.parse(mapOptionsAttr);

                    // Update container ID for this instance
                    mapContainerId = currentMapId;

                    // Initialize map
                    if (!initializeMapOptions(singleMapData, mapOptionsData)) {
                        $(config.mapLoaderSelector).hide();
                        return;
                    }

                    // Setup map components
                    addMarkerOrCircle(singleMapData, mapOptionsData);
                    setupMapControls(currentMapId);
                    setupStreetView();
                    setupTabResizeHandler();

                    // Remove loader
                    houzez.MapboxUtils.remove_map_loader(houzezMap);
                } catch (e) {
                    console.error(
                        `Error initializing single property map (${currentMapId}):`,
                        e
                    );
                    $(config.mapLoaderSelector).hide();
                }
            });
        };

        // Public API
        return {
            init: init,
            getMapInstances: () => mapInstances,
            getMapInstance: (id) => mapInstances[id],
            resizeAllMaps: () => {
                Object.values(mapInstances).forEach((mapInstance) => {
                    if (mapInstance && mapInstance.resize) {
                        mapInstance.resize();
                    }
                });
            },
        };
    })();

    // Initialize when document is ready
    $(document).ready(function () {
        // Initialize MapboxUtils first
        houzez.MapboxUtils.init();

        // Initialize Maps module only if we have a properties map
        if (
            $('#houzez-properties-map').length > 0 ||
            $('input[name="search_location"]').length > 0
        ) {
            houzez.Mapbox.init();
        }

        // Initialize SingleAgentMap only if we have an agent map
        if ($('#houzez-agent-sidebar-map').length > 0) {
            houzez.SingleAgentMapbox.init();
        }

        // Initialize SinglePropertyOverviewMap if we have an overview map
        if ($('#houzez-overview-listing-map').length > 0) {
            houzez.SinglePropertyOverviewMapbox.init();
        }

        // Initialize SinglePropertyMap if we have a single property map
        if (
            $('#houzez-single-listing-map').length > 0 ||
            $('#houzez-single-listing-map-address').length > 0 ||
            $('#houzez-single-listing-map-elementor').length > 0
        ) {
            houzez.SinglePropertyMapbox.init();
        }
    });

    // Make houzez object available globally
    window.houzez = houzez;
})(jQuery);
