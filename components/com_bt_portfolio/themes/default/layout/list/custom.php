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
$document= JFactory::getDocument();
$title = $this->category->title? $this->category->title : $document->getTitle();
JHTML::_('behavior.modal');
?>
<div class="custom-btp-template">
	<div class="btp">
	<!-- Show navigation categories -->
		<?php if($this->params->get('show_cat_navigation')):?>
		<div class="btp-categories">
			<?php foreach ($this->listCategories as $category): ?>
			<a <?php if ($category->id == JRequest::getInt("catid")) echo 'class="active"'; ?>	href="<?php echo JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $category->id.':'.$category->alias) ?>">
				<span><?php echo $category->title ?></span>
			</a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
	<!-- Show title & description -->
	<?php if($this->params->get('show_titlecat',1)): ?>
		<h1 class="btp-title">
			<?php echo $title ?>
		</h1>
	<?php endif; ?>
		<?php if($this->params->get('show_descat')): ?>
		<div class="btp-catdesc">
			<?php echo $this->category->description; ?>
		</div>
		<?php endif; ?>
	
	<!-- Show list children categories -->
	<?php if($this->params->get('show_childcat')): ?>
		<div class="btp-list btp-list-categories">
		<?php 
		
		foreach ($this->gridCategories as $category) {
		
		$img_url = $category->main_image? JURI::base().$category->main_image: COM_BT_PORTFOLIO_THEME_URL . 'images/no-image.jpg';
		?>	
			<div class="btp-item">
			<h3>
				<a href="<?php echo JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $category->id.':'.$category->alias) ?>">
					<?php echo $category->title ?>
				</a>
			</h3>
			<a class="img-link-cat" href="<?php echo JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $category->id.':'.$category->alias) ?>">
				<image src="<?php echo $img_url?>" alt="<?php echo htmlspecialchars($category->title) ?>" />
			</a>
			
			</div>
		<?php	} ?>
		</div>
		<div class="clr"></div>
	<?php endif; ?>

	<!-- Show separation -->
	<?php if($this->params->get('show_childcat') && count($this->gridCategories) && $this->params->get('show_portcat') && count($this->items)): ?>
		<br />
		<h2 class="separate"><?php echo JText::_('COM_BT_PORTFOLIO_ITEMSINCATEGORIES') ?></h2>
	<?php endif; ?>
		
	<!-- Show list portfolios -->
	<?php if($this->params->get('show_portcat',1)): ?>
		<div class="btp-list">
			<?php $i=0;
			$total = count($this->items);
			if($total){
			foreach ($this->items as $item) {
				$img_turl = Bt_portfolioHelper::getPathImage($item->id,'thumb',$item->image,$item->category_id);
				
				$rel = '{handler:\'iframe\'}';
				$img_ourl =	Bt_portfolioHelper::extractUrl($item->youembed);
				if(!$img_ourl){
					$img_ourl = Bt_portfolioHelper::getPathImage($item->id,'original',$item->image,$item->category_id);
					$rel = '{handler:\'image\'}';
				}				
				
				$link = JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&id=' . $item->id.':'.$item->alias.'&catid_rel=' . $item->category_id.':'.$item->category_alias);
			?>
			<div class="bg-custom-btp-item">
			<div class="btp-item" style="width:<?php echo $this->params->get('thumb_width', 336); ?>px;">
			    <div class="btp-item-image">
					<?php if($this->params->get('enable-slideshow','skiterslide') =="mediaslide"){?>
					<?php if($item->youembed):?>
						<a href="<?php echo $link ?>"><div id="iconyoutube"></div></a>
					<?php endif;?>
					<?php }?>
					<a class="img-link-custom-btp" href="<?php echo $link ?>">
						<img style="width:<?php echo $this->params->get('thumb_width', 336); ?>px;" src="<?php echo $img_turl ?>" alt="<?php echo htmlspecialchars($item->title) ?>">
					</a>
                    <div class="link-div">
						<?php if ($this->params->get('show_zoom_image',1)):	?>
							<a class="zoom-img-list-custom-btp modal" title="<?php echo $item->title ?>" rel="<?php echo $rel ?>" href="<?php echo $img_ourl; ?>">Link to image</a>
                        <?php endif;  ?>
						<?php if ($this->params->get('show_url') && $item->url): ?>
							&nbsp;
							<a class="visit-site" target="_blank" href="<?php  echo $item->url; ?>"><?php  echo JText::_('COM_BT_PORTFOLIO_VISIT_SITE') ?></a>
						<?php endif;  ?>
                    </div>
                </div>
			    <div class="btp-item-content1" style="width:<?php echo $this->params->get('thumb_width', 336); ?>px">
						<h3 class="btp-item-title">
							<a href="<?php echo $link ?>"><?php echo $item->title; ?> </a>
						</h3>
						<?php	if ($this->params->get('allow_voting')): ?>
						<div class="btp-item-voting">
								<span style="float: left; margin-right: 10px;"><?php echo JText::_('COM_BT_PORTFOLIO_VOTE_IT') ?></span>
								<?php echo Bt_portfolioHelper::getRatingPanel($item->id, $item->vote_sum, $item->vote_count); ?>
						</div>
						<?php endif; ?>
			      </div>

			</div>
			</div>
		<?php
		$i++;
		}
		}
		else{
			echo JText::_('COM_BT_PORTFOLIO_NO_ITEM');
		}
		?>
		</div>
		<div class="clr"></div>
		
		<!-- Show pagination -->
		<?php if ($this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination">
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	</div>
</div>
