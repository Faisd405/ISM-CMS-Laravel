var offset = 79;
var offsetChange = 0;

$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
		if ($(window).width() < 992) {
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top - offsetChange
			}, 1000, 'easeInOutExpo');
		} else {
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top - offset
			}, 1000, 'easeInOutExpo');	
		}
        event.preventDefault();
    });
});

$( window ).resize(function() {
	if ($(window).width() < 991) {
	   	$('body').attr('data-offset','-1');
	} else {
		$('body').attr('data-offset','80')	
	}
});