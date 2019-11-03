<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		3.0.3
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
JHtml::_('behavior.modal', 'a.modal');
$path = $this->params->get('images_path','images/bt_portfolio');
JText::script('JGLOBAL_USE_GLOBAL');
?>
<form
    action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="portfolio-form"
    enctype="multipart/form-data" class="form-validate <?php echo (!Bt_portfolioLegacyHelper::isLegacy() ? 'isJ30' : 'isJ25') ?>">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#details">Details</a></li>
		<li><a href="#extrafields" >Extra Fields</a></li>
		<li><a href="#images" >Media</a></li>
		<li><a href="#portfolio-options" >Advanced</a></li>
		<li><a href="#metadata" >Metadata</a></li>
	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane active">
			<div class="width-100 fltlft">
				<fieldset class="adminform">
					<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('details') as $field) : ?>
							<li><?php
						echo $field->label;
						if ($field->type == "Editor")
							echo '<div class="clr"></div>';
						echo $field->input;
							?>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div id="extrafields" class="tab-pane">
			<div class="width-100 fltlft">
				<fieldset class="adminform">
					<ul class="adminformlist">
						<?php
						echo $this->loadTemplate('extrafields');
						?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div id="images" class="tab-pane">
			<div class="width-100 fltlft">
				<fieldset class="adminform">
					<?php
					if ($this->params->get('upload_type', 1) == 1) {
						echo $this->loadTemplate('images_flash');
					} else {
						echo $this->loadTemplate('images_simple');
					}					
					?>
					<?php
					
					$params = new JRegistry();
					if($this->item->params){
						$params->loadArray($this->item->params);
					}
					$thumb_height = $params->get('thumb_height',$this->params->get("thumb_height", 180));
					$thumb_width = $params->get('thumb_width',$this->params->get("thumb_width", 336));
					if ($thumb_height > 100) {
						$thumb_width = floor($thumb_width * 100 / $thumb_height);
						$thumb_height = 100;
					}
					$thumbimgprocess = $this->params->get('thumbimgprocess',$this->params->get("thumbimgprocess", 1));
					?>
					<ul id="sortable">
						<?php
						if (is_array($this->images)) {
						
							foreach ($this->images as $image) {									
								?>
								<li title="Double-click to make default image" <?php if ($image->default) echo 'class="default_image"' ?>>
									<input class="input-default"
									<?php if ($image->default) echo 'checked="checked"' ?>
										   title="Make default" name="default_image" type="radio"
										   value="<?php echo $image->filename ?>" /> <img class="img-thumb"
										   src="<?php echo JURI::root() . $path . '/' . $this->item->id . '/'.($this->params->get('thumbimgprocess',1)==-1?'original':'thumb').'/' . $image->filename; ?>" />
										<input
										 type="hidden" name="image_id[]" value="<?php echo $image->id ?>" />
										<?php if ($image->youembed != ''){ 
											?>
											
												<img class="img_video"  src="../components/com_bt_portfolio/assets/img/video.png" />
											<?php 
											}
										?>
									<input type="hidden" name="image_filename[]"
										   value="<?php echo $image->filename ?>" /> <br />									
									<a href="javascript:void(0)" class="edit" onclick="editImage(this)">Edit</a>
									<a href="javascript:void(0)" class="remove" onclick="removeImage(this)" >Remove</a>
									</li>
									<?php
								}
								
							?>
								<input type="hidden" id="portfolio-hidden" name="video" value="<?php echo   base64_encode(json_encode($this->images)); ?>" />
							<?php
							}
							?>
								
					</ul>
					<a href="#" class="button" onclick="return removeAll()">Clear all</a>
				</fieldset>
				<?php echo JHtml::_('sliders.start', 'module-sliders',array('startOffset'=> -1,'allowAllClose'=>1, 'useCookie'=>1)); ?>
				<?php echo JHtml::_('sliders.panel', JText::_('COM_BT_PORTFOLIO_CONFIG_CUSTOM_SIZE_LABEL'), ''); ?>
				<fieldset class="adminform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset('image-configs') as $field) : ?>
						<li><?php
					echo $field->label;
					if ($field->type == "Editor")
						echo '<div class="clr"></div>';
					echo $field->input;
						?>
						</li>
					<?php endforeach; ?>
					<li><i><?php echo JText::_('COM_BT_PORTFOLIO_CONFIG_CUSTOM_SIZE_DESC'); ?></i></li>
					</ul>
				</fieldset>
				<?php echo JHtml::_('sliders.end'); ?>
					
			</div>
		</div>
		<div id="portfolio-options" class="tab-pane">
			<div class="width-100 fltlft">
				<fieldset class="adminform">
					<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('advanced') as $field) : ?>
							<li class="control-group"><?php
						echo $field->label;
						if ($field->type == "Editor")
							echo '<div class="clr"></div>';
						echo $field->input;
							?>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div id="metadata" class="tab-pane">
			<div class="width-100 fltlft">
				<fieldset class="adminform">
					<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('metadata') as $field) : ?>
							<li class="control-group"><?php
						echo $field->label;
						if ($field->type == "Editor")
							echo '<div class="clr"></div>';
						echo $field->input;
							?>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
		</div>
	</div>    
    <div>        
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" id="portfolio_id" value="<?php echo $this->item->id ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<style>
    .current {
        overflow: hidden;
    }

    #uploading {
        padding-top: 10px;
    }

    a.button {
        float: left;
        margin: 5px 5px 0px 0px;
        padding: 3px 5px;
        width: auto;
        background: none repeat scroll 0 0 #FFFFFF;
        border: 1px solid #CCCCCC;
        text-decoration: none;
    }

    .ui-state-highlight {
        background: #efefef;
    }

    #sortable li {
        float: left;
        margin: 5px;
        padding: 2px;
		width: <?php if($thumbimgprocess!=2) echo $thumb_width; ?>px;
        height: <?php echo $thumb_height+20 ?>px;
		position:relative;
		border: 1px solid #DDDDDD;
		background:#E6E6FA;
    }

    #sortable li.default_image {
        background: yellow;
		display:block;
    }

    #sortable li .img-thumb {
        cursor: move;
        margin: 0;
        padding: 0;
        
    }

    #sortable {
        display: block;
        border: 1px solid #CCCCCC;
        overflow: hidden;
        padding: 10px;
        margin-top: 5px;
    }

    #sortable .input-default {
        position: absolute;
        display: none;
		top:0px;;
		left:0px;
		margin:5px;
		
    }
	
    #sortable .img-thumb {
		width: <?php if($thumbimgprocess!=2) echo $thumb_width; ?>px;
        height: <?php echo $thumb_height ?>px;
        max-width: <?php echo $thumb_width; ?>px;
        float: left;
    }

    #sortable .input-title {
        width: <?php echo $thumb_width - 1; ?>px;
        margin-left: 0px;
		height:15px;
		padding:0;
		margin:2px 0px;
    }

    #sortable .img-delete {
        display: none;
        position: absolute;
		top:0px;
		right:0px;
        cursor: pointer;
		 margin: 5px;
    }
	 #sortable .img_video {        
    cursor: move;
    left: <?php if($thumbimgprocess!=2){ echo ($thumb_width/2-17).'px';} else{echo '40%';}?>; 
    position: absolute;
    top: <?php echo ($thumb_height/2)-13; ?>px;
    
    }
	#sortable a.edit {
	display: block;
	float: left;
	padding: 5px 5px;
	}
	#sortable a.remove {
		display: block;
		float: right;
		padding: 5px 5px;
	}
	#sortable li:hover .img-delete,#sortable li:hover .input-default{
		display:block;
	}
    .loadingExtrafields{
        margin-left:5px;
        font-weight:bold;
    }
</style>
<script type="text/javascript">	
	var BT = BT||{};		
    function removeFile(el){
        jQuery(el).parent().remove();return false;
    }
    function removeImage(el)
    {
        jQuery(el).parent().fadeOut(function(){
            jQuery(this).remove();
        });
    }
	function editImage(el)   {
			
			var youid = jQuery(el).data('youid') ? jQuery(el).data('youid') : '';
			var youdesc = jQuery(el).data('youdesc') ? jQuery(el).data('youdesc') : '';			
			var title = jQuery(el).data('title') ? jQuery(el).data('title') : '';		
			var a = new Element("div",{ id: 'btportfolio-dialog',
			html: '<fieldset class="adminform">' +
                        '<ul class="adminformlist">' +
                        '<li>' +
                        '<label id="btportfolio-title-lbl" class="hasTip" title="<?php echo JText::_('MOD_BTPORTFOLIO_FIELD_TITLE_DESC'); ?>" for="btportfolio-title"><?php echo JText::_('MOD_BTPORTFOLIO_FIELD_TITLE_LABEL'); ?></label>' +
                        '<input id="btportfolio-title" type="text" name="btportfolio-title" size="50"  value="' + title + '"/>' +
                        '</li>' +						
						'<li>'+
							'<label id="btportfolio-youid-lbl" class="hasTip"  for="btportfolio-youid"><?php echo JText::_('MOD_BTPORTFOLIO_FIELD_YOUID_LABEL'); ?></label>'+								
							'<input id="btportfolio-youid" type="text" name="btportfolio-youid" size="50" title="<?php echo JText::_('MOD_BTPORTFOLIO_FIELD_YOUID_TITLE'); ?>" />' +
						'</li>'+
						'<li>'+
							'<label id="btportfolio-youembed-lbl" class="hasTip"  for="btportfolio-youembed"><?php echo JText::_('MOD_BTPORTFOLIO_FIELD_EMBED_LABEL'); ?></label>'+								
							'<textarea id="btportfolio-youembed" cols="20"  name="btportfolio-youembed" style="width: 500px;"></textarea>'+
						'</li>'+
						'<li>'+
							'<label id="btportfolio-youdesc-lbl" class="hasTip"  for="btportfolio-youdesc"><?php echo JText::_('MOD_BTPORTFOLIO_FIELD_DESCRIPTION_LABEL'); ?></label>'+								
							'<textarea id="btportfolio-youdesc" cols="20" rows="3" name="btportfolio-youdesc" style="width: 500px;"></textarea>'+
						'</li>'+
                        '</ul>' +
                        '</fieldset>' +
                        '<button class="btportfolio-dialog-ok" style="margin-left: 10px;"><?php echo JText::_('MOD_BTPORTFOLIO_BTN_OK'); ?></button><button class="btportfolio-dialog-cancel" style="margin-left: 10px;"><?php echo JText::_('MOD_BTPORTFOLIO_BTN_CANCEL'); ?></button>'
                
            });	
			
				a.getElement("#btportfolio-title").set("value",jQuery(el).data('title'));					
				a.getElement("#btportfolio-youid").set("value",jQuery(el).data('youid'));				
				a.getElement("#btportfolio-youembed").set("value",jQuery(el).data('youembed'));				
				a.getElement("#btportfolio-youdesc").set("value",jQuery(el).data('youdesc'));				
				a.getElement(".btportfolio-dialog-ok").addEvent("click",function(){				
					jQuery(el).data('title',a.getElement("#btportfolio-title").get("value"));			
					jQuery(el).data('youid',a.getElement("#btportfolio-youid").get("value"));            
					jQuery(el).data('youembed',a.getElement("#btportfolio-youembed").get("value"));            
					jQuery(el).data('youdesc',a.getElement("#btportfolio-youdesc").get("value"));            
									
                SqueezeBox.close() 
				
            });
            a.getElement(".btportfolio-dialog-cancel").addEvent("click",function(){
                SqueezeBox.close()
            });
            SqueezeBox.open(a,{
                handler:"adopt",
                size:{
                    x:580,
                    y:350
                }
            })
    }
    function removeAll()
    {
        jQuery("#sortable li").fadeOut(function(){
            jQuery(this).remove();
        });
        return false;
    }
    function reNewItem(){
        jQuery('#sortable li').unbind('dblclick');
        jQuery('#sortable li').dblclick(function(){
            var defCheck = jQuery(this).find('.input-default');
            jQuery('#sortable li.default_image').removeClass('default_image');
            defCheck.parent().addClass('default_image');
            jQuery('.input-default').attr('checked',false);
            defCheck.attr('checked',true);

        });
        new Sortables('#sortable', {
            clone: true,
            revert: true,
            opacity: 0.3
        });
        jQuery('#sortable .input-default').unbind('click');
        jQuery('#sortable .input-default').click(function(){
            if (this.checked) {
                jQuery('#sortable li.default_image').removeClass('default_image');
                jQuery(this).parent().addClass('default_image');

            };
        });
    }
    window.addEvent('domready', function(){
        reNewItem();
    });
    jQuery(document).ready(function(){	
		jQuery('.tab-content .tab-pane').hide();
		jQuery('.tab-content .active').show();
        jQuery('.nav-tabs li a').click(function(){
           var li = jQuery(this).parent();
           if(jQuery(li).hasClass('active')) return false;
           var tab = jQuery(this).attr('href');
           jQuery('.nav-tabs li.active').removeClass('active');
           jQuery(li).addClass('active');
           
           jQuery('.tab-pane.active').hide().removeClass('active');
           jQuery(tab).addClass('active').show();
           return false;
        });
		jQuery('#image-configs input').each(function(){
			if(this.value==""){
				this.value = Joomla.JText._('JGLOBAL_USE_GLOBAL','Use global');
			}
		})
		jQuery('#image-configs input').blur(function(){
			if(this.value==""){
				this.value = Joomla.JText._('JGLOBAL_USE_GLOBAL','Use global');
			}
		})
		jQuery('#image-configs input').click(function(){
			if(this.value==Joomla.JText._('JGLOBAL_USE_GLOBAL','Use global')){
				this.value = "";
			}
		})
		
		//insert data for image
		
		var data = JSON.decode(new BT.Base64().base64Decode(jQuery('#portfolio-hidden').val()));		
		for(var i = 0; i< data.length; i++){
			jQuery('#sortable li ').eq(i).find('a.edit').data('title', data[i].title);             
			jQuery('#sortable li').eq(i).find('a.edit').data('youid', data[i].youid);                          
			jQuery('#sortable li').eq(i).find('a.edit').data('youembed', data[i].youembed);                          
			jQuery('#sortable li').eq(i).find('a.edit').data('youdesc', data[i].youdesc);                         
			  
		}
		 document.adminForm.onsubmit=function(){
                var e=[];
                var f=0;      
                jQuery("#sortable li a.edit").each(function(g){                    
					e[f]=jQuery(this).data();
					 f++;                    					
               });
			 jQuery('#portfolio-hidden').val(new BT.Base64().base64Encode(JSON.encode(e)));			 			
                  
     }
    });
    
</script>