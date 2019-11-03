jQuery.noConflict();
window.addEvent("domready", function() {
	jQuery('form[name=adminForm]').fadeIn('fast');
	var parent = 'li:first';
	if(jQuery(".row-fluid").length){
		parent = '.control-group:first';
	}
    jQuery("#jform_params_ajax-lbl").parent().hide();
    jQuery("#jform_params_asset-lbl").parents(parent).remove();
    jQuery("#jform_asset-lbl").parents(parent).remove();
	jQuery('li > .btn-group').each(function(){
		if(jQuery(".row-fluid").length) return;
		if(jQuery(this).find('input').length != 2 ) return;
		jQuery(this).hide();
		var group = this;
		var el = jQuery(group).find('input:checked');	
		var switchClass ='';

		if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
			switchClass = 'no';
		}else{
			switchClass = 'yes';
		}
		var switcher = new Element('div',{'class' : 'switcher-'+switchClass});
		switcher.inject(group, 'after');
		switcher.addEvent("click", function(){
			var el = jQuery(group).find('input:checked');	
			if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
				switcher.setProperty('class','switcher-yes');
			}else {
				switcher.setProperty('class','switcher-no');
			}
			jQuery(group).find('input:not(:checked)').attr('checked',true).trigger('click');
		});
	})
	jQuery(window).load(function() {
		jQuery(".mceEditor").css("clear", "left").css('display', 'block');
		jQuery("#editor-xtd-buttons").parent().css("overflow", "hidden")
	});
	jQuery('.bt_color').ColorPicker({ color : '0000ff',
		onShow : function(colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		}, onHide : function(colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		}, onSubmit : function(hsb, hex, rgb, el) {
			jQuery(el).val(hex);
			//jQuery(el).val("#" + hex);
			// jQuery(el).css('background',jQuery(el).val())
			jQuery(el).ColorPickerHide();
		}, onBeforeShow : function() {
			jQuery(this).ColorPickerSetColor(this.value);
		} }).bind('keyup', function() {
		jQuery(this).ColorPickerSetColor(this.value);
	});
	jQuery(".adminformlist textarea").parent().css('overflow', 'hidden');
	jQuery(".adminformlist select,.config-option-list select").each(function() {

		if (jQuery(this).is(":visible")) {
			jQuery(this).css("width",parseInt(jQuery(this).width())+30);
			jQuery(this).chosen();
		};
	});
	jQuery(".adminformlist li > input[type=hidden]").each(function() {
		if(jQuery(this).parent().find('.readonly').length ==0 && jQuery(this).parent().find('textarea').length ==0)
		jQuery(this).parent().hide();
			
	});
	jQuery(".chzn-container").click(function() {
		jQuery(".panel .pane-slider,.panel .panelform")
				.css("overflow", "visible");
	});
	jQuery(".panel .title").click(function() {
		jQuery(".panel .pane-slider,.panel .panelform")
				.css("overflow", "hidden");
	});
	
	// Group element
	jQuery(".bt_control").each(function(){
		var control = this;
		if(jQuery(control).parents(parent).css('display')=='none' ) return;
		
		// select box
		var name = this.id.replace('jform_params_','');
		name = this.id.replace('jform_','');
		jQuery(control).change(function(){
			jQuery(this).find('option').each(function(){
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).hide();
				})
			})
			jQuery('.'+name+'_'+jQuery(this).find('option:selected').val()).each(function(){
				jQuery(this).parents(parent).fadeIn(500);
			})
		});
		jQuery(control).change();
		
		// radio box
		jQuery(control).find('input').each(function(){
			if(jQuery(this).is(':not(:checked)')){ 
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).hide();
				});
			}
			jQuery(this).click(function(){
				jQuery(this).siblings().each(function(){
					jQuery('.'+name+'_'+this.value).each(function(){
						jQuery(this).parents(parent).fadeOut('fast');
					});
				})
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).fadeIn(500);
				});
			})
		})
		
	});
});
