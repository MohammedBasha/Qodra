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

class Hidden extends \ConvertForms\Field
{
	/**
	 *  Indicates if the container will be rendered or not in the front-end
	 *
	 *  @var  boolean
	 */
	protected $hideContainer = true;

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
		'label',
		'placeholder',
		'required',
		'description',
		'cssclass',
		'hidelabel',
		'browserautocomplete',
		'size',
		'inputmask',
		'readonly'
	);
}

?>