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
require_once(JPATH_ADMINISTRATOR.'/components/com_bt_portfolio/tables/portfolio.php');

class Bt_portfolioViewPortfolios extends BTView {

	protected $items = null;

	function display($tpl = null) {
		// Initialise variables
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$items = $this->get('items');
		$pagination = $this->get('pagination');
		$category = $this->get('category');
		$listCategories = $this->get('listCategories');
		$gridCategories = $this->get('gridChildCategories');
		$params = $this->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		// Content plugin content
		foreach ($items as $item){
			$item->description = JHTML::_('content.prepare', $item->description);
			$item->extra_fields = Bt_portfolioTablePortfolio::loadExtraFields($item->extra_fields,$item->catids);
		}

		
		$category_layout = $params->get('layout', 'default');
		$show_voting = $params->get('show_voting');

		$this->assignRef('params', $params);

		$this->assignRef('pagination', $pagination);
		$this->assignRef('gridCategories', $gridCategories);
		$this->assignRef('items', $items);
		$this->assignRef('show_voting', $show_voting);
		$this->assignRef('user', $user);
		$this->assignRef('listCategories', $listCategories);
		$this->assignRef('category', $category);
		$theme = $params->get('theme', 'default');
		$this->_addPath('template', JPATH_COMPONENT . '/themes/default/layout/list');
		$this->_addPath('template', JPATH_COMPONENT . '/themes/' . $theme . '/layout/list');
		$this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_portfolio/default/layout/list');
		$this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_portfolio/'. $theme . '/layout/list');
		$this->setLayout($category_layout);
		if(JRequest::getVar('task')=='filter'){
			$this->params->set('show_childcat',0);
		}
		
		$this->_prepareDocument();
		parent::display($tpl);
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		$menu = $menus->getActive();
		$id = (int) @$menu->query['catid'];
		
		

		$title = $this->params->get('page_title', '');

		if ($menu && ($menu->query['option'] != 'com_bt_portfolio' || $menu->query['view'] != 'portfolios' || $this->category->id != $id))
		{
			if($this->category->id != $id){
				$pathway->addItem($this->category->title, '');
			}
			$newTitle = $this->category->params->get('page_title',$this->category->title);
			if($newTitle) $title = $newTitle;
		}
		
		if (empty($title)) {
			$title = $this->category->title;
		}
		
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
		$this->document->setTitle($title);
		
		// META DATA
		if ($this->params->get('metadesc'))
		{
			$this->document->setDescription($this->params->get('metadesc'));
		}
		elseif (!$this->params->get('metadesc') && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('metakey'))
		{
			$this->document->setMetadata('keywords', $this->params->get('metakey'));
		}
		elseif (!$this->params->get('metakey') && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		elseif (!$this->params->get('robots') && $this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

	}
}