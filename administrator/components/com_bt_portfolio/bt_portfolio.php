<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		2.3.0
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
// require helper file

JLoader::register('Bt_portfolioHelper', dirname(__FILE__) . '/helpers/helper.php', true);
JLoader::register('Bt_portfolioLegacyHelper', dirname(__FILE__) .  '/helpers/legacy.php', true);
JLoader::register('BTImageHelper', dirname(__FILE__) .'/helpers/images.php');
JLoader::register('BTView', JPATH_COMPONENT.'/views/view.php');
Bt_portfolioHelper::addAdminScript();

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Bt_portfolio
$controller = Bt_portfolioLegacyHelper::getController();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
