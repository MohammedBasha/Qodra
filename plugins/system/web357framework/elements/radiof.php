<?php
/* ======================================================
# Web357 Framework - Joomla! System Plugin v1.5.1
# -------------------------------------------------------
# For Joomla! 3.0
# Author: Yiannis Christodoulou (yiannis@web357.eu)
# Copyright (©) 2009-2018 Web357. All rights reserved.
# License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
# Website: https://www.web357.eu/
# Support: support@web357.eu
# Last modified: 03 Jan 2018, 12:11:35
========================================================= */

/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Provides radio button inputs
 *
 * @link   http://www.w3.org/TR/html-markup/command.radio.html#command.radio
 * @since  11.1
 */
class JFormFieldRadiof extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Radiof';

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		JHTML::_('behavior.modal');
		
		$html = array();

		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;
				
		// begin: free
		$class = 'class="radio"';
		$disabled  = ' disabled';
		$pricing_prefix = !empty($this->element["pricing_prefix"]) ? $this->element["pricing_prefix"] : 'undefined';
		$link_to_pro = '<a href="https://www.web357.eu/pricing?extension='.$pricing_prefix.'&utm_source=CLIENT&utm_medium=CLIENT-ProLink-web357&utm_content=CLIENT-ProLink&utm_campaign=radiofelement" target="_blank">PRO</a>';
		$html[] = '<p><em>'.sprintf(JText::_('W357FRM_ONLY_IN_PRO'), $link_to_pro).'</em></p>';
		// end: free	
	
		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$disabled = !empty($option->disable) || ($readonly && !$checked);

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $required . $onclick
				. $onchange . $disabled . ' />';

			$html[] = '<label for="' . $this->id . $i . '"' . $class . ' >'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';

			$required = '';
		}

		// End the radio field output.
		$html[] = '</fieldset>';
		
		// begin: info screenshot for parameter explanation
		$screenshot_src = $this->element["screenshot_src"];
		$screenshot_width = $this->element["screenshot_width"];
		$screenshot_height = $this->element["screenshot_height"];
		if (!empty($screenshot_src)):
			$screenshot_url = str_replace('/administrator', '', JURI::base()).$screenshot_src;
			$html[] = '<a href="'.$screenshot_url.'" class="hasTooltip modal w357_screenshot_modal w357_screenshot_modal_free" data-original-title="Click to see an example." rel="{size: {x: '.$screenshot_width.', y: '.$screenshot_height.'}, handler:\'iframe\'}"><i style="margin-left: 13px; position: relative; top: 4px;" class="icon-eye-open"></i></a>';
		endif;
		// end: info screenshot for parameter explanation

		return implode($html);
	}

	/**
	 * Method to get the field options for radio buttons.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				$disabled
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			$tmp->onchange = (string) $option['onchange'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
