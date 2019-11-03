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
$document->addScript(JURI::root() . 'administrator/components/com_bt_portfolio/helpers/uploadify/uploadvideo.js');
?>
<div style="overflow: hidden">
<ul class="adminformlist" id="uploading">
	<li>
			<div id="inputlink"><input type="text" size="40" class="inputbox" value="" id="youtube_url" name="youtube_url"></div>
			<div id="btnGetVideosFromYoutube"><div class="custom-button bt-enable hasTip" title="<?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_LABEL'); ?>::<?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_DESC'); ?>"><strong><?php echo JText::_('COM_BT_PORTFOLIO_MEDIA_GETVIDEO_LABEL'); ?></strong></div></div>
			<div id="btss-loading" style="display:none"></div>
			<div id="btss-message"></div>
	</li>
	<li>
		<input type="hidden" size="40" class="inputbox" value="" id="getyoutube" name="youtubevalue">
		<input type="hidden" size="40" class="inputbox" value="" id="getyoutubeurl" name="youtubevalueurl">		
	</li>	
	<li><label>Upload Image</label> <input type="file" name="images[]">
		<a href="#" class="button" onclick="return addFile(this)">+</a>
	</li>
	
		
</ul>
</div>
<script type="text/javascript">

function addFile(el){
	jQuery(el).parent().append('<a href="#" class="button" onclick="return removeFile(this)">x</a>');
	jQuery(el).remove();
	jQuery("#uploading").append('<li><label>Upload Image</label><input type="file" name="images[]"><a href="#" class="button" onclick="return addFile(this)">+</a></li>');
	return false;
}
jQuery(document).ready(function() {
 jQuery("#btnGetVideosFromYoutube .custom-button").click(function(){
		jQuery("#btnGetVideosFromYoutube .custom-button").unbind('click');
		var yt_url = jQuery("#youtube_url").val();
		uploadvideo(yt_url);
						
	});
});
</script>
