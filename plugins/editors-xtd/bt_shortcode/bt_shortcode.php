<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die("Restricted access");

 
class plgButtonBt_shortcode extends JPlugin
{
	public function onDisplay($name){
	
		$js = "
		function btInsertShortCode(text)
		{
			jInsertEditorText(text, '" . $name . "');
			SqueezeBox.close();
		}";

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);
		
		$button = new JObject();
		$button->modal = true;
		$button->class = 'btn';
	    $button->link = 'index.php?btaction=bt_shortcode_system&task=listview&layout=modal&tmpl=component&' . JSession::getFormToken() . '=1';
		$button->text = JText::_('Insert shortcode');
		$button->name = version_compare(JVERSION, '3.0', 'ge') ? 'file-add' :  'image';
		$button->options = "{handler: 'iframe', size: {x: 900, y: 500}}";
		return $button;
	}
 
}
