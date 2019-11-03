<?php

/**
 * @package 	mod_bt_media_gallery - BT Media Gallery Module
 * @version		1.0.0
 * @created		Aug 2013
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$com_path = '/components/com_bt_portfolio';
JLoader::register('Bt_portfolioHelper', JPATH_SITE . $com_path . '/helpers/helper.php');

class modBtPortfolioGalleryHelper {

    // get twitter feed

    public static function getItems(&$params) {
        // Get an instance of the generic articles model
        if ($params->get('show_limit_number_for', 'all') == 'all') {
            return self::getItemFromAllCategory($params);
        } else {
            return self::getItemsByEachcategory($params);
        }
    }

    public static function getChildCategory($id) {
        $db = JFactory::getDbo();
        $rs = array();
        $subQuery = $db->getQuery(TRUE);
        $subQuery->select('id');
        $subQuery->from('#__bt_portfolio_categories');
        $subQuery->where('parent_id=' . $id);
        $db->setQuery($subQuery);
        $listCatId = $db->loadRowList();
        foreach ($listCatId as $value) {
            $rs[] = $value[0];
        }
        return $rs;
    }

    public static function getAllChildCategory($arrayCatId, &$catResult) {
        $newArrayCatId = array();
        if (!empty($arrayCatId)) {
            foreach ($arrayCatId as $id) {
                $listCat = self::getChildCategory($id);
                $catResult = array_merge($catResult, $listCat);
                $newArrayCatId = array_merge($newArrayCatId, $listCat);
            }
            self::getAllChildCategory($newArrayCatId, $catResult);
        }
        return $catResult;
    }

    public static function getItemFromAllCategory($params) {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $items = array();
        $item_ids = array();

        $query = $db->getQuery(TRUE);
        $query->select('a.id, a.title, a.alias, a.catids, a.image, a.description, a.full_description, b.id as catid');
        $query->from('#__bt_portfolios as a');
        $query->where('a.access IN (' . $groups . ')');
        $query->where('a.published = 1');
        $query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

        // Join over the categories.
        $query->select('b.id as category_id, b.title AS category_title, b.alias AS category_alias');
        $query->join('LEFT', '#__bt_portfolio_categories AS b ON a.catids like CONCAT(\'%,\',b.id,\',%\')');
        $query->where('b.access IN (' . $groups . ')');
        $query->where('b.published = 1');

        if ($params->get('show_featured_port', '') != '') {
            $query->where('a.featured = ' . $params->get('show_featured_port', ''));
        }


        $filter_by_cate = $params->get('catid', array('all'));
        if (!in_array('all', $filter_by_cate)) {
            if ($params->get('get_sub_cat', 0) == 0) {
                $query->where('b.id IN (' . implode(',', $params->get('catid')) . ')');
            } else {
                $listCatId = self::getAllChildCategory($filter_by_cate, $filter_by_cate);
                $query->where("b.id IN(" . implode(',', $listCatId) . ")");
            }
        }

        $orderby = $params->get('item_sort', 'ordering');
        $ordertype = $params->get('sort_type', 'DESC');

        if ($orderby == 'random') {
            $query->order("RAND()");
        } else {
            $query->order("a." . $orderby . ' ' . $ordertype);
        }
        $query->group('a.id');
        $db->setQuery($query, 0, $params->get('show_limit_items', 12));
        $listitem = $db->loadObjectList();
        foreach ($listitem as $item) {
            if (!in_array($item->id, $item_ids)) {
                $item_ids[] = $item->id;
                $catids = explode(',', $item->catids);
                $item->cats = array();
				$item->description = self::truncateStr($item->description,$params->get('des_limit_by','words'),$params->get('des_limit',50),'...',$params->get('strip_tags',1),$params->get('allow_tags'));
                foreach ($catids as $catid) {
                    if ($catid) {
                        $item->cats[] = self::getCategoryInfo($catid);
                    }
                }
                $items[] = $item;
            }
        }
        return $items;
    }

    public static function getItemsByEachcategory($params) {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $catids = array();

        $filter_by_cate = $params->get('catid', array('all'));
        if (!in_array('all', $filter_by_cate)) {
            $items = self::getItemFromCategory($params);
        } else {
            $query = $db->getQuery(TRUE);
            $query->select('id');
            $query->from('#__bt_portfolio_categories');
            $query->where('published=1');
            $query->where('access IN (' . $groups . ')');
            $query->where('parent_id=0');
            $db->setQuery($query);
            $listCatId = $db->loadObjectList();
            foreach ($listCatId as $catid) {
                $catids[] = $catid->id;
            }
            $items = self::getItemFromCategory($params, $catids);
        }
        return $items;
    }

    public static function getCategories($params) {
        $selectcats = $params->get('catid', array('all'));
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        if (!in_array('all', $selectcats)) {
            $query = $db->getQuery(TRUE);
            $query->select('a.id, a.title, a.alias, a.parent_id, (select count(b.id) from #__bt_portfolios as b where b.catids like CONCAT(\'%,\',a.id,\',%\') and b.published=1) as item_count');
            $query->from('#__bt_portfolio_categories as a');
            $query->where('a.published=1');
            $query->where('a.access IN (' . $groups . ')');
            if ($params->get('get_sub_cat', 0) == 0) {
                $query->where('a.id IN (' . implode(',', $selectcats) . ')');
            } else {
                $listCatId = self::getAllChildCategory($selectcats, $selectcats);
                $query->where("a.id IN(" . implode(',', $listCatId) . ")");
            }
            $db->setQuery($query);
            return $db->loadObjectList();
        } else {
            $query = $db->getQuery(TRUE);
            $query->select('a.id, a.title, a.alias, a.parent_id, (select count(b.id) from #__bt_portfolios as b where b.catids like CONCAT(\'%,\',a.id,\',%\') and b.published=1) as item_count');
            $query->from('#__bt_portfolio_categories as a');

            $query->where('a.published=1');
            $query->where('a.access IN (' . $groups . ')');
            $db->setQuery($query);
            return $db->loadObjectList();
        }
    }

    public static function getItemFromCategory($params, $catids = null) {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $items = array();
        $item_ids = array();
        if ($catids) {
            $filter_by_cate = $catids;
        } else {
            $filter_by_cate = $params->get('catid', array('all'));
        }
        if ($params->get('get_sub_cat', 0) == 1) {
            $filter_by_cate = self::getAllChildCategory($filter_by_cate, $filter_by_cate);
        }
        foreach ($filter_by_cate as $catid) {
            $query = $db->getQuery(TRUE);

            $query->select('a.*, b.id as catid');
            $query->from('#__bt_portfolios as a');
            $query->where('a.access IN (' . $groups . ')');
            $query->where('a.published = 1');
            $query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

            // Join over the categories.
            $query->join('LEFT', '#__bt_portfolio_categories AS b ON a.catids like CONCAT(\'%,\',b.id,\',%\')');
            $query->where('b.access IN (' . $groups . ')');
            $query->where('b.published = 1');

            if ($params->get('show_featured_port', '') != '') {
                $query->where('a.featured = ' . $params->get('show_featured_port', ''));
            }


            $query->where('b.id = ' . $catid);

            $orderby = $params->get('item_sort', 'ordering');
            $ordertype = $params->get('sort_type', 'DESC');
            if ($orderby == 'random') {
                $query->order("RAND()");
            } else {
                $query->order("a." . $orderby . ' ' . $ordertype);
            }
            $query->group('a.id');

            $db->setQuery($query, 0, $params->get('show_limit_items', 12));
            $listitem = $db->loadObjectList();
            foreach ($listitem as $item) {
                if (!in_array($item->id, $item_ids)) {
                    $item_ids[] = $item->id;
                    $catids = explode(',', $item->catids);
                    $item->cats = array();
					$item->description = self::truncateStr($item->description,$params->get('des_limit_by','words'),$params->get('des_limit',50),'...',$params->get('strip_tags',1),$params->get('allow_tags'));
                    foreach ($catids as $catid) {
                        if ($catid) {
                            $item->cats[] = self::getCategoryInfo($catid);
                        }
                    }
                    $items[] = $item;
                }
            }
        }
        return $items;
    }

    public static function getCategoryInfo($catid) {
        static $catcache = array();
        if (key_exists($catid, $catcache)) {
            return $catcache[$catid];
        } else {
            $db = JFactory::getDBO();
            $db->setQuery("select id, title, alias from #__bt_portfolio_categories where id =" . $catid);
            $catcache[$catid] = $db->loadObject();
            return $catcache[$catid];
        }
    }

    public static function addModuleScript($params) {
        $document = JFactory::getDocument();
        if ($params->get('load_jquery', 0) == 0) {
            $checkJqueryLoaded = false;
            $header = $document->getHeadData();
            foreach ($header['scripts'] as $scriptName => $scriptData) {
                if (substr_count($scriptName, '/jquery')) {
                    $checkJqueryLoaded = true;
                }
            }
            //Add js
            if (!$checkJqueryLoaded) {
                $document->addScript(JURI::root() . 'modules/mod_bt_portfolio_gallery/admin/js/jquery-2.1.1.min.js');
            }
        } else {
            $document->addScript(JURI::root() . 'modules/mod_bt_portfolio_gallery/admin/js/jquery-2.1.1.min.js');
        }
    }

    public static function createModuleItemImage($param, $item) {
        require __DIR__ . '/images.php';
        $image = new BTModImageHelper();
        if ($item->image) {
            $image->loadImage(JPATH_SITE . DIRECTORY_SEPARATOR . 'images/bt_portfolio/' . $item->id . '/original/' . $item->image);
        } else {
            $image->loadImage(JPATH_SITE . DIRECTORY_SEPARATOR . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $param->get('theme', 'default') . '/images/default.jpg');
        }
        $new_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'cache/mod_bt_portfolio/' . $item->id;
        if (!file_exists($new_dir)) {
            JFolder::create($new_dir);
        }
        if ($item->image) {
            $image->resize($new_dir . '/' . $item->image, $param->get('thumbnail_width', 600), $param->get('thumbnail_height', 400), 100, TRUE, 'crop_center');
        } else {
            $image->resize($new_dir . '/default.jpg', $param->get('thumbnail_width', 600), $param->get('thumbnail_height', 400), 100, TRUE, 'crop_center');
        }
    }

    public static function getItemImage($params, $item) {
        $file_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'cache/mod_bt_portfolio/' . $item->id;
        if ($item->image) {
            if (!file_exists($file_dir . '/' . $item->image)) {
                self::createModuleItemImage($params, $item);
            }
            return JUri::root() . 'cache/mod_bt_portfolio/' . $item->id . '/' . $item->image;
        } else {
            if (!file_exists($file_dir . '/default.jpg')) {
                self::createModuleItemImage($params, $item);
            }
            return JUri::root() . 'cache/mod_bt_portfolio/' . $item->id . '/default.jpg';
        }
    }

    public static function truncateStr($text, $type ='words', $length = 100, $replacer = '...', $isStrips = true, $allow_tags = '') {
		if($isStrips){
			$stringtags = '';
			if(!is_array($allow_tags)){
				$allow_tags = explode(',',$allow_tags);
			}
			foreach ($allow_tags as $tag) {
				$stringtags .= '<' . $tag . '>';
			}
			$text = preg_replace('/\<p.*\>/Us','',$text);
			$text = str_replace('</p>','<br/>',$text);
			$text = strip_tags($text, $stringtags);
		}
		if($type=='words'){
			$tmp = explode(" ", $text);
			if(count($tmp) < $length)
				return $text;
			$text = implode(" ", array_slice($tmp, 0, $length)) . $replacer;

		}else{
			if(function_exists('mb_strlen')){
			if (mb_strlen($text) < $length)	return $text;
			$text = mb_substr($text, 0, $length);
			}else{
				if (strlen($text) < $length)	return $text;
				$text = substr($text, 0, $length);
			}
			return $text . $replacer;
		}	
	}

}