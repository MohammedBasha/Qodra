jQuery.noConflict();
var BTP=jQuery;
var visit_site ='';
BTP(document).ready(function(){
	visit_site = BTP('.visit-site').attr('href');
	if(typeof(skOpt) !== 'undefined'){
		if(BTP('.box_skitter_large img').length){
			BTP('.box_skitter_large').skitter(skOpt);
		}	
	}
	else{
		BTP('.label_text').remove();
	}
	// custom layout
	if(typeof(lbOpt) !== 'undefined'){
		BTP('.zoom-img-list-custom-btp').lightBox(lbOpt);
	}
}); 
jQuery(window).load(function(){
	rebuildHoverIcon();
	jQuery(window).resize(function(){
		rebuildHoverIcon();
	})
	jQuery(".custom-btp-template .btp-item-image")
		.hover(function(){
			jQuery('img',this).stop().animate({ "opacity" : "0.25" }, "slow");
			jQuery("a.img-link-custom-btp",this).css('background-color', '#000');
		}, function(){
			jQuery('img',this).stop().animate({ "opacity" : "1" }, "slow")
	});
});

BT = new Class({
	initialize: function(liveSite) {
		this.liveSite = liveSite;
	}
});
BT.Showcase = new Class({
	initialize: function (liveSite) {
		this.liveSite = liveSite;
	},
	rate: function(portfolioId, rating) {
		var jsonRequest = new Request.JSON({
			url: this.liveSite + 'index.php?option=com_bt_portfolio&format=raw&task=portfolio.rate&id=' + portfolioId + '&rating=' + rating,
			onSuccess: function (responseJSON, responseText){
				if (responseJSON.success) {
					$$('.btp-rating-container-' + portfolioId).each(function(el){
						new Fx.Morph(el.getElement('.btp-rating-current'), {
						    duration: 'long',
						    transition: Fx.Transitions.Sine.easeOut
						}).start({
							width: responseJSON.rating_width + 'px'
						});
						var tmpEl = el.getElement('.btp-rating-notice');
						tmpEl.set('text', responseJSON.rating_text);
					});
				}
				else {
					alert(responseJSON.message);
				}
			},
			onFailure: function(xhr) {
				alert('Unknow Error!!!');
			}
		}).get();
	}
});
function makeLightBox(){
	rebuildHoverIcon();
	if(typeof(lbOpt) == 'undefined') return;
	BTP('.visit-site').attr('href',visit_site);
}
function openModalBox(el,rel){
	SqueezeBox.open(el.href,rel);
	return false;
}
function rebuildHoverIcon(){
	jQuery('.custom-btp-template .btp-item-image').each(function(){
				if(jQuery('.link-div a',this).length==2){
					jQuery('.zoom-img-list-custom-btp',this).css({'left':'auto','right': (jQuery(this).width() / 2 + 3)+ 'px'});
					jQuery('.visit-site',this).css('left', (jQuery(this).width() / 2 + 3	)+ 'px');
				}else{
					jQuery('.link-div a',this).css('left', ((jQuery(this).width() - jQuery('.link-div a',this).width()) / 2)+ 'px');
				}
				jQuery('.link-div a',this).css('top', ((jQuery(this).height() - jQuery('.link-div a',this).height()) / 2)+ 'px');
	});
	var zoom = BTP('.label_skitter .btp-zoom-image');
	var visit = BTP('.label_skitter .visit-site');
	var width = BTP('.container_skitter').width();
	var height = BTP('.box_skitter_large').height()
	if(zoom.length + visit.length == 1){
		BTP('.label_skitter .btp-zoom-image,.label_skitter .visit-site').each(function(){
			BTP(this).css('left',((width-BTP(this).width())/2)+'px');
			BTP(this).css('top',((height-BTP(this).height())/2)+'px');
		});
	}
	if(zoom.length + visit.length == 2){
		visit.css('left',(width/2)+'px');
		zoom.css('top',((height-zoom.height())/2)+'px');
		zoom.css({'left':'auto','right':(width/2)+'px'});	
		visit.css('top',((height-visit.height())/2)+'px');
	}	
}