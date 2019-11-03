<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		2.3.0
 * @created		Dec 2011
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT . '/helpers/helper.php';
require_once JPATH_COMPONENT . '/helpers/legacy.php';
JLoader::register('BtPortfolioController', JPATH_COMPONENT.'/controllers/controller.php');
JLoader::register('BTView', JPATH_COMPONENT.'/views/view.php');
JLoader::register('BtPortfolioModel', JPATH_COMPONENT.'/models/model.php');
$app = JFactory::getApplication();
$params = $app->getParams();
$theme = $params->get('theme', 'default');
$theme_url = '';
if (is_dir(JPATH_SITE . '/templates/'  . $app->getTemplate() . '/html/com_bt_portfolio/' . $theme)) {
	$theme_url = JURI::root().'templates/' . $app->getTemplate() . '/html/com_bt_portfolio/' . $theme . '/';
}
elseif (is_dir(JPATH_SITE . '/components/com_bt_portfolio/themes/' . $theme)) {
	$theme_url = JURI::root() . 'components/com_bt_portfolio/themes/' . $theme . '/';
}
if ($theme_url == '') {
	return JError::raiseError(500, sprintf(JText::_('COM_BT_PORTFOLIO_THEME_NOT_FOUND'), $theme));
}
if (!defined('COM_BT_PORTFOLIO_THEME_URL')) {
	define('COM_BT_PORTFOLIO_THEME_URL', $theme_url);
}
if (JRequest::getVar("format") != 'raw') {
	Bt_portfolioHelper::addSiteScript();
}
$controller = Bt_portfolioLegacyHelper::getController();
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
