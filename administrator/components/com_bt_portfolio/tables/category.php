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
 * Category Table class
 */
class Bt_portfolioTableCategory extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	var $_path = array();

	function __construct(&$db)
	{
		parent::__construct('#__bt_portfolio_categories', 'id', $db);
		if($this->id==0){
			$exAll = implode(',',self::getExtrafieldsAll());
			if(!$this->extra_fields && $exAll) $this->extra_fields .= ',';
			if($exAll) $this->extra_fields .= $exAll. ',';
			
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

		if (key_exists( 'extra_fields', $array ) && is_array( $array['extra_fields'] )) {
			$array['extra_fields'] = ','.implode( ',', $array['extra_fields'] ).',';
		}
		if(!count($array['extra_fields'])){
			$array['extra_fields'] = ",";
		}

		if(!key_exists( 'inherit', $array )){
			$array['inherit'] = 0;
		}
		return parent::bind($array, $ignore);
	}
	
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
			$exAll = implode(',',self::getExtrafieldsAll());
			if(!$this->extra_fields && $exAll) $this->extra_fields .= ',';
			if($exAll) $this->extra_fields .= $exAll. ',';
			return true;
		}
		else
		{
		
			return false;
		}
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
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		// Transform the params field
		//if (is_array($this->params)) {
		//	$registry = new JRegistry();
		//	$registry->loadArray($this->params);
		//	$this->params = (string)$registry;
		//}

		// Verify that the alias is unique
		$table = JTable::getInstance('Category', 'Bt_portfolioTable');
		if ($table->load(array('alias' => $this->alias, 'parent_id' => $this->parent_id, 'language' => $this->language))
			&& ($table->id != $this->id || $this->id == 0))
		{

			$this->setError(JText::_('COM_BT_PORTFOLIO_CATEGORY_ALIAS_UNIQUE'));

			return false;
		}
			
		$exAll = self::getExtrafieldsAll();
		if($this->extra_fields=='') $this->extra_fields = ',';
		foreach($exAll as $ex){
			if(substr_count($this->extra_fields, ','.$ex.',')){
				$this->extra_fields = str_replace(','.$ex.',', ',', $this->extra_fields);
			}else{
				$this->_db->setQuery("update #__bt_portfolio_categories set extra_fields = concat(extra_fields,". $ex .",',')");	
				$this->_db->query();
				$this->_db->setQuery('update #__bt_portfolio_extrafields set `all` = 0');
				$this->_db->query();
			}
		}
		
		if ($this->id) {

			// Get the old row
			$oldrow = JTable::getInstance('Category', 'Bt_portfolioTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}
			// Attempt to store the data.
			return parent::store($updateNulls);
			// Need to reorder ?
			if ( $oldrow->parent_id != $this->parent_id)
			{
				// Reorder the oldrow
				$this->reorder('`parent_id`=' . $this->_db->Quote($oldrow->parent_id));
			}
		} else {

			// Attempt to store the data.
			return parent::store($updateNulls);
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
		if (empty($this->ordering) || JRequest::getCmd('task') == 'save2copy') {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`parent_id`=' . $this->_db->Quote($this->parent_id));
		}
		return true;
	}
	public function getPath(){
		if(!count($this->_path)){
			$this->_path[] = $this->id . ':' . $this->alias;
			$table = JTable::getInstance('Category', 'Bt_portfolioTable');

			if($table->load($this->parent_id) && $table->id){
				$this->_path[] = $table->id . ':' . $table->alias;
			}
		}
		return $this->_path;
	}
	public function getExtrafieldsAll(){
		$db= JFactory::getDBO();
		$db->setQuery('SELECT id FROM #__bt_portfolio_extrafields WHERE published = 1 AND `all` = 1 order by ordering ');
		return $db->loadColumn();
	}
}
