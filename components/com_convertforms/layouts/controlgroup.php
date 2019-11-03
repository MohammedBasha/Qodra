<?php

/**
 * @package         Convert Forms
 * @version         2.3.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
extract($displayData);

$cssclass = isset($field->cssclass) ? $field->cssclass : '';

// Load Input Masking
if (isset($field->inputmask) && !empty($field->inputmask))
{
	JHtml::script('com_convertforms/inputmask.js', ['relative' => true, 'version' => 'auto']);
}

?>

<div class="cf-control-group <?php echo $cssclass; ?>" data-key="<?php echo $field->key; ?>">
	<?php if (isset($field->hidelabel) && !$field->hidelabel && !empty($field->label)) { ?>
		<div class="cf-control-label">
			<label class="cf-label" style="<?php echo implode(";", $field->labelStyles) ?>" for="<?php echo $field->id; ?>">
				<?php echo $field->label ?>
				<?php if ($form['params']->get('required_indication', true) && $field->required) { ?>
					<span class="cf-required-label">*</span>
				<?php } ?>
			</label>
		</div>
	<?php } ?>
	<div class="cf-control-input">
		<?php echo $field->input; ?>
		<?php if (isset($field->description) && !empty($field->description)) { ?>
			<div class="cf-control-input-desc" style="color:<?php echo $form['params']->get('labelscolor') ?>">
				<?php echo $field->description; ?>
			</div>
		<?php } ?>
	</div>
</div>