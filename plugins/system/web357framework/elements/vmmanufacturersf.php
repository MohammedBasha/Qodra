<?php
/* ======================================================
# Web357 Framework - Joomla! System Plugin v1.5.1
# -------------------------------------------------------
# For Joomla! 3.0
# Author: Yiannis Christodoulou (yiannis@web357.eu)
# Copyright (©) 2009-2018 Web357. All rights reserved.
# License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
# Website: https://www.web357.eu/
# Support: support@web357.eu
# Last modified: 03 Jan 2018, 12:11:35
========================================================= */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
class JFormFieldVmmanufacturersf extends JFormField
{
	var $type = 'vmmanufacturersf';

    function getInput() 
    {
		$pricing_prefix = !empty($this->element["pricing_prefix"]) ? $this->element["pricing_prefix"] : 'undefined';
		$link_to_pro = '<a href="https://www.web357.eu/pricing?extension='.$pricing_prefix.'&utm_source=CLIENT&utm_medium=CLIENT-ProLink-web357&utm_content=CLIENT-ProLink&utm_campaign=radiofelement" target="_blank">PRO</a>';
		return '<p><em>'.sprintf(JText::_('W357FRM_ONLY_IN_PRO'), $link_to_pro).'</em></p>';
	}
}