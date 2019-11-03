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
defined('_JEXEC') or die();


class Bt_portfolioControllerComment extends BtPortfolioController {

	function display($cachable = false, $urlparams = false) {
		if (!JRequest::getCmd('view')) {
			JRequest::setVar('view', 'comment');
		}
		parent::display($cachable,$urlparams);
	}

	function comment() {

		JRequest::checkToken() or jexit('Invalid Token');

		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$params = &$app->getParams();

		//JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bt_portfolio'.DS.'tables');
		//$table = JTable::getInstance('Comment', 'Bt_portfolioTable');
		//$table->save($_POST);

		$post = JRequest::getVar('jform', array(), 'post', 'array');
		$post['id'] = 0;
		$post['item_id'] = JRequest::getVar('item_id', '', 'post', 'string', 0);
		$post['ip'] = $_SERVER['REMOTE_ADDR'];

		$Itemid = JRequest::getVar('Itemid', 0, '', 'int');
		$limitStart = JRequest::getVar('limitstart', 0, '', 'int');
		$return_url = base64_decode(JRequest::getVar('return',''));

		$post['created'] = gmdate('Y-m-d H:i:s');
		$post['published'] = $params->get('auto_publish_comment', 1000) == 1;

		$maxCommentChar = $params->get('commentmax_characters', 1000);

		$post['content'] = substr($post['content'], 0, (int) $maxCommentChar);

		$post['user_id'] = $user->id;

		$model = $this->getModel('comment');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data = $model->validate($form, $post);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_bt_portfolio.comment.data', $post);

			// Redirect back to the registration screen.
			if($return_url){
				$this->setRedirect($return_url, false);
			}else{
				$this->setRedirect(JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&id=' . $post['item_id'] . '&Itemid=' . $Itemid, false));
			}
			return false;
		}
		$msg = '';
		if ($user->id > 0 || $params->get('allow_guest_comment', 0)) {
			if (!$model->comment($post)) {
				$msg = JText::_('COM_BT_PORTFOLIO_ERROR_COMMENT_SUBMITTING');
			}
			else {
				$msg = JText::_('COM_BT_PORTFOLIO_SUCCESS_COMMENT_SUBMIT');
				$post['content'] = '';
				$post['title'] = '';
				$app->setUserState('com_bt_portfolio.comment.data', $post);
			}
		}
		else {
			$app->redirect(JRoute::_('index.php?option=com_users&view=login', false), JText::_('COM_BT_PORTFOLIO_NOT_AUTHORISED_ACTION'));
			exit;
		}
		if($return_url){
			$this->setRedirect($return_url, $msg);
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&id=' . $post['item_id'] . '&Itemid=' . $Itemid, false), $msg);
		}
	}
}
?>