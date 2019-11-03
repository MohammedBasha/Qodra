<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();
$info    = $params->get('info_block_position', 0);

?>
<div class="item-page<?php echo $this->pageclass_sfx?>">

<div class="articleItem_detail">


			<?php  if (isset($images->image_fulltext) and !empty($images->image_fulltext)) : ?>
			<?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
			<div class="img-fulltext">
			<img
				<?php if ($images->image_fulltext_caption):
					echo 'class="caption"'.' title="' .htmlspecialchars($images->image_fulltext_caption) .'"';
				endif; ?>
				src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
			</div>
			<?php endif; ?>

<?php if ($this->params->get('show_page_heading')) : ?>
	<h2 class="articleList_title">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h2>
<?php endif; ?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
{
 echo $this->item->pagination;
}
 ?>
						<div class="articleList_head">
							<?php if ($params->get('show_title')) : ?>
								<h2 class="articleList_title">
								<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
									<a href="<?php echo $this->item->readmore_link; ?>">
									<?php echo $this->escape($this->item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($this->item->title); ?>
								<?php endif; ?>
								</h2>
							<?php endif; ?>

							<div class="k2ItemInfo">
								<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
								<div class="createdby">
								<?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
								<?php if (!empty($this->item->contactid) && $params->get('link_author') == true): ?>
								<?php
									$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
									$menu = JFactory::getApplication()->getMenu();
									$item = $menu->getItems('link', $needle, true);
									$cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
								?>
									<?php echo JText::sprintf('BT_CONSTRUCTION_ARTICLE_CREATED_BY').' '; 
									echo (JHtml::_('link', JRoute::_($cntlink), $author)); ?>
								<?php else: ?>
									<?php echo JText::sprintf('BT_CONSTRUCTION_ARTICLE_CREATED_BY').' '; echo ($author); ?>
								<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if ($params->get('show_publish_date')) : ?>
								<div class="published">
								<?php echo JHtml::_('date', $this->item->publish_up, JText::_('BT_CONSTRUCTION_DATE_ARTICLE_CREATED')); ?>
								</div>
							<?php endif; ?>
							
							<?php if ($params->get('show_category')) : ?>
								<div class="category-name">
								<?php 	$title = $this->escape($this->item->category_title);
								$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
								<?php if ($params->get('link_category') and $this->item->catslug) : ?>
									<?php echo ($url); ?>
								<?php else : ?>
									<?php echo ($title); ?>
								<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if ($params->get('show_hits')) : ?>
								<div class="hits">
								<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
								</div>
							<?php endif; ?>
							
							<?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
								<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

								<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
							<?php endif; ?>
							
							</div>

						</div>

<?php  if (!$params->get('show_intro')) :
	echo $this->item->event->afterDisplayTitle;
endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php $useDefList = (($params->get('show_parent_category'))	or ($params->get('show_create_date')) or ($params->get('show_modify_date'))); ?>


<?php if (isset ($this->item->toc)) : ?>
	<?php echo $this->item->toc; ?>
<?php endif; ?>



<?php if ($params->get('access-view')):?>


<div class="articleList_introtext"><?php echo $this->item->text; ?></div>



	<?php //optional teaser intro text for guests ?>
<?php elseif ($params->get('show_noauth') == true and  $user->get('guest') ) : ?>
	<?php echo $this->item->introtext; ?>
	<?php //Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JURI($link1);?>
		<div class="readmore">
		<a href="<?php echo $link; ?>">
		<?php $attribs = json_decode($this->item->attribs);  ?>
		<?php
		if ($attribs->alternative_readmore == null) :
			echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
		elseif ($readmore = $this->item->alternative_readmore) :
			echo $readmore;
			if ($params->get('show_readmore_title', 0) != 0) :
			    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif;
		elseif ($params->get('show_readmore_title', 0) == 0) :
			echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
		else :
			echo JText::_('COM_CONTENT_READ_MORE');
			echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		endif; ?></a>
		</div>
	<?php endif; ?>
	
	<?php if ($useDefList) : ?>
		<div class="article-info">
			<?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') : ?>
				<div class="parent-category-name">
				<?php	$title = $this->escape($this->item->parent_title);
				$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';?>
				<?php if ($params->get('link_parent_category') and $this->item->parent_slug) : ?>
					<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
				<?php else : ?>
					<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
				<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($params->get('show_create_date')) : ?>
				<div class="create">
				<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_modify_date')) : ?>
				<div class="modified">
				<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
				</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>
	
	
<?php endif; ?>



<?php echo $this->item->event->afterDisplayContent; ?>
</div>
</div>
