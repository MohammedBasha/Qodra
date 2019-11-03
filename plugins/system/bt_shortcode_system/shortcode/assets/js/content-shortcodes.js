/**
 * For content shortcodes
 */
 jQuery(document).ready(function ($) {
	 // Add class when hover column in price table
	 $('.btsc-pricetable .btsc-pricecol ').mouseenter(function(){
		 $('.btsc-pricetable .btsc-pricecol ').removeClass('btsc-pricecol-special');
		 $(this).addClass('btsc-pricecol-special');
	 });
	 
	 $('.bt-tooltip').each(function(){
		 var options = {
				 content: $(this).data('tooltip'),
				 style: {classes: $(this).data('classes')},
				 position: {
					 my: $(this).data('my'),
					 at: $(this).data('at')
				 }
		 }
		 $(this).qtip(options);
	 });
	  
 });