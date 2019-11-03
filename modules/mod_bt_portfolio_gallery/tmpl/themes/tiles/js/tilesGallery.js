(function( $ ){
	
	var TilesGalleryPrototype = {
		init: function(options, element){
			var self = this;
            self.element = $(element);
            self.options = $.extend({}, $.fn.tilesGallery.options, self.element.data(), options);
			self.tiles = self.element.find('.tile');
			if(self.options.lazyLoad){
				self.lazyLoad();
			}else{
				self.start();
			}
		},
		
		lazyLoad: function(){
			var self = this;
			var numberOfTiles = self.tiles.size();
			var count =0;
			self.tiles.find('.tile-image img').hide();
			var loadingDiv = $('<div>').addClass('tiles-gallery-loading');
			self.element.append(loadingDiv);
			self.tiles.each(function(){
				var img = new Image();
				$(img).attr('src', $(this).find('.tile-image img').attr('src'));
				$(img).load(function(){
					count++;
					if(count >= numberOfTiles){
						self.tiles.find('.tile-image img').fadeIn(300);
						self.element.find('.tiles-gallery-loading').remove();
						self.start();
					}
				});
			});
		},
		
		start: function(){
			var self = this;
			self.initLayout(); 
			self.tiles.click(function(){
				var tileClone = $(this).clone();
				
				var position = self.getTilePosition($(this));
				tileClone.css({position: 'absolute', top: position.top, left: position.left, 'z-index' : 1}).addClass('tile-clone');
				self.element.append(tileClone);
				
				tileClone.animate({
						left: 0,
						top: 0,
						width: self.element.width(),
						height: self.element.height(),
					}, 
					self.options.animationSpeed,
					function(){
						$(this).find('.tile-caption').fadeIn(300, function(){
							tileClone.mouseleave(function(){
								$(this).find('.tile-caption').fadeOut(300, function(){
									tileClone.animate({
										left: position.left,
										top: position.top,
										width: self.tileSize,
										height: self.tileSize
									},
									self.options.animationSpeed, 
									function(){
										$(this).remove();
									});
								});
							}); 
						}); 
						
					}
				
				);
				self.tileTimeout(position);
				
			});
			
			$(window).resize(function(){
				if(self.options.width && self.element.parent().width() < self.options.width){
					self.options.width = 	self.element.parent().width();
				}
				self.initLayout();
			});
		},
		initLayout : function(){
			var self = this;
			if(self.options.width){
				self.element.width(self.options.width);
			}
			self.tileSize = Math.floor((self.element.width() - (self.options.columns - 1) * self.options.margin) / self.options.columns);
			self.tiles.width(self.tileSize).height(self.tileSize).css({'margin-left': self.options.margin, 'margin-top' : self.options.margin});
			self.element.height(self.tileSize * self.options.rows + (self.options.rows - 1) * self.options.margin);
			self.element.find('.tiles-wrapper').css({
				'margin-top': -1 * self.options.margin, 
				'margin-left': -1 * self.options.margin,
				'width' : self.element.width() + self.options.margin,
				'height' : self.element.height() + self.options.margin,
			});	
			
		},
		
		tileTimeout : function(position){
			var self = this;
			self.element.mouseleave(function(){
				setTimeout(function(){
					var tileClone = self.element.find('.tile-clone');
					if(tileClone.size() > 0){
						tileClone.find('.tile-caption').fadeOut(300, function(){
							tileClone.animate({
								left: position.left,
								top: position.top,
								width: self.tileSize,
								height: self.tileSize
							},
							self.options.animationSpeed, 
							function(){
								$(this).remove();
							});
						});
					}
				}, self.options.maxTime);
			});
		},
		
		
		getTilePosition: function(tile){
			var self = this;
			var elementOffset = self.element.offset();
			
			var tileOffset = tile.offset();
			return {top: tileOffset.top - elementOffset.top, left: tileOffset.left - elementOffset.left};
		}
		
	}
	
	
	
	function TilesGallery (){}
	TilesGallery.prototype = TilesGalleryPrototype;
		
	$.fn.tilesGallery = function(options) {
		return this.each(function () {
            if ($(this).data("tg-init") === true) {
                return false;
            }
            $(this).data("tg-init", true);
            var tilesGallery = new TilesGallery();
            tilesGallery.init(options, this);
            $.data(this, "tilesGallery", tilesGallery);
        });
	}
	$.fn.tilesGallery.options = {
		rows : 2,
		columns: 2,
		margin: 1,
		width: false,
		event: 'click',
		animationSpeed: 500,
		maxTime: 5000,
		lazyLoad: true,
		afterInit: false,
		beforeAnimation: false,
		afterAnimation: false
	}
}(jQuery));