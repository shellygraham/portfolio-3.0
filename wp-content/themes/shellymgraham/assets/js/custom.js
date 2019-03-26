jQuery(document).ready(function($){

	var galleryNum = $(".gallery-container .item").length;

	// Work single gallery/slider
	$('.slider-for').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.slider-nav',
		adaptiveHeight: true
	});

	$('.slider-nav').slick({
		slidesToShow: galleryNum,
		slidesToScroll: 1,
		arrows: false,
		asNavFor: '.slider-for',
		centerMode: true,
		focusOnSelect: true,
	});

});