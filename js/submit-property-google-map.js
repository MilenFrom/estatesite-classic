/**
 * Google map for submit property
 */
jQuery(document).ready(function ($) {
    'use strict';

    // Inject styles into shadow DOM to match .form-control
    function injectAutocompleteStyles(element) {
        var css = '[class*="search-icon"], [class*="clear-icon"] { display: none !important; } ' +
            'input { border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; height: 100% !important; width: 100% !important; font-size: inherit !important; font-weight: 400 !important; font-family: inherit !important; padding: 0.375rem 0.75rem !important; color: #212529 !important; } ' +
            'input::placeholder { color: #6c757d !important; opacity: 1 !important; } ' +
            ':host { --mdc-shape-small: 0 !important; --mdc-outlined-text-field-outline-width: 0 !important; --mdc-outlined-text-field-focus-outline-width: 0 !important; } ' +
            '.mdc-notched-outline, .mdc-notched-outline__leading, .mdc-notched-outline__notch, .mdc-notched-outline__trailing, ' +
            '.mdc-text-field--outlined .mdc-notched-outline .mdc-notched-outline__leading, ' +
            '.mdc-text-field--outlined .mdc-notched-outline .mdc-notched-outline__trailing { border: none !important; } ' +
            '.focus-ring { display: none !important; }';
        function applyStyles() {
            if (element.shadowRoot) {
                var style = document.createElement('style');
                style.textContent = css;
                element.shadowRoot.appendChild(style);
            }
        }
        if (element.shadowRoot) {
            applyStyles();
        } else {
            var observer = new MutationObserver(function() {
                if (element.shadowRoot) {
                    applyStyles();
                    observer.disconnect();
                }
            });
            observer.observe(element, { childList: true, subtree: true });
            setTimeout(applyStyles, 500);
        }
    }

    async function initializeGoogleMap() {
        var geo_country_limit = houzez_vars.geo_country_limit;
        var geocomplete_country = houzez_vars.geocomplete_country;
        var is_edit_property = houzez_vars.is_edit_property;
        var map;
        var marker;

        var componentForm_listing = {
            locality: 'long_name',
            administrative_area_level_1: 'long_name',
            country: 'long_name',
            postal_code: 'short_name',
            neighborhood: 'long_name',
            sublocality_level_1: 'long_name',
            political: 'long_name',
        };

        var hasNewPlacesAPI = false;
        try {
            var placesLib = await google.maps.importLibrary('places');
            hasNewPlacesAPI = (typeof placesLib.PlaceAutocompleteElement === 'function');
        } catch (e) {
            hasNewPlacesAPI = (typeof google.maps.places.PlaceAutocompleteElement === 'function');
        }

        if (document.getElementById('geocomplete')) {
            var inputField, defaultBounds, autocomplete;
            inputField = document.getElementById('geocomplete');
            defaultBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(-90, -180),
                new google.maps.LatLng(90, 180)
            );

            var options = {
                bounds: defaultBounds,
                types: ['geocode', 'establishment'],
            };

            var mapDiv = $('#map_canvas');
            var maplat = mapDiv.data('add-lat');
            var maplong = mapDiv.data('add-long');

            if (maplat === '' || typeof maplat === 'undefined') {
                maplat = 25.68654;
            }

            if (maplong === '' || typeof maplong === 'undefined') {
                maplong = -80.431345;
            }

            maplat = parseFloat(maplat);
            maplong = parseFloat(maplong);

            map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: { lat: maplat, lng: maplong },
                zoom: 13,
                streetViewControl: 0,
                mapTypeId: window.google.maps.MapTypeId.ROADMAP,
            });

            if (is_edit_property) {
                var latlng = {
                    lat: parseFloat(maplat),
                    lng: parseFloat(maplong),
                };
                marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable: true,
                });
                map.setZoom(16);
            } else {
                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    position: new google.maps.LatLng(maplat, maplong),
                    anchorPoint: new google.maps.Point(0, -29),
                    visible: true,
                });
                map.setZoom(13);
            }

            try {
                var geocoder = new google.maps.Geocoder();

                // Drag marker listener
                window.google.maps.event.addListener(
                    marker,
                    'drag',
                    function (marker) {
                        var latLng = marker.latLng;
                        $('#latitude').val(latLng.lat());
                        $('#longitude').val(latLng.lng());
                    }
                );

                if (hasNewPlacesAPI) {
                    // --- NEW API: PlaceAutocompleteElement ---
                    var acOptions = {};
                    if (geo_country_limit != 0 && geocomplete_country != '') {
                        var country = geocomplete_country;
                        if (country == 'UAE') { country = 'AE'; }
                        acOptions.includedRegionCodes = [country.toLowerCase()];
                    }
                    acOptions.locationBias = map.getBounds();

                    var placeAutocomplete = new google.maps.places.PlaceAutocompleteElement(acOptions);

                    // Mount: hide original input, insert element
                    var wrapper = document.createElement('div');
                    wrapper.className = 'hz-place-autocomplete-wrap';
                    var sizeMatch = inputField.className.match(/elementor-size-\w+/);
                    if (sizeMatch) {
                        wrapper.classList.add(sizeMatch[0]);
                    }
                    inputField.parentNode.insertBefore(wrapper, inputField);
                    wrapper.appendChild(placeAutocomplete);
                    // Copy border styles from original input before hiding it
                    var inputBorder = window.getComputedStyle(inputField);
                    placeAutocomplete.style.borderStyle = inputBorder.borderStyle;
                    placeAutocomplete.style.borderWidth = inputBorder.borderWidth;
                    placeAutocomplete.style.borderColor = inputBorder.borderColor;
                    placeAutocomplete.style.borderRadius = inputBorder.borderRadius;
                    inputField.type = 'hidden';
                    injectAutocompleteStyles(placeAutocomplete);

                    // Pre-populate
                    if (inputField.value) {
                        placeAutocomplete.setAttribute('placeholder', inputField.value);
                    } else if (inputField.placeholder) {
                        placeAutocomplete.setAttribute('placeholder', inputField.placeholder);
                    }

                    // Update bias when map moves
                    map.addListener('bounds_changed', function () {
                        placeAutocomplete.locationBias = map.getBounds();
                    });

                    // Place selection
                    placeAutocomplete.addEventListener('gmp-select', async function (event) {
                        var place = event.placePrediction.toPlace();
                        await place.fetchFields({
                            fields: ['location', 'viewport', 'formattedAddress',
                                     'addressComponents', 'adrFormatAddress', 'displayName']
                        });

                        // Sync address to hidden input
                        inputField.value = place.formattedAddress || place.displayName || '';

                        // Fill form fields (new API version)
                        fillInAddress_for_form_new(place);

                        marker.setVisible(false);

                        if (!place.location) {
                            window.alert("No details available for input: '" +
                                (place.displayName || '') + "'");
                            return;
                        }

                        if (place.viewport) {
                            map.fitBounds(place.viewport);
                        } else {
                            map.setCenter(place.location);
                            map.setZoom(17);
                        }

                        marker.setPosition(place.location);
                        marker.setVisible(true);
                    });
                } else {
                    // --- LEGACY API: Autocomplete (fallback) ---
                    autocomplete = new google.maps.places.Autocomplete(
                        inputField,
                        options
                    );

                    if (geo_country_limit != 0 && geocomplete_country != '') {
                        if (geocomplete_country == 'UAE') {
                            geocomplete_country = 'AE';
                        }
                        autocomplete.setComponentRestrictions({
                            country: [geocomplete_country],
                        });
                    }
                    autocomplete.bindTo('bounds', map);

                    google.maps.event.addListener(
                        autocomplete,
                        'place_changed',
                        function () {
                            var place = autocomplete.getPlace();
                            fillInAddress_for_form(place);
                            marker.setVisible(false);
                            if (!place.geometry) {
                                window.alert(
                                    "No details available for input: '" +
                                        place.name +
                                        "'"
                                );
                                return;
                            }
                            if (place.geometry.viewport) {
                                map.fitBounds(place.geometry.viewport);
                            } else {
                                map.setCenter(place.geometry.location);
                                map.setZoom(17);
                            }
                            marker.setPosition(place.geometry.location);
                            marker.setVisible(true);
                        }
                    );
                }

                jQuery('#find_coordinates').on('click', function (e) {
                    e.preventDefault();

                    var address = document.getElementById('geocomplete').value;
                    var city = jQuery('#city').val();

                    var full_addr = address + ',' + city;
                    if (document.getElementById('countyState')) {
                        var state =
                            document.getElementById('countyState').value;
                        if (state) {
                            full_addr = full_addr + ',' + state;
                        }
                    }

                    if (document.getElementById('country')) {
                        var country = document.getElementById('country').value;
                        if (country) {
                            full_addr = full_addr + ',' + country;
                        }
                    }

                    geocoder.geocode(
                        { address: full_addr },
                        function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                marker.setVisible(false);
                                map.setCenter(results[0].geometry.location);
                                marker.setPosition(
                                    results[0].geometry.location
                                );
                                marker.setVisible(true);

                                document.getElementById('latitude').value =
                                    results[0].geometry.location.lat();
                                document.getElementById('longitude').value =
                                    results[0].geometry.location.lng();
                            } else {
                                //alert(status);
                            }
                        }
                    );
                });
            } catch (error) {
                console.error(
                    'Error initializing Google Maps Autocomplete:',
                    error
                );
            }
        }

        function fillInAddress_for_form(place) {
            var i, has_city, addressType, val;

            has_city = 0;

            $('#city').val('');
            $('#countyState').val('');
            $('#zip').val('');
            $('#neighborhood').val('');
            $('#country').val('');

            $('#city, #countyState, #neighborhood, #country').selectpicker(
                'refresh'
            );

            document.getElementById('latitude').value =
                place.geometry.location.lat();
            document.getElementById('longitude').value =
                place.geometry.location.lng();

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (i = 0; i < place.address_components.length; i++) {
                addressType = place.address_components[i].types[0];
                val =
                    place.address_components[i][
                        componentForm_listing[addressType]
                    ];

                if (addressType === 'neighborhood') {
                    $('#neighborhood').val(val);
                } else if (addressType === 'locality') {
                    $('#city').val(val);
                    if (val !== '') {
                        has_city = 1;
                    }
                } else if (addressType === 'country') {
                    $('#country').val(val);
                } else if (addressType === 'postal_code') {
                    $('#zip').val(val);
                } else if (addressType === 'administrative_area_level_1') {
                    $('#countyState').val(val);
                }
            }

            $('#address-place').html(place.adr_address);

            if (has_city === 0) {
                get_new_city_2('city', place.adr_address);
            }
        }

        function fillInAddress_for_form_new(place) {
            var i, has_city, addressType, val;
            has_city = 0;

            $('#city').val('');
            $('#countyState').val('');
            $('#zip').val('');
            $('#neighborhood').val('');
            $('#country').val('');
            $('#city, #countyState, #neighborhood, #country').selectpicker(
                'refresh'
            );

            // New API: place.location instead of place.geometry.location
            document.getElementById('latitude').value = place.location.lat();
            document.getElementById('longitude').value = place.location.lng();

            // New API: place.addressComponents with .longText/.shortText
            if (place.addressComponents) {
                for (i = 0; i < place.addressComponents.length; i++) {
                    var component = place.addressComponents[i];
                    addressType = component.types[0];

                    if (addressType === 'neighborhood') {
                        $('#neighborhood').val(component.longText);
                    } else if (addressType === 'locality') {
                        val = component.longText;
                        $('#city').val(val);
                        if (val !== '') { has_city = 1; }
                    } else if (addressType === 'country') {
                        $('#country').val(component.longText);
                    } else if (addressType === 'postal_code') {
                        $('#zip').val(component.shortText);
                    } else if (addressType === 'administrative_area_level_1') {
                        $('#countyState').val(component.longText);
                    }
                }
            }

            // New API: place.adrFormatAddress instead of place.adr_address
            var adrAddress = place.adrFormatAddress || '';
            $('#address-place').html(adrAddress);

            if (has_city === 0 && adrAddress) {
                get_new_city_2('city', adrAddress);
            }
        }

        function get_new_city_2(stringplace, adr_address) {
            var new_city;
            new_city = $(adr_address).filter('span.locality').html();
            $('#' + stringplace).val(new_city);
        }
    }

    // Check if Google Maps API is loaded
    function waitForGoogleMaps() {
        if (
            typeof google !== 'undefined' &&
            typeof google.maps !== 'undefined' &&
            typeof google.maps.Map !== 'undefined'
        ) {
            // Check if Places library is available
            if (typeof google.maps.places !== 'undefined') {
                // Google Maps API is loaded, initialize the map
                initializeGoogleMap();
            } else {
                console.warn('Google Maps Places library not loaded');
                // Could attempt to load places library here or show fallback
            }
        } else {
            // Google Maps API is not loaded yet, wait and try again
            setTimeout(waitForGoogleMaps, 100);
        }
    }

    // Start waiting for Google Maps API to load
    waitForGoogleMaps();
});
