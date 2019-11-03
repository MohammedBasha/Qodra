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
?>

		<div class="btp-comment">
		<a name="reviews"></a>
		<?php
			// Disqus comment
			if ($this->params->get('comment_system') == 'disqus'){
				echo Bt_portfolioHelper::getDisqusComment($this->params->get('disqus_shortname'));
			}
			else
			//Facebook comment
			if ($this->params->get('comment_system') == 'facebook') {
				echo Bt_portfolioHelper::getFacebookComment($this->params->get('facebook_app_id'),$this->params->get('number_comments', 5),$this->params->get('commmentbox_width', 600));
			}
			else
			// JCOMMENT
			if ($this->params->get('comment_system') == 'jcomments')
			{
				if (Bt_portfolioHelper::checkComponent('com_jcomments')) {
					include_once(JPATH_BASE . DS . 'components' . DS . 'com_jcomments' . DS . 'jcomments.php');
					echo JComments::showComments($this->item->id, 'bt_portfolio', $this->item->title);
				}
				else {
					echo "jComments is not installed";
				}
			}
			else{

			// Default comment feature
			$i= 0;
			$total = count($this->comment['data']);
			if ($total){
			?>
			<h3 class="btp-comment-head">
				<?php echo $this->item->review_count > 1 ? JText::_('COM_BT_PORTFOLIO_REVIEWS') : JText::_('COM_BT_PORTFOLIO_REVIEW') ?>
			</h3>

			<!-- Comment lits -->
			<div id="btp-comment-list">
            <?php foreach ($this->comment['data'] as $item){
            	$i++;
             ?>
				<div class="btp-comment-item">
					<div class="<?php if($i==0) echo 'comment-first'; else if ($i == $total) echo  'comment-last';?> <?php echo $item->admin? 'comment-admin':''; ?>">
						<div class="btp-comment-item-head">
							<?php if($item->image){?>
								<div class="comment-avatar">
									<a href="<?php echo $item->link;?>"><img src="<?php echo $item->image;?>" /></a>
								</div>
							<?php }?>
	                        <div class="in-content-comment">
	                        	<div class="comment-info">
	                        		<span class="comment-author"><?php echo $item->name; ?></span>
	                        		<span class="comment-created"><?php echo JText::sprintf('COM_BT_PORTFOLIO_CREATED_ON', JHtml::_('date', $item->created, JText::_('F d, Y')), JHTML::_('date',$item->created, JText::_('g:i a'))); ?></span>
								</div>
								<?php if ($this->params->get('show_title')) { ?>
								<div class="comment-title"> <?php  echo $item->title ?> </div>
								<?php } ?>
			                     <div class="btp-comment-item-content">
									<?php echo htmlspecialchars($item->content) ?>
								</div>
							</div>
						</div>
					</div>
                </div>
				<div class="clr"></div>
				<?php } ?>

				<?php if ($this->comment['nav']->get('pages.total') > 1) : ?>
				<div class="pagination">
					<!-- <p class="counter">
						<?php echo $this->comment['nav']->getPagesCounter(); ?>
						</p>
					-->
					<?php echo $this->comment['nav']->getPagesLinks(); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php }?>

		<!-- Comment form -->
		<div class="btp-comment-fom">
			<?php if ($this->params->get('allow_guest_comment') || $this->user->id) { ?>
        	<h3 class="review-form-title"><?php echo JText::_('COM_BT_PORTFOLIO_WRITE_A_REVIEW'); ?></h3>
			<form id="btp-form-comment"
				action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&task=comment.comment'); ?>" method="post" class="form-validate">

				<?php foreach ($this->comment['form']->getFieldsets() as $fieldset) : ?>
					<?php foreach ($this->comment['form']->getFieldset($fieldset->name) as $name => $field) : ?>
					<div class="table_body <?php echo 'field_'.$name;?>">
						<div class="item-label">
							<?php echo $field->label; ?>
						</div>
						<div class="item-input">
							<?php echo $field->input; ?>
						</div>
					</div>
					<?php endforeach; ?>
				<?php endforeach; ?>
					<div class="btp-submit-comment">
						<input type="hidden" name="item_id" value="<?php echo $this->item->id ?>">
						<input type="hidden" name="task" value="comment.comment" />
						<input type="hidden" name="return" value="<?php echo base64_encode($this->uri->toString(array('path', 'query', 'fragment'))) ?>">
						<span><button type="submit" class="validate">
							<?php echo JText::_('COM_BT_PORTFOLIO_SEND_REVIEW'); ?>
						</button></span>
						<?php echo JHtml::_('form.token'); ?>
					</div>
			</form>
			<?php }
					else {

			?>
			<div class="login-first">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($this->uri->toString(array('path', 'query', 'fragment')))); ?>"><?php echo JText::_('COM_BT_PORTFOLIO_LOGIN_FIRST'); ?></a>
			</div>
			<?php }?>
		</div>
		<?php } ?>
		</div>
		

