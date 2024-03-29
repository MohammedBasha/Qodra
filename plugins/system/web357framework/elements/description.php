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

class JFormFieldDescription extends JFormField {
	
	protected $type = 'description';
	
	protected function getInput()
	{
		return ' ';
	}

	/**
	 * Get the description after installation with useful buttons and links.
	 * 
	 * @extension_type = "plugin"
	 * @extension_name = "loginasuser"
	 * @plugin_type = "system"
	 * @real_name = "Login as User"
	 */
	function getHtmlDescription($extension_type = '', $extension_name = '', $plugin_type = '', $real_name = '')
	{
		// Get extension's details from XML
		$extension_type = (!empty($extension_type)) ? $extension_type : $this->element['extension_type']; // component, module, plugin 
		$extension_name = (!empty($extension_name)) ? $extension_name : preg_replace('/(plg_|com_|mod_)/', '', $this->element['extension_name']);
		$plugin_type = (!empty($plugin_type)) ? $plugin_type : $this->element['plugin_type'].' '; // system, authentication, content etc.
		$real_name = (!empty($real_name)) ? $real_name : $this->element['real_name'];
		$real_name = JText::_($real_name);
		
		// Retrieving request data using JInput
		$jinput = JFactory::getApplication()->input;
		$juri_base = str_replace('/administrator', '', JURI::base());

		// Get Joomla's version
		$jversion = new JVersion;
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8
		$major_version = 'v'.$short_version[0].'x'; // v3x
		
		/**
		 *  Get extension details from the json file
		 */
		$web357_items_json_file = JPATH_PLUGINS . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "web357framework" . DIRECTORY_SEPARATOR . "elements" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "web357-items.json";
		$web357_items_json_data = file_get_contents($web357_items_json_file);
		$web357_items_data = json_decode($web357_items_json_data);

		if (!isset($web357_items_data->$extension_name))
		{
			return 'no description for this extension';
		}

		// item vars
		$web357_item = $web357_items_data->$extension_name;
		$extension_type = str_replace('_', ' ', $web357_item->extension_type);
		$live_demo_url = $web357_item->live_demo_url;
		$more_info_url = $web357_item->more_info_url;
		$documentation_url = $web357_item->documentation_url;
		$changelog_url = $web357_item->changelog_url;
		$support_url = $web357_item->support_url;
		$jed_url = $web357_item->jed_url;
		$backend_settings_url = $web357_item->backend_settings_url;
		$ext_desc_html = $web357_item->description;
		$ext_desc_features_html = $web357_item->description_features;

		if (version_compare( $mini_version, "2.5", "<="))
		{
			$backend_settings_url = str_replace('filter[search]', 'filter_search', $backend_settings_url);
		}

		// output
		$html = '';

		// Header
		if (version_compare($mini_version, "4.0", "<"))
		{
			// is Joomla! 3.x
			$html .= '<h1>'.$real_name.' - Joomla! '.$extension_type.'</h1>';
		}

		// begin container
		$container_style = $jinput->get('option') == 'com_installer' ? ' style="margin: 30px !important;"' : '';

		// Header
		if (version_compare($mini_version, "4.0", ">="))
		{
			// is Joomla! 4.x
			$html .= '<div class="container '.$major_version.' w357 '.$jinput->get('option').'"'.$container_style.'>';
			$html .= '<h1 style="margin-bottom: 20px;">'.$real_name.' - Joomla! '.$extension_type.'</h1>';
			$html .= '<div class="row">';
		}
		else
		{
			$html .= '<div class="w357-container '.$major_version.' w357 '.$jinput->get('option').'"'.$container_style.'>';
			$html .= '<div class="row-fluid">';
		}

		// BEGIN: get product's image and buttons
		$product_image = $juri_base.'media/plg_system_web357framework/images/joomla-extensions/'.$extension_name.'.png';

		$html .= '<div class="span3 col text-center" style="max-width: 220px;">';

		// image
		$desc_img_style = $jinput->get('option') == 'com_installer' ? ' style="overflow: hidden; margin-bottom: 20px;"' : '';
		$html .= '<div class="web357framework-desc-img"'.$desc_img_style.'>';
		$html .= (!empty($more_info_url)) ? '<a href="'.$more_info_url.'" target="_blank">' : '';
		$html .= '<img src="'.$product_image.'" alt="'.$real_name.'" />';
		$html .= (!empty($more_info_url)) ? '</a>' : '';
		$html .= '</div>';

		// buttons
		$desc_btn_style = $jinput->get('option') == 'com_installer' ? ' style="display: inline-block; margin: 0 0 10px 10px;"' : '';
		if (!empty($backend_settings_url))
		{
			$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'><a href="'.$backend_settings_url.'" class="btn btn-secondary">Settings</a></div>';
		}

		$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'>';
		$html .= (!empty($live_demo_url)) ? '<a href="'.$live_demo_url.'" class="btn btn-primary" target="_blank">View Demo</a> ' : '';
		$html .= '</div>';

		$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'>';
		$html .= (!empty($more_info_url)) ? '<a href="'.$more_info_url.'" class="btn btn-success" target="_blank">More Details</a> ' : '';
		$html .= '</div>';

		$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'>';
		$html .= (!empty($documentation_url)) ? '<a href="'.$documentation_url.'" class="btn btn-warning" target="_blank">Documentation</a> ' : '';
		$html .= '</div>';
		
		$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'>';
		$html .= (!empty($changelog_url)) ? '<a href="'.$changelog_url.'" class="btn btn-info" target="_blank">Changelog</a> ' : '';
		$html .= '</div>';

		$html .= '<div class="web357framework-desc-btn"'.$desc_btn_style.'>';
		$html .= (!empty($support_url)) ? '<a href="'.$support_url.'" class="btn btn-danger" target="_blank">Support</a> ' : '';
		$html .= '</div>';

		$html .= '</div>'; // .span3
		// END: get product's image and buttons
		
		// Description
		$full_desc_style = $jinput->get('option') == 'com_installer' ? ' style="margin: 30px 0 0 10px !important;"' : '';
		$desc = <<<HTML

		<div class="w357_item_full_desc"{$full_desc_style}>
			
			<p class="uk-text-large">{$ext_desc_html}</p>

			{$ext_desc_features_html}

		</div><!-- end .w357_item_full_desc -->
HTML;
				
		if (!empty($desc))
		{
			$html .= '<div class="span9 col">';

			// description
			$html .= $desc;

			// jed review
			$html .= (!empty($jed_url)) ? '<div class="w357_item_full_desc"><h4>'.JText::_('W357FRM_HEADER_JED_REVIEW_AND_RATING').'</h4><p>'.sprintf(JText::_('W357FRM_LEAVE_REVIEW_ON_JED'), $jed_url, $real_name).'</p></div>' : '';

			$html .= '</div>'; // end .span9
		}
		else
		{
			$html .= '<div class="span9" style="color:red; font-weight: 700;">ERROR! The description of this product couldn\'t be displayed.<br />This is a small bug. Please, report this problem at support@web357.com.</div>';
		}
		
		$html .= '</div>'; // end .row

		$html .= '</div>'; // end .container

		return $html;
	}

	protected function getLabel()
	{
		return $this->getHtmlDescription();
	}

	protected function getTitle()
	{
		return $this->getLabel();
	}
}