<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		1.2.2
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ADMINISTRATOR. '/components/com_bt_portfolio/tables');

class Bt_portfolioModelPortfolios extends JModelList {
	protected $category = null;
	protected $parentid = 0;
	protected $siblings = null;
	protected $children = null;
	protected $params = 0;
	
	// construct model
	public function __construct($config = array()){
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		$this->category = JTable::getInstance('Category', 'Bt_portfolioTable');
		$categoryId = JRequest::getInt("catid");
		$this->category->id = $categoryId;
		$this->category->load($categoryId);
		$registry = new JRegistry();
		$registry->loadString($this->category->params);
		$this->category->params= $registry;
		$this->params->merge($registry);
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		// load siblings
		if($categoryId){
			$db->setQuery('select parent_id from #__bt_portfolio_categories where id= ' . $categoryId);
			$this->parentid = intval($db->loadResult());
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__bt_portfolio_categories');
			$query->where('published = 1');
			$query->where('parent_id = '.$this->parentid);
			$query->where('access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
			$query->where('language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
			$query->order($this->params->get('c_ordering', 'ordering').' '.$this->params->get('c_ordering_dir', 'DESC'));
			$db->setQuery($query);
			$this->siblings = $db->loadObjectlist();
		}
		
		// load children
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__bt_portfolio_categories');
			$query->where('published = 1');
			$query->where('parent_id = ' . $categoryId);
			$query->where('access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
			$query->where('language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
			
			$query->order($this->params->get('c_ordering', 'ordering').' '.$this->params->get('c_ordering_dir', 'DESC'));
			$db->setQuery($query);
			$this->children = $db->loadObjectlist();
	
		parent::__construct($config);
	}
	
	// function populate state of model
	protected function populateState($ordering = 'ordering', $direction = 'ASC') {
		$app = JFactory::getApplication();
		$params = &$this->params;
		
		if($params->get('theme')=='bt_foto' && $params->get('layout') =='default'){
			$this->setState('list.limit', 999);
			if(count($this->children)){
				$this->setState('list.category', $this->category->id);	
			}else{
				$this->setState('list.category', $this->parentid);
			}
			$this->setState('list.start', 0);
		}else{
			$value = $params->get('paging', 30);
			if(!$value)
			{
				$value = JRequest::getUInt('limit', $app->getCfg('list_limit', 0));
			}
			$this->setState('list.limit', $value);
			$this->setState('list.category', $this->category->id);
			$value = JRequest::getUInt('limitstart', 0);
			$this->setState('list.start', $value);
		}
		
		$ordering = $params->get('p_ordering', 'ordering');
		$orderingDir = $params->get('p_ordering_dir', 'DESC');
		$this->setState('list.ordering', $ordering);
		$this->setState('list.orderingDir', $orderingDir);
		$this->setState('filter.language', $app->getLanguageFilter());
		$this->setState('layout', JRequest::getCmd('layout'));
	}
	
	// function get query for list portfolios
	function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$categoryId = $this->getState('list.category');

		// Select fields
		$query->select('a.*');

		// Join over the categories.
		$query->select('b.id as category_id, b.title AS category_title, b.alias AS category_alias');
		$query->join('LEFT', '#__bt_portfolio_categories AS b ON a.catids like CONCAT(\'%,\',b.id,\',%\')');
		$query->where('b.access IN (' . $groups . ')');
		$query->where('b.published = 1');
		
		// Ordering
		$ordering = $this->getState('list.ordering');
		$orderingDir = $this->getState('list.orderingDir');
		switch($ordering){
			case 'featured':
				$query->order('a.featured '.$orderingDir. ', a.hits '.$orderingDir);
				break;
			case 'random':
				$query->order('rand()');
				break;
			default:
				$query->order('a.'.$ordering .' '.$orderingDir);
				break;
		}
		
			
		// Filter by category.
		if ($categoryId){
			//$query->where('a.catids like \'%,' . (int) $categoryId.',%\'');
			//if($this->params->get('show_portsubcat')){
				$query->where('b.id in ('.self::callBackAllChild($categoryId).')');
			//}else{
			//	$query->where('b.id = '.$categoryId);
			//}
		}
		
		// From the bt portfolios table
		$query->from('#__bt_portfolios as a');
		if($this->getState('filter.featured')){
			$query->where('a.featured = 1');
		}
		$query->where('a.published = 1');
		$query->where('a.access IN (' . $groups . ')');
		$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		if($this->params->get('excluded_portfolio')){
			$query->where('a.id not in('.$this->params->get('excluded_portfolio').')');
		}
		
		if(!($this->params->get('theme')=='bt_foto' && $this->params->get('layout') =='default'))
			$query->group('a.id');
		
		
		//-------------------------------------------Filter portfolio--------------------------------//
		$task = JRequest::getCmd('task');
		if($task =='filter'){
			//Join over the extrafield value
			$query->select('e.*');
			$query->join('LEFT', '#__bt_portfolio_extrafields_values AS e ON a.id = e.portfolio_id');
			$method= JRequest::getVar('method',null);			
			$searchword =JRequest::getVar('searchword');
			if (!empty($searchword)) {	
				$query->where("(a.title LIKE '%$searchword%' OR a.description LIKE '%$searchword%' OR a.full_description LIKE '%$searchword%')");
				
			}
			$category_select =JRequest::getVar('category_select');
			if (!empty($category_select)) {
				$query->where("a.catids LIKE '%$category_select%'");
			}
			//---------------------------------------------------------------//
			//Extra field single Select
			$extraIDs =JRequest::getVar('extraid', array(), 'get','array');
			$extraselects =JRequest::getVar('extraselect');			
			if(!empty($extraIDs)){			
				$qr = $db->getQuery(true);
				$qr->select('DISTINCT  `portfolio_id`');
				$qr->from('#__bt_portfolio_extrafields_values');
				$portfolioIds = array();
				foreach ($extraIDs as $key => $id){				
					if(isset($extraselects[$key]) && $extraselects[$key]){					
						$qr->clear('where');
						$qr->where('(extrafields_id ='.intval($id).' AND `value`='."'".$db->escape($extraselects[$key])."'".')');					
						$db->setQuery($qr);		
						$numb = $db->loadColumn();							
						if(!empty($numb)){						
							$portfolioIds[] = 'a.id IN ('. implode(',',$db->loadColumn()).')';
						}
						else{
							$portfolioIds[] = 'a.id IN ('.'0'.')';
						}
						
					}
				}				
				if(!empty($portfolioIds)){			
					$query->where(implode(''.$method.' ',$portfolioIds));
				}			
			}
			//End extra single select	
			//------------------------------------------------------------------------------------//
			//Extra Multi select
			$extraarrayid = JRequest::getVar('extraarrayid', null);
			$extraselect_array = JRequest::getVar('extraselect_array', null);
			if(!empty ($extraselect_array)){
				$quer = $db->getQuery(true);
				$quer->select('DISTINCT  `portfolio_id`');
				$quer->from('#__bt_portfolio_extrafields_values');
				$arrayVal= array();
				foreach($extraarrayid as $key => $id){				
				if(isset($extraselect_array[$key]) && $extraselect_array[$key]){
					$listVal='\'' . implode('\',\'', $extraselect_array[$key]) . '\'';
					$quer->clear('where');
					$quer->where('(extrafields_id ='.intval($id).' AND `value` IN ('.$listVal.'))');
					$db->setQuery($quer);		
					$numb = $db->loadColumn();
					if(!empty($numb)){						
							$arrayVal[] = 'a.id IN ('. implode(',',$db->loadColumn()).')';
						}
						else{
							$arrayVal[] = 'a.id IN ('.'0'.')';
					}
				}
	
				}	
				if(!empty($arrayVal)){
					$query->where(implode(''.$method.' ',$arrayVal));
				}
					
				
			}
			//End multi select
			//-------------------------------------------------------------------------//
			//Begin Price Range
					
			$exTextID =JRequest::getVar('exTextID',null);
			$extra_from =JRequest::getVar('extra_from',null);
			$extra_to =JRequest::getVar('extra_to',null);			
			if(isset($exTextID)){
				$process = $db->getQuery(true);
				$process->select('DISTINCT  `portfolio_id`');
				$process->from('#__bt_portfolio_extrafields_values');
				$arrayID = array();
				foreach ($exTextID as $key => $id){
				if(isset($extra_from[$id]) && $extra_from[$id]){
					if(isset($extra_to[$id]) && $extra_to[$id]){
						$process->clear('where');
						$process->where('(extrafields_id ='.intval($id).' AND `value` BETWEEN  '.$extra_from[$id].' AND '.$extra_to[$id].')');
						$db->setQuery($process);		
						$resultID = $db->loadColumn();
						if(!empty($resultID)){
							$arrayID[] = 'a.id IN ('. implode(',',$resultID).')';
						}else{
							$arrayID[] = 'a.id IN ('.'0'.')';
						}
					}else{
						$process->clear('where');
						$process->where('(extrafields_id ='.intval($id).' AND `value` >= '.$extra_from[$id].')');
						$db->setQuery($process);		
						$resultID = $db->loadColumn();
						if(!empty($resultID)){
							$arrayID[] = 'a.id IN ('. implode(',',$resultID).')';
						}else{
							$arrayID[] = 'a.id IN ('.'0'.')';
						}
					}
				}else{
					if(isset($extra_to[$id]) && $extra_to[$id]){
						$process->clear('where');
						$process->where('(extrafields_id ='.intval($id).' AND `value` <= '.$extra_to[$id].')');
						$db->setQuery($process);		
						$resultID = $db->loadColumn();
						if(!empty($resultID)){
							$arrayID[] = 'a.id IN ('. implode(',',$resultID).')';
						}else{
							$arrayID[] = 'a.id IN ('.'0'.')';
						}
					}
				}
				}
				if(!empty($arrayID)){
					$query->where(implode(''.$method.' ',$arrayID));
				}
			}
			
			//End price range
			//------------------------------------------------------------------------------------//
			//Begin Price Droplist
			$extraPriceID= JRequest::getVar('expriceID',null);			
			$extra_max =JRequest::getVar('extra_max',null);
			$extra_min =JRequest::getVar('extra_min',null);					
			if(isset($extraPriceID)){
				if(!empty($extra_max) && !empty($extra_min)){
				$q = $db->getQuery(true);
				$q->select('DISTINCT  `portfolio_id`');
				$q->from('#__bt_portfolio_extrafields_values');
				$portID = array();
				foreach ($extraPriceID as $key => $id){						
					$q->clear('where');
					$q->where('(extrafields_id ='.intval($id).' AND `value` BETWEEN  '.$extra_min[$key].' AND '.$extra_max[$key].')');
					$db->setQuery($q);		
					$numb = $db->loadColumn();					
					if(!empty($numb)){						
							$portID[] = 'a.id IN ('. implode(',',$db->loadColumn()).')';
						}
						else{
							$portID[] = 'a.id IN ('.'0'.')';
					}
					
					}
					if(!empty($portID)){
					$query->where(implode(''.$method.' ',$portID));
					}
				}	
			}
			//End Price
		}
		
		//----------------------------------------Endfilter--------------------------------------------------//
		
		
		return $query;
	}
	function getCategory() {
		return $this->category;
	}
	function getParams(){
		return $this->params;
	}
	
	// Function get list categories for component navigation
	function getListCategories() {
		if(count($this->children)){
			$categoryList = $this->children;
		}
		else{
			$categoryList = $this->siblings;
		}
		$newCategoryList = array();
		foreach ($categoryList as $category) {
			$registry = new JRegistry();
			$registry->loadString($category->params);
			if ($registry->get('show_nav', 1)) {
				$newCategoryList[] = $category;
			}
		}
		return $newCategoryList;
	}
	
	// Function get list categories for mod_bt_portfolio_categories
	public function getListChildCategories($categoryId){
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$params = $this->params;
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__bt_portfolio_categories');
		$query->where('published = 1');
		$query->where('parent_id = ' . $categoryId);
		$query->where('access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		$query->where('language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		
		$query->order($params->get('c_ordering', 'ordering').' '.$params->get('c_ordering_dir', 'DESC'));
		//$db->setQuery('select * from #__bt_portfolio_categories where published = 1 and parent_id = ' . $categoryId . ' '.$ordering );
		$db->setQuery($query);
		$categoryList = $db->loadObjectlist();
		$newCategoryList = array();
		foreach ($categoryList as $category) {
			$registry = new JRegistry();
			$registry->loadString($category->params);
			if ($registry->get('show_nav', 1)) {
				$newCategoryList[] = $category;
			}
		}
		return $newCategoryList;
	}
	
	// function get grid list categories for grid list layout
	function getGridChildCategories() {
		return $this->children;
	}
	public static function callBackAllChild($id) {
		$db =  JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__bt_portfolio_categories WHERE parent_id = $id");
		$r = $db->loadColumn();
		$ids = $id;
		foreach ($r as $i){
			$ids .= "," .self::callBackAllChild($i);
		}
		return $ids;
	}
}