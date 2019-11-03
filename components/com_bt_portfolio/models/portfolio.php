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
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class Bt_portfolioModelPortfolio extends JModelItem {

	protected $_context = 'com_bt_portfolio.portfolio';

	protected function populateState() {
		$app = JFactory::getApplication();
		$params = $app->getParams();
		// Load the object state.
		$id = JRequest::getInt('id');
		$this->setState('portfolio.id', $id);
		// Load the parameters.
		$this->setState('params', $params);
	}
	public function &getItem($id = null) {
		if ($this->_item === null) {
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('portfolio.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Portfolio', 'Bt_portfolioTable');

			// Attempt to load the row.
			if ($table->load($id)) {
				// Check published state.
				if ($published = $this->getState('filter.published')) {
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			}
			else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}

	public function hit($id = null) {
		if (empty($id)) {
			$id = $this->getState('portfolio.id');
		}

		$table = JTable::getInstance('Portfolio', 'Bt_portfolioTable');
		return $table->hit($id);
	}
	function getImages() {
		$db = $this->getDbo();
		$id = $this->getState($this->getName() . '.id');
		$params = $this->getState('params');
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__bt_portfolio_images as a');
		$query->where('a.item_id = '.$id);
		
		// Ordering
		$ordering = $params->get('i_ordering', 'ordering');
		$orderingDir = $params->get('i_ordering_dir', 'DESC');
		switch($ordering){
			case 'default':
				$query->order('a.default desc, a.ordering '.$orderingDir);
				break;
			case 'random':
				$query->order('rand()');
				break;
			default:
				$query->order('a.'.$ordering .' '.$orderingDir);
				break;
		}
		$db->setQuery($query);
		return $db->loadObjectList();

	}

}
