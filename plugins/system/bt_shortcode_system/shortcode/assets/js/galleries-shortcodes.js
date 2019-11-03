 jQuery(document).ready(function ($) {
	// Enable sliders
	
	$('.btsc-carousel').each(function(){
		
		$(this).owlCarousel({
			items : $(this).data('item-per-page'),
			autoPlay : $(this).data('autoplay'),
			slideSpeed : $(this).data('slide-speed'),
			navigation : $(this).data('navigation'),
			scrollPerPage : $(this).data('scroll') == 'page',
			pagination : $(this).data('pagination') != false,
			paginationNumbers : $(this).data('pagination') == 'number',
			navigationText: ['',''],
			lazyLoad: true
		});
	});
	
	$('.btsc-slider').each(function(){
		
		$(this).owlCarousel({
			singleItem: true,
			transitionStyle : 'fade',
			autoPlay : $(this).data('autoplay'),
			slideSpeed : $(this).data('slide-speed'),
			navigation : $(this).data('navigation'),
			pagination : $(this).data('pagination') != false,
			paginationNumbers : $(this).data('pagination') == 'number',
			navigationText: ['',''],
			lazyLoad: true
		});
	});
 });