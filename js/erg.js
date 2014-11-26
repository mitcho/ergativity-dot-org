jQuery(function($) {
	function erg_submit() {
		var data = {
			action: 'erg_submit',
			nonce: $('#erg_answers_nonce').val(),
			id: $('.erg_answer').attr('data-post'),
			value: $('.erg_answer:checked').attr('value')
		};

 		console.log(data);
		$.post(WP_AJAX.ajaxurl, data);
	}
	
	$('.erg_answer').change(erg_submit);
})
