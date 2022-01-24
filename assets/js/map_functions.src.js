function createMarker(map, ary, coords, title) {
	var marker = new google.maps.Marker({
		'position':coords,
		'title':title,
		'map':map
	});
	
	ary.push(marker);
}

function clearOverlays(ary) {
	if(ary.length > 0) {
		for(i in ary) {
			// console.log(i);
			ary[i].setMap(null);
		}
	}
}