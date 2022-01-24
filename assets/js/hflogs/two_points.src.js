var TPMap = function() {
    this.docId = false;
    this.map = false;
    this.markers = {rx:false, tx:false};
    this.bounds = new google.maps.LatLngBounds();
    this.line = false;
    this.fromIcon = false;
    this.toIcon = false;
    this.geocoder = new google.maps.Geocoder();
    this.initLoc = new google.maps.LatLng(30.017, -99.016);
    this.deviceLoc = new google.maps.LatLng(30.017, -99.016);
    this.searchtextField = null;
};

TPMap.prototype.setSearchTextField = function(id) {
    this.searchtextField = '#'+id;

    var _tp = this;
    $(document).on('keyup', this.searchtextField, function(e) {
        if(e.keyCode === 13) {
            if($(this.searchTextField).val() !== '') {
                _tp.geocoder.geocode({
                    address: $(this.searchTextField).val()
                }, function(results, status) {
                    if(status === google.maps.GeocoderStatus.OK) {
                        _tp.clearOverlays([
                            _tp.markers.rx,
                            _tp.line
                        ]);

                        _tp.plot(results[0].geometry.location, _tp.markers.tx.getPosition());
                    }
                });
            }
        }
    });
}

TPMap.prototype.init = function(id, fromIcon, toIcon) {
    this.fromIcon = fromIcon;
    this.toIcon = toIcon;
    this.docId = id;

    this.map = new google.maps.Map(document.getElementById(this.docId), {
        zoom: 2,
        center: this.initLoc,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var _tp = this;
    google.maps.event.addDomListener(window, 'resize', function() {
        $('#'+_tp.docId).width($('#'+_tp.docId).parent().width());
        var center = _tp.map.getCenter();
        google.maps.event.trigger(_tp.map, 'resize');
        _tp.map.setCenter(center);
    });

    google.maps.event.addListener(_tp.map, 'dblclick', function(evt) {
        _tp.clearOverlays([_tp.markers.rx, _tp.line]);
        _tp.plot(evt.latLng, _tp.markers.tx.getPosition());
    });
};

TPMap.prototype.createFromMarker = function(coords) {
    this.removeFromMarker();

    var marker = new google.maps.Marker({
        icon: this.fromIcon,
        position: coords,
        zIndex: 1
    });
    this.markers.rx = marker;

    if(this.markers.tx !== false) {
        this.createLine(this.map, coords, this.markers.tx.getPosition());
    }

    this.redraw();

    return coords;
};

TPMap.prototype.toMarkerExists = function() {
    if(this.markers.tx !== false) {
        return true;
    }

    return false;
}

TPMap.prototype.fromMarkerExists = function() {
    if(this.markers.rx !== false) {
        return true;
    }

    return false;
}

TPMap.prototype.createToMarker = function(coords) {
    if(this.toMarkerExists()) {
        this.removeToMarker();
    }

    if(this.line !== false) {
        this.line.setMap(null);
    }

    var marker = new google.maps.Marker({
        icon: this.toIcon,
        position: coords,
        zIndex: 2
    });

    this.markers.tx = marker;

    if(this.markers.rx !== false) {
        this.createLine(this.map, this.markers.rx.getPosition(), coords);
    }

    this.redraw();

    return coords;
};

TPMap.prototype.plot = function(from, to) {
    var fPos = this.createFromMarker(from);
    var tPos = this.createToMarker(to);

    return {from: fPos, to: tPos};
};

TPMap.prototype.createLine = function(map, from, to) {
    this.line = new google.maps.Polyline({
        geodesic: true,
        strokeColor: 'red',
        strokeOpacity: 0.5,
        strokeWeight: 2,
        zIndex: 3,
        path: [from, to],
        map: this.map
    });
};

TPMap.prototype.removeTXMarker = function() {
    if (this.markers.tx !== false) {
        this.markers.tx.setMap(null);
        this.markers.tx = false;
    }

    this.map.fitBounds(this.bounds);
};

TPMap.prototype.redraw = function() {
    this.bounds = new google.maps.LatLngBounds();

    if(this.markers.rx !== false) {
        var rxCoords = this.markers.rx.getPosition();
        this.markers.rx.setMap(this.map);
        this.bounds.extend(rxCoords);
    }

    if(this.markers.tx !== false) {
        var txCoords = this.markers.tx.getPosition();
        this.markers.tx.setMap(this.map);
        this.bounds.extend(txCoords);
    }

    this.map.fitBounds(this.bounds);
};

TPMap.prototype.removeFromMarker = function() {
    if(this.markers.rx !== false) {
        this.markers.rx.setMap(null);
        this.markers.rx = false;
    }

    this.map.fitBounds(this.bounds);
};

TPMap.prototype.clearOverlays = function(overlays) {
    if(overlays.length > 0) {
        for(i in overlays) {
            if(overlays[i] !== false) {
                overlays[i].setMap(null);
            }
        }
    }
};