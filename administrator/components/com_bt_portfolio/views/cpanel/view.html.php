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


class Bt_portfolioViewCpanel extends BTView {

    /**
     * Categories view display method
     * @return void
     */
    function display($tpl = null) {
        Bt_portfolioLegacyHelper::getCSS();
		$this->legacy = Bt_portfolioLegacyHelper::isLegacy();

        // Set the toolbar
        $this->addToolBar();

        $buttons = $this->getButtons();
        $this->assign('buttons', $buttons);

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        JToolBarHelper::preferences('com_bt_portfolio');
		$this->sidebar = '';
		Bt_portfolioHelper::addSubmenu(JRequest::getCmd('view', 'cpanel')); //added since 2.0 for j3.0
		if(!$this->legacy){
			$this->sidebar = JHtmlSidebar::render();
		}
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $document = JFactory::getDocument();
        JToolBarHelper::title(JText::_('COM_BT_PORTFOLIO_CPANEL'), 'portfolio.png');
        $document->setTitle(JText::_('COM_BT_PORTFOLIO_CPANEL_TITLE'));
    }

    function getButtons() {

        return array(
//            array(
//                'link' => JRoute::_('index.php?option=com_config&view=component&component=com_bt_portfolio&path=&tmpl=component'),
//                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/configuration.png',
//                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_CONFIGURATION'),
//                'class' => 'modal',
//                'rel' => "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
//            ),
            array(
                'link' => JRoute::_('index.php?option=com_bt_portfolio&view=categories'),
                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/categories.png',
                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_CATEGORIES_MANAGER'),
                'class' => '',
                'rel' => ''
            ),
            array(
                'link' => JRoute::_('index.php?option=com_bt_portfolio&view=portfolios'),
                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/portfolio.png',
                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_PORTFOLIOS_MANAGER'),
                'class' => '',
                'rel' => ''
            ),
            array(
                'link' => JRoute::_('index.php?option=com_bt_portfolio&view=comments'),
                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/review.png',
                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_REVIEWS_MANAGER'),
                'class' => '',
                'rel' => ''
            ),
            array(
                'link' => JRoute::_('index.php?option=com_bt_portfolio&view=extrafields'),
                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/extra-field.png',
                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_EXTRAFIELDS_MANAGER'),
                'class' => '',
                'rel' => ''
            ),
            array(
                'link' => JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&layout=edit'),
                'image' => JURI::root() . 'components/com_bt_portfolio/assets/icon/portfolioNew.png',
                'text' => JText::_('COM_BT_PORTFOLIO_CPANEL_ADD_NEW_PORTFOLIO'),
                'class' => '',
                'rel' => ''
            )
        );
    }

}
