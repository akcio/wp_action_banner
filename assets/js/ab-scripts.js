jQuery(document).ready(function( $ ) {
	if ($(".action-banner").children().length > 1) {
		$(".action-banner > div:gt(0)").hide();

		setInterval(function() { 
		  $('.action-banner > div:first')
		    .fadeOut(500)
		    .next()
		    .fadeIn(500)
		    .end()
		    .appendTo('.action-banner');
		},  5000);
	}
});