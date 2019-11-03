/*
** Author: hungtx@vsmarttech.com
** Website: bowthemes.com 
** Version: 1.0
** License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
** Base on slidesjs.com */

(function($){
	$.fn.btslidersOption = {
		start:1,
		autoPlay:true,
		hoverPause: true,
		easing: 'easeInQuad',
		captionEasing: 'easeInOutSine',
		slideSpeed: 500,
		captionSpeed:350,
		interval:3000,
		effect:'slide' // slide or fade
	};
	$.fn.btsliders = function( option ) {
		option = $.extend( {}, $.fn.btslidersOption, option);
		return this.each(function(){
			var wrapper = $(this);
			$(wrapper).bind("dragstart", function(event, ui){
			  return false;//edited
			});
			var sliders = $('.bt-window',wrapper);
			var navigation = wrapper.find('.bt-nav');
			var navPipe =  $('.bt-navpipe',wrapper);
			var caption = wrapper.find('.bt-caption');
			var img = new Image();
			var start = option.start-1, next = 0,preview = 0, current = 0,total = sliders.children().size(),direction,move,playing = false,intervalId,navTimeoutId,widthView,naviPosition,slidersPosition;
			$(img).load(function(){
				startSlider();
			}).error(function () {
				alert('invalid image');
			}).attr('src',sliders.find('img:last').attr('src'));

			function startSlider(){
				$B('.pmshow').show();
				widthView = wrapper.width();
				// set height				
				current = start;
				sliders.height(sliders.find('.bt-slide:first').outerHeight());
				if (sliders.children(':eq('+ next +')').find('.embedvideo').is(':hidden') ) {
							$('.bt-caption').css('bottom','40px');
						}
						else{
						$('.bt-caption').css('bottom','0px');
				}
				if(option.effect == 'fade'){
					sliders.children().css({
						display:'none'
					})
				}
				sliders.children(':eq(' + start + ')').fadeIn(option.slideSpeed, option.easing, function(){
				$(this).css({
					zIndex: 5
					});
					$(caption.get(current)).slideDown(option.captionSpeed,option.captionEasing);
				});
				
				
				$(navigation.get(current)).addClass('active');
				$(navigation[current]).append('<div class="navicon"></div>');
				wrapper.find('.next').click(function(){
					if (option.autoPlay) {	 pause();	}
					animate('next');
				})
				wrapper.find('.prev').click(function(){
					if (option.autoPlay) {	 pause();	}
					animate('prev');
				});
				navigation.click(function(){
					if (option.autoPlay) { pause();}
					animate($(this).index());
				})
				$(window).resize(function(){
					sliders.height(sliders.find('.bt-slide').eq(current).outerHeight());
					naviPosition = $('.bt-navpipe',wrapper).position().left;
					slidersPosition = sliders.position().left;
					widthView = wrapper.width();
				});
				if (option.hoverPause && option.autoPlay) {
					wrapper.bind('mouseover',function(){
						stop();
					});
					wrapper.bind('mouseleave',function(){
						pause();
					});
				}
				if (option.autoPlay){
					intervalId = setInterval(function(){
					animate('next');
					}, option.interval);
					sliders.data('intervalId',intervalId);
				}
				
				//populate navigation
				var maxHeightNav = Math.max.apply(null, $('.bt-nav',navPipe).map(function (){return $(this).outerHeight();}).get());
				navPipe.css({
					width:$(navigation.get(0)).outerWidth(true)* navigation.size()
				});
				//navPipe.children().css({height:maxHeightNav});
				//navPipe.parent().css({height:maxHeightNav});
				
				/* base on skitter slideshow http://thiagosf.net */
				navPipe.hover(function(){
					$(this).addClass('mhover');
				},function(){
					$(this).removeClass('mhover');
				})	
				navPipe.mousemove(function(e){
					if (navPipe.width() > navPipe.parent().width()){ 
					var navWidth= $(navigation.get(0)).outerWidth(true);
					var pipeWidth = navPipe.width();
					var viewWidth = navPipe.parent().width();
					var novo_width,x_value = 0,	y_value = wrapper.offset().top;
					var width_value = viewWidth - navWidth;
					x_value = wrapper.offset().left+navWidth;
					var x = e.pageX, y = e.pageY, new_x = 0;
					x = x - x_value;
					y = y - y_value;
					novo_width = pipeWidth - width_value;
					new_x = -((novo_width * x) / width_value);
					
					if (new_x > 0) new_x = 0;
					if (new_x < -(pipeWidth - viewWidth)) new_x = -(pipeWidth - viewWidth);
					navPipe.css({left: new_x});
					}
				});		
			}
			function animate(direction){
				if(playing || direction == current){
					return false;	
				}
				playing = true;
				position = '0%';								
				move = '0%';
				
				switch(direction){
				case 'next':
					prev = current;
					next = current + 1;
					next = total === next ? 0 : next;
					position = '66.66%';
					move = '-200%';
					current = next;
					break;
				case 'prev':
					prev = current;
					next = current - 1;
					next = next === -1 ? total-1 : next;									
					current = next;
					break;
				default:
					next = direction
					prev = current;
					current = next;
					if (next > prev){
						position = '66.66%';
						move = '-200%';
					}
					break;
				}
					sliders.children(':eq('+ next +')').find('.bt-video').html(sliders.children(':eq('+ next +')').find('.embedvideo').val());
					if (sliders.children(':eq('+ next +')').find('.embedvideo').is(':hidden') ) {
							$('.bt-caption').css('bottom','40px');
						}
						else{
						$('.bt-caption').css('bottom','0px');
					}
					$(caption.get(prev)).hide();
					if(option.effect == 'slide'){
					if($('.bt-slide').length > 1){
						sliders.children(':eq('+ next +')').css({
								left: position,
								display: 'block'
						});
						sliders.animate({
							left: move,
							height: sliders.children(':eq('+ next +')').outerHeight()
						},option.slideSpeed, option.easing, function(){
							// after animation reset control position
							sliders.css({
								left: '-100%'
							});
							// reset and show next
							sliders.children(':eq('+ next +')').css({
								left: '33.33%',
								zIndex: 5
							});
							// reset previous slide
							sliders.children(':eq('+ prev +')').css({
								left: '33.33%',
								display: 'none',
								zIndex: 0
							});
							sliders.children(':eq('+ prev +')').find('.bt-video').html('');
							if($(caption[next]).length){
								setTimeout(function(){ $(caption[next]).slideDown(option.captionSpeed,option.captionEasing,function(){playing=false;}) },option.captionSpeed);
							}else{
								playing=false;
							}
							
						});
						}
					}
					else
					{
					if($('.bt-slide').length > 1){
						sliders.children(':eq('+ next +')').css({
							zIndex: 5
						}).fadeIn(option.slideSpeed, option.easing, function(){
							sliders.animate({
							height: sliders.children(':eq('+ next +')').outerHeight()
						}, 400, function(){
							sliders.children(':eq('+ prev +')').css({
								display: 'none',
								zIndex: 0
							});								
							sliders.children(':eq('+ next +')').css({
								zIndex: 0
							});									
							sliders.children(':eq('+ prev +')').find('.bt-video').html('');
							if($(caption[next]).length){
								setTimeout(function(){ $(caption[next]).slideDown(option.captionSpeed,option.captionEasing,function(){playing=false;}) },option.captionSpeed);
							}else{
								playing=false;
							}
						});
							 
						});
					}
				 }
				changeNavigation();
			
			}
			function changeNavigation(){
				navigation.removeClass('active');
				navigation[current].addClass('active');
				$('.navicon').remove();
				$(navigation[current]).append('<div class="navicon"></div>');
				if (navPipe.width() > navPipe.parent().width() && !navPipe.hasClass('mhover')){ 
					var width= $(navigation.get(0)).outerWidth(true);
					var pipeWidth = navPipe.width();
					var viewWidth = navPipe.parent().width();
					var left = $(navigation[current]).position().left;
					var pipeLeft = navPipe.position().left;
					var move;
					if(left + pipeLeft > viewWidth - width){
						move = '-='+width;
					}				
					if(left > pipeWidth - viewWidth+2*width){
						move = -pipeWidth + viewWidth;
					}
					if(left+ pipeLeft < 0){
						move = -left;
					}
					if(left+ pipeWidth < width){
						move = -pipeWidth;
					}
					navPipe.animate({left:move+'px' },'',option.easing);
				}
				
			}
			function stop() {
				clearInterval(sliders.data('intervalId'));
			}
			function pause() {
				if (option.hoverPause) {
					clearInterval(sliders.data('intervalId'));
					intervalId = setInterval(function(){
						animate("next");
					},option.interval);
					sliders.data('intervalId',intervalId);
				}else {
					stop();
				}
			}
			
/*********************************************************
			 * Add touch screen plugin	for slider content			 *
			 * @author chinhpv@vsmarttech.com						 *
			 *********************************************************/
			var hammer = new Hammer($('.bt-sliders',wrapper)[0],
										{prevent_default: true,
										not_prevent_tags:
										{
											id			: [],
											className	: [],
											tagName 	: ['A']
											}
										});
			// get position of sliders
			slidersPosition = sliders.position().left;
			var nextTemp, prevTemp;
			var windowPosition = $('html,body').offset().top;
			$(window).scroll(function(){
				windowPosition = $('html,body').offset().top;
			});
			// on drag function
			hammer.ondrag = function (ev){
				if(current == total-1){
					nextTemp = 0;
					prevTemp = total -2;
				}else if(current == 0 ){
					nextTemp = 1;
					prevTemp = total -1;
				}else {
					nextTemp = current + 1;
					prevTemp = current - 1;
				}
				sliders.children(':eq('+ nextTemp +')').css({
					left: 2*widthView,
					display: 'block'
				});
				sliders.children(':eq('+ prevTemp +')').css({
					left: 0,
					display: 'block'
				});
				$(caption.get(prevTemp)).show();
				$(caption.get(nextTemp)).show();
				if(ev.direction =='left'){      // next
					sliders.css('left', slidersPosition - ev.distance);
				}
				if(ev.direction == 'right'){   // prev
					sliders.css('left', slidersPosition + ev.distance);
				}
				
			}
			// on end drag function 
			hammer.ondragend = function (ev){
				if(ev.distance > 100){
					if(ev.direction == 'left'){
						prev = current;
						next = current + 1;
						next = total === next ? 0 : next;
						position = 2*widthView;
						move = -2*widthView;
						current = next;
						sliders.children(':eq('+ prevTemp +')').css({
							left: widthView,
							display: 'none',
							zIndex: 0
						});
					}
					if(ev.direction == 'right'){
						prev = current;
						next = current - 1;
						next = next === -1 ? total-1 : next;									
						current = next;
						move = 0;
						sliders.children(':eq('+ nextTemp +')').css({
							left: widthView,
							display: 'none',
							zIndex: 0
						});
					}
					if(ev.direction == 'right' || ev.direction == 'left'){
						sliders.children(':eq('+ next +')').find('.bt-video').html(sliders.children(':eq('+ next +')').find('.embedvideo').val());
						if (sliders.children(':eq('+ next +')').find('.embedvideo').is(':hidden') ) {
								$('.bt-caption').css('bottom','40px');
							}
							else{
							$('.bt-caption').css('bottom','0px');
						}
						sliders.animate({
							left: move,
							height: sliders.children(':eq('+ next +')').outerHeight()
						},option.slideSpeed, option.easing, function(){
							// after animation reset control position
							sliders.css({
								left: - widthView
							});
							// reset and show next
							sliders.children(':eq('+ next +')').css({
								left: widthView,
								zIndex: 5
							});
							// reset previous slide
							sliders.children(':eq('+ prev +')').css({
								left: widthView,
								display: 'none',
								zIndex: 0
							});
							changeNavigation();
							sliders.children(':eq('+ prev +')').find('.bt-video').html('');
							slidersPosition = sliders.position().left;
						});
					}
				}else{
					sliders.animate({left: slidersPosition}, 250);
				}
				windowPosition = $('html,body').offset().top;
		}
		//swipe function 
		hammer.onswipe = function(ev){
				if(ev.direction == 'left'){
					prev = current;
					next = current + 1;
					next = total === next ? 0 : next;
					position = 2*widthView;
					move = - 2*widthView;
					current = next;
					sliders.children(':eq('+ prevTemp +')').css({
						left: widthView,
						display: 'none',
						zIndex: 0
					});
				}
				if(ev.direction == 'right'){
					prev = current;
					next = current - 1;
					next = next === -1 ? total-1 : next;									
					current = next;
					move = 0;
					sliders.children(':eq('+ nextTemp +')').css({
						left: widthView,
						display: 'none',
						zIndex: 0
					});
				}
				if(ev.direction == 'up'){
					$('html,body').animate({scrollTop: -windowPosition + 500},500);
				}
				if(ev.direction == 'down'){
					$('html,body').animate({scrollTop: -windowPosition - 500},500);
				}
				if(ev.direction == 'right' || ev.direction == 'left'){
					sliders.children(':eq('+ next +')').find('.bt-video').html(sliders.children(':eq('+ next +')').find('.embedvideo').val());
					if (sliders.children(':eq('+ next +')').find('.embedvideo').is(':hidden') ) {
							$('.bt-caption').css('bottom','40px');
						}
						else{
						$('.bt-caption').css('bottom','0px');
					}
					sliders.animate({
						left: move,
						height: sliders.children(':eq('+ next +')').outerHeight()
					},option.slideSpeed, option.easing, function(){
						// after animation reset control position
						sliders.css({
							left: - widthView
						});
						// reset and show next
						sliders.children(':eq('+ next +')').css({
							left: widthView,
							zIndex: 5
						});
						// reset previous slide
						sliders.children(':eq('+ prev +')').css({
							left: widthView,
							display: 'none',
							zIndex: 0
						});
						
						changeNavigation();
						sliders.children(':eq('+ prev +')').find('.bt-video').html('');
						slidersPosition = sliders.position().left;
					});
				}
			}
		
			/*********************************************************
			 * Add touch screen plugin	for navigation				 *
			 * @author chinhpv@vsmarttech.com						 *
			 *********************************************************/
		
			var naviHammer = new Hammer($('.bt-footernav',wrapper)[0],{prevent_default : true});
			naviPosition = $('.bt-navpipe',wrapper).position().left;
			// on drag function 
			naviHammer.ondrag = function (ev){
				if (navPipe.width() > navPipe.parent().width()){ 
					if(ev.direction == 'left'){
						//if($('.bt-navpipe',wrapper).position().left > $('.bt-footernav').width() - $('.bt-navpipe',wrapper).width() - 100){
							$('.bt-navpipe',wrapper).css('left', naviPosition - ev.distance);
						//}
					}
					if(ev.direction == 'right'){
						//if($('.bt-navpipe',wrapper).position().left <   100)
						$('.bt-navpipe',wrapper).css('left', naviPosition + ev.distance);
					}
				}
			}
			// on end drag function 
			naviHammer.ondragend = function(ev){
				if(ev.direction == 'left'){
					if($('.bt-navpipe',wrapper).position().left < $('.bt-footernav').width() - $('.bt-navpipe',wrapper).width()){
						$('.bt-navpipe',wrapper).animate({left:$('.bt-footernav').width() - $('.bt-navpipe',wrapper).width()},250,function(){
							naviPosition = $('.bt-navpipe',wrapper).position().left;
						});
					}
				}
				if(ev.direction == 'right'){
					if($('.bt-navpipe',wrapper).position().left > 0)
						$('.bt-navpipe',wrapper).animate({left: 0}, 250, function(){
							naviPosition = $('.bt-navpipe',wrapper).position().left;
						});
				}
				naviPosition = $('.bt-navpipe',wrapper).position().left;
				
			}
			// on swipe function 
			naviHammer.onswipe = function(ev){
				if (navPipe.width() > navPipe.parent().width()){ 
					if(ev.direction == 'left'){
						$('.bt-navpipe',wrapper).animate({left:naviPosition -300},250,function(){
							naviPosition = $('.bt-navpipe',wrapper).position().left;
						});
						if($('.bt-navpipe',wrapper).position().left < $('.bt-footernav').width() - $('.bt-navpipe',wrapper).width()){
							$('.bt-navpipe',wrapper).animate({left:$('.bt-footernav').width() - $('.bt-navpipe',wrapper).width()},300,function(){
								naviPosition = $('.bt-navpipe',wrapper).position().left;
							});
						}
					}
					if(ev.direction == 'right'){
						$('.bt-navpipe',wrapper).animate({left:naviPosition + 300},250,function(){
							naviPosition = $('.bt-navpipe',wrapper).position().left;
						});
						if($('.bt-navpipe',wrapper).position().left > 0)
							$('.bt-navpipe',wrapper).animate({left: 0}, 300, function(){
								naviPosition = $('.bt-navpipe',wrapper).position().left;
							});
					}
					naviPosition = $('.bt-navpipe',wrapper).position().left;
				}
			}
			// on tap function 
			naviHammer.ontap = function(ev){
				target = $(ev.originalEvent.target);
				if(target.attr('class') != 'bt-nav'){
					target = target.parents('.bt-nav');
				}
				if(target.length){
					if (option.autoPlay) { pause();}
					animate(target.index());
				}
			}
		});
	};	
})(jQuery);


$B=jQuery.noConflict();
$B(document).ready(function () {
$B( ".bt-thumb" ).each(function() {
 $B(this).hover(function(){
		jQuery(this).append('<div class="tooltipthumb"><p>'+$B(this).attr('rel')+'</p></div>');		
		var tooltip = jQuery('.tooltipthumb', this);
		var left = (jQuery(this).width() - tooltip.width())/ 2;
		tooltip.css({'left' : left + 'px', 'top' : (-1 * tooltip.height() - 14) + 'px' });
		//jQuery('.tooltipthumb:before', this).css({'left': ((tooltip.width() - 12) ) + 'px'});
	}, function () {
		$B("div.tooltipthumb").remove();
	});
});
	$B('img.hovereffect').hover(function () {
		$B(this).stop(true).animate({
			opacity : 0.5
		}, 300);
	}, function () {
		$B(this).animate({
			opacity : 1
		}, 300)
	});
	
});
