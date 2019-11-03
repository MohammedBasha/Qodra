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
die;
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Image Controller
 */
class Bt_portfolioControllerImage extends JControllerForm
{
	function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
	}
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Image');
		$form = $model->getForm();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$id = JRequest::getInt('id');

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_bt_portfolio.image.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_bt_portfolio&view=image&tmpl=component&id=' . $id, false));
			return false;
		}

		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_bt_portfolio.image.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_bt_portfolio&view=image&tmpl=component&id=' . $id), $message, 'error');
			return false;
		}

		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$message = JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
				$this->setRedirect('index.php?option=com_bt_portfolio&view=image&id=' . $id . '&tmpl=component&refresh=1', $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php?option=com_config&view=close&tmpl=component');
				break;
		}

		return true;
	}
}
