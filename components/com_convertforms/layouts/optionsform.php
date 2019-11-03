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

?>
		
<div class="fmItem" data-key="<?php echo $loadData['key']; ?>">
	<?php 
		echo $header; 

		foreach ($form->getFieldsets() as $fieldset)
		{ 
			// Skip fieldset is has no fields
			if (!$form->getFieldset($fieldset->name))
			{
				continue;
			}

			$label = $fieldset->name == 'basic' ? JText::_('COM_CONVERTFORMS_FIELD_' . $fieldTypeName) : ucfirst($fieldset->name);
		?>
		<h3>
			<?php echo $label; ?>
			<?php if ($fieldset->name == 'basic') { ?>
				<small>(ID: <?php echo $loadData['key']; ?>)</small>

				<span class="dropdown">
					<a href="#" data-toggle="dropdown">	
						<span class="icon-menu"></span>
					</a>
					<span class="dropdown-menu">
						<a href="#" class="copyField"><?php echo JText::_('COM_CONVERTFORMS_FIELDS_COPY') ?></a>
						<a href="#" class="removeField" data-focusnext="true"><?php echo JText::_('COM_CONVERTFORMS_FIELDS_DELETE') ?></a>
					</span>
				</span>
			<?php } ?>
		</h3>

		<div class="fmItemForm">
			<?php echo $form->renderFieldset($fieldset->name); ?>
		</div>
	<?php } ?>
</div>