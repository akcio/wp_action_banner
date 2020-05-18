jQuery(document).ready(function( $ ) {
	$(".action-banner > div:gt(0)").hide();

	setInterval(function() { 
	  $('.action-banner > div:first')
	    .fadeOut(1000)
	    .next()
	    .fadeIn(1000)
	    .end()
	    .appendTo('.action-banner');
	},  3000);
});