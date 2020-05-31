jQuery(document).ready(function( $ ) {
	if ($(".action-banner").children().length > 1) {
		$(".action-banner > div:eq(0)").show();

		setInterval(function() { 
		  $('.action-banner > div:first')
		    .fadeOut(500)
		    .next()
		    .fadeIn(500)
		    .end()
		    .appendTo('.action-banner');
		},  abSlideTimeout);
	}
});