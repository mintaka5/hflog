var logMap = false;
var centerCoord = new google.maps.LatLng(38.059, -95.579);
var markerBounds = new google.maps.LatLngBounds();
var markerAry = [];
var infoWin = new google.maps.InfoWindow();

$(function() {
	logMap = new google.maps.Map(document.getElementById('logMap'), {
		'zoom':3,
		'center': centerCoord,
		'mapTypeId': google.maps.MapTypeId.TERRAIN,
		'mapTypeControlOptions':{
			'mapTypeIds':[
							google.maps.MapTypeId.HYBRID,
							google.maps.MapTypeId.ROADMAP,
							google.maps.MapTypeId.SATELLITE,
							google.maps.MapTypeId.TERRAIN
							]
		},
		'zoomControlOptions':{
			'style':google.maps.ZoomControlStyle.SMALL
		},
		'scaleControl':true,
		'scaleControlOptions':{},
		'streetViewControl':false
	});
	
	$.get(globals.ajaxurl + 'hflog_map_default.php', {}, function(d) {
		if($('.locLat').length > 0) {
			$('.locLat').each(function(i, v) {
				var stnPos = new google.maps.LatLng($(v).val(), $(v).siblings('.locLng').val());
				var stnIcon = {
					'url':globals.relurl + 'assets/images/maps/markers/antenna/mobilephonetower3.png',
					'scaledSize':new google.maps.Size(16, 18),
					'size':new google.maps.Size(16, 18)
				};
				var stnMarker = new google.maps.Marker({
					/*'icon':new google.maps.MarkerImage(globals.relurl + 'assets/images/maps/markers/antenna/mobilephonetower3.png')*/
					'icon':stnIcon,
					'map':logMap,
					'position':stnPos,
					'zIndex':(i*2)
				});
				
				google.maps.event.addListener(stnMarker, 'click', function() {
					infoWin.setContent(
							'<h3>' + $(v).siblings('a').text() + '</h3>' +
							'<div>' + $($(v).parent().siblings('td')[2]).html() + '</div>'
					);
					infoWin.open(logMap, stnMarker);
				});
				
				markerBounds.extend(stnPos);
			});
			
			logMap.fitBounds(markerBounds);
		}
	}, 'json');
});