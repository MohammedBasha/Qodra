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

 
/**
 * Comment View
 */
class Bt_portfolioViewComments extends BTView
{
	/**
	 * Comment view display method
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
		JToolBarHelper::editList('comment.edit');
		JToolBarHelper::divider();
		JToolBarHelper::title(JText::_('COM_BT_PORTFOLIO_COMMENTS_MANAGER'), 'article.png');
		JToolBarHelper::publish('comments.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('comments.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'comments.delete');		
		JToolBarHelper::preferences('com_bt_portfolio');
		
		$this->sidebar = '';
		Bt_portfolioHelper::addSubmenu(JRequest::getCmd('view', 'cpanel')); //added since 2.0 for j3.0
		if(!$this->legacy){
			JHtmlSidebar::setAction('index.php?option=com_bt_portfolio&view=comments');

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', Bt_portfolioHelper::getPublishedOptions(), 'value', 'text', $this->state->get('filter.published'), true)
			);
			
			JHtmlSidebar::addFilter(
				JText::_('COM_BT_PORTFOLIO_COMMENT_SELECT_AUTHOR'),
				'filter_author',
				JHtml::_('select.options', Bt_portfolioHelper::getAuthorOptions(), 'value', 'text', $this->state->get('filter.author'), true)
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
		$document->setTitle(JText::_('COM_BT_PORTFOLIO_COMMENTS_MANAGER_TITLE'));
	}
	protected function getSortFields()
	{
		return array(
			'a.published' => JText::_('JSTATUS'),
			'a.title' => JText::_('COM_BT_PORTFOLIO_COMMENT_TITLE'),
			'a.content' => JText::_('COM_BT_PORTFOLIO_COMMENT_CONTENT_LABEL'),
			'a.name' => JText::_('COM_BT_PORTFOLIO_COMMENT_AUTHOR'),
			'a.email' => JText::_('COM_BT_PORTFOLIO_COMMENT_EMAIL'),
			'a.website' => JText::_('COM_BT_PORTFOLIO_COMMENT_WEBSITE'),
			'a.portfolio' => JText::_('COM_BT_PORTFOLIO_COMMENT_PORTFOLIO'),
			'a.created' => JText::_('COM_BT_PORTFOLIO_COMMENT_CREATED'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}