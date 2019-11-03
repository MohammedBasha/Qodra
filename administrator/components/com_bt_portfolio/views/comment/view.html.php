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
 * Comment View
 */
class Bt_portfolioViewComment extends BTView
{
	/**
	 * display method of Comment view
	 * @return void
	 */
	public function display($tpl = null)
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;


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

	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);		
		JToolBarHelper::title(JText::_('COM_BT_PORTFOLIO_EDIT_COMMENT'), 'article-add.png');
		JToolBarHelper::apply('comment.apply');
		JToolBarHelper::save('comment.save');
		JToolBarHelper::cancel('comment.cancel', 'JTOOLBAR_CLOSE');	

	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BT_PORTFOLIO_COMMENT_EDITTING'));
		$document->addScript(JURI::root() . "/administrator/components/com_bt_portfolio/views/comment/submitbutton.js");
		JText::script('COM_BT_PORTFOLIO_ERROR_UNACCEPTABLE');
	}
}
