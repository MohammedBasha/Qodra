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

use \ConvertForms\Helper;

class Recaptcha extends \ConvertForms\Field
{
	/**
	 *  Exclude all common fields
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'name',
		'required',
		'size',
		'value',
		'placeholder',
		'browserautocomplete',
		'inputmask',
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

		$this->field->required = true;

		return $this;
	}

	/**
	 *  Get the reCAPTCHA Site Key used in Javascript code
	 *
	 *  @return  string
	 */
	public function getSiteKey()
	{
		return Helper::getComponentParams()->get('recaptcha_sitekey');
	}

	/**
	 *  Get the reCAPTCHA Secret Key used in communication between the website and the reCAPTCHA server
	 *
	 *  @return  string
	 */
	public function getSecretKey()
	{
		return Helper::getComponentParams()->get('recaptcha_secretkey');
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
        jimport('recaptcha', JPATH_PLUGINS . '/system/nrframework/helpers/wrappers');

        $recaptcha = new \NR_ReCaptcha(
            array('secret' => $this->getSecretKey())
        );

		$response = isset($form_data['g-recaptcha-response']) ? $form_data['g-recaptcha-response'] : null;

        $recaptcha->validate($response);

        if (!$recaptcha->success())
        {
            throw new \Exception($recaptcha->getLastError());
        }
	}

	/**
	 *  Display a text before the form options
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader()
	{
		if ($this->getSiteKey() && $this->getSecretKey())
		{
			return;
		}

		$url = \JURI::base() . 'index.php?option=com_config&view=component&component=com_convertforms#advanced';

		return 
			\JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE') . 
			' <a onclick=\'window.open("' . $url . '", "cfrecaptcha", "width=1000, height=750");\' href="#">' 
				. \JText::_("COM_CONVERTFORMS_FIELD_RECAPTCHA_CONFIGURE") . 
			'</a>.';
	}
}

?>