var editLog = {
    map: {
        bounds: new google.maps.LatLngBounds(),
        instance: false,
        markers: {rx: false, tx: false},
        polyline: false,
        homeIcon: {
            url: globals.assetsurl + 'images/maps/markers/house.png',
            size: new google.maps.Size(16, 16),
            scaledSize: new google.maps.Size(16, 16)
        },
        stnIcon: {
            url: globals.assetsurl + 'images/maps/markers/antenna/mobilephonetower3.png',
            size: new google.maps.Size(16, 18),
            scaledSize: new google.maps.Size(16, 18)
        },
        geocoder: new google.maps.Geocoder(),
        initLoc: new google.maps.LatLng(30.017, -99.016),
        deviceLoc: new google.maps.LatLng(30.017, -99.016),
        init: function (id) {
            editLog.map.instance = new google.maps.Map(document.getElementById(id), {
                zoom: 2,
                center: editLog.map.initLoc,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}]
            });
        }
    },
    getLocation: function (loc_id) {
        return $.post(globals.ajaxurl + 'search_sw_locations.php', {'_mode': 'by_id', 'id': loc_id}, null, 'json');
    },
    getLocations: function (station_name) {
        return $.post(globals.ajaxurl + 'search_sw_locations.php', {
            '_mode': 'by_stn',
            'stn_id': station_name
        }, null, 'json');
    },
    createTXMarker: function (coords) {
        editLog.removeTXMarker();

        if (editLog.map.polyline !== false) {
            editLog.map.polyline.setMap(null);
        }

        var txMarker = new google.maps.Marker({
            icon: editLog.map.stnIcon,
            position: coords,
            zIndex: 2
        });
        editLog.map.markers.tx = txMarker;

        if (editLog.map.markers.rx !== false) {
            editLog.createPolyline(editLog.map.instance, editLog.map.markers.rx.getPosition(), coords);
        }

        editLog.redraw();
    },
    createRXMarker: function (coords) {
        editLog.removeRXMarker();

        var rxMarker = new google.maps.Marker({
            icon: editLog.map.homeIcon,
            position: coords,
            zIndex: 1
        });
        editLog.map.markers.rx = rxMarker;

        if (editLog.map.markers.tx !== false) {
            editLog.createPolyline(editLog.map.instance, coords, editLog.map.markers.tx.getPosition());
        }

        editLog.redraw();

        $('#rxLat').text(coords.lat().toFixed(2));
        $('#rxLng').text(coords.lng().toFixed(2));

        $('#latHdn').val(coords.lat());
        $('#lngHdn').val(coords.lng());
    },
    redraw: function () {
        // reset bounds
        editLog.map.bounds = new google.maps.LatLngBounds();

        if (editLog.map.markers.rx !== false) {
            var rxCoords = editLog.map.markers.rx.getPosition();
            editLog.map.markers.rx.setMap(editLog.map.instance);
            editLog.map.bounds.extend(rxCoords);
        }

        if (editLog.map.markers.tx !== false) {
            var txCoords = editLog.map.markers.tx.getPosition();
            editLog.map.markers.tx.setMap(editLog.map.instance);
            editLog.map.bounds.extend(txCoords);
        }

        editLog.map.instance.fitBounds(editLog.map.bounds);
    },
    removeRXMarker: function () {
        if (editLog.map.markers.rx !== false) {
            editLog.map.markers.rx.setMap(null);
            editLog.map.markers.rx = false;
        }

        editLog.map.instance.fitBounds(editLog.map.bounds);
    },
    removeTXMarker: function () {
        if (editLog.map.markers.tx !== false) {
            editLog.map.markers.tx.setMap(null);
            editLog.map.markers.tx = false;
        }

        editLog.map.instance.fitBounds(editLog.map.bounds);
    },
    createPolyline: function (mapInstance, coordsFrom, coordsTo) {
        editLog.map.polyline = new google.maps.Polyline({
            geodesic: true,
            strokeColor: 'red',
            strokeOpacity: 0.5,
            strokeWeight: 2,
            zIndex: 3,
            path: [coordsFrom, coordsTo],
            map: mapInstance
        });
    }
};

$(function () {
    $('#audioUploader').fileinput({
        uploadUrl: globals.ajaxpath + 'log_audio_uploader.php',
        uploadExtraData: {_id: $('#audioUploader').data('logid')},
        uploadAsync: true,
        minFileCount: 1,
        maxFileCount: 1
    });
    $('#audioUploader').on('fileuploaded', function (event, data, previewId, index) {
        location.reload();
    });

    editLog.map.init('googleMap');

    google.maps.event.addDomListener(window, 'resize', function () {
        $('#googleMap').width($('#googleMap').parent().width());
        var center = editLog.map.instance.getCenter();
        google.maps.event.trigger(editLog.map.instance, 'resize');
        editLog.map.instance.setCenter(center);
    });

    google.maps.event.addListener(editLog.map.instance, 'dblclick', function (evt) {
        clearOverlays([editLog.map.markers.rx, editLog.map.polyline]);
        editLog.createRXMarker(evt.latLng);
        editLog.createPolyline(editLog.map.instance, evt.latLng, editLog.map.markers.tx);
    });

    if ($('#latHdn').val() !== '' || $('#lngHdn').val() !== '') {
        // set receive location marker
        var coords = {lat: parseFloat($('#latHdn').val()), lng: parseFloat($('#lngHdn').val())};
        coords = new google.maps.LatLng(coords.lat, coords.lng);
        editLog.createRXMarker(coords);
    }

    $.when(editLog.getLocation($('#locHdn').val())).done(function (d) {
        if (!$.isEmptyObject(d.result)) {
            var hdnCoords = new google.maps.LatLng(parseFloat(d.result.coordinates.lat), parseFloat(d.result.coordinates.lng));
            editLog.createTXMarker(hdnCoords);
        }
    });

    $('#dateOn').datepicker();

    $('#mapBtn').on('click', function (e) {
        if ($('#mapSearch').val() !== '') {
            editLog.map.geocoder.geocode({'address': $('#mapSearch').val()}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    editLog.map.instance.setCenter(results[0].geometry.location);
                    editLog.map.instance.setZoom(15);
                    editLog.createRXMarker(results[0].geometry.location);
                }
            });
        }
    });

    $('#stationSel').on('change', function (e) {
        var ele = this;
        var stationName = $(ele).val();

        $('#locSel').empty();

        $.when(editLog.getLocations(stationName)).done(function (d) {
            editLog.removeTXMarker();

            if (d.length > 0) {
                $('#locSel').removeAttr('disabled');

                $('<option />').val('').html('- select -').appendTo('#locSel');
                $(d).each(function (i, v) {
                    var site = (v.site == null) ? 'Unknown - ' : v.site + ' - ';
                    var lang = (v.language === false) ? '' : ' - ' + v.language.language;
                    var timeSlot = (v.time_slot == null) ? '' : ' - ' + v.time_slot;
                    $('<option />').val(v.id).html(site + v.frequency + lang + timeSlot).appendTo('#locSel');
                });
            } else {
                $('#locSel').attr('disabled', 'disabled');
            }
        });
    });

    $('#locSel').on('change', function (e) {
        var ele = this;
        var locId = $(ele).val();

        if (locId === '') {
            editLog.removeTXMarker();
        }

        $.when(editLog.getLocation(locId)).done(function (d) {
            if (!$.isEmptyObject(d)) {
                var ll = new google.maps.LatLng(parseFloat(d.lat), parseFloat(d.lng));

                $('#locHdn').val(d.id);

                editLog.createTXMarker(ll);
            } else {
                $('#locHdn').val('');
            }
        });
    });

    $('#mapSearch').on('keyup', function (e) {
        if (e.keyCode === 13) {
            if ($("#mapSearch").val() !== "") {
                editLog.map.geocoder.geocode({'address': $("#mapSearch").val()}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        clearOverlays([editLog.map.markers.rx, editLog.map.polyline]);

                        editLog.map.instance.setCenter(results[0].geometry.location);
                        editLog.map.instance.setZoom(15);
                        editLog.createRXMarker(results[0].geometry.location);
                        editLog.createPolyline(editLog.map.instance, results[0].geometry.location, editLog.map.markers.tx.getPosition());
                    }
                });
            }
        }
    });
});