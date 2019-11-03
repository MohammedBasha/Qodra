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

/**
 * Bt_portfolio component helper.
 */
abstract class Bt_portfolioHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu) {
		if(class_exists('JHtmlSidebar')){
			JHtmlSidebar::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_CPANEL'), 'index.php?option=com_bt_portfolio', $submenu == 'cpanel');
			JHtmlSidebar::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_PORTFOLIO'), 'index.php?option=com_bt_portfolio&view=portfolios', $submenu == 'portfolios');
			JHtmlSidebar::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_CATEGORIES'), 'index.php?option=com_bt_portfolio&view=categories', $submenu == 'categories');
			JHtmlSidebar::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_EXTRAFIELDS'), 'index.php?option=com_bt_portfolio&view=extrafields', $submenu == 'extrafields');
			JHtmlSidebar::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_COMMENTS'), 'index.php?option=com_bt_portfolio&view=comments', $submenu == 'comments');
		}
		else{
			JSubMenuHelper::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_CPANEL'), 'index.php?option=com_bt_portfolio', $submenu == 'cpanel');
			JSubMenuHelper::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_PORTFOLIO'), 'index.php?option=com_bt_portfolio&view=portfolios', $submenu == 'portfolios');
			JSubMenuHelper::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_CATEGORIES'), 'index.php?option=com_bt_portfolio&view=categories', $submenu == 'categories');
			JSubMenuHelper::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_EXTRAFIELDS'), 'index.php?option=com_bt_portfolio&view=extrafields', $submenu == 'extrafields');
			JSubMenuHelper::addEntry(JText::_('COM_BT_PORTFOLIO_SUBMENU_COMMENTS'), 'index.php?option=com_bt_portfolio&view=comments', $submenu == 'comments');
		}
    }

    public static function getPublishedOptions() {
        // Build the filter options.
        $options = array();
        $options[] = JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
        $options[] = JHtml::_('select.option', '0', JText::_('JUNPUBLISHED'));
        return $options;
    }

    public static function getFeaturedOptions() {
        // Build the filter options.
        $options = array();
        $options[] = JHtml::_('select.option', '1', JText::_('COM_BT_PORTFOLIO_FEATURED'));
        $options[] = JHtml::_('select.option', '0', JText::_('COM_BT_PORTFOLIO_UNFEATURED'));
        return $options;
    }

    public static function getCategoryOptions() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id as value,title as text,parent_id,ordering');
        $query->from('#__bt_portfolio_categories');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        return self::MakeTree($categories);
    }

    public static function getAuthorOptions() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('distinct name as value,name as text');
        $query->from('#__bt_portfolio_comments');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getTypeOptions() {
        // Build the filter options.
        $options = array();
		$options[] = JHtml::_('select.option', 'link', JText::_('COM_BT_PORTFOLIO_LINK'));
        $options[] = JHtml::_('select.option', 'image', JText::_('COM_BT_PORTFOLIO_IMAGE'));
        $options[] = JHtml::_('select.option', 'string', JText::_('COM_BT_PORTFOLIO_STRING'));
        $options[] = JHtml::_('select.option', 'text', JText::_('COM_BT_PORTFOLIO_TEXT'));
        $options[] = JHtml::_('select.option', 'measurement', JText::_('COM_BT_PORTFOLIO_MEASUREMENT'));
        $options[] = JHtml::_('select.option', 'dropdown', JText::_('COM_BT_PORTFOLIO_DROPDOWN'));		
        $options[] = JHtml::_('select.option', 'date', JText::_('COM_BT_PORTFOLIO_DATE'));
        return $options;
    }

    public static function MakeTree($categories, $id = 0) {
        $tree = array();
        $tree = self::TreeTitle($categories, $tree, 0);
        $tree_array = array();
        if ($id > 0) {
            $tree_sub = array();
            $id_sub = '';
            $subcategories = self::SubTree($categories, $tree_sub, 0, $id_sub);
            foreach ($subcategories as $key0 => $value0) {
                $subcategories_array[$key0] = explode(',', $value0);
            }

            foreach ($tree as $key => $value) {

                foreach ($categories as $key2 => $value2) {

                    $syntax_check = 1;

                    if ($id == $key) {
                        $syntax_check = 0;
                    }

                    foreach ($subcategories_array as $key3 => $value3) {
                        foreach ($value3 as $key4 => $value4) {
                            if ($value4 == $id && $key == $key3) {
                                $syntax_check = 0;
                            }
                        }
                    }

                    if ($syntax_check == 1) {
                        if ($key == $value2->value) {
                            $tree_object = new JObject();
                            $tree_object->text = $value;
                            $tree_object->value = $key;
                            $tree_array[] = $tree_object;
                        }
                    }
                }
            }
        } else {
            foreach ($tree as $key => $value) {
                foreach ($categories as $key2 => $value2) {
                    if ($key == $value2->value) {
                        $tree_object = new JObject();
                        $tree_object->text = $value;
                        $tree_object->value = $key;
                        $tree_array[] = $tree_object;
                    }
                }
            }
        }
        return $tree_array;
    }

    public static function TreeTitle($data, $tree, $id = 0, $text = '') {

        foreach ($data as $key) {
            $show_text = $text . $key->text;
            if ($key->parent_id == $id) {
                $tree[$key->value] = $show_text;
                $tree = Bt_portfolioHelper::TreeTitle($data, $tree, $key->value, $text . " -- ");
                // &raquo;
            }
        }
        return ($tree);
    }

    public static function SubTree($data, $tree, $id = 0, $id_sub = '') {
        foreach ($data as $key) {
            $show_id_sub = $id_sub . $key->value;
            if ($key->parent_id == $id) {
                $tree[$key->value] = $id_sub;
                $tree = self::SubTree($data, $tree, $key->value, $show_id_sub . ",");
            }
        }
        return ($tree);
    }

    public static function addAdminScript() {
        $checkJqueryLoaded = false;
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        JHTML::_('behavior.framework');
		if (!version_compare(JVERSION, '3.0', 'ge')) {
			foreach ($header['scripts'] as $scriptName => $scriptData) {
				if (substr_count($scriptName, '/jquery')) {
					$checkJqueryLoaded = true;
				}
			}

			//Add js
			if (!$checkJqueryLoaded)
				$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.min.js');
		}	
		
        //$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.ui.core.min.js');
        //$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.ui.widget.min.js');
        //$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.ui.accordion.min.js');
        $document->addScriptDeclaration('jQuery.noConflict();');
        $document->addStyleSheet(JURI::root() . 'components/com_bt_portfolio/assets/icon/admin.css');
        
		$document->addStyleSheet(JURI::root() . 'components/com_bt_portfolio/assets/css/legacy.css');
		if (!Bt_portfolioLegacyHelper::isLegacy()) {	
            JHtml::_('formbehavior.chosen', 'select');
        }
    }

    public static function substring($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {

        $string = $isStrips ? strip_tags($text, $stringtags) : $text;
        if (mb_strlen($string) < $length)
            return $string;
        $string = mb_substr($string, 0, $length);
        return $string . $replacer;
    }

}
