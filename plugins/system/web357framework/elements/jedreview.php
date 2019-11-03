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

class JFormFieldjedreview extends JFormField {
	
	protected $type = 'jedreview';

	protected function getInput()
	{
		return ' ';
	}

	protected function getLabel()
	{	
		$html  = '';

		// Get Joomla's version
		$jversion = new JVersion;
		$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
		$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8

		// get extension's details
		$extension_type_single = $this->element['extension_type']; // component, module, plugin 
		$extension_type = $this->element['extension_type'].'s'; // components, modules, plugins 
		$extension_name = preg_replace('/(plg_|com_|mod_)/', '', $this->element['extension_name']);
		$plugin_type = $this->element['plugin_type']; // system, authentication, content etc.
		$plugin_folder = (!empty($plugin_type) && $plugin_type != '') ? $plugin_type.'/' : '';
		$real_name = $this->element['real_name'];
		$real_name = JText::_($real_name);
		
		if (empty($extension_type) || empty($extension_name)):
			JFactory::getApplication()->enqueueMessage("Error in XML. Please, contact us at support@web357.com!", "error");
			return false;
		endif;
		
		// BEGIN: get button links
		switch ($extension_name):
			case "cookiespolicynotificationbar":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/site-management/cookie-control/cookies-policy-notification-bar';
			break;
			case "countdown":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/calendars-a-events/events/count-down';
			break;
			case "datetime":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/calendars-a-events/time/display-date-time';
			break;
			case "contactinfo":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/contacts-and-feedback/contact-details/contact-info/';
			break;
			case "failedloginattempts":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/access-a-security/site-security/failed-login-attempts';
			break;
			case "loginasuser":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/clients-a-communities/user-management/login-as-user';
			break;
			case "monthlyarchive":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/news-display/articles-display/monthly-archive';
			break;
			case "fixedhtmltoolbar": // toolbar
				$jed_link = 'https://extensions.joomla.org/extensions/extension/social-web/social-display/toolbar';
			break;
			case "vmcountproducts":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/extension-specific/virtuemart-extensions/virtuemart-count-products';
			break;
			case "vmsales":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/extension-specific/virtuemart-extensions/virtuemart-sales';
			break;
			case "supporthours":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/contacts-and-feedback/opening-hours/support-hours';
			break;
			case "fix404errorlinks":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/site-management/fix-404-error-links';
			break;
			case "wwwredirect":
				$jed_link = 'https://extensions.joomla.org/extensions/extension/site-management/url-redirection/www-redirect/';
			break;

			default:
				$jed_link = '';
		endswitch;
		// END: get button links
		
		if (!empty($jed_link))
		{
			$html .= '<div class="w357frm_param_header">';
			$html .= '<label>';
			$html .= JText::_('W357FRM_HEADER_JED_REVIEW_AND_RATING');
			$html .= '</label>';
			$html .= '</div>';

			if (version_compare( $mini_version, "2.5", "<="))
			{
				// j25
				$html .= '<div class="w357frm_leave_review_on_jed" style="clear:both;padding-top:20px;">'.sprintf(JText::_('W357FRM_LEAVE_REVIEW_ON_JED'), $jed_link, $real_name).'</div>';
			}
			else
			{
				// j3x
				$html .= '<div class="w357frm_leave_review_on_jed">'.sprintf(JText::_('W357FRM_LEAVE_REVIEW_ON_JED'), $jed_link, $real_name).'</div>';
			}
		}
		
		return $html;	
	}

}