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

defined('_JEXEC') or die();

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');

if (!class_exists('ShopFunctions'))
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('VmHtml'))
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'html.php');
if (!class_exists('TableCategories'))
	require(VMPATH_ADMIN . DS . 'tables' . DS . 'categories.php');
jimport('joomla.form.formfield');

/*
 * This element is used by the menu manager
 * Should be that way
 */
class JFormFieldVmcategoriesd extends JFormField {

	var $type = 'vmcategoriesd';

	protected function getInput() {

		VmConfig::loadConfig();
		vmLanguage::loadJLang('com_virtuemart');

		if(!is_array($this->value))$this->value = array($this->value);
		$categorylist = ShopFunctions::categoryListTree($this->value);

		$name = $this->name;
		if($this->multiple){
			$name = $this->name;
			$this->multiple = ' multiple="multiple" ';
		}
		$id = VmHtml::ensureUniqueId('vmcategories');
		$html = '<select id="'.$id.'" class="inputbox"   name="' . $name . '" '.$this->multiple.' >';
		if(!$this->multiple)$html .= '<option value="0">' . vmText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL') . '</option>';
		$html .= $categorylist;
		$html .= "</select>";
		return $html;
	}

}