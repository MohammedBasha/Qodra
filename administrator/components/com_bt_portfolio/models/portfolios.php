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
 * Bt_portfolio Model
 */
class Bt_portfolioModelPortfolios extends JModelList
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
				'id', 'a.id',
				'title', 'a.title',
				'catid', 'a.catid', 'category_title','language','access_level',
				'published', 'a.published',
				'ordering', 'a.ordering',
				'created', 'a.created',
				'vote_sum/vote_count',
				'featured', 'a.featured',
				'hits', 'a.hits'
				);
		}
		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
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

		$featured= $this->getUserStateFromRequest($this->context.'.filter.featured', 'filter_featured');
		$this->setState('filter.featured', $featured);

		$catid = $this->getUserStateFromRequest($this->context.'.filter.catid', 'filter_catid');
		$this->setState('filter.catid', $catid);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select fields
		$query->select('a.*');
		
		// From the bt portfolios table
		$query->from('#__bt_portfolios as a');

		// Join over the categories.
		$query->select('b.id as category_id, GROUP_CONCAT(b.title) AS category_title, b.alias AS category_alias');
		$query->join('LEFT', '#__bt_portfolio_categories AS b ON  a.catids = \',,\' OR a.catids like CONCAT(\'%,\',b.id,\',%\')');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');


		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');


		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}

		// Filter by featured state
		$featured = $this->getState('filter.featured');
		if (is_numeric($featured)) {
			$query->where('a.featured = '.(int) $featured);
		}

		// Filter by category.
		$catid = $this->getState('filter.catid');
		if (is_numeric($catid)) {
			$query->where('b.id in ('.self::categoryChild($catid).')');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
		    $groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.title LIKE '.$search);
			}
		}
		
		$query->group('a.id');
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));
               
		return $query;
	}


	/**
	 * Method to get all child a category
	 *
	 * @return	string	ids - separate by comma
	 */
	function categoryChild($id) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__bt_portfolio_categories WHERE parent_id = $id");
		$r = $db->loadColumn();
		$ids = $id;
		foreach ($r as $i){
			$ids .= "," .self::categoryChild($i);
		}
		return $ids;
	}
}