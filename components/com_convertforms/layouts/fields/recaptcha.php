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

extract($displayData);

if (!$class->getSiteKey() || !$class->getSecretKey())
{
	echo JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA') . ': ' . JText::_('COM_CONVERTFORMS_FIELD_RECAPTCHA_KEYS_NOTE');
	return;
}

JText::script('COM_CONVERTFORMS_RECAPTCHA_NOT_LOADED');
JHtml::_('script', 'plg_captcha_recaptcha/recaptcha.min.js', ['version' => 'auto', 'relative' => true]);
JHtml::_('script', 'https://www.google.com/recaptcha/api.js?onload=JoomlaInitReCaptcha2&render=explicit&hl=' . JFactory::getLanguage()->getTag());
JHtml::_('script', 'com_convertforms/recaptcha.js', ['version' => 'auto', 'relative' => true]);

?>

<div class="nr-recaptcha g-recaptcha"
	data-sitekey="<?php echo $class->getSiteKey(); ?>"
	data-theme="<?php echo $field->theme ?>"
	data-size="<?php echo $field->size ?>">
</div>