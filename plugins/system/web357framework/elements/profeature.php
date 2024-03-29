<?php
/* ======================================================
# Web357 Framework for Joomla! - v1.7.7 (Free version)
# -------------------------------------------------------
# For Joomla! CMS
# Author: Web357 (Yiannis Christodoulou)
# Copyright (©) 2009-2019 Web357. All rights reserved.
# License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
# Website: https:/www.web357.com/
# Demo: https://demo.web357.com/joomla/web357framework
# Support: support@web357.com
# Last modified: 10 Jun 2019, 18:31:42
========================================================= */

defined('JPATH_BASE') or die;

require_once(JPATH_PLUGINS . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "web357framework" . DIRECTORY_SEPARATOR . "elements" . DIRECTORY_SEPARATOR . "elements_helper.php");

jimport('joomla.form.formfield');
jimport( 'joomla.form.form' );

class JFormFieldProfeature extends JFormField {
	
	protected $type = 'profeature';

	protected function getLabel()
	{
		// Get Joomla's version
		$jversion = new JVersion;
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8
		$major_version = 'v'.$short_version[0].'x'; // v3x

		// Data
		$id = $this->element["id"];
		$label = JText::_($this->element["label"]);
		if (version_compare($mini_version, "3.8", ">="))
		{
			// is Joomla! 4.x
			$title = '';
			$data_content = JText::_($this->element["description"]);
			$data_original_title = $label;
			$class = 'hasPopover';
		}
		else
		{
			// Joomla! 2.5.x and Joomla! 3.x
			$title = '&lt;strong&gt;'.JText::_($this->element["label"]).'&lt;/strong&gt;&lt;br /&gt;'.JText::_($this->element["description"]);
			$data_content = '';
			$data_original_title = '';
			$class = 'hasTooltip';
		}

		// an einai j4 den to deixneis, alliws to deixneis
		
		return '<label id="jform_params_'.$id.'-lbl" for="jform_params_'.$id.'" class="'.$class.'" title="'.$title.'" data-content="'.$data_content.'" data-original-title="'.$data_original_title.'">'.$label.'</label>';	
	}

	protected function getInput() 
	{
		// Get Joomla's version
		$jversion = new JVersion;
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8
		$major_version = 'v'.$short_version[0].'x'; // v3x

		// Data
		$id = $this->element["id"];
		$label = JText::_($this->element["label"]);
		if (version_compare($mini_version, "2.5", "<="))
		{
			// is Joomla! 2.5.x
			$style = ' style="padding-top: 5px; font-style: italic; display: block; clear: both;"';
		}
		else
		{
			$style = ' style="padding-top: 5px; font-style: italic;"';
		}

		$pricing_prefix = !empty($this->element["pricing_prefix"]) ? $this->element["pricing_prefix"] : 'undefined';
		$link_to_pro = '<a href="https://www.web357.com/pricing?extension='.$pricing_prefix.'&utm_source=CLIENT&utm_medium=CLIENT-ProLink-web357&utm_content=CLIENT-ProLink&utm_campaign=radiofelement" target="_blank">PRO</a>';
		$html = '<div'.$style.'>'.sprintf(JText::_('W357FRM_ONLY_IN_PRO'), $link_to_pro).'</div>';

		return $html;
	}

}