<?php
/**
 * @package 	BT BACKGROUNDSCROLLING
 * @version		1.0.0
 * @created		May 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));



$document	= JFactory::getDocument();
$header = $document->getHeadData();
$mainframe = JFactory::getApplication();
$template = $mainframe->getTemplate();

$loadJquery = true;
foreach($header['scripts'] as $scriptName => $scriptData)
{
	if(substr_count($scriptName,'/jquery'))
	{
		$loadJquery = false;
		break;
	}
}
if($loadJquery)
{
	$document->addScript(JURI::root().'modules/mod_bt_backgroundscrolling/assets/js/jquery.min.js');
}
$document->addScript(JURI::root().'modules/mod_bt_backgroundscrolling/assets/js/jquery.parallax-1.1.3.js');
require JModuleHelper::getLayoutPath('mod_bt_backgroundscrolling', $params->get('layout', 'default'));
