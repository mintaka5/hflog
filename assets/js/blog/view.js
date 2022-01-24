$(function() {
	$("#cfHolder").load(globals.ajaxurl + "blog_comment_form.php", {'pid':pid}, function() {});

	$("#cfSubmit").live("click", function() {
		//console.log(this);
		var formVals = $("#commentForm").serialize();
		
		$.post(globals.ajaxurl + "blog_comment_form.php", formVals, function(d) {
			$("#cfHolder").html(d);
		});
	});
});