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
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

/**
 * Portfolio Table class
 */
class Bt_portfolioTablePortfolio extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__bt_portfolios', 'id', $db);
		
		if($this->id==0){
			$this->extra_fields = self::loadExtraFields($this->extra_fields, 0);
		}
		
	}
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '')
	{
		
		 if (isset($array['params']) && is_array($array['params']))
		 {
		 // Convert the params field to a string.
		 $parameter = new JRegistry;
		 $parameter->loadArray($array['params']);
		 $array['params'] = (string)$parameter;

		 }
		$extrafields = JRequest::getVar('extra_fields', array(), 'post', 'array');

		if (count($extrafields))
		{
			$array['extra_fields'] = serialize($extrafields);
		}
		//$array['catids'] = implode(',',self::convertToArray($array['catids']));
		return parent::bind($array, $ignore);
	}

	/**
	 * Stores a portfolio
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{

		$date = JFactory::getDate();
		$user = JFactory::getUser();
		if(is_array($this->catids))
		{
			$this->catids = ',' . implode(',', $this->catids) . ',';
		}
		// Verify that the alias is unique
		$table = JTable::getInstance('Portfolio', 'Bt_portfolioTable');
		if ($table->load(array('alias' => $this->alias, 'language' => $this->language))
			&& ($table->id != $this->id || $this->id == 0))
		{

			$this->setError(JText::_('COM_BT_PORTFOLIO_PORTFOLIO_ALIAS_UNIQUE'));

			return false;
		}

		if ($this->id)
		{
			// Existing item
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');

			// Get the old row
			$oldrow = JTable::getInstance('Portfolio', 'Bt_portfolioTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}
			if(!$this->check()){
				return false;
			}
			// Attempt to store the data.
			return parent::store($updateNulls);

		}
		else
		{
			// New portfolio
			if (!intval($this->created))
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
			if(!$this->check()){
				return false;
			}
			// Attempt to store the data.
			return parent::store($updateNulls);
		}

	}

	/**
	 * Overloaded load function
	 *
	 * @param       int $pk primary key
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true)
	{
		if (parent::load($pk, $reset))
		{
			//Convert the params field to a registry.
			//$extra_fields = new JRegistry;
			//$extra_fields->loadJSON($this->extra_fields);
			//$this->extra_fields = unserialize($this->extra_fields );
			//$this->catids = self::convertToArray($this->catids);

			if (JRequest::getVar("view") == "portfolio")
			{
				if(JRequest::getVar('catids') && (JRequest::getVar('layout')=='edit_extrafields' || JRequest::getVar('layout')=='edit')){
					$this->catids = JRequest::getVar('catids');
				}
				if($this->catids)
				{
					$this->extra_fields = self::loadExtraFields($this->extra_fields, $this->catids);
				}

			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	1.5
	 */
	function check()
	{
		jimport('joomla.filter.output');

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias)) {
			$this->alias = JApplication::stringURLSafe($this->title);
		}

		// Set ordering
		if (empty($this->ordering) || JRequest::getCmd('task') == 'save2copy')
		{
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder();
		}
		if(!count($this->catids)){
			$this->setError("Category is required!");
			return false;
		}
		else{

			if(count($this->catids) == 1 && !$this->catids[0]){
				$this->setError("Category is required!");
				return false;}
		}
		return true;
	}
	public static function loadExtraFields($els, $catids)
	{
		static $exstore = array();
		if ($els){
			$els = @unserialize($els);
		}
		else{
			$els = array();
		}	
		$catids = self::convertToArray($catids);
		$keyString = implode(',',$catids);
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$store = $session->get('ex-store',array());
		$result = array();
		if(!array_key_exists($keyString,$exstore)){
			$db = JFactory::getDBO();
			$extrafield_ids = "";
			if(count($catids)){
				$db->setQuery('SELECT inherit FROM #__bt_portfolio_categories WHERE id in (' . implode(',', $catids) . ')');
				if ($db->loadResult())
				{
					$catidsArr = array();
					foreach ($catids as $catid)
					{
						$catidsArr[] = self::categoryParent($catid);
					}
					$db->setQuery('SELECT extra_fields FROM #__bt_portfolio_categories WHERE id in (' . implode(',', $catidsArr) . ') ');
				}
				else
				{
					$db->setQuery('SELECT extra_fields FROM #__bt_portfolio_categories WHERE id in (' . implode(',', $catids) . ')');
				}
				$r = $db->loadColumn();
				if(count($r))
				foreach ($r as $e)
				{
					if ($extrafield_ids && $e)
						$extrafield_ids .= ',';
					$extrafield_ids .= $e;
				}
			}
			$extrafield_ids =explode(',',$extrafield_ids);
			$extrafield_ids= self::remove_array_empty_values($extrafield_ids);
			$extraid = implode(", ", $extrafield_ids);	
			if ($extraid)
			{		
				$db->setQuery('SELECT * FROM #__bt_portfolio_extrafields WHERE published = 1 AND (id in (' . $extraid . ') or `all` = 1) order by ordering ');
			}else{
				$db->setQuery('SELECT * FROM #__bt_portfolio_extrafields WHERE published = 1 AND `all` = 1 order by ordering ');
			}
			$result = $db->loadObjectList();
			foreach ($result as $k => $e){
				switch($e->type){
				case 'link':
					$e->default_value = @unserialize($e->default_value);
					break;
				case 'measurement':
					$e->default_value = @unserialize($e->default_value);							
					break;
				case 'dropdown':
					$e->default_value = explode("\r\n", $e->default_value);
					break;
				default:
					break;
				}
			}
			$exstore[$keyString] = $result;
		}
		$ex = array();
		foreach ($exstore[$keyString] as $k => $e)
		{
			$ex[$k] = clone $e;
			if (array_key_exists($ex[$k]->id, $els))
			{
				$ex[$k]->value = $els[$ex[$k]->id];
			}else{
				if($ex[$k]->type=='measurement'){
					$ex[$k]->value = $ex[$k]->value = $ex[$k]->default_value[0]; 
				}else{
					$ex[$k]->value = $ex[$k]->default_value;
				}
			}
		}
		$newef = array();
		if($app->isSite()){
			foreach($ex as &$ef){
				if(!is_array($ef->value)){   
					if(@trim($ef->value)){
						$newef[] = $ef;
					}
				}else{
					if(@trim($ef->value[0])){
						$newef[] = $ef;
					}
				}
			}
			return $newef;
		}
		
		return $ex;
        
	}
	
	public static function categoryParent($id)
	{
		$db = JFactory::getDBO();
		$db->setQuery('SELECT parent_id FROM #__bt_portfolio_categories WHERE id = ' . $id);
		$r = $db->loadResult();
		if ($r)
			$id .= "," . self::categoryParent($r);
		return $id;
	}

	/**
	 * Method to get all child a category
	 *
	 * @return	string	ids - separate by comma
	 */
	public static function remove_array_empty_values($array)
	{
			$new_array = array();			 
			$null_exceptions = array();			 
			foreach ($array as $key => $value)
			{
				$value = trim($value);		
			 
				if(!in_array($value, $null_exceptions) && $value != "")
				{
				$new_array[] = $value;
				}
			}
			return $new_array;
	}
	
	public static function convertToArray($catids)
	{
		$result = array();
		if (!is_array($catids))
		{
			$catids = explode(',', $catids);
			foreach ($catids as $catid)
			{
				if ($catid)
					$result[] = $catid;
			}
		}
		return $result;
	}
}
