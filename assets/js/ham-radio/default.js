var qsoMap = false;
		
$(function() {	
	var centerCoord = new google.maps.LatLng(30.717, -73.702);
	
	var mapOptions = {
		'zoom':8,
		'center': centerCoord,
		'mapTypeId': google.maps.MapTypeId.ROADMAP,
		'mapTypeControlOptions':{
			'mapTypeIds':[
							google.maps.MapTypeId.HYBRID,
							google.maps.MapTypeId.ROADMAP,
							google.maps.MapTypeId.SATELLITE,
							google.maps.MapTypeId.TERRAIN
							]
		},
		'streetViewControl':false
	};
	
	qsoMap = new google.maps.Map(document.getElementById('qsoMap'), mapOptions);

	var infoWin = new google.maps.InfoWindow();
	
	/*var qsoLyr = new google.maps.KmlLayer('http://qualsh.com/QSOs.kml?rand=' + (new Date()).valueOf());
	qsoLyr.setMap(qsoMap);*/

	var markerBounds = new google.maps.LatLngBounds();
	var markerAry = [];
	$.ajax({
		'type':'GET',
		'url':'/QSOs.kml',
		'dataType':'xml',
		'success': function(d) {
			console.log($(d).find("Document > Placemark"));
			var homeCoords = $(d).find("Document > Placemark > Point > coordinates").text().split(",");
			var homeLatLng = new google.maps.LatLng(homeCoords[1], homeCoords[0]);
			var homeMarker = new google.maps.Marker({
				'icon':new google.maps.MarkerImage(globals.relurl + 'assets/images/home_marker.png'),
				'map':qsoMap,
				'position':homeLatLng,
				'zIndex':1
			});
			markerBounds.extend(homeLatLng);
			markerAry.push(homeMarker);
			
			var lines = $(d).find("Folder > Placemark > LineString");
			$(lines).each(function(i, v) {
				var linePlots = $(v).children("coordinates").text().replace(/[\n\s]+/i, "").split(" ").slice(0,2);
				//var startPlots = linePlots[0].split(",");
				var endPlots = linePlots[1].split(",");
				var startPlot = homeLatLng;
				var endPlot = new google.maps.LatLng(parseInt(endPlots[1]), parseInt(endPlots[0]));
				var qsoPath = new google.maps.Polyline({
					'geodesic':true,
					'strokeColor':'red',
					'strokeOpacity':0.5,
					'strokeWeight':2,
					'zIndex':(i*10),
					'path':[startPlot, endPlot]
				});
				qsoPath.setMap(qsoMap);
			});

			var spots = $(d).find("Folder > Placemark > Point");
			var newZ = $(spots).length + 1;
			$(spots).each(function(j, v) {
				var callsign = $(v).parent().find("name").text();
				var qsoInfo = $(v).parent().find("description").text();
				var coordStr = $(v).children("coordinates").text().split(",");
				var markerLatLng = new google.maps.LatLng(parseInt(coordStr[1]), parseInt(coordStr[0]));
				var callsignIcon = 'http://qualsh.com/ode/controllers/ajax/gmap_marker_label.php?txt=' + callsign;
				var spotMarker = new google.maps.Marker({
					'icon': callsignIcon
					/*'icon':new google.maps.MarkerImage(globals.relurl + 'assets/images/map-marker.png')*/,
					'map':qsoMap,
					'position':markerLatLng,
					'zIndex':j
				});

				google.maps.event.addListener(spotMarker, 'click', function() {
					infoWin.setContent('<h3>' + callsign + '</h3><div>' + qsoInfo + '</div>');
					infoWin.open(qsoMap, spotMarker);
				});
				
				google.maps.event.addListener(spotMarker, 'mouseover', function() {
					newZ++;
					this.setZIndex(newZ);
				});
				
				google.maps.event.addListener(spotMarker, 'mouseout', function() {});
				
				markerAry.push(spotMarker);
				markerBounds.extend(markerLatLng);
			});

			qsoMap.fitBounds(markerBounds);
		}
	});

	$("#timeSel").live("change", function() {
		$('#propGraph').block({'message':'Processing...'});
		
		$.post(globals.ajaxurl + 'solar_cdx.php', {
			'_mode':'graph',
			'_task':'range',
			'spanStr':$("#timeSel").val()
		}, function(d) {
			$("#propGraph").html(d);
			
			$('#propGraph').unblock();
		}, 'html');
	});
	
	$('#propGraph').load(globals.ajaxurl + 'solar_cdx.php', {
		'_mode':'graph',
		'_task':'range',
		'spanStr':7
	});
});