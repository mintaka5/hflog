var initLocation = new google.maps.LatLng(39.01, -98.84);
var geocoder;
var centerLoc;
var gmap;
var markers;

$(function () {
    markers = [];

    geocoder = new google.maps.Geocoder();

    centerLoc = new google.maps.LatLng(30.017, -99.016);

    gmap = new google.maps.Map(document.getElementById('googleMap'), {
        zoom: 2,
        center: centerLoc,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#839496"},{"saturation":-42},{"lightness":-46}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#262626"},{"hue":"#000000"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#5f8700"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":-50},{"lightness":6}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#5f8700"},{"weight":1}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"color":"#5f8700"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0087ff"}]}]
    });

    google.maps.event.addDomListener(window, 'load', function () {
        $('#googleMap').width($('#googleMap').parent().width());
        var center = gmap.getCenter();
        google.maps.event.trigger(gmap, 'resize');
        gmap.setCenter(center);

        $('#latTxt').html((Math.round(center.lat() * 10000) / 10000).toString());
        $('#lngTxt').html((Math.round(center.lng() * 10000) / 10000).toString());
    });

    if ($("#latHdn").val() !== "") {
        $("#latHdn").val($("#latHdn").val());
        $("#lngHdn").val($("#lngHdn").val());

        createMarker(gmap, markers, new google.maps.LatLng($("#latHdn").val(), $("#lngHdn").val()), 'location');
    }

    google.maps.event.addListener(gmap, 'click', function (evt) {
        clearOverlays(markers);

        var evtLat = Math.round(evt.latLng.lat() * 10000) / 10000;
        var evtLng = Math.round(evt.latLng.lng() * 10000) / 10000;

        $("#latHdn").val(evt.latLng.lat());
        $("#lngHdn").val(evt.latLng.lng());

        $('#latTxt').html(evtLat);
        $('#lngTxt').html(evtLng);

        createMarker(gmap, markers, evt.latLng, 'location');
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            var geoPos = new google.maps.LatLng(pos);

            gmap.setCenter(geoPos);
            gmap.setZoom(2);

            var geoPosLat = Math.round(geoPos.lat() * 10000) / 10000;
            var geoPosLng = Math.round(geoPos.lng() * 10000) / 10000;
            $('#latTxt').html(geoPosLat.toString());
            $('#lngTxt').html(geoPosLng.toString());
            $('#latHdn').val(geoPos.lat());
            $('#lngHdn').val(geoPos.lng());

            createMarker(gmap, markers, geoPos, 'location');
        });
    } else {
        gmap.setCenter(initLocation);
        gmap.setZoom(2);

        var initLat = Math.round(initLocation.lat() * 10000) / 10000;
        var initLng = Math.round(initLocation.lng() * 10000) / 10000;
        $('#latTxt').html(initLat.toString());
        $('#lngTxt').html(initLng.toString());
        $('#latHdn').val(initLocation.lat());
        $('#lngHdn').val(initLocation.lng());

        createMarker(gmap, markers, initLocation, 'location');
    }

    $(document).on("click", '#mapBtn', function (e) {
        if ($("#mapSearch").val() !== "") {
            geocoder.geocode({'address': $("#mapSearch").val()}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    gmap.setCenter(results[0].geometry.location);
                    gmap.setZoom(15);
                }
            });
        }
    });

    $(document).on('keyup', '#mapSearch', function (e) {
        if (e.keyCode === 13) {
            if ($('#mapSearch').val() !== '') {
                geocoder.geocode({'address': $("#mapSearch").val()}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        gmap.setCenter(results[0].geometry.location);
                        gmap.setZoom(15);
                    }
                });
            }
        }
    });
});