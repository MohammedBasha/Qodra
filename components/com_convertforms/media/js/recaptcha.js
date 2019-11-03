/**
 *  @package         Convert Forms
 *  @version         2.3.3 Free
 * 
 *  @author          Tassos Marinos <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *  
 *  Each reCAPTCHA user response token should be used only once. 
 *  
 *  If a verification attempt has been made with a particular token, it cannot be used again
 *  and we need to reset the CAPTCHA widget and ask the end user to verify it again.
 */

jQuery(function($) {
	'use strict';
	$(window).on("convertFormsAfterSubmit", function(event, form) {
		var widgetID = $(form).find(".g-recaptcha").attr("data-recaptcha-widget-id");
		if (widgetID) {
			grecaptcha.reset(widgetID);
		}
	})
})

// If the Google reCAPTCHA script is not loaded, display an error message next to each element.
document.addEventListener("convertFormsInit", function(e) {
	// Validate the recaptcha global object exists
	if (typeof grecaptcha === "object") {
		return;
	}

	// Check to see if we have un-initialized captcha elements
	var invalid_captchas = document.querySelectorAll(".nr-recaptcha:not([data-recaptcha-widget-id])");

	// Success!
	if (invalid_captchas.length == 0) {
		return;
	}

	// Display the error message next to each reCATCHA element.
	invalid_captchas.forEach(function(el) {
		el.innerHTML = Joomla.JText._('COM_CONVERTFORMS_RECAPTCHA_NOT_LOADED');
	});
});