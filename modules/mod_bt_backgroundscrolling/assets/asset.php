<?php
/**
 * @package 	formfield
 * @version		1.0
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField {

    protected $type = 'Asset';

    protected function getInput() {		
		JHTML::_('behavior.framework');		
		$document	= &JFactory::getDocument();		
		$document->addStyleDeclaration('#jform_params_text_parent{float:left;}#jform_params_asset-lbl{display:none;}');
        return null;
    }
}
?>