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
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form
	action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=image&id=' . (int) $this->item->id);
			?>"
	method="post"
	name="adminForm"
	id="image-form"
	enctype="multipart/form-data"
	class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button
				type="button"
				onclick="Joomla.submitform('image.apply', this.form);">
				<?php echo JText::_('JAPPLY'); ?>
			</button>
			<button
				type="button"
				onclick="Joomla.submitform('image.save', this.form);">
				<?php echo JText::_('JSAVE'); ?>
			</button>
			<button
				type="button"
				onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';
						 ?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
		</div>
		<div class="configuration">
			<?php echo JText::_('COM_BT_PORTFOLIO_IMAGE_EDIT'); ?>
		</div>
	</fieldset>
	<fieldset class="adminform">
		<ul class="adminformlist">

			<?php foreach ($this->form->getFieldset('details') as $field) : ?>
			<li><?php echo $field->label;
					if ($field->type == "Editor")
						echo '<div class="clr"></div>';
					echo $field->input;
				?>
			</li>
			<?php endforeach; ?>
			<li>
			<label></label>
			<img src="<?php echo JURI::root().'images/bt_portfolio/'.$this->item->item_id.'/large/'.$this->item->filename; ?>" />
		</li>
		</ul>
		<input
			type="hidden"
			name="task"
			value="" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
