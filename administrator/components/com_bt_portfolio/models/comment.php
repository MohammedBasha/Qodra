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
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Comment Model
 */
class Bt_portfolioModelComment extends JModelAdmin
{
	/**
	 * Class constructor.
	 *
	 * @param	array	$config	A named array of configuration variables.
	 *
	 * @return	JControllerForm
	 * @since	1.6
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 */

	public function getTable($type = 'Comment', $prefix = 'Bt_portfolioTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bt_portfolio.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;

	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bt_portfolio.edit.comment.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}


	
	public  function save($data){
		if(parent::save($data)){
			$this->updateCounter();
			return true;
		}
		else{
			return false;
		}
	}
	public function delete(&$pks){
		// IF delete successfully
		if(parent::delete($pks)){
			$this->updateCounter();
			return true;
		}
		else{
			return false;
		}
	}
	public function publish(&$pks, $value = 1){
		if(parent::publish($pks,$value)){
			$this->updateCounter();
			return true;
		}
		else{
			return false;
		}
	}
	public function updateCounter(){
		$db = JFactory::getDBO();
		$db->setQuery("update #__bt_portfolios as p set p.review_count = (select count(*) from #__bt_portfolio_comments as c where c.item_id = p.id and c.published = 1)");
		$db->excute();
	}
}
