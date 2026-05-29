/*
 ** Show properties for header map and half map
 */
(function ($) {
    'use strict';

    // Main Houzez object that will contain all modules
    var houzez = window.houzez || {}; 

    houzez.OSMUtils = (function () {
        // Variables needed for utility functions
        let thousands_separator = '';
        let ajaxurl = '';
        let userID = '';
        let not_found = '';
        let markers = new Array();
        let current_marker = 0;
        let current_marker_id = 0;
        let activePopup = null;
        let infoWindowPlac = '';
        let markerPricePins = 'no';
        let mapbox_access_token = '';
        let map_cluster_enable = 1;
        let clusterer_zoom = 12;
        let closeIcon = '';
        let marker_spiderfier = 0;
        let mapType = 'roadmap';
        let googlemap_style = '';
        let default_lat = 25.68654;
        let default_lng = -80.431345;
        let houzez_rtl = false;
        let houzez_default_radius = 0;
        let clusterIcon = '';

        // Initialize variables from houzez_vars
        if (typeof houzez_vars !== 'undefined') {
            ajaxurl = houzez_vars.admin_url + 'admin-ajax.php';
            userID = houzez_vars.user_id;
            not_found = houzez_vars.not_found;
            thousands_separator = houzez_vars.thousands_separator;
            infoWindowPlac = houzez_vars.infoWindowPlac;
            markerPricePins = houzez_vars.markerPricePins;
            mapbox_access_token = houzez_vars.api_mapbox;
            houzez_default_radius = parseInt(houzez_vars.houzez_default_radius);
            houzez_rtl = houzez_vars.houzez_rtl === 'yes';
        }

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

        // Getter methods for variables
        const getAjaxUrl = () => ajaxurl;
        const getIsMobile = () => is_mobile();
        const getUserID = () => userID;
        const getNotFound = () => not_found;
        const getThousandsSeparator = () => thousands_separator;
        const getInfoWindowPlac = () => infoWindowPlac;
        const getMarkerPricePins = () => markerPricePins;
        const getMapboxAccessToken = () => mapbox_access_token;
        const getMapClusterEnable = () => map_cluster_enable;
        const getClustererZoom = () => clusterer_zoom;
        const getCloseIcon = () => closeIcon;
        const getMarkerSpiderfier = () => marker_spiderfier;
        const getMapType = () => mapType;
        const getGoogleMapStyle = () => googlemap_style;
        const getDefaultLat = () => default_lat;
        const getDefaultLng = () => default_lng;
        const getHouzezRtl = () => houzez_rtl;
        const getDefaultRadius = () => houzez_default_radius;
        const getClusterIcon = () => clusterIcon;
        const getMarkers = () => markers;
        const getCurrentMarker = () => current_marker;
        const getCurrentMarkerId = () => current_marker_id;
        const getActivePopup = () => activePopup;

        // Setter methods for variables that need to be updated
        const setCurrentMarker = (value) => {
            current_marker = value;
        };
        const setCurrentMarkerId = (value) => {
            current_marker_id = value;
        };
        const setActivePopup = (value) => {
            activePopup = value;
        };
        const setMarkers = (value) => {
            markers = value;
        };

        /**
         * Get appropriate tile layer based on configuration
         */
        const getTileLayer = () => {
            return L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }
            );
        };

        /**
         * Utility function to debounce function calls
         */
        const debounce = (func, wait) => {
            let timeout;
            let lastInvoked = 0;
            const minInterval = 800; // Minimum time between actual executions

            return function (...args) {
                const context = this;
                const now = Date.now();

                // Clear any existing timeouts
                clearTimeout(timeout);

                // Check if enough time has passed since last invocation
                if (now - lastInvoked < minInterval) {
                    // If called too soon, reschedule
                    timeout = setTimeout(() => {
                        lastInvoked = Date.now();
                        func.apply(context, args);
                    }, wait);
                } else {
                    // Execute immediately if enough time has passed
                    timeout = setTimeout(() => {
                        lastInvoked = Date.now();
                        func.apply(context, args);
                    }, wait);
                }
            };
        };

        const init = () => {
            // Any additional initialization if needed
        };

        return {
            init: init,
            getAjaxUrl,
            getIsMobile,
            getTileLayer,
            getUserID,
            getNotFound,
            getThousandsSeparator,
            getInfoWindowPlac,
            getMarkerPricePins,
            getMapboxAccessToken,
            getMapClusterEnable,
            getClustererZoom,
            getCloseIcon,
            getMarkerSpiderfier,
            getMapType,
            getGoogleMapStyle,
            getDefaultLat,
            getDefaultLng,
            getHouzezRtl,
            debounce,
            getDefaultRadius,
            getClusterIcon,
            getMarkers,
            getCurrentMarker,
            getCurrentMarkerId,
            getActivePopup,
            setCurrentMarker,
            setCurrentMarkerId,
            setActivePopup,
            setMarkers,
        };
    })();

    /**
     * OSMMap Module
     * Handles OpenStreetMap functionality for property maps
     */
    houzez.OSMMap = (function () {
        // Map variables
        let houzezMap;
        let osm_markers_cluster;
        let markers = new Array();
        let houzez_map_properties = []; // Store map properties data

        let clusterIcon = '';
        let map_cluster_enable = 1;
        let clusterer_zoom = 12;
        let closeIcon = '';
        let infoWindowPlac = '';
        let marker_spiderfier = 0;
        let current_marker = 0;
        let current_page = 0;
        let lastClickedMarker;
        let markerPricePins = 'no';
        let googlemap_style = '';
        let mapType = 'roadmap';
        let isSearchInProgress = false; // Flag to prevent recursive map searches
        let pendingSearch = false; // Coalesce flag: queue one re-run if input changes during AJAX
        let pagination_only = false;
        let viewport_search = false;
        let map_message_timeout = null;
        let default_lat = 0;
        let default_lng = 0;
        let default_zoom = 12;
        let max_zoom = 18;
        let auto_load_map_listings = 0;
        let is_halfmap = 0;
        let ajaxurl = '';
        let userID = '';
        let not_found = '';
        let thousands_separator = '';
        let isUpdatingMapProgrammatically = false; // Flag to prevent recursive updates

        // Ajax and other variables

        if (typeof houzez_vars !== 'undefined') {
            ajaxurl = houzez_vars.admin_url + 'admin-ajax.php';
            userID = houzez_vars.user_id;
            not_found = houzez_vars.not_found;
            thousands_separator = houzez_vars.thousands_separator;
            is_halfmap = parseInt(houzez_vars.is_halfmap);
            auto_load_map_listings = parseInt(
                houzez_vars.auto_load_map_listings
            );
        }

        /**
         * Navigate to next map marker
         */
        const houzez_map_next = function (hMap) {
            current_marker++;
            if (current_marker > markers.length) {
                current_marker = 1;
            }
            while (markers[current_marker - 1].visible === false) {
                current_marker++;
                if (current_marker > markers.length) {
                    current_marker = 1;
                }
            }

            hMap.setView(markers[current_marker - 1].getLatLng());
            if (!markers[current_marker - 1]._icon) {
                markers[current_marker - 1].__parent.spiderfy();
            }

            if (
                current_marker - 1 == 0 ||
                current_marker - 1 == markers.length
            ) {
                setTimeout(function () {
                    markers[current_marker - 1].fire('click');
                }, 500);
            } else {
                markers[current_marker - 1].fire('click');
            }
        };

        /**
         * Navigate to previous map marker
         */
        const houzez_map_prev = function (hMap) {
            current_marker--;
            if (current_marker < 1) {
                current_marker = markers.length;
            }
            while (markers[current_marker - 1].visible === false) {
                current_marker--;
                if (current_marker > markers.length) {
                    current_marker = 1;
                }
            }

            hMap.setView(markers[current_marker - 1].getLatLng());
            if (!markers[current_marker - 1]._icon) {
                markers[current_marker - 1].__parent.spiderfy();
            }

            if (current_marker - 1 == 0 || current_marker == markers.length) {
                setTimeout(function () {
                    markers[current_marker - 1].fire('click');
                }, 500);
            } else {
                markers[current_marker - 1].fire('click');
            }
        };

        /**
         * Zoom in on the map
         */
        const houzez_map_zoomin = function (hMap) {
            $('#listing-mapzoomin').on('click', function () {
                var current = parseInt(hMap.getZoom(), 10);
                current++;
                if (current > 20) {
                    current = 20;
                }

                hMap.setZoom(current);

                // On desktop, manually trigger viewport update
                // On mobile, the zoomend event will handle it automatically
                if (!houzez.OSMUtils.getIsMobile()) {
                    updateViewportCoordinates();
                }
            });
        };

        /**
         * Zoom out on the map
         */
        const houzez_map_zoomout = function (hMap) {
            $('#listing-mapzoomout').on('click', function () {
                var current = parseInt(hMap.getZoom(), 10);
                current--;
                if (current < 0) {
                    current = 0;
                }

                hMap.setZoom(current);

                // On desktop, manually trigger viewport update
                // On mobile, the zoomend event will handle it automatically
                if (!houzez.OSMUtils.getIsMobile()) {
                    updateViewportCoordinates();
                }
            });
        };

        /**
         * Reload markers
         */
        const reloadOSMMarkers = function () {
            // Loop through markers and set map to null for each
            for (var i = 0; i < markers.length; i++) {
                houzezMap.removeLayer(markers[i]);
            }
            // Reset the markers array
            markers = [];
            if (osm_markers_cluster) {
                houzezMap.removeLayer(osm_markers_cluster);
            }
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

        /**
         * Get map bounds from properties
         */
        const getMapBounds = function (mapDataProperties) {
            // get map bounds
            var mapBounds = [];
            for (var i = 0; i < mapDataProperties.length; i++) {
                if (
                    mapDataProperties[i].latitude &&
                    mapDataProperties[i].longitude
                ) {
                    mapBounds.push([
                        mapDataProperties[i].latitude,
                        mapDataProperties[i].longitude,
                    ]);
                }
            }
            return mapBounds;
        };

        /**
         * Fit map bounds to markers with minimum zoom level
         */
        const houzez_map_bounds = function () {
            if (!houzezMap || markers.length === 0) {
                return;
            }

            const bounds = L.latLngBounds();

            markers.forEach(function (marker) {
                bounds.extend(marker.getLatLng());
            });

            // Set flag to prevent triggering viewport search
            isUpdatingMapProgrammatically = true;

            // If we have only one marker or very close markers
            if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
                // Single point - use default zoom and center on the point
                const center = bounds.getCenter();
                houzezMap.setView(center, default_zoom, { animate: false });
                setTimeout(() => {
                    isUpdatingMapProgrammatically = false;
                }, 100);
                return;
            }

            // Calculate what zoom level would be used for these bounds
            const boundsZoom = houzezMap.getBoundsZoom(bounds);

            if (boundsZoom > default_zoom) {
                // If bounds zoom is larger than default (more zoomed in),
                // use default zoom and center on bounds
                houzezMap.setView(bounds.getCenter(), default_zoom, {
                    animate: false,
                });
            } else {
                // Otherwise use fitBounds with padding to ensure all markers are visible
                houzezMap.fitBounds(bounds, {
                    animate: false,
                    padding: [50, 50], // Add padding to ensure markers near edges are visible
                });
            }

            // Reset flag after map settles
            setTimeout(() => {
                isUpdatingMapProgrammatically = false;
            }, 100);
        };

        /**
         * Add markers to the map
         */
        const houzezAddMarkers = function (
            map_properties,
            houzezMap,
            preservePosition = false
        ) {
            var propertyMarker;

            var mBounds = getMapBounds(map_properties);

            if (map_cluster_enable == 1) {
                osm_markers_cluster = new L.MarkerClusterGroup({
                    iconCreateFunction: function (cluster) {
                        var markers1 = cluster.getAllChildMarkers();
                        var html =
                            '<div class="houzez-osm-cluster">' +
                            markers1.length +
                            '</div>';
                        return L.divIcon({
                            html: html,
                            className: 'mycluster',
                            iconSize: L.point(47, 47),
                        });
                    },
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: true,
                    zoomToBoundsOnClick: true,
                });
            }

            console.log(map_properties);

            for (var i = 0; i < map_properties.length; i++) {
                // Validate latitude and longitude are valid numbers
                const lat = parseFloat(map_properties[i].latitude);
                const lng = parseFloat(map_properties[i].longitude);

                // Skip properties with invalid coordinates
                if (
                    !map_properties[i].latitude ||
                    !map_properties[i].longitude ||
                    isNaN(lat) ||
                    isNaN(lng) ||
                    lat < -90 ||
                    lat > 90 ||
                    lng < -180 ||
                    lng > 180
                ) {
                    console.warn(
                        'Skipping property with invalid coordinates:',
                        {
                            property_id: map_properties[i].property_id,
                            latitude: map_properties[i].latitude,
                            longitude: map_properties[i].longitude,
                        }
                    );
                    continue;
                }

                var mapData = map_properties[i];
                let marker_color = mapData.marker_color;
                var mapCenter = L.latLng(lat, lng);
                var markerOptions = {
                    riseOnHover: true,
                };

                if (mapData.title) {
                    markerOptions.title = mapData.title;
                }

                if (markerPricePins == 'yes') {
                    var pricePin =
                        '<div data-id="' +
                        mapData.property_id +
                        '" class="osm-marker map-marker-label"' +
                        (marker_color
                            ? ' style="background-color: ' +
                              marker_color +
                              '; border-color: ' +
                              marker_color +
                              '; color: #ffffff;"'
                            : '') +
                        '>' +
                        mapData.pricePin +
                        '</div>';

                    var myIcon = L.divIcon({
                        className: 'someclass',
                        iconSize: new L.Point(0, 0),
                        html: pricePin,
                    });

                    if (map_cluster_enable == 1) {
                        propertyMarker = new L.Marker(mapCenter, {
                            icon: myIcon,
                        });
                    } else {
                        propertyMarker = L.marker(mapCenter, {
                            icon: myIcon,
                        }).addTo(houzezMap);
                    }
                } else {
                    // Marker icon
                    if (mapData.marker) {
                        var iconOptions = {
                            iconUrl: mapData.marker,
                            iconSize: [44, 56],
                            iconAnchor: [20, 57],
                            popupAnchor: [1, -57],
                        };
                        if (mapData.retinaMarker) {
                            iconOptions.iconRetinaUrl = mapData.retinaMarker;
                        }
                        markerOptions.icon = L.icon(iconOptions);
                    }

                    if (map_cluster_enable == 1) {
                        propertyMarker = new L.Marker(mapCenter, markerOptions);
                    } else {
                        propertyMarker = L.marker(
                            mapCenter,
                            markerOptions
                        ).addTo(houzezMap);
                    }
                }

                if (map_cluster_enable == 1) {
                    osm_markers_cluster.addLayer(propertyMarker);
                }

                var mainContent = document.createElement('div');
                mainContent.className = 'property-info-window';
                var innerHTML = '';

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
                        infoWindowPlac +
                        '" alt="' +
                        map_properties[i].title +
                        '"/>' +
                        '</a>' +
                        '</div>';
                }

                innerHTML += '<div class="info-content" style="padding:10px;">';

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

                mainContent.innerHTML = innerHTML;

                // Ensure property ID is stored as a string for consistent comparisons
                propertyMarker.id = mapData.property_id.toString();
                markers.push(propertyMarker);
                propertyMarker.bindPopup(mainContent);
            } // end for loop

            if (map_cluster_enable == 1) {
                houzezMap.addLayer(osm_markers_cluster);
            }

            // After all markers are added, fit the map bounds if needed
            if (!preservePosition && markers.length > 0) {
                houzez_map_bounds();
            }
        }; // end houzezAddMarkers

        /**
         * Check if a marker is in a cluster
         */
        const is_marker_in_cluster = function (marker, cluster) {
            if (!marker) return false;

            // Ensure marker ID is a string for comparison
            const markerIdStr = marker.toString();

            var length = cluster.length;
            for (var j = 0; j < length; j++) {
                if (cluster[j].id && cluster[j].id.toString() === markerIdStr) {
                    return true;
                }
            }
            return false;
        };

        /**
         * Open popup for a marker
         */
        const openPopup = function (marker_id) {
            if (!marker_id) {
                console.log('No marker ID provided to openPopup');
                return;
            }

            // Ensure we're working with a string
            const markerIdStr = marker_id.toString();
            console.log('Opening popup for marker ID:', markerIdStr);

            houzezMap.eachLayer(function (layer) {
                if (typeof layer._childCount !== 'undefined') {
                    var markers_in_cluster = layer.getAllChildMarkers();

                    if (is_marker_in_cluster(markerIdStr, markers_in_cluster)) {
                        layer.spiderfy();

                        markers_in_cluster.forEach(function (prop_marker) {
                            if (
                                prop_marker.id &&
                                prop_marker.id.toString() === markerIdStr
                            ) {
                                prop_marker.openPopup();
                            }
                        });
                    }
                } else {
                    if (layer.id && layer.id.toString() === markerIdStr) {
                        layer.openPopup();
                    }
                }
            });
        };

        /**
         * Close popup for a marker
         */
        const closePopup = function (marker_id) {
            if (!marker_id) return;

            // Ensure we're working with a string
            const markerIdStr = marker_id.toString();

            houzezMap.eachLayer(function (layer) {
                if (typeof layer._childCount !== 'undefined') {
                    var markers_in_cluster = layer.getAllChildMarkers();

                    if (is_marker_in_cluster(markerIdStr, markers_in_cluster)) {
                        layer.unspiderfy();

                        markers_in_cluster.forEach(function (prop_marker) {
                            if (
                                prop_marker.id &&
                                prop_marker.id.toString() === markerIdStr
                            ) {
                                layer.closePopup();
                            }
                        });
                    }
                } else {
                    if (layer.id && layer.id.toString() === markerIdStr) {
                        layer.closePopup();
                        houzezMap.closePopup();
                    }
                }
            });
        };

        /**
         * Setup infobox popup trigger for hovering over property items
         */
        const setupInfoboxPopupTrigger = function () {
            if (houzez.OSMUtils.getIsMobile()) return;

            $('#half-map-listing-area .hz-map-trigger').each(function () {
                // Get the Property ID - handle both string formats and objects
                var propertyID = $(this).data('hz-id');

                // Handle different property ID formats
                if (typeof propertyID === 'string') {
                    // If it has 'hz-' prefix, remove it
                    if (propertyID.indexOf('hz-') === 0) {
                        propertyID = propertyID.replace('hz-', '');
                    }
                } else {
                    // Just get the numeric value
                    propertyID = $(this)
                        .data('hz-id')
                        .toString()
                        .replace(/[^\d.]/g, '');
                }

                // Store the ID to ensure it's available in the event handlers
                const propID = propertyID;

                $(this)
                    .on('mouseenter', function () {
                        console.log('Mouse enter on property ID:', propID);
                        openPopup(propID);
                    })
                    .on('mouseleave', function () {
                        closePopup(propID);
                    });
            });

            return false;
        };

        // Trigger map search when slider interaction ends
        const triggerDistanceRangeSearch = function () {
            if ($('#houzez-properties-map').length > 0) {
                current_page = 0;
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
                current_page = 0;
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
                current_page = 0;
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
         * Set URL based on search form parameters
         */
        const houzez_set_url = function (currentForm) {
            var $form =
                currentForm || $('form.houzez-search-filters-js').first();

            if ($form.length) {
                $form.addClass('loading');
            }

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

            houzezSetPushState(url);
        };

        /**
         * Trigger search on change
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

            $form.addClass('Current Search Form');

            // Safety check to ensure we have a form
            if (!$form || !$form.length) {
                console.warn('No valid form found for search');
                return;
            }

            isSearchInProgress = true;
            $('.hz-halfmap-paged').val(current_page);
            houzez_set_url($form);
            houzez_half_map_listings(current_page, $form);
        };

        /**
         * Update viewport coordinates
         */
        const updateViewportCoordinates = () => {
            if (!auto_load_map_listings) {
                return;
            }

            // Prevent recursive calls when map is being updated programmatically
            if (isUpdatingMapProgrammatically) {
                return;
            }

            viewport_search = true;
            let $form = $('#desktop-search-form');

            let overlayform = $(
                '#mobile-search-form.hz-mobile-overlay-search-js'
            );

            if (overlayform.length > 0) {
                $form = $('#mobile-search-form');
            }

            const bounds = houzezMap.getBounds();

            if (bounds) {
                const ne = bounds.getNorthEast();
                const sw = bounds.getSouthWest();
                const zoom = houzezMap.getZoom();
                $('input[name="ne_lat"]').val(ne.lat);
                $('input[name="ne_lng"]').val(ne.lng);
                $('input[name="sw_lat"]').val(sw.lat);
                $('input[name="sw_lng"]').val(sw.lng);
                $('input[name="zoom"]').val(zoom);
                $('input[name="use_radius"]').prop('checked', false);

                houzez_search_on_change(null, $form);
            }
        };

        const initPropertiesInViewport = () => {
            if (!houzezMap) return;

            // Remove any existing event handlers to prevent duplicates
            houzezMap.off('dragend');
            houzezMap.off('zoomend');

            // Use a single handler for all map movements to avoid multiple triggers
            const debouncedMapMoveHandler = houzez.OSMUtils.debounce(
                updateViewportCoordinates,
                300
            );

            // Store a reference to the debounced handler
            houzezMap.debouncedMapMoveHandler = debouncedMapMoveHandler;

            // Always listen to dragend for pan/drag operations (desktop and mobile)
            houzezMap.on('dragend', debouncedMapMoveHandler);

            // On mobile devices ONLY, also listen to zoomend for pinch-to-zoom
            // This captures ALL zoom events on mobile (pinch-to-zoom, double-tap, and zoom buttons)
            // Desktop zoom buttons handle their own updates via direct calls
            if (houzez.OSMUtils.getIsMobile()) {
                houzezMap.on('zoomend', debouncedMapMoveHandler);
            }
        };

        /**
         * AJAX search for half map view
         */
        const houzez_half_map_listings = function (current_page, current_form) {
            var ajax_container = $('#houzez_ajax_container');
            var $halfmapBtn = current_form.find('.half-map-search-js-btn');
            var ajax_map_wrap = $('.map-wrap');
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
                url: ajaxurl,
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
                            // Store current map position and zoom if this is a viewport search
                            let currentCenter = houzezMap.getCenter();
                            let currentZoom = houzezMap.getZoom();

                            reloadOSMMarkers();
                            houzezAddMarkers(
                                data.properties,
                                houzezMap,
                                viewport_search
                            );

                            // Restore map position and zoom if this is a viewport search
                            if (viewport_search) {
                                isUpdatingMapProgrammatically = true;
                                houzezMap.setView(currentCenter, currentZoom);
                                // Reset flag after a short delay to allow map to settle
                                setTimeout(() => {
                                    isUpdatingMapProgrammatically = false;
                                }, 100);
                            }

                            // Show map message with property counts
                            showMapMessage(
                                data.properties.length,
                                data.total_results
                            );
                        } // End of if (data.getProperties === true)

                        ajax_container.empty().html(data.propHtml);
                        total_results.empty().html(data.total_results);
                        setupMapAjaxPagination();

                        houzez_listing_lightbox();
                        houzez_grid_image_gallery();
                        houzez_grid_call_to_action();
                        compare_for_ajax_map();

                        $('[data-bs-toggle="tooltip"]').tooltip();

                        // Only initialize infobox trigger if we're not on mobile and have properties
                        if (!houzez.OSMUtils.getIsMobile()) {
                            setupInfoboxPopupTrigger();
                        }
                    } else {
                        reloadOSMMarkers();

                        let currentCenter = houzezMap.getCenter();
                        let currentZoom = houzezMap.getZoom();

                        houzezMap.setView(currentCenter, currentZoom);
                        $('#houzez-properties-map').append(
                            '<div class="map-notfound" style="z-index: 1000;">' +
                                not_found +
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

        /**
         * Update browser URL with current search parameters
         */
        const houzezSetPushState = function (pageUrl) {
            window.history.pushState(
                {
                    houzezTheme: true,
                },
                '',
                pageUrl
            );
        };

        /**
         * Setup pagination for AJAX loaded results
         */
        const setupMapAjaxPagination = function () {
            $('.houzez_ajax_pagination a').on('click', function (e) {
                e.preventDefault();

                if (auto_load_map_listings) {
                    pagination_only = true;
                }

                current_page = $(this).data('houzepagi');
                $('.hz-halfmap-paged').val(current_page);
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
         * Initialize map event handlers for search and sorting
         */
        const initializeMapSearchHandlers = function () {
            setupMapAjaxPagination();

            // Sorting
            $('#ajax_sort_properties').on('change', function () {
                let $form = $('#desktop-search-form');
                let mobile_sortby = $(this).hasClass('mobile-sortby');
                if (mobile_sortby) {
                    $form = $('#mobile-search-form');
                }
                houzez_search_on_change(null, $form);
            });

            // Search fields
            $('select.houzez_search_ajax, input.houzez_search_ajax').on(
                'change',
                function () {
                    let $form = $(this).closest('form');
                    houzez_search_on_change(null, $form);
                }
            );

            // Search button
            $('.btn-apply, .half-map-search-js-btn, #auto_complete_ajax').on(
                'click',
                function (e) {
                    e.preventDefault();
                    let $form = $(this).closest('form');
                    houzez_search_on_change(null, $form);
                }
            );
        };

        /**
         * Initialize location autocomplete
         */
        const initLocationAutocomplete = function () {
            if ($('.hz-map-field-js').length > 0) {
                // Get country limit settings from houzez_vars
                var geo_country_limit = houzez_vars.geo_country_limit;
                var geocomplete_country = houzez_vars.geocomplete_country;

                // Format country code for Nominatim
                var countryRestriction = '';
                if (geo_country_limit != 0 && geocomplete_country != '') {
                    // Handle special case for UAE
                    if (geocomplete_country == 'UAE') {
                        geocomplete_country = 'AE';
                    }
                    countryRestriction = geocomplete_country.toLowerCase();
                }

                // Initialize each map field separately to prevent conflicts
                var MapField = function ($container) {
                    this.$container = $container;
                    this.currentRequest = null;
                    this.requestDelay = 300; // 300ms delay between requests
                };

                MapField.prototype = {
                    init: function () {
                        this.initDomElements();
                        this.autocomplete();
                    },

                    initDomElements: function () {
                        this.addressField =
                            this.$container.data('address-field');
                    },

                    autocomplete: function () {
                        var that = this;
                        var addressField = this.addressField;

                        if (!addressField) {
                            return;
                        }

                        var inputField = document.getElementById(addressField);
                        if (!inputField) {
                            return;
                        }
                        var $inputField = $(inputField);
                        var $parent = $inputField.parents('.location-search');

                        // Initialize autocomplete for this specific field
                        $inputField.autocomplete({
                            appendTo: 'body', // Append to body to avoid z-index issues
                            source: function (request, response) {
                                // Cancel any pending request for this field
                                if (
                                    that.currentRequest &&
                                    that.currentRequest.readyState !== 4
                                ) {
                                    that.currentRequest.abort();
                                }

                                // Clear any existing timeout for this field
                                clearTimeout(that.searchTimeout);

                                // Set a timeout to delay the request
                                that.searchTimeout = setTimeout(() => {
                                    // Prepare request parameters
                                    var params = {
                                        format: 'json',
                                        q: request.term,
                                        limit: 10,
                                        addressdetails: 1,
                                    };

                                    // Add country restriction if enabled
                                    if (countryRestriction) {
                                        params.countrycodes =
                                            countryRestriction;
                                    }

                                    that.currentRequest = jQuery.ajax({
                                        url: 'https://nominatim.openstreetmap.org/search',
                                        type: 'GET',
                                        data: params,
                                        dataType: 'json',
                                        cache: false,
                                        success: function (result) {
                                            if (!result.length) {
                                                response([
                                                    {
                                                        value: '',
                                                        label: 'there are no results',
                                                    },
                                                ]);
                                                return;
                                            }
                                            response(
                                                result.map(function (place) {
                                                    return {
                                                        label: place.display_name,
                                                        latitude: place.lat,
                                                        longitude: place.lon,
                                                        value: place.display_name,
                                                    };
                                                })
                                            );
                                        },
                                        error: function (xhr) {
                                            if (xhr.statusText !== 'abort') {
                                                console.warn(
                                                    'Nominatim API request failed:',
                                                    xhr.status,
                                                    xhr.statusText
                                                );
                                                response([
                                                    {
                                                        value: '',
                                                        label: 'Search service temporarily unavailable',
                                                    },
                                                ]);
                                            }
                                        },
                                    });
                                }, that.requestDelay);
                            },
                            minLength: 2, // Minimum characters before triggering search
                            delay: 0, // We handle delay manually above
                            select: function (event, ui) {
                                // Update coordinates in the closest form context
                                $parent
                                    .find('input[name="lat"]')
                                    .val(ui.item.latitude);
                                $parent
                                    .find('input[name="lng"]')
                                    .val(ui.item.longitude);
                                $parent.find('input[name="ne_lat"]').val('');
                                $parent.find('input[name="ne_lng"]').val('');
                                $parent.find('input[name="sw_lat"]').val('');
                                $parent.find('input[name="sw_lng"]').val('');
                                $parent.find('input[name="zoom"]').val('');
                                $parent
                                    .find('input[name="use_radius"]')
                                    .prop('checked', true);

                                if (is_halfmap) {
                                    let $this = $(this);
                                    houzez_search_on_change($this);
                                }
                            },
                        });

                        // Set high z-index for autocomplete dropdown
                        $inputField
                            .autocomplete('widget')
                            .css('z-index', 99999);
                    },
                };

                // Initialize each map field separately
                var initGeoField = function () {
                    var $this = $(this);
                    var mapField = new MapField($this);
                    mapField.init();
                };

                $('.hz-map-field-js').each(initGeoField);

                // Add location trigger functionality with debouncing to prevent rate limiting
                let locationRequestsInProgress = new Set();

                $('.location-trigger').on('click', function (e) {
                    e.preventDefault();

                    let $this = $(this);
                    let $parent = $this.parents('.location-search');
                    let $input = $parent.find('input.search_location_js');
                    let fieldId = $input.attr('id') || 'default';

                    // Prevent multiple simultaneous location requests for this specific field
                    if (locationRequestsInProgress.has(fieldId)) {
                        return;
                    }

                    $this.find('.icon-location-target').addClass('icon-spin');
                    locationRequestsInProgress.add(fieldId);

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                // Get location name from coordinates using Nominatim
                                $.get(
                                    'https://nominatim.openstreetmap.org/reverse',
                                    {
                                        format: 'json',
                                        lat: position.coords.latitude,
                                        lon: position.coords.longitude,
                                        zoom: 18,
                                        addressdetails: 1,
                                    },
                                    function (data) {
                                        if (data && data.display_name) {
                                            $input.val(data.display_name);
                                        } else {
                                            $input.val(
                                                houzez_vars.current_location
                                            );
                                        }

                                        // Update coordinates in the specific form context
                                        $parent
                                            .find('input[name="lat"]')
                                            .val(position.coords.latitude);
                                        $parent
                                            .find('input[name="lng"]')
                                            .val(position.coords.longitude);
                                        $parent
                                            .find('input[name="ne_lat"]')
                                            .val('');
                                        $parent
                                            .find('input[name="ne_lng"]')
                                            .val('');
                                        $parent
                                            .find('input[name="sw_lat"]')
                                            .val('');
                                        $parent
                                            .find('input[name="sw_lng"]')
                                            .val('');
                                        $parent
                                            .find('input[name="zoom"]')
                                            .val('');
                                        $parent
                                            .find('input[name="use_radius"]')
                                            .prop('checked', true);

                                        // Trigger search if in half map
                                        if (is_halfmap) {
                                            houzez_search_on_change($this);
                                        } else {
                                            $this
                                                .find('.icon-location-target')
                                                .removeClass('icon-spin');
                                        }
                                    },
                                    'json'
                                )
                                    .fail(function (xhr) {
                                        console.warn(
                                            'Reverse geocoding failed:',
                                            xhr.status,
                                            xhr.statusText
                                        );
                                        // Fallback to generic current location text
                                        $input.val(
                                            houzez_vars.current_location
                                        );

                                        // Still update coordinates even if reverse geocoding fails
                                        $parent
                                            .find('input[name="lat"]')
                                            .val(position.coords.latitude);
                                        $parent
                                            .find('input[name="lng"]')
                                            .val(position.coords.longitude);
                                        $parent
                                            .find('input[name="ne_lat"]')
                                            .val('');
                                        $parent
                                            .find('input[name="ne_lng"]')
                                            .val('');
                                        $parent
                                            .find('input[name="sw_lat"]')
                                            .val('');
                                        $parent
                                            .find('input[name="sw_lng"]')
                                            .val('');
                                        $parent
                                            .find('input[name="zoom"]')
                                            .val('');
                                        $parent
                                            .find('input[name="use_radius"]')
                                            .prop('checked', true);

                                        if (is_halfmap) {
                                            houzez_search_on_change($this);
                                        } else {
                                            $this
                                                .find('.icon-location-target')
                                                .removeClass('icon-spin');
                                        }
                                    })
                                    .always(function () {
                                        locationRequestsInProgress.delete(
                                            fieldId
                                        );
                                    });
                            },
                            function (error) {
                                console.warn('Geolocation failed:', error);
                                $this
                                    .find('.icon-location-target')
                                    .removeClass('icon-spin');
                                locationRequestsInProgress.delete(fieldId);
                            }
                        );
                    } else {
                        $this
                            .find('.icon-location-target')
                            .removeClass('icon-spin');
                        locationRequestsInProgress.delete(fieldId);
                    }
                });
            }
        };

        /**
         * Initialize toggle for fullscreen map view
         */
        const initializeFullScreenToggle = function () {
            $('#houzez-gmap-full-osm').on('click', function () {
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
                houzezMap.invalidateSize();
                houzezMap.panTo(houzezMap.getCenter());
            });
        };

        /**
         * Initialize toggle between map and list views
         */
        const initializeMapViewToggles = function () {
            $('#houzez-btn-map-view').on('click', function (e) {
                e.preventDefault();
                $('#half-map-listing-area, .listing-wrap').hide();
                $('#map-view-wrap').show();
                $('#mobile-search-form').addClass(
                    'hz-mobile-overlay-search-js'
                );
                houzezMap.invalidateSize();
                houzezMap.panTo(houzezMap.getCenter());
                var mBounds = getMapBounds(houzez_map_properties);

                if (1 < mBounds.length) {
                    houzezMap.fitBounds(mBounds);
                }
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

        /**
         * Initialize the map for properties display
         */
        const initializeMap = function () {
            if ($('#houzez-properties-map').length > 0) {
                // Get map data from data attributes
                let mapElement = $('#houzez-properties-map');
                let mapDataJSON = mapElement.data('map');
                let mapOptionsJSON = mapElement.data('options');
                let mapData, mapOptions;

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
                    marker_spiderfier = mapOptions.marker_spiderfier;
                    markerPricePins = mapOptions.markerPricePins;
                    mapType = mapOptions.map_type;
                    googlemap_style = mapOptions.googlemap_style;
                    default_lat = parseFloat(mapOptions.default_lat);
                    default_lng = parseFloat(mapOptions.default_lng);
                    default_zoom = parseFloat(mapOptions.default_zoom);
                    max_zoom = parseFloat(mapOptions.max_zoom);

                    if (
                        searchLat &&
                        searchLng &&
                        !isNaN(parseFloat(searchLat)) &&
                        !isNaN(parseFloat(searchLng))
                    ) {
                        default_lat = parseFloat(searchLat);
                        default_lng = parseFloat(searchLng);
                    }

                    // Override default coordinates with taxonomy coordinates if available
                    if (mapOptions.center_lat && mapOptions.center_lng) {
                        default_lat = parseFloat(mapOptions.center_lat);
                        default_lng = parseFloat(mapOptions.center_lng);
                    }
                }

                var tileLayer = houzez.OSMUtils.getTileLayer();

                if (mapData.properties && mapData.properties.length > 0) {
                    // Store map properties data for use in other functions
                    houzez_map_properties = mapData.properties;

                    var mapBounds = getMapBounds(mapData.properties);
                    // Basic map
                    var mapCenter = L.latLng(default_lat, default_lng);

                    var mapDragging = true;
                    var mapConfig = {
                        dragging: mapDragging,
                        center: mapCenter,
                        zoom: default_zoom,
                        maxZoom: max_zoom,
                        tap: false,
                        zoomControl: false,
                        gestureHandling: true,
                        // Better mobile handling
                        touchZoom: true,
                        bounceAtZoomLimits: false,
                    };

                    houzezMap = L.map('houzez-properties-map', mapConfig);

                    houzezMap.scrollWheelZoom.disable();

                    if (1 < mapBounds.length) {
                        isUpdatingMapProgrammatically = true;
                        houzezMap.fitBounds(mapBounds);
                        setTimeout(() => {
                            isUpdatingMapProgrammatically = false;
                        }, 100);
                    }

                    houzezMap.addLayer(tileLayer);

                    // Initialize zoom controls if present
                    if (document.getElementById('listing-mapzoomin')) {
                        houzez_map_zoomin(houzezMap);
                    }
                    if (document.getElementById('listing-mapzoomout')) {
                        houzez_map_zoomout(houzezMap);
                    }

                    // Add markers to the map
                    houzezAddMarkers(mapData.properties, houzezMap);

                    $('.houzez-map-loading').hide();

                    let total_results =
                        $('#total-results').data('total-results');

                    // Show initial map message
                    if (total_results > 0) {
                        showMapMessage(
                            mapData.properties.length,
                            total_results
                        );
                    }

                    // Only initialize infobox trigger if properties exist and not on mobile
                    if (!houzez.OSMUtils.getIsMobile()) {
                        setupInfoboxPopupTrigger();
                    }
                } else {
                    // Fallback for no properties
                    houzez_map_properties = []; // Initialize as empty array

                    var fallbackMapOptions = {
                        center: [default_lat, default_lng],
                        zoom: default_zoom,
                        tap: false,
                        touchZoom: true,
                        gestureHandling: true,
                        bounceAtZoomLimits: false,
                    };

                    houzezMap = L.map(
                        'houzez-properties-map',
                        fallbackMapOptions
                    );
                    houzezMap.addLayer(tileLayer);
                    houzezMap.scrollWheelZoom.disable();

                    $('.houzez-map-loading').hide();
                }

                // Setup navigation buttons
                $('#houzez-gmap-next').on('click', function () {
                    houzez_map_next(houzezMap);
                });

                $('#houzez-gmap-prev').on('click', function () {
                    houzez_map_prev(houzezMap);
                });

                // Setup map view/list view toggle
                initializeMapViewToggles();
                initializeFullScreenToggle();

                if (auto_load_map_listings && is_halfmap) {
                    initPropertiesInViewport();
                }
            }
        };

        /**
         * Initialize the OSMMap module
         */
        const init = function () {
            initializeMap();
            initializeMapSearchHandlers();
            initLocationAutocomplete();
            triggerPriceRangeSearch();
            triggerPriceRangeSearchMobile();
            triggerDistanceRangeSearch();
        };

        // Public API
        return {
            init: init,
            reloadMarkers: reloadOSMMarkers,
            addMarkers: houzezAddMarkers,
            getMapBounds: getMapBounds,
            mapBounds: houzez_map_bounds,
            halfMapListings: houzez_half_map_listings,
            searchOnChange: houzez_search_on_change,
            setUrl: houzez_set_url,
            setupInfoboxTrigger: setupInfoboxPopupTrigger,
            triggerPriceRangeSearch: triggerPriceRangeSearch,
            triggerPriceRangeSearchMobile: triggerPriceRangeSearchMobile,
            triggerDistanceRangeSearch: triggerDistanceRangeSearch,
        };
    })();

    /**
     * SingleAgentOSM Module
     * Handles OpenStreetMap functionality for single agent page
     */
    houzez.SingleAgentOSM = (function () {
        let houzezMap;
        let mapBounds;
        let streetCount = 0;
        let mapZoom = 15;
        let panorama = null;
        let propertyMarker;

        /**
         * Initialize the map
         */
        const init = function () {
            if ($('#houzez-agent-sidebar-map').length <= 0) {
                return;
            }

            const agent_lat = $('#houzez-agent-sidebar-map').data('lat');
            const agent_lng = $('#houzez-agent-sidebar-map').data('lng');

            if (agent_lat != '' && agent_lng != '') {
                const tileLayer = houzez.OSMUtils.getTileLayer();
                const mapCenter = L.latLng(agent_lat, agent_lng);

                const mapOptions = {
                    dragging: !L.Browser.mobile,
                    center: mapCenter,
                    zoom: mapZoom,
                    zoomControl: true,
                    tap: false,
                    touchZoom: true,
                    gestureHandling: true,
                    bounceAtZoomLimits: false,
                };

                houzezMap = L.map('houzez-agent-sidebar-map', mapOptions);
                houzezMap.scrollWheelZoom.disable();
                houzezMap.addLayer(tileLayer);
            }
        };

        return {
            init: init,
        };
    })();

    /**
     * SinglePropertyOverviewOSM Module
     * Handles OpenStreetMap functionality for single property overview page
     */
    houzez.SinglePropertyOverviewOSM = (function () {
        let houzezMap;
        let mapZoom = 15;
        let showCircle = false;
        let map_pin_type = 'marker';
        let markerPricePins = 'no';
        let propertyMarker;

        /**
         * Initialize the map
         */
        const init = function () {
            if ($('#houzez-overview-listing-map').length <= 0) {
                return;
            }

            const mapElement = $('#houzez-overview-listing-map');
            const mapDataJSON = mapElement.data('map');
            const mapOptionsJSON = mapElement.data('options');

            if (!mapDataJSON) {
                return;
            }

            let houzez_single_property_map;
            let houzez_map_options;

            try {
                // Parse map data
                if (typeof mapDataJSON === 'object') {
                    houzez_single_property_map = mapDataJSON;
                } else {
                    houzez_single_property_map = JSON.parse(mapDataJSON);
                }

                // Parse map options
                if (typeof mapOptionsJSON === 'object') {
                    houzez_map_options = mapOptionsJSON;
                } else if (mapOptionsJSON) {
                    houzez_map_options = JSON.parse(mapOptionsJSON);
                }
            } catch (e) {
                console.error('Error parsing map data:', e);
                return;
            }

            // Initialize map options from the data
            if (houzez_map_options) {
                markerPricePins = houzez_map_options.markerPricePins;
                map_pin_type = houzez_map_options.map_pin_type;

                if (map_pin_type == 'circle') {
                    showCircle = true;
                }

                if (houzez_map_options.single_map_zoom > 0) {
                    mapZoom = parseInt(houzez_map_options.single_map_zoom);
                }
            }

            if (
                !houzez_single_property_map.latitude ||
                !houzez_single_property_map.longitude
            ) {
                console.error('Property coordinates not provided');
                return;
            }

            const tileLayer = houzez.OSMUtils.getTileLayer();
            const mapCenter = L.latLng(
                houzez_single_property_map.latitude,
                houzez_single_property_map.longitude
            );

            const mapOptions = {
                dragging: !L.Browser.mobile,
                center: mapCenter,
                zoom: mapZoom,
                zoomControl: true,
                tap: false,
                touchZoom: true,
                gestureHandling: true,
                bounceAtZoomLimits: false,
            };

            houzezMap = L.map('houzez-overview-listing-map', mapOptions);
            houzezMap.scrollWheelZoom.disable();
            houzezMap.addLayer(tileLayer);

            // Create property marker
            if (showCircle) {
                // Add circle to represent property area
                L.circle(mapCenter, 200, {
                    color: '#4f5962',
                    fillColor: '#4f5962',
                    fillOpacity: 0.35,
                    weight: 2,
                    opacity: 0.8,
                }).addTo(houzezMap);
            } else {
                // Check if price pins are enabled
                if (
                    markerPricePins === 'yes' &&
                    houzez_single_property_map.pricePin
                ) {
                    // Create price pin
                    let marker_color = houzez_single_property_map.marker_color;
                    var pricePin =
                        '<div data-id="' +
                        (houzez_single_property_map.property_id ||
                            houzez_single_property_map.post_id) +
                        '" class="osm-marker map-marker-label"' +
                        (marker_color
                            ? ' style="background-color: ' +
                              marker_color +
                              '; border-color: ' +
                              marker_color +
                              '; color: #ffffff;"'
                            : '') +
                        '>' +
                        houzez_single_property_map.pricePin +
                        '</div>';

                    var myIcon = L.divIcon({
                        className: 'someclass',
                        iconSize: new L.Point(0, 0),
                        html: pricePin,
                    });

                    propertyMarker = L.marker(mapCenter, {
                        icon: myIcon,
                        riseOnHover: true,
                    }).addTo(houzezMap);
                } else {
                    // Standard marker with icon
                    const markerOptions = {
                        riseOnHover: true,
                    };

                    if (houzez_single_property_map.title) {
                        markerOptions.title = houzez_single_property_map.title;
                    }

                    // Marker icon
                    if (houzez_single_property_map.marker) {
                        const iconOptions = {
                            iconUrl: houzez_single_property_map.marker,
                            iconSize: [44, 56],
                            iconAnchor: [20, 57],
                            popupAnchor: [1, -57],
                        };

                        if (houzez_single_property_map.retinaMarker) {
                            iconOptions.iconRetinaUrl =
                                houzez_single_property_map.retinaMarker;
                        }

                        markerOptions.icon = L.icon(iconOptions);
                    }

                    propertyMarker = L.marker(mapCenter, markerOptions).addTo(
                        houzezMap
                    );
                }
            }

            // Resize map when tabs are shown to ensure proper rendering
            $(
                '.map-media-tab, a[href="#pills-map"], a[href="#property-address"]'
            ).on('shown.bs.tab', function () {
                setTimeout(() => {
                    houzezMap.invalidateSize();
                    houzezMap.panTo(houzezMap.getCenter());
                }, 50);
            });
        };

        return {
            init: init,
        };
    })();

    /**
     * SinglePropertyOSM Module
     * Handles OpenStreetMap functionality for single property detail page
     */
    houzez.SinglePropertyOSM = (function () {
        // Private variables
        let houzezMap;
        let mapBounds;
        let streetCount = 0;
        let mapZoom = 15;
        let panorama = null;
        let showCircle = false;
        let map_pin_type = 'marker';
        let markerPricePins = 'no';
        let propertyMarker;
        let propertyLatLng;
        const mapInstances = {}; // Store all map instances by container ID

        // Configuration object
        const config = {
            mapContainerId: 'houzez-single-listing-map',
            mapLoaderSelector: '.houzez-map-loading',
            streetViewTabSelector: 'a[href="#pills-street-view"]',
            propertyAddressTabSelector:
                'a[href="#pills-map"], a[href="#property-address"]',
        };

        // Private helper functions
        const _initializeMapOptions = function (singleMapData, mapOptionsData) {
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

            if (mapOptionsData) {
                markerPricePins = mapOptionsData.markerPricePins || 'no';
                map_pin_type = mapOptionsData.map_pin_type || 'marker';

                if (map_pin_type === 'circle') {
                    showCircle = true;
                }

                if (mapOptionsData.single_map_zoom > 0) {
                    mapZoom = parseInt(mapOptionsData.single_map_zoom);
                }
            }

            propertyLatLng = L.latLng(
                singleMapData.latitude,
                singleMapData.longitude
            );
            return true; // Initialization successful
        };

        const _createMap = function () {
            const mapContainer = document.getElementById(config.mapContainerId);
            if (!mapContainer) {
                console.error(
                    'Map container element not found:',
                    config.mapContainerId
                );
                return null;
            }

            const mapOptions = {
                dragging: !L.Browser.mobile,
                center: propertyLatLng,
                zoom: mapZoom,
                zoomControl: true,
                tap: false,
                touchZoom: true,
                gestureHandling: true,
                bounceAtZoomLimits: false,
            };

            houzezMap = L.map(config.mapContainerId, mapOptions);
            houzezMap.scrollWheelZoom.disable();

            const tileLayer = houzez.OSMUtils.getTileLayer();
            houzezMap.addLayer(tileLayer);

            // Store the map instance
            mapInstances[config.mapContainerId] = houzezMap;

            return houzezMap;
        };

        const _addMarkerOrCircle = function (singleMapData) {
            if (!houzezMap) return;

            if (showCircle) {
                _addCircle();
            } else {
                _addMarker(singleMapData);
            }
        };

        const _addCircle = function () {
            L.circle(propertyLatLng, 200, {
                color: '#4f5962',
                fillColor: '#4f5962',
                fillOpacity: 0.35,
                weight: 2,
                opacity: 0.8,
            }).addTo(houzezMap);
        };

        const _addMarker = function (singleMapData) {
            // Check if price pins are enabled
            if (markerPricePins === 'yes' && singleMapData.pricePin) {
                // Create price pin
                let marker_color = singleMapData.marker_color;
                var pricePin =
                    '<div data-id="' +
                    (singleMapData.property_id || singleMapData.post_id) +
                    '" class="osm-marker map-marker-label"' +
                    (marker_color
                        ? ' style="background-color: ' +
                          marker_color +
                          '; border-color: ' +
                          marker_color +
                          '; color: #ffffff;"'
                        : '') +
                    '>' +
                    singleMapData.pricePin +
                    '</div>';

                var myIcon = L.divIcon({
                    className: 'someclass',
                    iconSize: new L.Point(0, 0),
                    html: pricePin,
                });

                propertyMarker = L.marker(propertyLatLng, {
                    icon: myIcon,
                    riseOnHover: true,
                }).addTo(houzezMap);
            } else {
                // Standard marker with icon
                const markerOptions = {
                    riseOnHover: true,
                };

                if (singleMapData.title) {
                    markerOptions.title = singleMapData.title;
                }

                // Marker icon
                if (singleMapData.marker) {
                    const iconOptions = {
                        iconUrl: singleMapData.marker,
                        iconSize: [44, 56],
                        iconAnchor: [20, 57],
                        popupAnchor: [1, -57],
                    };
                    if (singleMapData.retinaMarker) {
                        iconOptions.iconRetinaUrl = singleMapData.retinaMarker;
                    }
                    markerOptions.icon = L.icon(iconOptions);
                }

                propertyMarker = L.marker(propertyLatLng, markerOptions).addTo(
                    houzezMap
                );
            }
        };

        const _setupTabListeners = function () {
            // Handle tab changes for map resize
            $('.map-media-tab, ' + config.propertyAddressTabSelector).on(
                'shown.bs.tab',
                function () {
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
                                mapInstance.invalidateSize();
                                mapInstance.panTo(mapInstance.getCenter());
                            }, 50);
                        }
                    } else {
                        // If we can't find specific container, resize all maps
                        Object.values(mapInstances).forEach((mapInstance) => {
                            if (mapInstance) {
                                setTimeout(() => {
                                    mapInstance.invalidateSize();
                                    mapInstance.panTo(mapInstance.getCenter());
                                }, 50);
                            }
                        });
                    }
                }
            );
        };

        const _removeMapLoader = function () {
            $(config.mapLoaderSelector).hide();
        };

        /**
         * Initialize the map
         */
        const init = function () {
            // Check for multiple possible map container IDs
            const mapIds = [
                config.mapContainerId,
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

                let singleMapData, mapOptionsData;

                if (mapDataAttr && mapOptionsAttr) {
                    try {
                        singleMapData = JSON.parse(mapDataAttr);
                        mapOptionsData = JSON.parse(mapOptionsAttr);
                    } catch (e) {
                        console.error(
                            `Error parsing map data attributes for ${currentMapId}:`,
                            e
                        );
                        _removeMapLoader();
                        return;
                    }
                } else if (
                    typeof houzez_single_property_map !== 'undefined' &&
                    typeof houzez_map_options !== 'undefined'
                ) {
                    // Fallback to global variables for backward compatibility
                    singleMapData = {
                        latitude: houzez_single_property_map.lat,
                        longitude: houzez_single_property_map.lng,
                        title: houzez_single_property_map.title,
                        marker: houzez_single_property_map.marker,
                        retinaMarker: houzez_single_property_map.retinaMarker,
                        pricePin: houzez_single_property_map.pricePin,
                        term_id: houzez_single_property_map.term_id,
                        post_id: houzez_single_property_map.property_id,
                    };
                    mapOptionsData = houzez_map_options;
                } else {
                    console.log(
                        `Map container ${currentMapId} found, but missing data attributes and global variables.`
                    );
                    _removeMapLoader();
                    return;
                }

                // Update config with current map ID
                config.mapContainerId = currentMapId;

                // Initialize options and check if map should be shown
                if (!_initializeMapOptions(singleMapData, mapOptionsData)) {
                    _removeMapLoader();
                    return;
                }

                // Create the map
                houzezMap = _createMap();
                if (!houzezMap) {
                    _removeMapLoader();
                    return;
                }

                // Add marker or circle
                _addMarkerOrCircle(singleMapData);

                // Setup tab listeners
                _setupTabListeners();

                // Remove loader
                _removeMapLoader();
            });
        };

        return {
            init: init,
            config: config,
            getMapInstances: () => mapInstances,
            getMapInstance: (id) => mapInstances[id],
            resizeAllMaps: () => {
                Object.values(mapInstances).forEach((mapInstance) => {
                    if (mapInstance && mapInstance.invalidateSize) {
                        mapInstance.invalidateSize();
                        mapInstance.panTo(mapInstance.getCenter());
                    }
                });
            },
        };
    })();

    // Initialize when document is ready
    $(document).ready(function () {
        // Initialize the OSMUtils module with the configuration
        houzez.OSMUtils.init();

        // Initialize OSMMap if we have a properties map
        if (
            $('#houzez-properties-map').length > 0 ||
            $('input[name="search_location"]').length > 0
        ) {
            houzez.OSMMap.init();
        }

        // Initialize SingleAgentMap if we have an agent map
        if ($('#houzez-agent-sidebar-map').length > 0) {
            houzez.SingleAgentOSM.init();
        }

        // Initialize SinglePropertyOverviewMap if we have an overview map
        if ($('#houzez-overview-listing-map').length > 0) {
            houzez.SinglePropertyOverviewOSM.init();
        }

        // Initialize SinglePropertyOSM if we have a single property map
        if (
            $('#houzez-single-listing-map').length > 0 ||
            $('#houzez-single-listing-map-address').length > 0 ||
            $('#houzez-single-listing-map-elementor').length > 0
        ) {
            houzez.SinglePropertyOSM.init();
        }
    });

    // Make houzez object available globally
    window.houzez = houzez;
})(jQuery);
