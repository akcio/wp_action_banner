jQuery(document).ready(function( $ ) {
	$(".action-banner > div:eq(0)").show();
		
	if ($(".action-banner").children().length > 1) {
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