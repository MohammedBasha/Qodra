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
$params = $this->form->getFieldsets('params');
$component_params = JComponentHelper::getParams("com_bt_portfolio" );
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="portfolio-form" enctype="multipart/form-data"
        class="form-validate <?php echo Bt_portfolioLegacyHelper::isLegacy() ? 'isJ25' : 'isJ30' ?>">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;		
				if($field->type == "Editor") echo '<div class="clr"></div>';
				echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</div>

</form>



