var audioposition = 0;
var audiocurrent = false;

stationMap = {
    map:false,
    bounds:new google.maps.LatLngBounds(),
    initCoord:new google.maps.LatLng(38.059, -95.579),
    markers:[],
    infoWin:new google.maps.InfoWindow({maxWidth:300}),
    stnIcon:{
        'url':globals.relurl + 'assets/images/maps/markers/antenna-green-32.png',
        'size':new google.maps.Size(25, 25),
        'scaledSize':new google.maps.Size(25, 25)
    },
    homeIcon: {
        'url':globals.relurl + 'assets/images/maps/markers/house-green-32.png',
        'size':new google.maps.Size(25, 25),
        'scaledSize':new google.maps.Size(25, 25)
    }
};

$(function() {
    soundManager.setup({
        url:globals.relurl + 'assets/soundmanager/swf/soundmanager2.swf',
        flashVersion:9,
        useFlashBlock:false,
        debugMode:false,
        debugFlash:false,
        onready: function() {
            $('.audioSample').each(function(i, v) {
                soundManager.createSound({
                    id: $(v).attr('id'),
                    url: globals.relurl + 'audio/item/?id=' + $(v).data('id'),
                    autoLoad:true,
                    autoPlay:false,
                    onload: function() {
                        this.setPosition(0);

                        $('#' + this.id + ' > .btn-group > .audioPlay').removeAttr('disabled');
                        $('#' + this.id + ' > .progress > .progress-bar').css({width: '0%'}).removeClass('progress-bar-striped').addClass('progress-bar-info').text('0%');
                    },
                    whileloading: function () {
                        var fraction = (this.bytesLoaded / this.bytesTotal);
                        var percentage = Math.round(fraction * 100);

                        $('#' + this.id + ' > .progress > .progress-bar').css({width: percentage + '%'}).text(percentage + '%');
                    },
                    onplay: function() {
                        $('#' + this.id + ' > .btn-group > .audioStop').removeAttr('disabled');
                        $('#' + this.id + ' > .btn-group > .audioPlay').attr('disabled', 'disabled');
                    },
                    onstop: function() {
                        audiocurrent = false;

                        $('#' + this.id + ' > .btn-group > .audioPlay').removeAttr('disabled');
                        $('#' + this.id + ' > .btn-group > .audioStop').attr('disabled', 'disabled');

                        this.setPosition(0);

                        $('#' + this.id + ' > .btn-group > .audioStop').attr('disabled', 'disabled');
                        $('#' + this.id + ' > .btn-group > .audioPlay').removeAttr('disabled');

                        $('#' + this.id + ' > .progress > .progress-bar').css({width: '0%'}).text('0%');
                        $('#' + this.id + ' + div > .audioTimer').text('00:00');
                    },
                    onfinish: function() {
                        this.setPosition(0);

                        $('#' + this.id + ' > .btn-group > .audioStop').attr('disabled', 'disabled');
                        $('#' + this.id + ' > .btn-group > .audioPlay').removeAttr('disabled');

                        $('#' + this.id + ' > .progress > .progress-bar').css({width: '0%'}).text('0%');
                        $('#' + this.id + ' + div > .audioTimer').text('00:00');
                    },
                    multiShot:false,
                    volume:50
                });
            });
        }
    });

    $('.audioPlay').on('click', function(e) {
        e.preventDefault();

        soundManager.stopAll();

        var sound_id = $(this).data('referer');

        soundManager.play(sound_id, {
            whileplaying: function() {
                var fraction = (this.position / this.duration);
                var percentage = Math.round(fraction * 100);

                $('#' + this.id + ' > .progress > .progress-bar').css({width: percentage + '%'}).text(percentage + '%');
                $('#' + this.id + ' + div > .audioTimer').text(getClockString(this.position));
            }
        });
    });

    $('.audioStop').on('click', function(e) {
        e.preventDefault();

        var sound_id = $(this).data('referer');

        soundManager.stop(sound_id);
    });

    stationMap.map = new google.maps.Map(document.getElementById('logMap'), {
        zoom:3,
        center: stationMap.initCoord,
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        mapTypeControlOptions:{
            mapTypeIds:[
                google.maps.MapTypeId.HYBRID,
                google.maps.MapTypeId.ROADMAP,
                google.maps.MapTypeId.SATELLITE,
                google.maps.MapTypeId.TERRAIN
            ]
        },
        zoomControlOptions:{
            style:google.maps.ZoomControlStyle.SMALL
        },
        scaleControl:true,
        scaleControlOptions:{},
        streetViewControl:false,
        styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}]
    });

    google.maps.event.addDomListener(window, 'resize', function() {
        $('#logMap').width($('#logMap').parent().width());
        //var center = logMap.getCenter();
        google.maps.event.trigger(stationMap.map, 'resize');
        //logMap.setCenter(center);
        stationMap.map.fitBounds(stationMap.bounds);
    });

    google.maps.event.addDomListener(window, 'load', function() {
        $('#logMap').width($('#logMap').parent().width());
        var center = stationMap.map.getCenter();
        google.maps.event.trigger(stationMap.map, 'resize');
        stationMap.map.setCenter(center);
    });

    if($('.location').length > 0) {
        $('.location').each(function(i, v) {
            var coords = $(v).data('coords');
            if(coords !== '') {
                coords = coords.split(',', 2);
                var lat = parseFloat(coords[0].trim());
                var lng = parseFloat(coords[1].trim());
                var markerPos = new google.maps.LatLng(lat, lng);
                var marker = new google.maps.Marker({
                    'icon':stationMap.stnIcon,
                    'map':stationMap.map,
                    'position':markerPos,
                    'zIndex':(i*2)
                });
                stationMap.bounds.extend(markerPos);
            }

            //console.log($(v).siblings('.user-location').data('coords'));
            var userCoords = null;
            if($(v).siblings('.user-location').data('coords') !== undefined) {
                userCoords = $(v).siblings('.user-location').data('coords');
            }

            if(userCoords !== null) {
                var userLoc = $(v).siblings('.user-location').data('coords').split(',', 2);

                var userLat = parseFloat(userLoc[0].trim());
                var userLng = parseFloat(userLoc[1].trim());
                var userPos = new google.maps.LatLng(userLat, userLng);
                var userMarker = new google.maps.Marker({
                    icon: stationMap.homeIcon,
                    map: stationMap.map,
                    position: userPos,
                    zIndex: (i*3)
                });
                stationMap.bounds.extend(userPos);
                var userPath = new google.maps.Polyline({
                    geodesic:true,
                    strokeColor:'red',
                    strokeOpacity:0.5,
                    strokeWeight:2,
                    zIndex:(i*10),
                    path:[markerPos, userPos]
                });
                userPath.setMap(stationMap.map);
            }
        });
        stationMap.map.fitBounds(stationMap.bounds);
    }

    $('#audioUploader').fileinput({
        uploadUrl: globals.ajaxpath + 'station_audio_uploader.php',
        uploadExtraData: {_id: $('#audioUploader').data('stationid')},
        uploadAsync: true,
        minFileCount: 1,
        maxFileCount: 5,
        allowedFileExtensions: ['mp3']
    });
    $('#audioUploader').on('fileuploaded', function (event, data, previewId, index) {
        // reload page or update audio list
        location.reload();
    });
});

function getClockString(msec) {
    var seconds = msec/1000;
    var date = new Date(null);
    date.setSeconds(seconds);
    var time = date.toTimeString().substr(3, 5);

    return time;
}