$(function() {
	$("#stnWin").dialog({
		'autoOpen':false,
		'modal':true,
		'create':function(evt, ui) {
			// load up form.
		},
		'title':'Station/Location Assignment'
	});
	
	$("#assignStn").click(function() {
		$("#stnWin").dialog("open");
	});
	
	$.post(globals.ajaxurl + 'location_selects.php', {'stnId': $('#stnId').val()}, function(d) {
		$(d.result).each(function(i, v) {
			$('<option />').val(v.id).html(v.site + ' ' + v.time_slot).appendTo('#location');
		});
	}, 'json');
	
	$('#location').change(function() {
		// console.log($(this).val());
	});
});