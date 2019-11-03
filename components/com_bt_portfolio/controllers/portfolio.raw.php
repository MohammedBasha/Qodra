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

jimport('joomla.application.component.controller');

class Bt_portfolioControllerPortfolio extends BtPortfolioController {
	function rate() {
		$app = JFactory::getApplication();
		$params = &$app->getParams();

		$result = array();
		$result['success'] = true;

		$user = JFactory::getUser();
		if ($user->id == 0 && $params->get('allow_guest_comment') == 0) {
			$result['success'] = false;
			$result['message'] = JText::_('COM_BT_PORTFOLIO_RATING_LOGIN_NOTICE');
		}
		else {
			$portfolioId = JRequest::getInt('id', 0);
			$rating = JRequest::getInt('rating', 0);

			$db = JFactory::getDbo();

			$sqlQuery = "SELECT * FROM #__bt_portfolios WHERE id=" . $portfolioId;
			$db->setQuery($sqlQuery);
			$portfolio = $db->loadObject();

			// Fake submit
			if (!$portfolio || $rating == 0 || $rating > 5) {
				die();
			}
			$ip = $_SERVER['REMOTE_ADDR'];
			if ($user->id)
				$sqlQuery = "SELECT COUNT(*) FROM #__bt_portfolio_vote WHERE item_id={$portfolioId} AND user_id={$user->id}";
			else
				$sqlQuery = "SELECT COUNT(*) FROM #__bt_portfolio_vote WHERE item_id={$portfolioId} AND ip='{$ip}' AND user_id = 0";

				$db->setQuery($sqlQuery);
			if ($db->loadResult() > 0) {
				$result['success'] = false;
				$result['message'] = JText::_('COM_BT_PORTFOLIO_RATING_HAVE_VOTED');
			}
			else {
				$sqlQuery = "UPDATE #__bt_portfolios SET vote_sum=vote_sum + {$rating}, vote_count=vote_count + 1 WHERE id={$portfolioId}";
				$db->setQuery($sqlQuery);
				$db->query();

				$date = JFactory::getDate();
				$created = $date->toSql();

				$sqlQuery = "INSERT INTO #__bt_portfolio_vote(item_id, user_id, created, vote,ip)" . "\n VALUES({$portfolioId}, {$user->id}, '{$created}', {$rating}, '{$ip}')";
				$db->setQuery($sqlQuery);
				$db->query();

				$result['message'] = JText::_('COM_BT_PORTFOLIO_RATING_SUCCESS_MESSAGE');
				$result['rating_sum'] = $portfolio->vote_sum + $rating;
				$result['rating_count'] = $portfolio->vote_count + 1;
				$result['rating'] = $result['rating_sum'] / $result['rating_count'];
				$result['rating_text'] = sprintf(JText::_('COM_BT_PORTFOLIO_RATING_TEXT'), $result['rating'], $result['rating_count']);
				$result['rating_width'] = round(15 * $result['rating']);
			}
		}

		echo json_encode($result);
	}
}
?>