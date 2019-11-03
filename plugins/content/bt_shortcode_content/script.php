<?php
/**
 * @package 	BT Shortcode
 * @version		1.0.0
 * @created		November 2014
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2014 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport('joomla.installer.installer');
class plgContentBt_shortcode_contentInstallerScript
{

    public function postflight($type, $parent)
    {
        $db = JFactory::getDBO();        
        $query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote('bt_shortcode_content')." AND folder=".$db->Quote('content');
        $db->setQuery($query);
        $db->query();       
    }

    
}        