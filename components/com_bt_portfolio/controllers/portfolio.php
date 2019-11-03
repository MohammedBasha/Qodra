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



class Bt_portfolioControllerPortfolio extends BtPortfolioController {
	function display($cachable = false, $urlparams = false) {
		//$this->setModel($this->getModel("comment"), 'comment');
		parent::display($cachable,$urlparams); 

	}
	function next() {
		$this->goToDirection('>', 'asc');
	}
	function preview() {
		$this->goToDirection('<', 'desc');
	}
	function goToDirection($direction, $orderDir) {
		$id = JRequest::getInt('id');
		$destination = 0;
		$db = JFactory::getDbo();
		$query = 'select * from #__bt_portfolios where id = ' . $id;
		$db->setQuery($query);
		$portfolio = $db->loadObject();
		
		$catid_rel = JRequest::getVar('catid_rel');
		$catids = explode(',',$portfolio->catids);
		if(!$catid_rel || !in_array($catid_rel, $catids)){
			$catid_rel = $catids[1];
		}
		$desObj = 0;
		if ($portfolio) {
			$query = 'select * from #__bt_portfolios where catids like \'%,'.$catid_rel.',%\' and published = 1 and ordering ' . $direction . '  ' . $portfolio->ordering . ' order by ordering limit 1';
			$db->setQuery($query);
			$desObj = $db->loadObjectList();
			if(count($desObj)){
				$desObj = $desObj[0];
				$destination=$desObj->id;
			}
		}
		if (!$destination){
			$query = 'select * from #__bt_portfolios where catids like \'%,'.$catid_rel.',%\'  and published = 1 order by ordering ' . $orderDir . ' limit 1';
			$db->setQuery($query);
			$desObj = $db->loadObjectList();
			if(count($desObj)){
				$desObj = $desObj[0];
				$destination=$desObj->id;
			}
		}
		if (!$destination){
			$destination = $id;
			$desObj = $portfolio;
		}
		$catids = explode(',',$desObj->catids);
		if(!in_array($catid_rel, $catids)){
			$catid_rel = $catids[1];
		}
		$link = 'index.php?option=com_bt_portfolio&view=portfolio&id=' . $destination.'&catid_rel='.$catid_rel;
		$this->setRedirect(str_replace('&amp;', '&', JRoute::_($link)));

	}
	function upload() {
		JLoader::register('BTImageHelper', JPATH_ADMINISTRATOR . '/components/com_bt_portfolio/helpers/images.php');
		BtPortfolioModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bt_portfolio/models');
		$model = BtPortfolioModel::getInstance('portfolio', 'Bt_PortfolioModel');
		$model->upload();
		exit;
	}
	function viewimage(){
		Bt_portfolioHelper::showWmImage();
	}
}
?>