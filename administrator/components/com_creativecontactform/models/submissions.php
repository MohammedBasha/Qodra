<?php
/**
 * Joomla! component Creative Contact Form
 *
 * @version $Id: 2012-04-05 14:30:25 svn $
 * @author creative-solutions.net
 * @package Creative Contact Form
 * @subpackage com_creativecontactform
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restircted access');

jimport('joomla.application.component.modellist');

class CreativeContactFormModelSubmissions extends JModelList {
	
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
					'id', 'sp.id',
					'name', 'sp.name',
					'template_title',
					'num_fields',
					'published', 'sp.published',
					'checked_out', 'sp.checked_out',
					'checked_out_time', 'sp.checked_out_time',
					'access', 'sp.access', 'access_level',
					'ordering', 'sp.ordering',
					'featured', 'sp.featured',
					'publish_up', 'sp.publish_up',
					'publish_down', 'sp.publish_down'
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
	
		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}
	
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	
		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
	
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$formId = $this->getUserStateFromRequest($this->context.'.filter.form_id', 'filter_form_id');
		$this->setState('filter.form_id', $formId);

		$statusId = $this->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);
	
		// List state information.
		parent::populateState('sp.id', 'desc');
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
	
		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'
							sp.id, 
							sp.name,
							sp.name, 
							sp.email, 
							sp.date, 
							sp.ip, 
							sp.page_title, 
							sp.page_url, 
							sp.message, 
							sp.browser,  
							sp.op_s,  
							sp.sc_res,  
							sp.viewed,
							sp.star_index,
							sp.imp_index,
							sp.uploads
						'
				)
		);
		$query->from('#__creative_submissions AS sp');
		
		// Join over the answers.
		$query->select('sf.name AS form_name');
		$query->join('LEFT', '#__creative_forms AS sf ON sf.id=sp.id_form');
	
	
		// Filter by  forms
		$formId = $this->getState('filter.form_id');
		if (is_numeric($formId)) {
			$query->where('sp.id_form = '.(int) $formId);
		}	
		// Filter by  forms
		$statusId = $this->getState('filter.status_id');
		if (is_numeric($statusId)) {
			if($statusId == 1)
				$query->where('sp.star_index != \'0\'');
			if($statusId == 2)
				$query->where('sp.imp_index != \'0\'');
		}
		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('sp.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(sp.name LIKE '.$search.' OR sp.email LIKE '.$search.' OR sp.message LIKE '.$search.' OR sp.ip LIKE '.$search.')');
			}
		}
	
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'sp.id');
		$orderDirn	= $this->state->get('list.direction', 'desc');
		/*
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		*/
		$query->order($db->escape($orderCol.' '.$orderDirn));
		$query->group('sp.id');
	
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

		/**
	 * Method to get category options
	 *
	 */
	public function getCreativeForms() {
		$db		= $this->getDbo();
		$sql = 
				"
					SELECT 
						cf.id, cf.name, COUNT(cs.id) count_cs
					FROM 
						`#__creative_forms` cf 
					LEFT JOIN 
						`#__creative_submissions` cs
					ON
						cs.id_form = cf.id
					WHERE 
						`published` <> '-2' 
					GROUP by
						cf.id
					order by 
						`id` 
				";
		$db->setQuery($sql);
		return $opts = $db->loadObjectList();
	}

	function deleteSubmission($pks) {
		if(is_array($pks)) {
			foreach($pks as $f_id) {
				//delete form
				$query = "DELETE FROM `#__creative_submissions` WHERE `id` = '".$f_id."'";
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}

	function make_read($pks) {
		if(is_array($pks)) {
			foreach($pks as $f_id) {
				//delete form
				$query = "UPDATE `#__creative_submissions` SET `viewed` = '1' WHERE `id` = '".$f_id."'";
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}
	function make_unread($pks) {
		if(is_array($pks)) {
			foreach($pks as $f_id) {
				//delete form
				$query = "UPDATE `#__creative_submissions` SET `viewed` = '0' WHERE `id` = '".$f_id."'";
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
	}
	
}