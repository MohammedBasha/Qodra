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
jimport('joomla.html.pagination');

class Bt_portfolioViewPortfolio extends BTView
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$params = $app->getParams();
		$model = $this->getModel();
		$uri = JFactory::getURI();
		$lang = JFactory::getLanguage();
		
		// Get some data from the models
		$state = $this->get('State');
		$item = $this->get('Item');
		
		$groups = $user->getAuthorisedViewLevels();
		if (!in_array($item->access, $groups))
		{
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model->hit();
				
		$registry = new JRegistry();
		$registry->loadString($item->params);
		$item->params = $registry;
		$params->merge($registry);
	
		$images = array();
		$images = $this->get("Images");
		
		$category = JTable::getInstance('Category', 'Bt_portfolioTable');
		$catids = explode(',', $item->catids);
		$catid_rel = JRequest::getInt('catid_rel');
		$mainCategoryID = 0;
		if (in_array($catid_rel, $catids) && $catid_rel !=0)
		{
			$mainCategoryID = $catid_rel;

		}
		else
		{
			foreach ($catids as $catid)
			{
				if ($catid)
				{
					$mainCategoryID = $catid;
					break;
				}
			}
		}
		$category->load($mainCategoryID);

		if($params->get("allow_comment", 1) && $params->get('comment_system')=='none'){
			$modelComment = JModelLegacy::getInstance('Comment', 'Bt_portfolioModel', array('ignore_request' => true));
			$formComment = $modelComment->getForm();
			$limitstart = (int) JRequest::getVar("limitstart", 0);
			$orderDir = $params->get("comment_displayorder", 'desc');
			$skitervideo =$params->get("enable-slideshow", 'skiterslide');
			$commentList = $modelComment->getListComment($item->id, $params->get('number_comments', 0), $limitstart, $orderDir);
			$pageComment = new JPagination($modelComment->getCommentTotal($item->id), $limitstart, $params->get('number_comments', 0));

			$comment = array();
			$comment['data'] = $commentList;
			$comment['nav'] = $pageComment;
			$comment['form'] = $formComment;
			$this->assignRef('comment', $comment);
		}
		// Content plugin 
		$item->full_description = JHTML::_('content.prepare', $item->full_description);
		
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('uri', $uri);
		$this->assignRef('lang', $lang);
		$this->assignRef('images', $images);
		$this->assignRef('user', $user);

		$this->assignRef('category', $category);
		$this->assignRef('skitervideo', $skitervideo);
		$theme = $params->get('theme', 'default');
		$this->_addPath('template', JPATH_COMPONENT . '/themes/default/layout/detail');
		$this->_addPath('template', JPATH_COMPONENT . '/themes/' . $theme . '/layout/detail');
		$this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_portfolio/default/layout/detail');
		$this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_portfolio/'. $theme . '/layout/detail');
		
		


		$this->_prepareDocument();
		$this->_loadSlideshow();
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
		
		$id = (int) @$menu->query['id'];
			
		$title = $this->params->get('page_title', '');

		if ($menu && ($menu->query['option'] != 'com_portfolios' || $menu->query['view'] != 'portfolio' || $this->item->id != $id))
		{
			if($this->item->id != $id){
				if($this->category->id){
					$pathwayNames = $pathway->getPathwayNames();
					if(count($pathwayNames) == 0 ||trim(strtolower($this->category->title)) != trim(strtolower($pathwayNames[count($pathwayNames)-1]))){
						$pathway->addItem($this->category->title, JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $this->category->id.':'.$this->category->alias));
					}
				}
				$pathway->addItem($this->item->title,'');
				$newTitle = $this->item->params->get('page_title',$this->item->title);
				if($newTitle) $title = $newTitle;	
				
			}
		}
		
		if (empty($title)) {
			$title = $this->item->title;
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
		
		if ($this->params->get('rights'))
		{
			$this->document->setMetaData('rights', $this->params->get('rights'));
		}
		
		if ($app->getCfg('MetaAuthor') == '1' && $this->params->get('author'))
		{
			$this->document->setMetaData('author', $this->params->get('author'));
		}
		
		if(JRequest::getVar('layout')=='print'){
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}else{
			$this->setLayout("detail");
		}
	}
	protected function _loadSlideshow(){
		$params = $this->params;
		if($params->get('enable-slideshow','mediaslide')=="skiterslide"){
			$this->document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.animate-colors-min.js');
			$this->document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.skitter.min.js');
			$this->document->addStyleSheet(COM_BT_PORTFOLIO_THEME_URL . 'css/skitter.styles.css');

			$width 	= $params->get('crop_width',600);
			$height = $params->get('crop_height',400);
			$ss_thumb_width = $params->get('ss_thumb_width','70');
			$ss_thumb_height = $params->get('ss_thumb_height','40');
			$interval = $params->get('ss_interval','3500');
			$velocity = $params->get('ss_velocity',1);
			$nex_back 	= $params->get('ss_next_back',1) ? 'true': 'false';
			$effect 	= $params->get('ss_effect','random');
			$ss_navigation = $params->get('ss_navigation','');
			$numbers 	= $ss_navigation == 'numbers' ? 'true':'false';
			$dots 		= $ss_navigation == 'dots' || $ss_navigation == 'dots_preview' ? 'true':'false';
			$preview 	= $ss_navigation == 'dots_preview' ? 'true':'false';
			$thumbs 	= $ss_navigation == 'thumbs' ? 'true':'false';
			$background = $params->get('ss_background','');
			$responsive = $params->get('ss_responsive',0);

			$this->document->addScriptDeclaration(
				"var skOpt = {
					navigation: $nex_back,
					animation:'$effect',
					numbers:$numbers,
					width_skitter:$width,
					height_skitter:$height,
					width_thumb:$ss_thumb_width,
					height_thumb:$ss_thumb_height,
					interval:$interval,
					velocity:$velocity,
					dots:$dots,
					preview:$preview,
					thumbs:$thumbs,
					responsive:$responsive
				}"
			);
			if($dots=='true'){
				$this->document->addStyleDeclaration('.btp-slideshow{padding-bottom:50px;}');
			}
			if(!$responsive){
				$this->document->addStyleDeclaration(".btp-slideshow .container_skitter img, .box_skitter_large{height:{$height}px;width:{$width}px;max-width:none;}");
			}else{
				$this->document->addStyleDeclaration(".box_skitter_large{height:{$height}px;width:{$width}px;}");
			}
			// Thumb
			if($thumbs=='true'){
				$this->document->addStyleDeclaration(".box_skitter .info_slide_thumb .image_number,.box_skitter .info_slide_thumb .image_number img{height:{$ss_thumb_height}px;width:{$ss_thumb_width}px;} .box_skitter .info_slide_thumb{height:".($ss_thumb_height+5)."px;}");
			}
			// Preview
			if($preview=='true'){
				$this->document->addStyleDeclaration("#preview_slide ul li,#preview_slide ul li img,#preview_slide{height:{$ss_thumb_height}px;width:{$ss_thumb_width}px;} #preview_slide ul{height:{$ss_thumb_height}px;}");
			}
			if($background){
				$this->document->addStyleDeclaration(".box_skitter .info_slide,.box_skitter .container_thumbs, .btp-detail .box_skitter_large{background:#{$background}!important;}");
			}
		}
		else{
		if($params->get('enable-slideshow','mediaslide')=="mediaslide"){
			$this->document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/hammer.js');
			$this->document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/slideshow.js');
			$this->document->addStyleSheet(COM_BT_PORTFOLIO_THEME_URL . 'css/btportfoliomedia.css');
		}
		else{
			$this->document->addStyleSheet(COM_BT_PORTFOLIO_THEME_URL . 'css/btportfoliomedia.css');
		}
		}
	}
}
?>