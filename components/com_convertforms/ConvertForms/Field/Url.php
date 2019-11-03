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

use ConvertForms\Validate;

class Url extends \ConvertForms\Field
{
    /**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'inputmask'
	);  

	protected $inheritInputLayout = 'text';
	
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
		$parent = parent::validate($value, $field_options, $form_data);

		if ($parent)
		{
			return true;
		}

		if (!Validate::url($value))
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_URL_INVALID'), $field_options);
		}
	}
}

?>