$(function() {
	$('#shed').imagesLoaded(function() {
		$('#shed').masonry({
			itemSelector:'.bucket',
			columnWidth: 40
		});
	});
});