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

JFormHelper::loadFieldClass('subform');

class JFormFieldCFSubform extends JFormFieldSubform
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.6
	 */
	protected function getInput()
	{
		// The following script toggles the required attribute for all Email Notification options.
		JFactory::getDocument()->addScriptDeclaration('
			jQuery(function($) {
				var $switch = $("input[name=\'jform[sendnotifications]\']");

				$switch.on("change", function() {
					var toggle = $(this).val() == "1" ? true : false;
					toggleRequiredAttribute(toggle);
				});

				function toggleRequiredAttribute(enabled) {
					var fields = $("#behavior-emails .subform-repeatable-group").find("input, textarea, select").not("input[id$=reply_to]");

					if (enabled) {
						fields.attr("required", "required").addClass("required");
					} else {
						fields.removeAttr("required").removeClass("required");
					}
				}
			});
		');

		return parent::getInput();
	}
}
