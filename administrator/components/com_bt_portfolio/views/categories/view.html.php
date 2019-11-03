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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Categories View
 */
class Bt_portfolioViewCategories extends BTView
{
	/**
	 * Categories view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->legacy = Bt_portfolioLegacyHelper::isLegacy();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::addNew('category.add');
		JToolBarHelper::editList('category.edit');
		JToolBarHelper::divider();
		JToolBarHelper::title(JText::_('COM_BT_PORTFOLIO_CATEGORY_MANAGER'), 'categories.png');
		JToolBarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::checkin('categories.checkin');
		JToolBarHelper::deleteList('', 'categories.delete');
		JToolBarHelper::preferences('com_bt_portfolio');
		
		$this->sidebar = '';
		Bt_portfolioHelper::addSubmenu(JRequest::getCmd('view', 'cpanel')); //added since 2.0 for j3.0
		if(!$this->legacy){
			JHtmlSidebar::setAction('index.php?option=com_bt_portfolio&view=categories');

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', Bt_portfolioHelper::getPublishedOptions(), 'value', 'text', $this->state->get('filter.published'), true)
			);
			
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_CATEGORY'),
				'filter_parent_id',
				JHtml::_('select.options', Bt_portfolioHelper::getCategoryOptions(), 'value', 'text', $this->state->get('filter.parent_id'))
			);
			
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_ACCESS'),
				'filter_access',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
			);
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_LANGUAGE'),
				'filter_language',
				JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
			);
			$this->sidebar = JHtmlSidebar::render();
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BT_PORTFOLIO_CATEGORY_MANAGER_TITLE'));
	}
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'parent_title' => JText::_('Parent'),
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
