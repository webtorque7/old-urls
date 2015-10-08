(function($){

	$.entwine('ss', function($){

		$('select[name=RedirectType]').entwine({
			onadd:function() {
				var self = this;

				setTimeout(function(){
					self.trigger('change');
				}, 50);
			},
			onchange:function() {
				if (this.val() == 'Internal') {
					$('.internal-fields').show();
					$('.custom-fields').hide();
				} else {
					$('.internal-fields').hide();
					$('.custom-fields').show();
				}
			}
		});
	});

})(jQuery);