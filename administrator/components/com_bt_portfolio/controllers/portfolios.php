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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Portfolios Controller
 */
class Bt_portfolioControllerPortfolios extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'Portfolio', $prefix = 'Bt_portfolioModel',$config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    function unfeatured() {
        self::featured();
    }

    function featured() {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $user = JFactory::getUser();
        $ids = JRequest::getVar('cid', array(), '', 'array');
        $values = array('featured' => 1, 'unfeatured' => 0);

        $task = $this->getTask();
        $value = JArrayHelper::getValue($values, $task, 0, 'int');

        if (empty($ids)) {
            JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Publish the items.
            if (!$model->featured($ids, $value)) {
                JError::raiseWarning(500, $model->getError());
            }
        }

        $this->setRedirect('index.php?option=com_bt_portfolio&view=portfolios');
    }
    function rebuild(){
		$model = $this->getModel();
		$output = $model->rebuild();
		$this->setRedirect('index.php?option=com_bt_portfolio&view=portfolios', $output . '<br>' . JText::_('COM_BT_PORTFOLIO_PORTFOLIOS_REBUILD_IMAGES_SUCCESSFUL'));
    }
}
