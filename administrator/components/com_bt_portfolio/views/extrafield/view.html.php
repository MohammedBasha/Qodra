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
 * Extrafield View
 */
class Bt_portfolioViewExtrafield extends BTView {

    /**
     * display method of Extrafield view
     * @return void
     */
    public function display($tpl = null) {
        
        
        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->images = $this->get("Images");

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
    protected function addToolbar() {
        if (Bt_portfolioLegacyHelper::isLegacy()) {
            JRequest::setVar('hidemainmenu', true);
        } else {
            JRequest::setVar('hidemainmenu', false);
        }
        $user = JFactory::getUser();
        $userId = $user->get('id');
        $isNew = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        JToolBarHelper::title($isNew ? JText::_('COM_BT_PORTFOLIO_NEW_EXTRAFIELD') : JText::_('COM_BT_PORTFOLIO_EDIT_EXTRAFIELD'), 'article-add.png');

        if ($isNew) {
            JToolBarHelper::apply('extrafield.apply');
            JToolBarHelper::save('extrafield.save');
            JToolBarHelper::save2new('extrafield.save2new');

            JToolBarHelper::cancel('extrafield.cancel');
        } else {
            // Can't save the record if it's checked out.
            if (!$checkedOut) {
                JToolBarHelper::apply('extrafield.apply');
                JToolBarHelper::save('extrafield.save');
                JToolBarHelper::save2new('extrafield.save2new');
            }

            JToolBarHelper::cancel('extrafield.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_BT_PORTFOLIO_EXTRAFIELD_CREATING') : JText::_('COM_BT_PORTFOLIO_EXTRAFIELD_EDITTING'));
        $document->addScript(JURI::root() . "/administrator/components/com_bt_portfolio/views/extrafield/submitbutton.js");
        JText::script('COM_BT_PORTFOLIO_ERROR_UNACCEPTABLE');
    }

}
