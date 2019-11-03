<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Portfolio Table class
 */
class Bt_portfolioTableExtrafield extends JTable
{	
	protected $categories = null;
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__bt_portfolio_extrafields', 'id', $db);
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
		if (isset($array['type_link']) && is_array($array['type_link'])) 
		{
		}
		
		if( parent::bind($array, $ignore)){
			
			return true;
		}
		return FALSE;
	}
	
	/**
	 * Stores a extrafield
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		
		// Verify that the name is unique
		$table = JTable::getInstance('Extrafield', 'Bt_portfolioTable');
		if ($table->load(array('name'=>$this->name)) && ($table->id != $this->id || $this->id==0)) {
			$this->setError(JText::_('COM_BT_PORTFOLIO_ERROR_UNIQUE_EXTRAFIELD_NAME'));
			return false;
		}
		
		$categories = $this->categories;
		if(empty($categories)|| in_array(0,$categories)){
			$this->all = 1;
			$categories = array();
		}else{
			$this->all = 0;
		}
		unset($this->categories);
		unset($this->type_text);
		unset($this->type_string);
		unset($this->type_measurement);
		unset($this->type_link);
		unset($this->type_dropdown);
		unset($this->type_date);
		unset($this->type_image);
		// Attempt to store the data.
		if(parent::store($updateNulls)){
			if(!empty($categories)){
				$this->_db->setQuery("update #__bt_portfolio_categories set extra_fields = REPLACE(extra_fields,',". $this->id .",',',') where id not in (".implode(',',$categories).')');
				$this->_db->query();
				$this->_db->setQuery('SELECT id FROM #__bt_portfolio_categories WHERE id in ('.implode(',',$categories).') and extra_fields like \'%,' . $this->id . ',%\'');
				$assigned = $this->_db->loadColumn();
				$unassigned = array_diff($categories,$assigned);
				foreach($unassigned as $catid){
					$this->_db->setQuery("update #__bt_portfolio_categories set extra_fields = concat(extra_fields,". $this->id .",',') where id=$catid");
					$this->_db->query();
				}
			}else{
				$this->_db->setQuery("update #__bt_portfolio_categories set extra_fields = REPLACE(extra_fields,',". $this->id .",',',')");
				$this->_db->query();
			}
			return true;
		}else{
			return false;
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
			//$this->extra_fields = $extra_fields;
			$this->{'type_'.$this->type} = $this->default_value;
			
			if($this->id && $this->all==0){
				$this->_db->setQuery('SELECT id FROM #__bt_portfolio_categories WHERE extra_fields like \'%,' . $this->id . ',%\'');
				$this->categories = implode(',',$this->_db->loadColumn());
			}else{
				$this->categories = 0;
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
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);

		// Set ordering
		if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder();
		}
		if($this->type=='0') return false;
		if(is_array($_REQUEST['jform']['type_'.$this->type])){
			$_REQUEST['jform']['type_'.$this->type] = serialize($_REQUEST['jform']['type_'.$this->type]);
		}
		$this->default_value = $_REQUEST['jform']['type_'.$this->type];
		return true;
	}
}
