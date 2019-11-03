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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');
$document= JFactory::getDocument();
$title = $this->category->title? $this->category->title : $document->getTitle();
$catid_rel = JRequest::getInt('catid_rel');
?>
<div class="btp">
	<div class="btp-detail">
		<h1 class="btp-detail-title">
			<?php echo $this->item->title; ?>
			<span class="btp-direction">
				<a class="preview" href="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&task=portfolio.preview&id='.$this->item->id.'&catid_rel='.$catid_rel)?>"><span><?php echo JText::_('COM_BT_PORTFOLIO_PREVIEW') ?></span></a>
				<a class="next" href="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=portfolio&task=portfolio.next&id='.$this->item->id.'&catid_rel='.$catid_rel)?>"><span><?php echo JText::_('COM_BT_PORTFOLIO_NEXT') ?></span></a>
			</span>
		</h1>
        <div class="vote-social-share">
			<?php if ($this->params->get('allow_voting') || $this->params->get('allow_comment')):?>
			<div class="btp-detail-voting">
				<?php if ($this->params->get('allow_voting')):?>
				<span style="float: left; margin-right: 10px;"><?php echo JText::_('COM_BT_PORTFOLIO_VOTE_IT');?></span>
				<?php echo Bt_portfolioHelper::getRatingPanel($this->item->id, $this->item->vote_sum, $this->item->vote_count);?>
				<?php endif; ?>
				<?php if ($this->params->get('comment_system') == 'none' && $this->params->get('allow_comment')): ?>
	            <a class="review_count" href="#reviews">
					<?php echo $this->item->review_count ?>
					<?php echo $this->item->review_count > 1 ? JText::_('COM_BT_PORTFOLIO_REVIEWS') : JText::_('COM_BT_PORTFOLIO_REVIEW'); ?>
	            </a>
				<?php endif; ?>
			</div>
			<?php endif; ?>		
        <div class="social_share">
			<?php
			if ($this->params->get('show_social_share')) {
				Bt_portfolioHelper::getSocialShare($this->params->get('social_share_buttons'));
			}
			?>
			<?php
			if ($this->params->get('show_print')) {
				echo Bt_portfolioHelper::getPrintButton(1,$this->item->id);
			}
			?>
        </div>
        <div class="clr"></div>
        </div><!-- end vote-social-share -->
		<br clear="all">
		<?php if(count($this->images )):?>
        <div class="btp-slideshow">
			<?php 
				switch($this->params->get('enable-slideshow','skiterslide')){
				case'skiterslide':
					echo $this->loadTemplate('slideshow_skitter');
					break;
				case'mediaslide':
					echo $this->loadTemplate('slideshow_media');
					break;
				default:
					echo $this->loadTemplate('slideshow_none');
					break;
				
				}
			?>
		</div>
	<?php endif; ?>
		<div class="btp-detail-extrafields">
			<?php foreach ($this->item->extra_fields as $field){
				if(count($field) ==0) continue;
				?>
				<div class="extrafield-row <?php echo preg_replace("/[^a-zA-Z0-9]/", "", strtolower($field->name)) ?>" >
					<span class="extrafield-title"><?php echo $field->name; ?>:</span>
					<?php
					switch($field->type){
						case'link':							
							if($field->value[0]){
								$itrolink =$field->value[0];						
							}else{
								$itrolink =$field->value[1];
							}
							?>
								<span class="extrafield-value"><a href="<?php echo $field->value[1]; ?>"target="<?php echo $field->value[2]; ?>"><?php echo $itrolink; ?></a></span>
							<?php
							break;
						case'image':
							?><span class="extrafield-value"><img src="<?php echo JURI::root().$field->value ?>"></span><?php
							break;
						case'measurement':
							?><span class="extrafield-value"><?php echo $field->value.' '.$field->default_value[1]; ?></span><?php 
							break;
						case'dropdown':
							?><span class="extrafield-value"><?php echo $field->value[0]; ?></span><?php 
							break;
						default:
							?><span class="extrafield-value"><?php echo $field->value; ?></span><?php 
							break;
					}					
					?>
				</div>
			<?php } ?>
		</div>
		<div class="btp-detail-desc">
			<div class="btp-detail-desc-in">
				<?php echo $this->item->full_description; ?>
			</div>
		</div>
		<br clear="all">
		<div class="btp-direction">			
			<!-- url -->
			<?php if ($this->params->get('show_url') && $this->item->url): ?>
				<a target="_blank" alt="<?php echo JText::_('COM_BT_PORTFOLIO_VISIT_SITE'); ?>" class="visit-site" href="<?php echo $this->item->url; ?>">
					<?php echo JText::_('COM_BT_PORTFOLIO_VISIT_SITE'); ?>
				</a>
			<?php endif; ?>
			<a class="back" href="<?php echo JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $this->category->id.':'.$this->category->alias) ?>"><?php  echo JText::_('COM_BT_PORTFOLIO_BACK'); ?></a>
		</div>
		<div class="clr"></div>
		<?php
		if ($this->params->get('allow_comment')) {
			echo $this->loadTemplate('comment_form');			
		}
	?>  
	</div>
</div>
