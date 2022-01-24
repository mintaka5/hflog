var homeMap = {
    map: false,
    center: new google.maps.LatLng(37.4419, -122.1419),
    markers: [],
    sunMarker: false,
    infoWin: new google.maps.InfoWindow(),
    updateDayNite: function () {
        nite.refresh();

        homeMap.sunMarker.setPosition(nite.getSunPosition());
    },
    stnIcon: {
        'url': globals.relurl + 'assets/images/maps/markers/antenna-green-32.png',
        'size': new google.maps.Size(32, 32),
        'scaledSize': new google.maps.Size(32, 32)
    },
    homeIcon: {
        'url': globals.relurl + 'assets/images/maps/markers/house-green-32.png',
        'size': new google.maps.Size(32, 32),
        'scaledSize': new google.maps.Size(32, 32)
    },
    sunIcon: {
        url: globals.relurl + 'assets/images/maps/markers/sun-yellow-32.png',
        size: new google.maps.Size(32, 32),
        scaledSize: new google.maps.Size(32, 32)
    },
    init: function () {
        homeMap.map = new google.maps.Map(document.getElementById('homeMap'), {
            zoom: 3,
            minZoom: 2,
            maxZoom: 15,
            center: homeMap.center,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            mapTypeControlOptions: {
                mapTypeIds: [
                    google.maps.MapTypeId.HYBRID,
                    google.maps.MapTypeId.ROADMAP,
                    google.maps.MapTypeId.SATELLITE,
                    google.maps.MapTypeId.TERRAIN
                ]
            },
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL
            },
            scaleControl: true,
            scaleControlOptions: {},
            styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}],
            streetViewControl: false
        });

        nite.init(homeMap.map);
        homeMap.sunMarker = new google.maps.Marker({
            icon: homeMap.sunIcon,
            position: nite.getSunPosition(),
            map: homeMap.map
        });

        google.maps.event.addListenerOnce(homeMap.map, 'idle', function () {
            // fully loaded map
            // remove 'Google' attribution
            $($('.gm-style > div')[1]).css('display', 'none');
            $($('.gm-style > div')[3]).css('display', 'none');
            $($('.gm-style > div')[5]).css('display', 'none');
            // remove map data attributions

        });

        google.maps.event.addListenerOnce(homeMap.map, 'idle', function () {
            homeMap.loadLogs();
        });

        google.maps.event.addListener(homeMap.infoWin, 'domready', function() {
            var iwOuter = $('.gm-style-iw');
            var iwBackground = iwOuter.prev();
            iwBackground.children(':nth-child(2)').css({
                'background-color': 'rgba(0, 43, 54, 0.9)',
                border: '1px solid #5f8700'
            });
            iwBackground.children(':nth-child(3)').css({display: 'none'});
            iwBackground.children(':nth-child(4)').css({display: 'none'});
        });

        $.when(homeMap.getMyLocation()).done(function (a) {
            if (a.status === 'ok') {
                homeMap.map.setCenter(a.result);

                var marker = new google.maps.Marker({
                    icon: homeMap.homeIcon,
                    map: homeMap.map,
                    position: a.result
                });
            }
        });
    },
    getMyLocation: function () {
        return $.get(globals.ajaxurl + 'index.php', {
            _mode: 'user',
            _task: 'location'
        }, null, 'json');
    },
    loadLogs: function () {
        $.when(homeMap.getLogs()).done(function (a) {
            $(a).each(function (i, v) {
                if (v.location !== null) {
                    if (v.location.coordinates.lat !== null) {
                        var markerDate = new Date(v.time_on_iso8601);
                        var markerPos = new google.maps.LatLng(parseFloat(v.location.coordinates.lat), parseFloat(v.location.coordinates.lng));

                        var marker = new MarkerWithLabel({
                            icon: homeMap.stnIcon,
                            map: homeMap.map,
                            position: markerPos,
                            zIndex: (i * 2),
                            labelContent: v.html,
                            labelStyle: {
                                'background-color': '#073642',
                                border: '1px solid #93a1a1',
                                color: '#93a1a1',
                                padding:'5px',
                                width:'100px'
                            },
                            labelAnchor: new google.maps.Point(22,0)
                        });
                        marker.set('logid', v.id);
                        google.maps.event.addListener(marker, 'click', function () {
                            //console.log(this.logid);
                            if (this.logid !== undefined) {
                                $('#homeMap').block({message: 'Gathering location info...'});

                                $.when(homeMap.getLogInfo(this.logid)).done(function (a) {
                                    $('#homeMap').unblock();
                                    homeMap.infoWin.setContent(a);
                                    homeMap.infoWin.open(homeMap.map, marker);
                                });
                            }
                        });
                        homeMap.markers.push(marker);
                    }
                }
            });

            var cluster = new MarkerClusterer(homeMap.map, homeMap.markers, {
                maxZoom: 8,
                imagePath: globals.relurl + 'assets/js/gmapsutil/markerclusterer/images/m'
            });
        });
    },
    getLogInfo: function (logid) {
        return $.post(globals.ajaxurl + 'hflogs.php', {
            _mode: 'log',
            _task: 'infowin',
            log_id: logid
        }, null, 'html');
    },
    getLogs: function () {
        return $.get(globals.ajaxurl + 'index.php', {}, null, 'json');
    }
};

$(function () {
    homeMap.init();

    /**
     * update the daynight overlay
     */
    setInterval(function () {
        homeMap.updateDayNite();
    }, 3000);
});
