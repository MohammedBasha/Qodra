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
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's <input type="color">
 *
 * @link   http://www.w3.org/TR/html-markup/input.color.html
 * @since  11.3
 */
class JFormFieldColorf extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $type = 'Colorf';

	/**
	 * The control.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $control = 'hue';

	/**
	 * The position.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $position = 'right';

	/**
	 * The colors.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $colors;

	/**
	 * The split.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $split = 3;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'control':
			case 'exclude':
			case 'colors':
			case 'split':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'split':
				$value = (int) $value;
			case 'control':
			case 'exclude':
			case 'colors':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->control  = isset($this->element['control']) ? (string) $this->element['control'] : 'hue';
			$this->position = isset($this->element['position']) ? (string) $this->element['position'] : 'right';
			$this->colors   = (string) $this->element['colors'];
			$this->split    = isset($this->element['split']) ? (int) $this->element['split'] : 3;
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.3
	 */
	protected function getInput()
	{
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Control value can be: hue (default), saturation, brightness, wheel or simple
		$control = $this->control;

		// Position of the panel can be: right (default), left, top or bottom
		$position = ' data-position="' . $this->position . '"';

		$onchange  = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
		$class     = $this->class;
		$required  = $this->required ? ' required aria-required="true"' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		
		// begin: free
		$html = '';
		$disabled  = ' disabled';
		$pricing_prefix = !empty($this->element["pricing_prefix"]) ? $this->element["pricing_prefix"] : 'undefined';
		$link_to_pro = '<a href="https://www.web357.eu/pricing?extension='.$pricing_prefix.'&utm_source=CLIENT&utm_medium=CLIENT-ProLink-web357&utm_content=CLIENT-ProLink&utm_campaign=radiofelement" target="_blank">PRO</a>';
		$only_in_pro = '<p><em>'.sprintf(JText::_('W357FRM_ONLY_IN_PRO'), $link_to_pro).'</em></p>';
		// end: free
		
		$color = strtolower($this->value);

		if (!$color || in_array($color, array('none', 'transparent')))
		{
			$color = 'none';
		}
		elseif ($color['0'] != '#')
		{
			$color = '#' . $color;
		}

		if ($control == 'simple')
		{
			$class = ' class="' . trim('simplecolors chzn-done ' . $class) . '"';
			JHtml::_('behavior.simplecolorpicker');

			$colors = strtolower($this->colors);

			if (empty($colors))
			{
				$colors = array(
					'none',
					'#049cdb',
					'#46a546',
					'#9d261d',
					'#ffc40d',
					'#f89406',
					'#c3325f',
					'#7a43b6',
					'#ffffff',
					'#999999',
					'#555555',
					'#000000'
				);
			}
			else
			{
				$colors = explode(',', $colors);
			}

			$split = $this->split;

			if (!$split)
			{
				$count = count($colors);

				if ($count % 5 == 0)
				{
					$split = 5;
				}
				else
				{
					if ($count % 4 == 0)
					{
						$split = 4;
					}
				}
			}

			$split = $split ? $split : 3;

			$html = array();

			// begin: free
			$html[] = $only_in_pro;
			// end: free
					
			$html[] = '<select name="' . $this->name . '" id="' . $this->id . '"' . $disabled . $required
				. $class . $position . $onchange . $autofocus . ' style="visibility:hidden;width:22px;height:1px">';

			foreach ($colors as $i => $c)
			{
				$html[] = '<option' . ($c == $color ? ' selected="selected"' : '') . '>' . $c . '</option>';

				if (($i + 1) % $split == 0)
				{
					$html[] = '<option>-</option>';
				}
			}

			$html[] = '</select>';

			return implode('', $html);
		}
		else
		{
			$class        = ' class="' . trim('minicolors ' . $class) . '"';
			$control      = $control ? ' data-control="' . $control . '"' : '';
			$readonly     = $this->readonly ? ' readonly' : '';
			$hint         = $hint ? ' placeholder="' . $hint . '"' : ' placeholder="#rrggbb"';
			$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : '';

			// Including fallback code for HTML5 non supported browsers.
			JHtml::_('jquery.framework');
			JHtml::_('script', 'system/html5fallback.js', false, true);

			JHtml::_('behavior.colorpicker');

			return 
				$only_in_pro // web357 free
				.'<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '"' . $hint . $class . $position . $control
				. $readonly . $disabled . $required . $onchange . $autocomplete . $autofocus . '/>';
		}
	}
}
