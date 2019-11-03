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

require_once dirname(__FILE__) . '/portfolios.php';

class Bt_portfolioModelFeatured extends Bt_portfolioModelPortfolios {
	// function populate state of model
	protected function populateState($ordering = 'ordering', $direction = 'ASC') {
		parent::populateState($ordering, $direction);
		$this->setState('filter.featured', true);
	}
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= $this->getState('filter.featured');
		return parent::getStoreId($id);
	}
	
}