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
$document->addScript(COM_BT_PORTFOLIO_THEME_URL.'js/jquery.quicksand.js');
$document->addScript(COM_BT_PORTFOLIO_THEME_URL.'js/foto.js');
$activeID = JRequest::getInt("catid");
$all = true;
foreach ($this->listCategories as $category){
	if($category->id == $activeID){
		$all = false;
	}
}
?>
<div class="btp">
	<div class="btp-header">
	<!-- Show navigation categories -->
	<?php if($this->params->get('show_cat_navigation')){ ?>
	<div class="btp-categories">
		<div class="container">
			<a data-value="all" <?php if ($all) echo 'class="active"';	?>	href="#">
					<span><?php echo JText::_('JALL') ?></span>
					<input type="hidden" value="">
			</a>
			<?php foreach ($this->listCategories as $category) {
			?>
				<a data-value="p<?php
				echo str_replace(',',',p',Bt_portfolioModelPortfolios::callBackAllChild($category->id)); 		
				?>" <?php if ($category->id == $activeID) echo 'class="active"';	?>	href="#">
					<span><?php echo $category->title ?></span>
					<input type="hidden" value="<?php echo htmlspecialchars($category->description) ?>">
				</a>
			<?php
			}
			?>
		</div>
	</div>
	<?php } ?>
	</div>
	
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
	
	<!-- Show list portfolios -->
	<?php if($this->params->get('show_portcat',1)): ?>
	
	<div class="row">
	<div class="btp-list" style="display:none">
		
				<?php
				//Show list children categories
				if($this->params->get('show_childcat')){
					 foreach ($this->listCategories as $parentCat) {
						foreach ($this->getModel()->getListChildCategories($parentCat->id) as $category) {
						$img_url = $category->main_image? JURI::base().$category->main_image: COM_BT_PORTFOLIO_THEME_URL . 'images/no-image.jpg';
						$link = JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $category->id.':'.$category->alias);
						?>	
						<div data-id="pid<?php echo $category->id; ?>" class="btp-item p<?php echo $category->parent_id?>" <?php if ($item->parent_id != $activeID && !$all) echo 'style="display:none"';	?>>
							
								<a class="image-link" href="<?php echo $link ?>">
								
								<div class="btp_item_info">
									<span class="title-hover"><span><?php echo $category->title; ?></span></span>
									<div class="btp_list_introtext"><?php echo $category->title; ?></div>
									<!--<div class="extrafieldRow">
										<?php foreach ($item->extra_fields as $field){ 
										if(count($field) ==0) continue; ?>
											<?php if ($field->type == 'string'){ ?>
												<span class="extrafield-title"><?php echo $field->name; ?>:</span>
												<span class="extrafield-value"><?php echo $field->value; ?></span>
											<?php } ?>
										<?php } ?>
									</div> -->
									
									
								</div>
									
									<img class="image-default"  style="width:<?php echo $this->params->get('thumb_width',150) ?>px;height:<?php echo $this->params->get('thumb_height',150) ?>px" src="<?php echo $img_url ?>" alt="<?php echo htmlspecialchars($category->title) ?>">
									<!--<div class="iframe-hover"><div class="iframe-hover-inner"><div class="iframe-hover-inner2"></div></div></div>-->
								</a>
							
						</div>
						<?php	
						}
					}
				}
				$total = count($this->items);
				if($total){
				foreach ($this->items as $item) {	
					$img_url = Bt_portfolioHelper::getPathImage($item->id,'thumb',$item->image,$item->category_id);
					$link = JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&id=' . $item->id.':'.$item->alias.'&catid_rel=' . $item->category_id.':'.$item->category_alias);
				?>
				<div data-id="pid<?php echo $item->id; ?>" class="btp-item p<?php echo $item->category_id?>" <?php if ($item->category_id != $activeID && !$all) echo 'style="display:none"';	?>>		

								<a class="image-link" href="<?php echo $link ?>">
								
								<div class="btp_item_info">
									<span class="title-hover"><span><?php echo $item->title; ?></span></span>
									<?php if($this->params->get('enable-slideshow','skiterslide') =="mediaslide"){?>
									<?php if($item->youembed):?>
									<span><div id="iconyoutube"></div></span>
									<?php endif;?>
									<?php } ?>
									
									<div class="btp_list_introtext"><?php echo (substr($item->full_description,0,80)); ?></div>
									<div class="btp_list_details_text"><?php echo JText::_('TPL_BT_FITNESS_PORTFOLIO_LIST_DETAILS'); ?></div>
									<!--<div class="extrafieldRow">
										<?php foreach ($item->extra_fields as $field){ 
										if(count($field) ==0) continue; ?>
											<?php if ($field->type == 'string'){ ?>
												<span class="extrafield-title"><?php echo $field->name; ?>:</span>
												<span class="extrafield-value"><?php echo $field->value; ?></span>
											<?php } ?>
										<?php } ?>
									</div> -->
								</div>
								
								<img class="image-default" src="<?php echo $img_url ?>" alt="<?php echo htmlspecialchars($item->title) ?>">
								<div class="iframe-hover"><div class="iframe-hover-inner"><div class="iframe-hover-inner2"></div></div></div>
							</a>
						
				</div>
				<?php
				}
				}
				else{
					echo '<div class="col-xs-12">'.JText::_('COM_BT_PORTFOLIO_NO_ITEM').'</div>';
				}
				?>
		</div>
	</div>
	<!-- Show pagination -->
	<?php if ($this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination">
				<!--  <p class="counter"><?php  echo $this->pagination->getPagesCounter(); ?></p> -->
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
	<?php endif; ?>
	<?php endif; ?>
</div>

