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
 * Image Table class
 */
class Bt_portfolioTableImage extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__bt_portfolio_images', 'id', $db);
	}
	public function store($updateNulls = false)
	{

		$db = JFactory::getDBO();
		if($this->default){
			$db->setQuery('UPDATE #__bt_portfolio_images Set `default` = 0 where item_id = '.$this->item_id);
			$db->query();
		}
		
		return parent::store($updateNulls);

	}
}
