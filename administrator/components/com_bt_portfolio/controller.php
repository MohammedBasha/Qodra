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
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of bt_portfolio component
 */
/*
 * This trick allows us to extend the correct class, based on whether it's Joomla! 3.0 or 2.5
 */
if (version_compare(JVERSION, '3.0', 'ge')) {
    class Bt_portfolioControllerFake extends JControllerLegacy {
    }

} else {
    class Bt_portfolioControllerFake extends JController{
        
    }

}

class Bt_portfolioController extends Bt_portfolioControllerFake {

    /**
     * display task
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false) {
        
        // set default view if not set
        JRequest::setVar('view', JRequest::getCmd('view', 'cpanel'));

        // call parent behavior
        parent::display($cachable,$urlparams);

        // Set the submenu
        
    }

}