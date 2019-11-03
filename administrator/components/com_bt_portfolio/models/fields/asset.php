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
// No direct access to this file
defined('_JEXEC') or die;


jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField {

    protected $type = 'Asset';

    protected function getInput() {
        JLoader::register('Bt_portfolioLegacyHelper', JPATH_ADMINISTRATOR . '/components/com_bt_portfolio/helpers/legacy.php');
        JHTML::_('behavior.framework');
        $checkJqueryLoaded = false;
        $document = JFactory::getDocument();
        /* $header = $document->getHeadData();
          foreach($header['scripts'] as $scriptName => $scriptData)
          {
          if(substr_count($scriptName,'/jquery')){
          $checkJqueryLoaded = true;
          }
          }

          //Add js
          if(!$checkJqueryLoaded)
         */

        if (!version_compare(JVERSION, '3.0', 'ge')) {
            $document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.min.js');
            $document->addScript(JURI::root() . $this->element['path'] . 'js/chosen.jquery.min.js');
			$document->addStyleSheet(JURI::root() . $this->element['path'] . 'css/chosen.css');
        }
        $document->addScript(JURI::root() . $this->element['path'] . 'js/colorpicker/colorpicker.js');
        $document->addScript(JURI::root() . $this->element['path'] . 'js/bt.js');
        $document->addScript(JURI::root() . $this->element['path'] . 'js/btbase64.min.js');   


        //Add css
        $document->addStyleSheet(JURI::root() . $this->element['path'] . 'css/bt.css');
        $document->addStyleSheet(JURI::root() . $this->element['path'] . 'js/colorpicker/colorpicker.css');
        

        return null;
    }

}