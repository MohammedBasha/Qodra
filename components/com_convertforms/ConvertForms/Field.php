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

namespace ConvertForms;

use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted access');

/**
 *  Convert Forms Field Main Class
 */
class Field
{
	/**
	 *  Field Object
	 *
	 *  @var  object
	 */
	protected $field;

	/**
	 *  The prefix name used for input names
	 *
	 *  @var  string
	 */
	private $namePrefix = 'cf';

	/**
	 *  Indicates if the container will be rendered or not.
	 *
	 *  @var  boolean
	 */
	protected $hideContainer = false;

	/**
	 *  Filter user value before saving into the database. By default converts the input to a string; strips all HTML tags / attributes.
	 *
	 *  @var  string
	 */
	protected $filterInput = 'HTML';

	/**
	 *  Exclude common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields;

	/**
	 *  Data passed to layout rendering
	 *
	 *  @var  object
	 */
	private $layoutData;
	
	/**
	 * Indicates whether it accepts multiple values
	 *
	 * @var bool
	 */
	protected $multiple = false;

	/**
	 *  Class constructor
	 *
	 *  @param   mixed  $field   Object or Array Field options
	 *
	 *  @return  void
	 */
	public function __construct($field = null)
	{
		if ($field)
		{
			$this->field = $field;
		}
	}

	/**
	 * Event fired during form saving in the backend to help us validate user options.
	 *
	 * @param  object	$model			The Form Model
	 * @param  array	$form_data		The form data to be saved
	 * @param  array	$field_options	The field data
	 *
	 * @return bool
	 */
	public function onBeforeFormSave($model, $form_data, &$field_options)
	{
		if (isset($field_options['name']) && $field_options['name'] == '')
		{
			$field_options['name'] = $field_options['type'] . '_' . $field_options['key'];
		}

		return true;
	}

	/**
	 *  Set field object
	 * 
	 *  Rename to prepareField
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		$field = is_array($field) ? (object) $field : $field;

		if (!isset($field->name) || empty($field->name))
		{
			$field->name = $this->getName() . '_' . $field->key;
		}

		// Generate a unique field ID to avoid conflicts with a form rendered multiple times on the same page.
		$field->id   = 'form' . $field->namespace . '_' . $field->name . '_' . rand();
		$field->name = $this->namePrefix . '[' . $field->name . ']';

		$this->field = $field;

		return $this;
	}

	/**
	 *  Discovers the actual field name from the called class
	 *
	 *  @return  string
	 */
	protected function getName()
	{
		$class_parts = explode('\\', get_called_class());
		return strtolower(end($class_parts));
	}

	/**
	 *  Renders the field's input element
	 *
	 *  @return  string  	HTML output
	 */
	protected function getInput()
	{
		$layoutsPath = \ConvertForms\Helper::getLayoutsPath() . '/fields/';

		// Override layout path if it's available
		$layoutName = isset($this->inheritInputLayout) ? $this->inheritInputLayout : $this->getName();
	
		// Check if an admininistrator layout is available
		if (\JFactory::getApplication()->isAdmin() && \JFile::exists($layoutsPath . $layoutName . '_admin.php'))
		{
			$layoutName .= '_admin';
		}

		return \JLayoutHelper::render($layoutName, $this->getInputData(), $layoutsPath);
	}

	/**
	 *  Prepares the field's input layout data
	 *
	 *  @return  array
	 */
	protected function getInputData()
	{
		return array(
			'class' => $this,
			'field' => $this->field,
			'form'  => $this->field->form
		);
	}

	/**
	 *  Renders the Field's control group that will contain both input and label parts.
	 *
	 *  @return  string 	HTML Output
	 */
	public function getControlGroup()
	{
		$this->field->input = $this->getInput();

		if ($this->hideContainer)
		{
			return $this->field->input;
		}

		$layoutData = array(
			'field' => $this->field,
			'form'  => $this->field->form
		);

    	return \JLayoutHelper::render('controlgroup', $layoutData, \ConvertForms\Helper::getLayoutsPath());
	}

	/**
	 *  Renders the Field's Options Form used in the backend
	 *
	 *  @param   string  $formControl  From control prefix
	 *  @param   array   $loadData     Form data to bind
	 *
	 *  @return  string                The final HTML output
	 */
	public function getOptionsForm($formControl = 'jform', $loadData = null)
	{
		// Setup the common form first
		$form = new \JForm('cf', array('control' => $formControl));

		$form->addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/fields');
		$form->addFieldPath(JPATH_PLUGINS . '/system/nrframework/fields');
		$form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/ConvertForms/xml/field.xml');

		// Exclude Fields
		if (is_array($this->excludeFields))
		{
			$reservedFields = array(
				'key',
				'type'
			);

			foreach ($form->getFieldSets() as $key => $fieldSetName)
			{	
				$fields = $form->getFieldset($fieldSetName->name);

				foreach ($fields as $key => $field)
				{
					// We can't exclude reserved fields
					if (in_array($field->fieldname, $reservedFields))
					{
						continue;
					}

					if (!in_array($field->fieldname, $this->excludeFields) && 
						!in_array('*', $this->excludeFields))
					{
						continue;
					}

					$form->removeField($field->fieldname);
				}
			}
		}

		// Load field based options
		$form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/ConvertForms/xml/field/' . $this->getName() . '.xml');

		// Bind Data
		$form->bind($loadData);

		// Give individual field classes to manipulate the form before the render
		if (method_exists($this, 'onBeforeRenderOptionsForm'))
		{
			$this->onBeforeRenderOptionsForm($form);
		}

		// Render Layout
		$data = array(
			'form' 			=> $form,
			'header'		=> $this->getOptionsFormHeader(),
			'fieldTypeName' => $this->getName(),
			'loadData' 	 	=> $loadData
		);
		
		return \JLayoutHelper::render('optionsform', $data, \ConvertForms\Helper::getLayoutsPath());
	}

	/**
	 *  Display a text before the form options
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader()
	{
		return;
	}

	/**
	 *  Validate form submitted value
	 *
	 *  @param   mixed  $value           The field's value to validate (Passed by reference)
	 *  @param   array  $field_options   The field's options (Entered in the backend)
	 *  @param   array  $form_data       The form submitted data
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value, $field_options, $form_data)
	{
		$is_required = isset($field_options['required']) ? (bool) $field_options['required'] : false;

		if (!$is_required)
		{
			return true;
		}
		
		$empty = false;
		
		if (is_array($value) && count($value) == 0)
		{
			$empty = true;
		} else 
		{
			// Note: Do not use empty() as evaluates '0' as true which is a valid value for the Number field.
			if ($value == '' || is_null($value))
			{
				$empty = true;
			}
		}

		if ($empty)
		{
			$this->throwError(\JText::_('COM_CONVERTFORMS_FIELD_REQUIRED'), $field_options);
		}

		// Let's do some filtering.
		$value = $this->filterInput($value);
	}

	/**
	 *  Filter user input
	 *
	 *  @param   mixed  $input   User input value
	 *
	 *  @return  mixed           The filtered user input
	 */
	protected function filterInput($input)
	{
		$filter = $this->getFieldFilter();

		// Safehtml is a special filter angd we need to initialize JFilterInput class differently
		if ($filter == 'safehtml')
		{
			return \JFilterInput::getInstance(null, null, 1, 1)->clean($input, 'html');
		}

		return \JFilterInput::getInstance()->clean($input, $filter);
	}

	/**
	 * Return field's user input filter
	 *
	 * @return string
	 */
	protected function getFieldFilter()
	{
		// Return filter set in the backend
		if ($this->field && isset($this->field['filter']) && !empty($this->field['filter']))
		{
			return $this->field['filter'];
		}

		// Return default filter
		return $this->filterInput;
	}

	/**
	 *  Throw an error exception
	 *
	 *  @param   [type]  $message        [description]
	 *  @param   [type]  $field_options  [description]
	 *
	 *  @return  [type]                  [description]
	 */
	public function throwError($message, $field_options)
	{
		$label = isset($field_options['label']) && !empty($field_options['label']) ? $field_options['label'] : $field_options['name'];
		throw new \Exception($label . ': ' . \JText::_($message));
	}
}
?>