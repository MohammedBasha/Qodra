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
 * Extrafields Model
 */
class Bt_portfolioModelExtrafields extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JController
     * @since	1.6
     */
    public function __construct($config = array()) { 
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'type', 'a.type',
                'published', 'a.published',
                'ordering', 'a.ordering'
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
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        // Adjust the context to support modal layouts.
        if ($layout = JRequest::getVar('layout')) {
            //$this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
        $this->setState('filter.published', $published);

        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');

        $this->setState('filter.type', $type);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery() {
        // Create a new query object.
        $db =  JFactory::getDBO();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();

        // Select fields
        $query->select('a.*');

        // From the bt portfolio_extrafields table
        $query->from('#__bt_portfolio_extrafields as a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        }

        // Filter by featured state
        $type = $this->getState('filter.type');
        if ($type) {
            $query->where('a.type = ' . (Bt_portfolioLegacyHelper::isLegacy() ? $db->quote($db->getEscaped($type)) : $db->quote($db->escape($type))));
        }

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . (Bt_portfolioLegacyHelper::isLegacy() ? $db->getEscaped($search, true) : $db->escape($search, true)) . '%');

                $query->where('a.name LIKE ' . $search);
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        
        $orderDirn = $this->state->get('list.direction'); 
        $query->order(Bt_portfolioLegacyHelper::isLegacy() ? $db->getEscaped($orderCol . ' ' . $orderDirn) : $db->escape($orderCol . ' ' . $orderDirn));
        
        return $query;
    }

}
