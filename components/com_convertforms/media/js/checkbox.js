/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *
 * When using the HTML5 required attribute for a group of checkboxes the browser forces the user to check all inputs.
 * This code below intends to fix this issue by toggling the required attribute whenever a checkbox value changes.
*/
jQuery(function($) {
    $(".convertforms .cf-checkbox-group-required input:checkbox").change(function() {
    	var inputContainer = $(this).closest(".cf-control-input");

    	// Continue only if we have more than 1 inputs
    	if (inputContainer.find(".cf-checkbox-group").length <= 1) {
    		return true;
		}
		
		// Get all inputs
		var inputs = inputContainer.find("input");

		// Count checked inputs
		var totalChecked = inputs.filter(":checked").length;

		if (totalChecked > 0) {
			inputs.removeAttr("required").filter(":checked").attr("required", "required");
		} else {
			inputs.attr("required", "required");
		}
    }).trigger("change");
});