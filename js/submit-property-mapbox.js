/**
 * Mapbox map for submit property
 */
jQuery(document).ready(function ($) {
    'use strict';

    var geo_country_limit = houzez_vars.geo_country_limit;
    var geocomplete_country = houzez_vars.geocomplete_country;
    var is_edit_property = houzez_vars.is_edit_property;
    var api_mapbox = houzez_vars.api_mapbox;
    var mapLanguage = houzez_vars.mapboxLocaleShort;
    var map;
    var marker;

    if (!mapboxgl) {
        console.error(
            'Mapbox GL JS is not loaded. Please check your configuration.'
        );
        return;
    }

    // Set Mapbox access token
    mapboxgl.accessToken = api_mapbox;

    if (document.getElementById('geocomplete')) {
        var inputField, mapDiv, maplat, maplong;
        inputField = document.getElementById('geocomplete');
        mapDiv = $('#map_canvas');
        maplat = mapDiv.data('add-lat');
        maplong = mapDiv.data('add-long');

        // Set default coordinates if none provided
        if (maplat === '' || typeof maplat === 'undefined') {
            maplat = 25.68654;
        }

        if (maplong === '' || typeof maplong === 'undefined') {
            maplong = -80.431345;
        }

        maplat = parseFloat(maplat);
        maplong = parseFloat(maplong);

        // Initialize Mapbox map
        map = new mapboxgl.Map({
            container: 'map_canvas',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [maplong, maplat],
            zoom: 13,
        });

        const language = new MapboxLanguage({
            defaultLanguage: mapLanguage,
        });
        map.addControl(language);

        // Add navigation controls
        map.addControl(new mapboxgl.NavigationControl(), 'top-right');

        // Add the marker
        var markerElement = document.createElement('div');
        markerElement.className = 'mapbox-marker';
        markerElement.style.backgroundImage =
            'url(https://docs.mapbox.com/mapbox-gl-js/assets/pin.svg)';
        markerElement.style.width = '30px';
        markerElement.style.height = '40px';
        markerElement.style.backgroundSize = 'contain';
        markerElement.style.backgroundRepeat = 'no-repeat';
        markerElement.style.cursor = 'pointer';

        marker = new mapboxgl.Marker({
            element: markerElement,
            draggable: true,
        })
            .setLngLat([maplong, maplat])
            .addTo(map);

        if (is_edit_property) {
            map.setZoom(16);
        } else {
            map.setZoom(13);
        }

        // Set up marker drag functionality
        marker.on('dragend', function () {
            var lngLat = marker.getLngLat();
            $('#latitude').val(lngLat.lat);
            $('#longitude').val(lngLat.lng);
            geocodePosition(lngLat);
        });

        // Set up Mapbox geocoder
        setupAddressAutocomplete();

        // Function to set up the address autocomplete
        function setupAddressAutocomplete() {
            var $inputField = $(inputField);
            var $inputParent = $inputField.parent();

            // Create suggestions container
            var $suggestions = $(
                '<div class="mapbox-suggestions" style="position:absolute;z-index:1000;background:white;width:100%;border:1px solid #ccc;max-height:200px;overflow-y:auto;display:none;"></div>'
            );
            $inputParent.css('position', 'relative');
            $inputParent.append($suggestions);
            // Set country restrictions if enabled
            var countryRestrictions = '';
            if (geo_country_limit != 0 && geocomplete_country != '') {
                // Handle special case for UAE
                if (geocomplete_country == 'UAE') {
                    geocomplete_country = 'AE';
                }
                countryRestrictions = geocomplete_country.toLowerCase();
            }

            // Add input event to search locations
            $inputField.on('input', function () {
                var query = $(this).val();
                if (query.length < 3) {
                    $suggestions.hide();
                    return;
                }

                // Call Mapbox Geocoding API
                $.ajax({
                    url:
                        'https://api.mapbox.com/geocoding/v5/mapbox.places/' +
                        encodeURIComponent(query) +
                        '.json',
                    data: {
                        access_token: mapboxgl.accessToken,
                        autocomplete: true,
                        language: houzez_vars.mapboxLocale,
                        types: 'address,poi,place',
                        country: countryRestrictions,
                    },
                    success: function (data) {
                        $suggestions.empty();

                        if (data.features && data.features.length > 0) {
                            data.features.forEach(function (feature) {
                                var $item = $(
                                    '<div class="suggestion-item" style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #eee;"></div>'
                                );
                                $item.text(feature.place_name);
                                $item.data('feature', feature);
                                $item.on('click', function () {
                                    // Set input value
                                    $inputField.val(feature.place_name);

                                    // Update coordinates
                                    $('#latitude').val(feature.center[1]);
                                    $('#longitude').val(feature.center[0]);

                                    // Update marker position
                                    marker.setLngLat(feature.center);

                                    // Fit map to the selected location
                                    if (feature.bbox) {
                                        map.fitBounds(
                                            [
                                                [
                                                    feature.bbox[0],
                                                    feature.bbox[1],
                                                ],
                                                [
                                                    feature.bbox[2],
                                                    feature.bbox[3],
                                                ],
                                            ],
                                            { padding: 50 }
                                        );
                                    } else {
                                        map.flyTo({
                                            center: feature.center,
                                            zoom: 16,
                                        });
                                    }

                                    // Fill address fields
                                    fillInAddressFields(feature);

                                    // Hide suggestions
                                    $suggestions.hide();
                                });
                                $suggestions.append($item);
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide();
                        }
                    },
                });
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function (e) {
                if (
                    !$(e.target).closest('.mapbox-suggestions').length &&
                    !$(e.target).is($inputField)
                ) {
                    $suggestions.hide();
                }
            });
        }

        // Fill in address fields based on Mapbox response
        function fillInAddressFields(feature) {
            // Reset form fields
            $('#city').val('');
            $('#countyState').val('');
            $('#zip').val('');
            $('#neighborhood').val('');
            $('#country').val('');

            // Optional: refresh select elements if using selectpicker
            // $('#city, #countyState, #neighborhood, #country').selectpicker('refresh');

            if (!feature.context) return;

            // Parse context information
            feature.context.forEach(function (context) {
                var id = context.id.split('.')[0];
                var text = context.text;

                console.log(id + ' = ' + text);

                if (id === 'place') {
                    // Mapbox uses 'place' for city/locality
                    $('#city').val(text);
                } else if (id === 'region') {
                    // Mapbox uses 'region' for state/administrative area
                    $('#countyState').val(text);
                } else if (id === 'country') {
                    $('#country').val(text);
                } else if (id === 'postcode') {
                    $('#zip').val(text);
                } else if (id === 'neighborhood') {
                    // Mapbox uses 'neighborhood'
                    $('#neighborhood').val(text);
                } else if (id === 'district') {
                    // Sometimes district might be relevant for neighborhood
                    if ($('#neighborhood').val() === '') {
                        // Only fill if neighborhood isn't already set
                        $('#neighborhood').val(text);
                    }
                }
            });

            // Fallback: If city is still empty, try the main feature name, especially if feature_type is 'place'
            if (
                $('#city').val() === '' &&
                feature.properties &&
                feature.properties.name &&
                feature.properties.feature_type === 'place'
            ) {
                $('#city').val(feature.properties.name);
                console.log(
                    'Fallback: Used feature.properties.name for city: ' +
                        feature.properties.name
                );
            }

            // Also check the main feature properties for potential address parts
            // Sometimes the postcode might be in properties.address
            if (
                $('#zip').val() === '' &&
                feature.properties &&
                feature.properties.address
            ) {
                // Attempt to extract postcode if missing - this is less reliable
                const postcodeMatch =
                    feature.properties.address.match(/\d{5}(-\d{4})?$/);
                if (postcodeMatch) {
                    $('#zip').val(postcodeMatch[0]);
                }
            }
            // Ensure city is filled if possible from main feature properties
            if (
                $('#city').val() === '' &&
                feature.properties &&
                feature.properties.place_name
            ) {
                // Less ideal, but might capture city from place_name if context missed it
                // This part needs careful consideration based on typical responses
            }

            // Optional: refresh select elements again after setting values
            // $('#city, #countyState, #neighborhood, #country').selectpicker('refresh');
        }

        // Geocode position from lat/lng
        function geocodePosition(lngLat) {
            $.ajax({
                url:
                    'https://api.mapbox.com/geocoding/v5/mapbox.places/' +
                    lngLat.lng +
                    ',' +
                    lngLat.lat +
                    '.json',
                data: {
                    access_token: mapboxgl.accessToken,
                    types: 'address',
                },
                success: function (data) {
                    if (data.features && data.features.length > 0) {
                        // Update the address field
                        $('#geocomplete').val(data.features[0].place_name);

                        // Fill the address fields
                        fillInAddressFields(data.features[0]);
                    }
                },
            });
        }

        // Find coordinates button handler
        $('#find_coordinates').on('click', function (e) {
            e.preventDefault();

            var address = $('#geocomplete').val();
            var city = $('#city').val();
            var state = $('#countyState').val() || '';
            var country = $('#country').val() || '';

            var full_addr = address;
            if (city) full_addr += ', ' + city;
            if (state) full_addr += ', ' + state;
            if (country) full_addr += ', ' + country;

            // Geocode the address
            $.ajax({
                url:
                    'https://api.mapbox.com/geocoding/v5/mapbox.places/' +
                    encodeURIComponent(full_addr) +
                    '.json',
                data: {
                    access_token: mapboxgl.accessToken,
                    limit: 1,
                },
                success: function (data) {
                    if (data.features && data.features.length > 0) {
                        var feature = data.features[0];

                        // Update coordinates
                        $('#latitude').val(feature.center[1]);
                        $('#longitude').val(feature.center[0]);

                        // Update marker position
                        marker.setLngLat(feature.center);

                        // Fly to the location
                        map.flyTo({
                            center: feature.center,
                            zoom: 16,
                        });
                    }
                },
            });
        });
    }
});
