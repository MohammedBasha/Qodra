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
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * Comments Model
 */
class Bt_portfolioModelComments extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.id','a.name', 'a.email','a.website','a.title','a.created', 'a.published','portfolio'
				);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published= $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$author= $this->getUserStateFromRequest($this->context.'.filter.author', 'filter_author');
		$this->setState('filter.author', $author);
		// List state information.
		parent::populateState('a.created', 'asc');
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db =  JFactory::getDBO();
		$query = $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select fields
		$query->select('a.*');

		// From the bt portfolio_comments table
		$query->from('#__bt_portfolio_comments as a');

		$query->select('pl.title AS portfolio');
		$query->join('LEFT', '#__bt_portfolios AS pl ON pl.id=a.item_id');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}
		// Filter by author state
		$author = $this->getState('filter.author');
		if ($author) {
			$query->where('a.name = '.($db->quote( Bt_portfolioLegacyHelper::isLegacy() ?  $db->getEscaped($author, true) : $db->escape($author, true))));
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.( Bt_portfolioLegacyHelper::isLegacy() ? $db->getEscaped($search, true) : $db->escape($search, true) ).'%');
				$query->where('a.title LIKE '.$search,'or');
				$query->where('a.content LIKE '.$search,'or');
				$query->where('a.name LIKE '.$search,'or');
				$query->where('a.website LIKE '.$search,'or');
				$query->where('a.email LIKE '.$search,'or');
				$query->where('pl.title LIKE '.$search,'or');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order(Bt_portfolioLegacyHelper::isLegacy() ? $db->getEscaped($orderCol.' '.$orderDirn) : $db->escape($orderCol.' '.$orderDirn) );
		return $query;
	}
}
