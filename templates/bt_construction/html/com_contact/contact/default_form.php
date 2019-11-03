<?php

 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
 if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="contact-form">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<fieldset>
			
			<div class="form_contact">
			<div class=" row">
			<div style="height:0;opacity:0;">
			<?php echo $this->form->getLabel('contact_name'); ?>
			<?php echo $this->form->getLabel('contact_email'); ?>
			<?php echo $this->form->getLabel('contact_subject'); ?>
			<?php echo $this->form->getLabel('contact_message'); ?>
			</div>
				
				<div class="col-md-4 col-sm-6 col-xs-12"><?php //echo $this->form->getInput('contact_name'); ?>
					<div class="contact-form-field">
						<input 	type="text" size="30" class="required contact_form_input" value="" 
								id="jform_contact_name" name="jform[contact_name]"
								placeholder="Your name"
						>
					</div>
				</div>
				
				
				<div class="col-md-4 col-sm-6 col-xs-12"><?php // echo $this->form->getInput('contact_email'); ?>
					<div class="contact-form-field">
						<input 	type="email" size="30" 
								value="" id="jform_contact_email" 
								class="validate-email required contact_form_input"
								name="jform[contact_email]"
								placeholder="Email address ex: email@domain.tld"
						>
					</div>
				</div>
				
				
				<div class="col-md-4 col-sm-12 col-xs-12"><?php //echo $this->form->getInput('contact_subject'); ?>
					<div class="contact-form-field">
						<input 	type="text"  size="60" 
								class="required contact_form_input" value="" 
								id="jform_contact_emailmsg" 
								name="jform[contact_subject]"
								placeholder="Your subject"
						>
					</div>
				</div>
				
				
				<div class="col-xs-12"><?php //echo $this->form->getInput('contact_message'); ?>
					<div class="contact-form-field">
						<textarea 	class="required contact_form_textarea" rows="10" cols="50" id="jform_contact_message" 
									name="jform[contact_message]" placeholder="Your message"></textarea>
					
					</div>
				</div>
				
			</div>

				<div class="row">
				<div class="col-sm-6 col-xs-12">
					<div class="contact-form-field">
						<button class="button validate send_button" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
					</div>
				</div>
				<div class="col-sm-6 col-xs-12">
					<div class="contact-form-field">
						<button class="button cancel_button" type="reset"><?php echo JText::_('COM_CONTACT_CONTACT_RESET'); ?></button>
					</div>
				</div>
				
					<input type="hidden" name="option" value="com_contact" />
					<input type="hidden" name="task" value="contact.submit" />
					<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
					<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
					<?php echo JHtml::_( 'form.token' ); ?>
				</div>
			</div>
		</fieldset>
	</form>
</div>