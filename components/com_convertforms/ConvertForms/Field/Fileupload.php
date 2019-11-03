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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

use ConvertForms\Helper;
use ConvertForms\Validate;
use ConvertForms\UploadHelper;
use Joomla\Registry\Registry;

class FileUpload extends \ConvertForms\Field
{
	/**
	 * The default upload folder
	 *
	 * @var string
	 */
	protected $default_upload_folder = '/media/com_convertforms/uploads';

	/**
	 * If enabled, the AJAX response with the uploaded filename will be returned encrypted
	 *
	 * @var bool
	 */
	private $encrypt_filename = true;

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'inputmask',
		'size',
		'value',
		'browserautocomplete',
		'placeholder',
		'readonly'
	);

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);
		$field = $this->field;

		if (!isset($field->limit_files)) 
		{
			$field->limit_files = 1;
		}

		if (!isset($field->upload_types) || empty($field->upload_types)) 
		{
			$field->upload_types = 'image/*';
		}

		// Accept multiple values
		if ((int) $field->limit_files != 1)
		{
			$field->name .= '[]';
		}

		return $this;
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *  @param   array  $field_options   The field's options (Entered in the backend)
	 *  @param   array  $form_data       The form submitted data
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value, $field_options, $form_data)
	{
		$field_options = new Registry($field_options); // Move this parent class

		$is_required 	   = $field_options->get('required', false);
		$max_files_allowed = $field_options->get('limit_files', 1);
		$allowed_types     = $field_options->get('upload_types');
		$upload_folder     = $field_options->get('upload_folder', $this->default_upload_folder);

		// Remove null and empty values
		$value = is_array($value) ? $value : (array) $value;
		$value = array_filter($value);

		// We expect a not empty array
		if ($is_required && empty($value))
		{
			$this->throwError(\JText::_('COM_CONVERTFORMS_FIELD_REQUIRED'), $field_options);
		}

		// Do we have the correct number of files?
		if ($max_files_allowed > 0 && count($value) > $max_files_allowed)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_UPLOAD_MAX_FILES_LIMIT', $max_files_allowed), $field_options);
		}

		// Validate file paths
		foreach ($value as &$file)
		{
			// Decrypt file first
			if ($this->encrypt_filename)
			{
				$file = UploadHelper::getCrypt()->decrypt($file);
			}

			// Check if the file really uploaded
			$file_path = JPATH_ROOT . '/' . $upload_folder . '/' . $file;

			if (!\JFile::exists($file_path))
			{	
				$this->throwError(\JText::_('COM_CONVERTFORMS_UPLOAD_FILE_IS_MISSING'), $field_options);
			}

			// Check file type
			if (!UploadHelper::isInAllowedTypes($allowed_types, $file_path))
			{
				\JFile::delete($file_path);
				$this->throwError(\JText::sprintf('COM_CONVERTFORMS_UPLOAD_INVALID_FILE_TYPE', $file, $allowed_types), $field_options);
			}

			// Get absolute URL
			$file = UploadHelper::absURL($file_path);
		}

		// If we expect a single file, save it as a string instead of array.
		if (!empty($value) && $max_files_allowed == 1 && isset($value[0]))
		{
			$value = $value[0];
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
		if (empty($field_options['upload_folder']))
		{
			$field_options['upload_folder'] = $this->default_upload_folder;
		}

		// Validate Upload Folder
		$upload_folder = JPATH_ROOT . '/' . $field_options['upload_folder'];

		if (!UploadHelper::folderExistsAndWritable($upload_folder))
		{
			$model->setError(\JText::sprintf('COM_CONVERTFORMS_UPLOAD_FOLDER_INVALID', $upload_folder));
			return false;
		}

		return parent::onBeforeFormSave($model, $form_data, $field_options);
	}

	/**
	 * Event fired before the field options form is rendered in the backend
	 *
	 * @param  object $form
	 *
	 * @return void
	 */
	protected function onBeforeRenderOptionsForm($form)
	{
		// Set the maximum upload size limit to the respective options form field
		$max_upload_size_str = \JHtml::_('number.bytes', \JUtility::getMaxUploadSize());
		$max_upload_size_int = (int) $max_upload_size_str;

		$form->setFieldAttribute('max_file_size', 'max', $max_upload_size_int);

		$desc_lang_str = $form->getFieldAttribute('max_file_size', 'description');
		$desc = \JText::sprintf($desc_lang_str, $max_upload_size_str);
		$form->setFieldAttribute('max_file_size', 'description', $desc);
	}

	/**
	 * Ajax method triggered by System Plugin during file upload.
	 *
	 * @param	string	$form_id
	 * @param	string	$field_key
	 *
	 * @return	array
	 */
	public function onAjax($form_id, $field_key)
	{
        // Make sure we have a valid form and a field key
        if (!$form_id || !$field_key)
        {
            $this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR');
		}
		
		// Make sure we have a valid file passed
        if (!$file = \JFactory::getApplication()->input->files->get('file'))
        {
            $this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_FILE');
		}
		
        // In case we allow multiple uploads the file parameter is a 2 levels array.
        $first_property = array_pop($file);
        if (is_array($first_property))
        {
            $file = $first_property;
		}

		if (!$upload_field_settings = Helper::getFieldSettings($form_id, $field_key))
		{
        	$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_FIELD');
		}

		$upload_folder = $upload_field_settings->get('upload_folder', $this->default_upload_folder);

		// Upload the file but before add a random prefix and .tmp suffix.
		if (!$uploaded_filename = UploadHelper::upload($file, $upload_folder))
		{
			$this->die('COM_CONVERTFORMS_UPLOAD_ERROR_CANNOT_UPLOAD_FILE');
		}

		if ($this->encrypt_filename)
		{
			$uploaded_filename = UploadHelper::getCrypt()->encrypt($uploaded_filename);
		}

		return [
			'file' => $uploaded_filename,
		];
	}

	/**
	 * DropzoneJS detects errors based on the response error code.
	 *
	 * @param  string $error_message
	 *
	 * @return void
	 */
	private function uploadDie($error_message)
	{
		http_response_code('500');
		die(\JText::_($error_message));
	}
}


?>