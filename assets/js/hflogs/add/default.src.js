var add = {
    map: false,
    markers: {rx:false,tx:false},
    geocoder: new google.maps.Geocoder(),
    initLoc: new google.maps.LatLng(30.017, -99.016),
    deviceLoc: new google.maps.LatLng(30.017, -99.016),
    bounds: new google.maps.LatLngBounds(),
    getLocations:function(station_name) {
        return $.post(globals.ajaxurl+'search_sw_locations.php', {'_mode':'by_stn', 'stn_id':station_name}, null, 'json');
    },
    getLocation:function(loc_id) {
        return $.post(globals.ajaxurl+'search_sw_locations.php', {'_mode':'by_id', 'id':loc_id}, null, 'json');
    },
    getStations: function() {
        return $.post(globals.ajaxurl+'station_selects.php', {}, null, 'json');
    },
    getUserLocation: function() {
        return $.get(globals.ajaxurl+'user.php', {
            _mode: 'location'
        }, null, 'json');
    },
    addStation: function (stnTitle) {
        return $.post(globals.ajaxurl+'admin_station.php', {
            _mode: 'add',
            name: stnTitle
        }, null, 'json');
    },
    addLocation: function(locData) {
        return $.post(globals.ajaxurl + 'admin_location.php', {
            _mode: 'add',
            data: locData
        }, null, 'json');
    },
    removeRXMarker: function() {
        if(add.markers.rx !== false) {
            add.markers.rx.setMap(null);
            add.markers.rx = false;
        }
    },
    removeTXMarker:function() {
        if(add.markers.tx !== false) {
            add.markers.tx.setMap(null);
            add.markers.tx = false;

            add.line.setMap(null);
            add.line = false;
        }
    },
    createRXMarker:function(coords) {
        add.removeRXMarker();

        if(add.line !== false) {
            add.line.setMap(null);
            add.line = false;
        }

        var mkr = new google.maps.Marker({
            'icon':add.rxMarker,
            'position':coords,
            'zIndex':1
        });
        add.markers.rx = mkr;

        if(add.markers.tx !== false) {
            add.line = new google.maps.Polyline({
                geodesic:true,
                strokeColor:'red',
                strokeOpacity:0.5,
                strokeWeight:2,
                zIndex:3,
                path:[coords, add.markers.tx.getPosition()],
                map:add.map
            });
        }

        add.redraw();

        $('#rxLat').text(coords.lat().toFixed(2));
        $('#rxLng').text(coords.lng().toFixed(2));

        $('#latHdn').val(coords.lat());
        $('#lngHdn').val(coords.lng());
    },
    txMarker:{
        url: globals.assetsurl + 'images/lightblue.png',
        size:new google.maps.Size(24,26),
        scaledSize:new google.maps.Size(24,26)
    },
    rxMarker:{
        url: globals.assetsurl + 'images/red.png',
        size:new google.maps.Size(24,26),
        scaledSize:new google.maps.Size(24,26)
    },
    createTXMarker:function(coords) {
        add.removeTXMarker();

        if(add.line !== false) {
            add.line.setMap(null);
            add.line = false;
        }

        var mkr = new google.maps.Marker({
            icon:add.txMarker,
            position:coords,
            zIndex:2
        });

        add.markers.tx = mkr;

        if(add.markers.rx !== false) {
            add.line = new google.maps.Polyline({
                geodesic:true,
                strokeColor:'red',
                strokeOpacity:0.5,
                strokeWeight:2,
                zIndex:3,
                path:[add.markers.rx.getPosition(), coords],
                map:add.map
            });
        }

        add.redraw();
    },
    redraw:function() {
        if(add.markers.rx !== false) {
            var rx_coords = add.markers.rx.getPosition();
            add.markers.rx.setMap(add.map);
            add.bounds.extend(rx_coords);
        }

        if(add.markers.tx !== false) {
            var tx_coords = add.markers.tx.getPosition();
            add.markers.tx.setMap(add.map);
            add.bounds.extend(tx_coords);
        }

        add.map.fitBounds(add.bounds);
    },
    line:false
};

var timeon_timer = false;

$(function() {
    timeon_timer = $.timer(function() {
        var now = new Date();
        var hour = now.getUTCHours();
        var minutes = now.getUTCMinutes();
        var seconds = now.getUTCSeconds();

        if(seconds <= 9) {
            seconds = new String('0' + seconds);
        }

        if(minutes <= 9) {
            minutes = new String('0' + minutes);
        }

        if(hour < 10) {
            hour = new String('0' + hour);
        }

        $('#timeOn').val(hour + ':' + minutes);
    }, 1000, true);

    $(document).on('focus', '#timeOn', function(e) {
        timeon_timer.stop();
    });

    if($('#locHdn').val() !== '') {
        $.when(add.getLocations($('#stationSel').val())).done(function(d) {
            if(d.result.length > 0) {
                $('#locSel').removeAttr('disabled');

                $('<option />').val('').html('- select -').appendTo('#locSel');
                $(d.result).each(function(i, v) {
                    var site = (v.site == null) ? 'Unknown - ' : v.site+' - ';
                    var lang = (v.language == null) ? '' : ' - ' + v.language;
                    $('<option />').val(v.id).html(site+v.frequency+lang).appendTo('#locSel');
                });

                $('#locSel').val($('#locHdn').val());
            }
        });

        $.when(add.getLocation($('#locHdn').val())).done(function(d) {
            if(!$.isEmptyObject(d.result)) {
                var hdnCoords = new google.maps.LatLng(parseFloat(d.result.lat), parseFloat(d.result.lng));
                add.createTXMarker(hdnCoords);
            }
        });
    }

    add.map = new google.maps.Map(document.getElementById('googleMap'), {
        'zoom': 2,
        minZoom: 2,
        maxZoom: 15,
        center:add.initLoc,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}]
    });

    google.maps.event.addDomListener(window, 'load', function() {
        $('#googleMap').width($('#googleMap').parent().width());
        var center = add.map.getCenter();
        google.maps.event.trigger(add.map, 'resize');
        add.map.setCenter(center);
        add.createRXMarker(center);
    });

    google.maps.event.addDomListener(window, 'resize', function() {
        $('#googleMap').width($('#googleMap').parent().width());
        var center = add.map.getCenter();
        google.maps.event.trigger(add.map, 'resize');
        add.map.setCenter(center);
        add.createRXMarker(center);
    });

    $.when(add.getUserLocation()).then(function(data) {
        var loc;

        if(!$.isEmptyObject(data)) {
            loc = new google.maps.LatLng(data.lat, data.lng);
        } else { // geolocate user
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    loc = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                });
            }
        }

        add.map.setCenter(loc);
        add.map.setZoom(13);
        add.createRXMarker(add.map.getCenter());
    });

    $("#dateOn").datepicker();

    $(document).on("click", "#mapBtn", function(e) {
        if($("#mapSearch").val() !== "") {
            add.geocoder.geocode({'address':$("#mapSearch").val()}, function(results, status) {
                if(status === google.maps.GeocoderStatus.OK) {
                    add.map.setCenter(results[0].geometry.location);
                    add.map.setZoom(15);
                    add.createRXMarker(results[0].geometry.location);
                }
            });
        }
    });

    $(document).on('keyup', '#mapSearch', function(e) {
        if(e.keyCode === 13) {
            if($("#mapSearch").val() !== "") {
                add.geocoder.geocode({'address':$("#mapSearch").val()}, function(results, status) {
                    if(status === google.maps.GeocoderStatus.OK) {
                        clearOverlays(add.markers.rx);
                        add.map.setCenter(results[0].geometry.location);
                        add.map.setZoom(15);
                        add.createRXMarker(results[0].geometry.location);
                    }
                });
            }
        }
    });

    if($('#latHdn').val() !== '') {
        add.createRXMarker(new google.maps.LatLng($("#latHdn").val(), $("#lngHdn").val()));
    }

    google.maps.event.addListener(add.map, 'click', function(evt) {
        add.createRXMarker(evt.latLng);
    });

    $('.add-station').on('click', function(e) {
        $('#dlgAddStn').modal('show');
    });

    $('#btnSaveStation').on('click', function(e) {
        var title = $('#stnTitle').val();
        if(title.length > 1) {
            $.when(add.addStation(title)).done(function (station) {
                $('#stationSel').empty();

                var sel = (station) ? station.station_name : false;

                $.when(add.getStations()).done(function (stations) {
                    $(stations.result).each(function (i, station) {
                        var opt = $('<option/>').val(station.station_name).html(station.title);

                        if(sel === station.station_name) {
                            opt.attr({'selected': 'selected'});
                        }

                        opt.appendTo('#stationSel');
                    });

                    $('#stnTitle').val('');
                    $('#dlgAddStn').modal('hide');
                })
            });
        }
    });

    $('.add-location').on('click', function(e) {
        var stationName = $('#stationSel').val();

        if(stationName !== '') {
            $('#dlgAddLoc').modal('show');
        } else {
            $('#selStnError').modal('show');
        }
    });

    /**
     * validate times
     */
    $.validator.addMethod('time', function(value, element) {
        return this.optional(element) || /^\d{2}:\d{2}$/.test(value);
    }, 'Please, provide time 24-hour time format e.g. 01:00');

    /**
     * validate frequency
     */
    $.validator.addMethod('frequency', function(value, element) {
        return this.optional(element) || /^\d{3,5}(\.\d{1,2})?$/.test(value);
    }, 'Please, provide kilohertz format e.g. 14070 or 10051.00');

    $.validator.addMethod('coordinate', function(value, element) {
        return this.optional(element) || /^\-?\d{1,3}(\.\d{1,5})?$/.test(value);
    }, 'Please decimal degree format e.g. 22.010');

    $('#locStart, #locEnd').mask('99:99', {
        placeholder: 'hh:mm'
    });

    var addLocValid = $('#addLocForm').validate({
        errorClass: 'text-danger',
        focusCleanup: true
    });

    /*$('#locLong').rules('add', {
     remote: {
     url: globals.ajaxurl + 'admin_location.php',
     type: 'post',
     data: {
     locLat: function() {
     return $('#locLat').val()
     },
     locLong: function() {
     return $('#locLong').val()
     },
     _mode: 'validate',
     _task: 'coordinates'
     }
     },
     messages: {
     remote: 'Not a valid fixed location on planet Earth'
     }
     });*/

    /**
     * clear all form fields
     * and reset validation
     */
    $('#dlgAddLoc').on('hidden.bs.modal', function(e) {
        $('#addLocForm').find('input, select').each(function(i, field) {
            $(field).val('');
        });
        addLocValid.resetForm();
    });

    /**
     * reset name value of currently selected station
     * in location form
     */
    $('#dlgAddLoc').on('show.bs.modal', function(e) {
        $('#locStnName').val($('#stationSel').val());
    });

    $('#btnSaveLocation').on('click', function(e) {
        console.log(addLocValid);
        if($('#addLocForm').valid()) {
            $.when(add.addLocation($('#addLocForm').serializeFormJSON())).done(function(aData) {
                $('#locSel').empty();

                var sel = (aData.locationId) ? aData.locationId : false;
                $('#locHdn').val(sel);

                $.when(add.getLocations(aData.submittedData.locStnName)).done(function(locations) {
                    add.removeTXMarker();

                    if(locations.length > 0) {
                        $('#locSel').removeAttr('disabled');

                        $('<option />').val('').html('- select -').appendTo('#locSel');
                        $(locations).each(function(i, location) {
                            var site = (!location.site) ? 'Unknown - ' : location.site+' - ';
                            var lang = (!location.language) ? '' : ' - ' + location.language.language;
                            var timeSlot = (!location.times) ? '' : ' - ' + location.times;
                            var opt = $('<option />').val(location.id).html(site+location.frequency+lang+timeSlot).appendTo('#locSel');
                            if(sel === location.id) {
                                opt.prop('selected', true);
                            }
                        });
                    } else {
                        $('#locHdn').val('');
                        $('#locSel').attr('disabled', 'disabled');
                    }

                    $('#dlgAddLoc').modal('hide');

                    // trigger location change to redraw map marker
                    $('#locSel').trigger('change');
                });
            });
        }
    });

    $('#locSite').typeahead({
        source: function(query, process) {
            return $.post(globals.ajaxurl + 'search_sw_locations.php', {
                _mode: 'search',
                _task: 'by_stn',
                stn_name: $('#stationSel').val(),
                q:query
            }, function (d) {
                return process(d);
            }, 'json');
        },
        updater: function(item) {
            $('#locLat').val(item.lat);
            $('#locLong').val(item.lng);

            return item;
        },
        displayText: function(item) {
            return item.site;
        }
    });

    $('#stationSel').on('change', function(e) {
        var ele = this;
        var stationName = $(ele).val();

        $('#locSel').empty();

        $('#locStnName').val(stationName);
        $('#selStnTitle').html($(this).find('option[value="'+ stationName +'"]').html());

        $.when(add.getLocations(stationName)).done(function(locations) {
            add.removeTXMarker();

            if(locations.length > 0) {
                $('#locSel').removeAttr('disabled');

                $('<option />').val('').html('- select -').appendTo('#locSel');
                $(locations).each(function(i, location) {
                    var site = (!location.site) ? 'Unknown - ' : location.site + ' - ';
                    var lang = (!location.language) ? '' : ' - ' + location.language.language;
                    var timeSlot = (!location.times) ? '' : ' - ' + location.times;
                    $('<option />').val(location.id).html(site + location.frequency + lang + timeSlot).appendTo('#locSel');
                });
            } else {
                $('#locSel').attr('disabled', 'disabled');
            }
        });
    });

    $('#locSel').on('change', function(e) {
        var ele = this;
        var locId = $(ele).val();

        if(locId === '') {
            add.removeTXMarker();
        }

        $.when(add.getLocation(locId)).done(function(d) {
            if(!$.isEmptyObject(d.result)) {
                var ll = new google.maps.LatLng(parseFloat(d.result.coordinates.lat), parseFloat(d.result.coordinates.lng));

                $('#locHdn').val(d.result.id);

                add.createTXMarker(ll);
            } else {
                $('#locHdn').val('');
            }
        });
    });
});