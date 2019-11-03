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
$document = JFactory::getDocument();
$document->addScript(JURI::root() . 'administrator/components/com_bt_portfolio/helpers/uploadify/jquery.uploadify.v2.1.4.min.js');
$document->addScript(JURI::root() . 'administrator/components/com_bt_portfolio/helpers/uploadify/uploadvideo.js');
$document->addScript(JURI::root() . 'administrator/components/com_bt_portfolio/helpers/uploadify/swfobject.js');
$document->addStyleSheet(JURI::root() . 'administrator/components/com_bt_portfolio/helpers/uploadify/uploadify.css');
$path = $this->params->get('images_path','images/bt_portfolio');
?>

	<ul class="adminformlist" id="uploading">
		<li>
			<input class="btss-ac upload" id="uploadify-file" type="file" name="uploadify-file" />	
		</li>		
		<li>
			<div id="inputlink"><input type="text" size="40" class="inputbox" value="" id="youtube_url" name="youtube_url"></div>
			<div id="btnGetVideosFromYoutube"><div class="custom-button bt-enable hasTip" title="<?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_LABEL'); ?>::<?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_DESC'); ?>"><strong><?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_LABEL'); ?></strong></div></div>
			<div id="btss-loading" style="display:none"></div><div id="btss-message"></div>
		</li>		
		<li>
		<input type="hidden" size="40" class="inputbox" value="" id="getyoutube" name="youtubevalue">
		<input type="hidden" size="40" class="inputbox" value="" id="getyoutubeurl" name="youtubevalueurl">		
		</li>
	</ul>

<script type="text/javascript">
            jQuery(document).ready(function() {
                
                jQuery("#uploadify-file").uploadify({
                    "uploader"  : "<?php echo JURI::base() . 'components/com_bt_portfolio/helpers/uploadify/uploadify.swf'; ?>",
                    "script"    : "<?php echo JURI::root() . 'index.php' ?>",
                    "cancelImg" : "<?php echo JURI::base() . 'components/com_bt_portfolio/helpers/uploadify/cancel.png'; ?>",
                    "scriptData"  : {'option':'com_bt_portfolio','task':'portfolio.upload','id':'<?php echo $this->item->id ?>','tmpl':'component',
'<?php echo $this->session->getName() ?>': '<?php echo $this->session->getId(); ?>','<?php echo JSession::getFormToken(); ?>':'1'
                        },
                    "buttonImg" : "<?php echo JURI::base() . 'components/com_bt_portfolio/helpers/uploadify/browser.png'; ?>",
                    "fileExt"     : "*.jpg;*.jpeg;*.gif;*.png",
                    "fileDesc"    : "Image Files",
                    "width"     : 110,
                    "height"    : 35,
                    "auto"      : true,
                    "multi"     : true,
                    "method"    : "post",
                    "onComplete": function(event, ID, fileObj, response, data){					
                        var data = jQuery.parseJSON(response);
                        if(data == null){
                            showMessage("#btss-message", "<br /><b>Loading Failed</b>");
                        }else{
                            var file = data.files;
                            if (!data.success) {
                                showMessage("#btss-message", "<br /><span style=\"color: red;\"> " + data.message +"</span>");
                            }
                            else {
								
				        		var html = '<li>';
				    				html += '<input class="input-default" title="Make default" name="default_image" type="radio" value="'+file.filename+'" />';
				    				html += '<img class="img-thumb" src="<?php echo JURI::root() . $path. '/tmp/'.($this->params->get('thumbimgprocess',1)==-1?'original':'thumb').'/'; ?>'+file.filename+'" />';
				    				html += '<input type="hidden" name="image_id[]" value="0" />';
				    				html += '<input type="hidden" name="image_filename[]" value="'+file.filename+'" /><br/>';				    				
									html +='<a href="javascript:void(0)" class="edit" onclick="editImage(this)">Edit</a>';
									html +='<a href="javascript:void(0)" class="remove" onclick="removeImage(this)" >Remove</a>';
				    				html += '</li>';									
								jQuery('#sortable').append(html);
								jQuery('#sortable li:last-child ').find('a.edit').data('title', file.title);								 
								reNewItem();
                                }
                        }
                    }
                });
			
				
				
			 jQuery("#btnGetVideosFromYoutube .custom-button").bind('click',function(){
						jQuery("#btnGetVideosFromYoutube .custom-button").unbind('click');
						var yt_url = jQuery("#youtube_url").val();
						uploadvideo(yt_url);
						
					 });
				
                jQuery("#uploadify-upload").click(function(){
                    jQuery("#uploadify-file").uploadifyUpload();
                    return false;
                });
				
            });
				

            function showMessage(a,d){
				jQuery(a).append(d);
            }

 </script>
