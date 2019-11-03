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

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Filter\InputFilter;
use ConvertForms\Form;
use ConvertForms\FieldsHelper;

defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Conversion Model Class
 */
class ConvertFormsModelConversion extends JModelAdmin
{
    /**
     *  The database object
     *
     *  @var  object
     */
    private $db;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JModelLegacy
     * @since   1.6
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->db = JFactory::getDbo();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Conversion', $prefix = 'ConvertFormsTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.6.1
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
        if (!isset($data->params))
        {
            return parent::preprocessForm($form, $data, $group);
        }

        // Add form custom fields to form
        $form_  = JModelLegacy::getInstance('Form', 'ConvertFormsModel', array('ignore_request' => true))->getItem($data->form_id);
        $fields = [];

        foreach ($form_->fields as $key => $field)
        {
            $type = $field['type'];

            // Skip these fields as they don't require any user input
            if (in_array($type, ['html', 'submit', 'recaptcha']))
            {
                continue;
            }

            // Map of fields types that need to be transformed in order to be recognized by the XML parser.
            $transformFields = [
                'hidden'     => 'text',
                'currency'   => 'NR_Currencies',
                'country'    => 'NR_Geo',
                'checkbox'   => 'checkboxes',
                'dropdown'   => 'list',
                'fileupload' => 'textlist'
            ];

            if ($type == 'fileupload')
            {
                if (!isset($field['limit_files']) || (isset($field['limit_files']) && $field['limit_files'] == '1'))
                {
                    $transformFields['fileupload'] = 'text';

                    // In case the previous multiple field is turn into a single field, we need to transform the value from array to string too.
                    if (is_array($data->params[$field['name']]))
                    {
                        $data->params[$field['name']] = implode(',', $data->params[$field['name']]);
                    }
                }
            }            
            
            if (array_key_exists($type, $transformFields))
            {
                $type = $transformFields[$type];
            }

            // Radio fields doesn't accept Array as a value and we need to transform it into a string.
            if (in_array($type, ['radio']))
            {
                if (isset($data->params[$field['name']]))
                {
                    $data->params[$field['name']] = implode('', (array) $data->params[$field['name']]);
                }
            }

            // Create the field
            $fld = new SimpleXMLElement('<field/>');
            $fld->addAttribute('name', $field['name']);
            $fld->addAttribute('type', $type);

            if (isset($field['label']) && !empty($field['label']))
            {
                $label = $field['label'];
            } elseif (isset($field['placeholder']) && !empty($field['placeholder']))
            {
                $label = $field['placeholder'];
            } else 
            {
                $label = ucfirst($field['name']);
            }
 
            $fld->addAttribute('label', $label);

            $fld->addAttribute('hint', isset($field['placeholder']) && !empty($field['placeholder']) ? $field['placeholder'] : $fld->attributes()->label);
            $fld->addAttribute('description', $fld->attributes()->hint);
            $fld->addAttribute('required', isset($field['required']) ? (bool) $field['required'] : false);
            $fld->addAttribute('class', 'input-xlarge');
            $fld->addAttribute('rows', 10); // Used for textarea inputs

            // Define options to list-based fields
            if (in_array($type, ['list', 'radio', 'checkboxes']) && isset($field['choices']))
            {
                foreach ($field['choices']['choices'] as $choice)
                {
                    $option = $fld->addChild('option', htmlspecialchars($choice['label']));
                    $option->addAttribute('value', empty($choice['value']) ? $choice['label'] : $choice['value']);
                }
            }

            // Get field's XML
            $fields[] = str_replace('<?xml version="1.0"?>', '', $fld->asXml());
        }
        
        $form->setField(new SimpleXMLElement('
            <fieldset name="params">
                <fields name="params">
                    ' . implode('', $fields) . '
                </fields>
            </fieldset>
        '));

		parent::preprocessForm($form, $data, $group);
	}

    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_convertforms.conversion', 'conversion', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) 
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_convertforms.edit.conversion.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     *  Validate data before saving
     *
     *  @param   object  $form   The form to validate
     *  @param   object  $data   The data to validate
     *  @param   string  $group  
     *
     *  @return  array           The validated data
     */
    public function validate($form, $data, $group = null)
    {
        // Validate conversion edited via the backend
        if (JFactory::getApplication()->isAdmin())
        {
            return parent::validate($form, $data, $group);
        }

        // Make sure we have a valid Form data
        if (!isset($data['cf']) || empty($data['cf']))
        {
            throw new Exception('No submission data found');
        }

        // Make sure we have a valid Form ID passed
        if (!isset($data['cf']['form_id']) || !$formid = (int) $data['cf']['form_id'])
        {
            throw new Exception('Form ID is either missing or invalid');
        }

        // Let the user manipulate the post data before saved into the database.
        $payload = ['post' => &$data['cf']];
        Form::runPHPScript($formid, 'formprocess', $payload);

        // Get form from payload or load a new instance
        $form = isset($payload['form']) ? $payload['form'] : Form::load($formid);

        // Honeypot check
        if (isset($data['cf']['hnpt']) && !empty($data['cf']['hnpt']))
        {
            throw new Exception('Honeypot field triggered');
            die();
        }

        // Make sure the right form is loaded
        if (is_null($form['id']))
        {
            throw new Exception('Unknown Form');
        }

        // Initialize the object that is going to be saved in the database
        $newData = array(
            'form_id'     => $formid,
            'campaign_id' => (int) $form['params']['campaign']
        );

        // Let's validate submitted data
        foreach ($form['fields'] as $key => $form_field)
        {
            $field_name  = isset($form_field['name']) ? $form_field['name'] : null;
            $field_class = FieldsHelper::getFieldClass($form_field['type'], $form_field);
            $user_value  = (!is_null($field_name) && isset($data['cf'][$field_name])) ? $data['cf'][$field_name] : null;

            // Validate and Filter user value. If an error occurs the submission aborts with an exception shown in the form
            $field_class->validate($user_value, $form_field, $data);

            // Skip unknown fields or fields with an empty value
            if (!$field_name || $user_value == '')
            {
                continue;
            }

            $newData['params'][$field_name] = $user_value;
        }

        // JSON_UNESCAPED_UNICODE encodes multibyte unicode characters literally. 
        // Without: Τάσος => \u03a4\u03ac\u03c3\u03bf\u03c2
        // With:    Τάσος => Τάσος
        $newData['params'] = json_encode($newData['params'], JSON_UNESCAPED_UNICODE);

        return $newData;
    }

    /**
     *  Create a new conversion based on the post data.
     *
     *  @return  object     The new conversion row object
     */
    public function createConversion($data)
    {
        // Validate data
        $data = $this->validate(null, $data);

        // Log debug message
        $debugData = urldecode(http_build_query($data, '', ', '));
        ConvertForms\Helper::log('New Lead: ' . $debugData);

        // Everything seems fine. Let's save data to the database.
        if (!$this->save($data))
        {
            throw new Exception($this->getError());
        }

        $submission = $this->getItem();

        // Run user's PHP script after the form has been processed, stored into the database and all addons have run.
        $payload = ['submission' => &$submission];
        Form::runPHPScript($data['form_id'], 'afterformsubmission', $payload);
        
        return $submission;
    }

    /**
     *  Get a conversion item
     *
     *  @param   interger  $pk  The conversion row primary key
     *
     *  @return  object         The conversion object
     */
    public function getItem($pk = null)
    {
        if (!$item = parent::getItem($pk))
        {
            return;
        }

        // Load Form & Campaign Model
        $modelForm = JModelLegacy::getInstance('Form', 'ConvertFormsModel', array('ignore_request' => true));
        $modelCampaign = JModelLegacy::getInstance('Campaign', 'ConvertFormsModel', array('ignore_request' => true));

        $item->form = $modelForm->getItem($item->form_id);
        $item->campaign = $modelCampaign->getItem($item->campaign_id);

        return $item;
    }
}