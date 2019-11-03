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
 // no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

class Bt_portfolioModelComment extends JModelForm {
	/**
	 * @var		object	The user registration data.
	 * @since	1.6
	 */
	protected $data;

	public function getData() {
		if ($this->data === null) {

			$this->data = new stdClass();
			$user = JFactory::getUser();

			$data = (array) JFactory::getApplication()->getUserState('com_bt_portfolio.comment.data', array());

			if (array_key_exists('name', $data)) {

				if (array_key_exists('title', $data))
					$this->data->title = $data['title'];
				if (array_key_exists('website', $data))
					$this->data->website = $data['website'];
				$this->data->name = $data['name'];
				$this->data->email = $data['email'];
				$this->data->content = $data['content'];

			}
			else {
				if ($user->id) {
					$this->data->name = $user->name;
					$this->data->email = $user->email;
				}
			}
		}

		return $this->data;
	}

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_bt_portfolio.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$app = JFactory::getApplication();
		$params = $app->getParams();
		if (!$params->get('show_website', 1)) {
			$form->removeField('website');
		}
		if (!$params->get('show_title', 1)) {
			$form->removeField('title');
		}
		//$user = JFactory::getUser();
		//if ($user->id) {
		//	$form->removeField('email');
		//}
		return $form;
	}
	protected function loadFormData() {
		return $this->getData();
	}
	public function getListComment($portfolioId, $limit, $limitstart, $orderDir) {

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select fields
		$query->select('*');
		// From the bt portfolio_comments table
		$query->from('#__bt_portfolio_comments');
		$query->where('published = 1');
		$query->where('item_id = ' . $portfolioId);
		$query->order('created ' . $orderDir);

		if(!$limit){
			$limit = $app->getCfg('list_limit', 0);
		}
		$items = $this->_getList($query, $limitstart, $limit);

		$params = JComponentHelper::getParams("com_bt_portfolio");
		$user->image = '';
		$user->link = '';
		$avatar_type = $params->get('avatars_integrated');

		foreach ($items as $item) {
			$item->image = '';
			$item->link = '';
			$item->admin = false;

			if($user->authorise('core.admin')){
				$item->admin = true;
			}
			switch ($avatar_type) {
				case 'k2':
					if (is_file(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php')) {
						JLoader::register('K2HelperRoute', JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');
						JLoader::register('K2HelperUtilities', JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php');
						$item->image = K2HelperUtilities::getAvatar($item->user_id, $item->email);
						if ($item->user_id)
							$item->link = JRoute::_(K2HelperRoute::getUserRoute($item->user_id));
						else
							$item->link = "#";
					}
					break;
				case 'cb':
					global $_CB_framework, $_CB_database, $ueConfig, $mainframe, $_SERVER;
					if (defined('JPATH_ADMINISTRATOR')) {
						if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')) {
							echo 'CB not installed';
							break;
						}
						include_once(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php');
					}
					else {
						if (!file_exists($mainframe->getCfg('absolute_path') . '/administrator/components/com_comprofiler/plugin.foundation.php')) {
							echo 'CB not installed';
							break;
						}
						include_once($mainframe->getCfg('absolute_path') . '/administrator/components/com_comprofiler/plugin.foundation.php');
					}

					$cbUser = &CBuser::getInstance($item -> user_id);
					$item->image = $cbUser->avatarFilePath();
					if ($item -> user_id) {
						$item->link = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $item -> user_id);
					}
					break;
				default:
					break;
			}

		}

		return $items;

	}
	function getCommentTotal($portfolioId) {
		$db = JFactory::getDBO();
		$db->setQuery("select count(*) from #__bt_portfolio_comments where published = 1 AND item_id = " . $portfolioId);
		return $db->loadResult();
	}
	function comment($data) {

		$row = JTable::getInstance('Comment', 'Bt_portfolioTable');

		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$isNew = $row->id == 0;
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if($isNew && $row->published){
			$db = JFactory::getDBO();
			$db->setQuery("update #__bt_portfolios set review_count = review_count + 1 where id = " . $row->item_id);
			$db->query();
		}
		
		// Send notification email 
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$recipient = $params->get('email_recipient');
		$ItemLink = JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&id=' . JRequest::getInt('item_id') . '&Itemid=' . JRequest::getVar('Itemid', 0, '', 'int'),false,-1);
		$AdminLink = JURI::root().('administrator/index.php?option=com_bt_portfolio&view=comments');
		if($params->get('comment_notification') && $recipient){
			$config	= JFactory::getConfig();
			$emailSubject	= JText::sprintf(
					'COM_BT_PORTFOLIO_NEW_COMMENT_MAIL_SUBJECT',
					$config->get('sitename')
			);

			$emailBody = JText::sprintf(
					'COM_BT_PORTFOLIO_NEW_COMMENT_MAIL_BODY',
					$row->name,
					$row->title,
					$row->content,
					$ItemLink,
					$AdminLink
				);

			JUtility::sendMail($config->get('mailfrom'), $config->get('fromname'), $recipient, $emailSubject, $emailBody);

		}
		return true;
	}
}
