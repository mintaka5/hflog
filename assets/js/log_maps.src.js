var logMap = false;
var centerCoord = new google.maps.LatLng(38.059, -95.579);
var markerBounds = new google.maps.LatLngBounds();
var markerAry = [];
var infoWin = new google.maps.InfoWindow({maxWidth: 300});

var goojax = {
    getMarkerLogInfo: function (id) {
        return $.post(globals.ajaxurl + 'hflogs.php', {
            _mode: 'log',
            _task: 'infowin',
            log_id: id
        }, null, 'html');
    },
    getLogEntry: function(logId) {
        return $.post(globals.ajaxurl + 'hflogs.php', {
            _mode: 'log',
            _task: 'json',
            id: logId
        }, null, 'json');
    }
};

$(function () {
    if (document.getElementById('logMap') !== null) {
        logMap = new google.maps.Map(document.getElementById('logMap'), {
            zoom: 3,
            minZoom: 2,
            maxZoom: 15,
            styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}],
            center: centerCoord,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControlOptions: {
                'mapTypeIds': [
                    google.maps.MapTypeId.HYBRID,
                    google.maps.MapTypeId.ROADMAP,
                    google.maps.MapTypeId.SATELLITE,
                    google.maps.MapTypeId.TERRAIN
                ]
            },
            zoomControlOptions: {
                'style': google.maps.ZoomControlStyle.SMALL
            },
            scaleControl: true,
            scaleControlOptions: {},
            streetViewControl: false
        });

        google.maps.event.addListener(infoWin, 'domready', function() {
            var iwOuter = $('.gm-style-iw');
            var iwBackground = iwOuter.prev();
            iwBackground.children(':nth-child(2)').css({
                'background-color': 'rgba(0, 43, 54, 0.9)',
                border: '1px solid #5f8700'
            });
            iwBackground.children(':nth-child(3)').css({display: 'none'});
            iwBackground.children(':nth-child(4)').css({display: 'none'});
        });

        google.maps.event.addDomListener(window, 'resize', function () {
            $('#logMap').width($('#logMap').parent().width());
            //var center = logMap.getCenter();
            google.maps.event.trigger(logMap, 'resize');
            //logMap.setCenter(center);
            logMap.fitBounds(markerBounds);
        });

        google.maps.event.addDomListener(window, 'load', function () {
            $('#logMap').width($('#logMap').parent().width());
            var center = logMap.getCenter();
            google.maps.event.trigger(logMap, 'resize');
            logMap.setCenter(center);
        });
    }

    if ($('.locLat').length > 0) {
        $('.locLat').each(function (i, v) {
            var usrLat = $(v).siblings('.usrLat').val();
            var usrLng = $(v).siblings('.usrLng').val();
            if (usrLat.length > 0) {
                var usrPos = new google.maps.LatLng(usrLat, usrLng);

                var usrIcon = {
                    'url': globals.relurl + 'assets/images/maps/markers/house-green-32.png',
                    'size': new google.maps.Size(25, 25),
                    'scaledSize': new google.maps.Size(25, 25)
                };

                var usrMarker = new google.maps.Marker({
                    'icon': usrIcon,
                    'map': logMap,
                    'position': usrPos,
                    'zIndex': (i * 3)
                });

                markerBounds.extend(usrPos);
            }

            if ($(v).val() !== "") { // if we have a location plot it on map
                if (parseFloat($(v).val()) !== 0 && parseFloat($(v).siblings('.locLng').val()) !== 0) { // Lat 0, Lon 0 is not ever gonna happen
                    var stnPos = new google.maps.LatLng($(v).val(), $(v).siblings('.locLng').val());

                    var stnIcon = {
                        url: globals.relurl + 'assets/images/maps/markers/antenna-green-32.png',
                        'size': new google.maps.Size(25, 25),
                        'scaledSize': new google.maps.Size(25, 25)
                    };

                    var stnMarker = new google.maps.Marker({
                        'icon': stnIcon,
                        'map': logMap,
                        'position': stnPos,
                        'zIndex': (i * 2)
                    });
                    stnMarker.set('logId', $(v).siblings('.logid').val());

                    google.maps.event.addListener(stnMarker, 'click', function () {
                        var logid = $(v).siblings('.logid').val();

                        if (logid !== undefined) {
                            $('#logMap').block({message: 'loading data...'});

                            $.when(goojax.getMarkerLogInfo(logid)).done(function (a) {
                                $('#logMap').unblock();
                                infoWin.setContent(a);
                                infoWin.open(logMap, stnMarker);
                            });
                        }
                    });

                    var usrPath = new google.maps.Polyline({
                        'geodesic': true,
                        'strokeColor': '#d75f00',
                        'strokeOpacity': 0.8,
                        'strokeWeight': 3,
                        'zIndex': (i * 10),
                        'path': [stnPos, usrPos]
                    });
                    usrPath.setMap(logMap);

                    markerBounds.extend(stnPos);
                }
            }
        });

        logMap.fitBounds(markerBounds);
    }
});