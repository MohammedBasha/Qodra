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

?>
<?php
if (count($this->item->extra_fields))
{
	foreach ($this->item->extra_fields as $key => $el)
	{
		if ($key == '_errors')
			continue;
?>
<li><label><?php echo $el->name; ?> </label> <?php switch ($el->type)
													 {
														 case 'date':
															 echo JHTML::_('calendar', $el->value, 'extra_fields[' . $el->id . ']', 'created');
															 break;
														 case 'string':
															 echo '<input size="80" type="text" name="extra_fields[' . $el->id . ']" value="' . $el->value . '">';
															 break;
														 case 'text':
															 $wysiwyg = JFactory::getEditor();
															 echo '<br clear="both" />';
															 echo $wysiwyg->display('extra_fields[' . $el->id . ']', $el->value, '100%', '400px', '', '');
															 break;
														case'link':																															
															echo '<input size="20" type="text" name="extra_fields[' . $el->id . '][]" value="' . $this->escape($el->value[0]) . '">';
															echo '<input size="20" type="text" name="extra_fields[' . $el->id . '][]" value="' . $this->escape($el->value[1]) . '">';
															$options = array();
															$options[] = JHtml::_('select.option', '_self', JText::_('COM_BT_PORTFOLIO_PORTFOLIO_SAME_WINDOW'));
															$options[] = JHtml::_('select.option', '_blank', JText::_('COM_BT_PORTFOLIO_PORTFOLIO_NEW_WINDOW'));
															echo JHtml::_('select.genericlist',$options,'extra_fields[' . $el->id . '][]','','value','text',$el->value[2]);
															
															 break;
														case'measurement':
															echo '<input size="20" type="text" name="extra_fields[' . $el->id . ']" value="' . $el->value . '">';
															echo $el->default_value[1];
															break;
														case'dropdown':													
															$options = array();
															foreach ($el->default_value as $value){
															$options[] = JHtml::_('select.option', $value,$value);
															}	
															echo JHtml::_('select.genericlist',$options,'extra_fields[' . $el->id . '][]','','value','text',$el->value);
															
															
															break;
														case'image':
															JHtml::_('behavior.modal');

															// Build the script.
															$script = array();
															$script[] = '	function jInsertFieldValue(value, id) {';
															$script[] = '		var old_value = document.id(id).value;';
															$script[] = '		if (old_value != value) {';
															$script[] = '			var elem = document.id(id);';
															$script[] = '			elem.value = value;';
															$script[] = '			elem.fireEvent("change");';
															$script[] = '			if (typeof(elem.onchange) === "function") {';
															$script[] = '				elem.onchange();';
															$script[] = '			}';
															$script[] = '			jMediaRefreshPreview(id);';
															$script[] = '		}';
															$script[] = '	}';

															$script[] = '	function jMediaRefreshPreview(id) {';
															$script[] = '		var value = document.id(id).value;';
															$script[] = '		var img = document.id(id + "_preview");';
															$script[] = '		if (img) {';
															$script[] = '			if (value) {';
															$script[] = '				img.src = "' . JURI::root() . '" + value;';
															$script[] = '				document.id(id + "_preview_empty").setStyle("display", "none");';
															$script[] = '				document.id(id + "_preview_img").setStyle("display", "");';
															$script[] = '			} else { ';
															$script[] = '				img.src = ""';
															$script[] = '				document.id(id + "_preview_empty").setStyle("display", "");';
															$script[] = '				document.id(id + "_preview_img").setStyle("display", "none");';
															$script[] = '			} ';
															$script[] = '		} ';
															$script[] = '	}';

															$script[] = '	function jMediaRefreshPreviewTip(tip)';
															$script[] = '	{';
															$script[] = '		tip.setStyle("display", "block");';
															$script[] = '		var img = tip.getElement("img.media-preview");';
															$script[] = '		var id = img.getProperty("id");';
															$script[] = '		id = id.substring(0, id.length - "_preview".length);';
															$script[] = '		jMediaRefreshPreview(id);';
															$script[] = '	}';

															// Add the script to the document head.
															JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
															//$htmlMediaCode = array('icon'=>'');
															//foreach ($htmlMediaCode as $key => $value){
																#create media html code
																// Initialize variables.
																$htmlMedia = array();
																$attr = '';				
														
															
																
																
																$htmlMedia[] = '<div class="fltlft">';
																$htmlMedia[] = '	<input type="text" name="extra_fields[' . $el->id . ']" id="btg-'.$el->id.'" value="' . $el->value . '" readonly="readonly"  />';
																$htmlMedia[] = '</div>';
														
																// The button.
																$htmlMedia[] = '<div class="button2-left">';
																$htmlMedia[] = '	<div class="blank">';
																$htmlMedia[] = '		<a class="modal"'
																						 .'title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '"' 
																						 . ' href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;&amp;fieldid=btg-'.$el->id. '&amp;folder=' . '"'
																						 . ' rel="{handler: &apos;iframe&apos;, size: {x: 800, y: 500}}">';
																$htmlMedia[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a>';
																$htmlMedia[] = '	</div>';
																$htmlMedia[] = '</div>';
														
																$htmlMedia[] = '<div class="button2-left">';
																$htmlMedia[] = '	<div class="blank">';
																$htmlMedia[] = '		<a title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="#" onclick="';
																$htmlMedia[] = 'jInsertFieldValue(&apos;&apos;, &apos;btg-' . $el->id . '&apos;);';
																$htmlMedia[] = 'return false;';
																$htmlMedia[] = '">';
																$htmlMedia[] = JText::_('JLIB_FORM_BUTTON_CLEAR') . '</a>';
																$htmlMedia[] = '	</div>';
																$htmlMedia[] = '</div>';
														
																$preview = '';
																$showPreview = true;
																$showAsTooltip = false;
																switch ($preview)
																{
																	case 'false':
																	case 'none':
																		$showPreview = false;
																		break;
																	case 'true':
																	case 'show':
																		break;
																	case 'tooltip':
																	default:
																		$showAsTooltip = true;
																		$options = array(
																			'onShow' => 'jMediaRefreshPreviewTip',
																		);
																		JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
																		break;
																}
														
																if ($showPreview)
																{
																	$src = $el->value;
																	$attr = array(
																		'id' => 'btg-'.$el->id . '_preview',
																		'class' => 'media-preview',
																		'style' => 'max-width:200px; max-height:30px;'
																	);
																	$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $attr);
																	$previewImg = '<div id="btg-' . $el->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
																	$previewImgEmpty = '<div id="btg-' .$el->id  . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
																		. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';
														
																	$htmlMedia[] = '<div class="media-preview fltlft">';
																	
																		$htmlMedia[] = ' ' . $previewImgEmpty;
																		$htmlMedia[] = ' ' . $previewImg;
																	
																	$htmlMedia[] = '</div>';
																}
														
																//$htmlMediaCode[$key] = implode("", $htmlMedia); 
																?>
																	<script type="text/javascript">
																	jQuery(document).ready(function(){
																	var value = jQuery("#btg-icon").val();																	
																	jQuery.ajax({
																		url: location.href,
																		data: "action=get_image&data="+value,
																		type: "post",
																		
																		success: function(response){
																		var data = (response);	
																			jQuery('#btg-icon_preview').attr('src',data);
																			jQuery('#btg-icon_preview_empty').css('display','none');
																			jQuery('#btg-icon_preview_img').css("display", "");
																		}
																		});
																	});
																	</script>
																<?php
																	//}
																	echo implode("", $htmlMedia);?> 
																	
																	
																	
																	
													<?php
															 break;
													 }
											   ?>
</li>
<?php

	}
}
else
{
	echo JText::_('COM_BT_PORTFOLIO_PORTFOLIO_EXTRAFIELD_NOTE');

}
?>
