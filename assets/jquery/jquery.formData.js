(function($) {
	$.fn.formData = function() {
		var data = {};
		
		var $this = this;
		
		$this.find("input, textarea, select").each(function(i) {
			$(data).data($(this).attr("name"), $(this).val());
		});
		
		return data;
	};
})(jQuery);