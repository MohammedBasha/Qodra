<?php
/**
 * @package 	mod_bt_portfolio_gallery - BT Portfolio Gallery Module
 * @version		1.0.0
 * @created		Jun 2014
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2014 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the latest functions only once
JLoader::register('modBtPortfolioGalleryHelper', dirname(__FILE__).'/helper/helper.php');
modBtPortfolioGalleryHelper::addModuleScript($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list = modBtPortfolioGalleryHelper::getItems($params);
$cats = modBtPortfolioGalleryHelper::getCategories($params);
$layout = $params->get('theme', 'default');
require JModuleHelper::getLayoutPath('mod_bt_portfolio_gallery');
