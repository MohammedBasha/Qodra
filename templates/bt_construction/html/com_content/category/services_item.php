<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
$canEdit	= $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

?>


<div class="servicesItem">

<?php  if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
	<div class="servicesItem_left">
		<div class="servicesItem_introImage">
			<img src="<?php echo htmlspecialchars($images->image_intro); ?>" />
		</div>
		<div class="servicesItem_readmore">
			<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>">
				<?php echo JText::_('BT_INSTRUCTION_READMORE'); ?>
			</a>
		</div>
	</div>
<?php endif; ?>

	<div class="servicesItem_body">
		<?php if ($params->get('show_title')) : ?>
			<h2 class="servicesItem_title">
				<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>">
				<?php echo $this->escape($this->item->title); ?></a>
			</h2>
		<?php endif; ?>

		<div class="servicesItem_introtext">
			<?php echo $this->item->introtext; ?>
		</div>
		
	</div>

<div style="clear:both;"></div>
</div>


