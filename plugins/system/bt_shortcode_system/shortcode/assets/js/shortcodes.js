	
jQuery(document).ready(function ($) {
	// Tabs
	$('body:not(.bt-other-shortcodes-loaded)').on('click', '.bt-tabs-nav span', function (e) {
		var $tab = $(this),
			index = $tab.index(),
			is_disabled = $tab.hasClass('bt-tabs-disabled'),
			$tabs = $tab.parent('.bt-tabs-nav').children('span'),
			$panes = $tab.parents('.bt-tabs').find('.bt-tabs-pane'),
			$gmaps = $panes.eq(index).find('.bt-gmap:not(.bt-gmap-reloaded)');
		// Check tab is not disabled
		if (is_disabled) return false;
		// Hide all panes, show selected pane
		$panes.hide().eq(index).show();
		// Disable all tabs, enable selected tab
		$tabs.removeClass('bt-tabs-current').eq(index).addClass('bt-tabs-current');
		// Reload gmaps
		if ($gmaps.length > 0) $gmaps.each(function () {
			var $iframe = $(this).find('iframe:first');
			$(this).addClass('bt-gmap-reloaded');
			$iframe.attr('src', $iframe.attr('src'));
		});
		// Set height for vertical tabs
		tabs_height();
		e.preventDefault();
	});

	// Activate tabs
	$('.bt-tabs').each(function () {
		var active = parseInt($(this).data('active')) - 1;
		$(this).children('.bt-tabs-nav').children('span').eq(active).trigger('click');
		tabs_height();
	});

	// Activate anchor nav for tabs and spoilers
	anchor_nav();
	
	function tabs_height() {
		$('.bt-tabs-vertical, .bt-tabs-vertical-right').each(function () {
			var $tabs = $(this),
				$nav = $tabs.children('.bt-tabs-nav'),
				$panes = $tabs.find('.bt-tabs-pane'),
				height = 0;
			$panes.css('min-height', $nav.outerHeight(true));
		});
	}
	function anchor_nav() {
		// Check hash
		if (document.location.hash === '') return;
		// Go through tabs
		$('.bt-tabs-nav span[data-anchor]').each(function () {
			if ('#' + $(this).data('anchor') === document.location.hash) {
				var $tabs = $(this).parents('.bt-tabs'),
					bar = ($('#wpadminbar').length > 0) ? 28 : 0;
				// Activate tab
				$(this).trigger('click');
				// Scroll-in tabs container
				window.setTimeout(function () {
					$(window).scrollTop($tabs.offset().top - bar - 10);
				}, 100);
			}
		});
		// Go through spoilers
		$('.bt-spoiler[data-anchor]').each(function () {
			if ('#' + $(this).data('anchor') === document.location.hash) {
				var $spoiler = $(this),
					bar = ($('#wpadminbar').length > 0) ? 28 : 0;
				// Activate tab
				if ($spoiler.hasClass('bt-spoiler-closed')) $spoiler.find('.bt-spoiler-title:first').trigger('click');
				// Scroll-in tabs container
				window.setTimeout(function () {
					$(window).scrollTop($spoiler.offset().top - bar - 10);
				}, 100);
			}
		});
	}

	if ('onhashchange' in window) $(window).on('hashchange', anchor_nav);
	
	
	$('body').addClass('bt-other-shortcodes-loaded');
	
	/**
	 * Accordion
	 */
	$('.bt-accordion .bt-spoiler-content').hide();
	$('.bt-accordion').each(function(){
		if($(this).data('active-first') == 'yes'){
			$(this).find('.bt-spoiler').eq(0).addClass('bt-spoiler-opened').children('.bt-spoiler-content').show();
		}
	});
	$('.bt-spoiler-title').click(function(e){
		var $spoiler = $(this).parent();
		var $accordion = $spoiler.parent();
		$accordion.find('.bt-spoiler-opened .bt-spoiler-content').stop();
		if($spoiler.hasClass('bt-spoiler-opened')){
		
			$spoiler.removeClass('bt-spoiler-opened');
			$(this).next('.bt-spoiler-content').slideUp(300);
			
		}else{
			$accordion.find('.bt-spoiler-opened .bt-spoiler-content').slideUp(300);
			$accordion.find('.bt-spoiler').removeClass('bt-spoiler-opened');
			$spoiler.addClass('bt-spoiler-opened');
			$(this).next('.bt-spoiler-content').slideDown(300);
		}
		
	});
	
});