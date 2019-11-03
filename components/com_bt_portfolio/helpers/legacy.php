<?php
/**
 * This class is used to check version of joomla and work something in different
 * @package             bt_portfolio - BT Portfolio Component
 * @version		2.0
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright           Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;

class Bt_portfolioLegacyHelper{
    
    /**
     * Load behavior mootool
     * 
     */
    public static function isLegacy(){
        if (version_compare(JVERSION, '3.0', 'ge')) {
            return false;
        }else{
            return true;
        } 
    }
    public static function getController(){
        if (version_compare(JVERSION, '3.0', 'ge')) {
            
            return JControllerLegacy::getInstance('Bt_portfolio');
            
        }else{
            return JController::getInstance('Bt_portfolio');
        } 
    }
    public static function getCSS(){
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $document = JFactory::getDocument();
            $view = JRequest::getCmd('view', 'cpanel');
            $document->addStyleSheet(JUri::base(). 'components/com_bt_portfolio/views/' . $view . '/tmpl/css/legacy.css');
            return JControllerLegacy::getInstance('Bt_portfolio');
        }
    }
    
}
?>
