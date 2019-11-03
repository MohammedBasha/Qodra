<?php
 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams ('com_media');
?>
<div class="contact<?php echo $this->pageclass_sfx?>">
	
	<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?>
				
				<div class="contact-misc-head">
					<div class="contact-misc-title"><?php echo JText::_('COM_CONTACT_MISC_TITLE'); ?></div>
					<div class="contact-misc-subtitle"><?php echo JText::_('COM_CONTACT_MISC_SUB_TITLE'); ?></div>
				</div>
				
				<div class="contact-miscinfo">
					<div class="contact-misc">
						<?php echo JHTML::_('content.prepare', $this->contact->misc); ?>
					</div>
				</div>
	<?php endif; ?>
	
	
	
	
	
	<?php $modules = JModuleHelper::getModules('map_contact'); ?>
	<?php if($modules){	?>
			<div class="map_contact">	
				<div class="map_contact_inner">	
					<?php foreach ( $modules as $mod) {	?>
							<?php echo  JModuleHelper::renderModule ($mod, array('style'=>'T3Xhtml')); ?>
					<?php } ?>
				</div>
			</div>
		<script type='text/javascript'>
			jQuery(document).ready(function(){
				jQuery('#t3-mainbody').addClass('hasMap');
			 });
		</script>
	<?php } ?>
	
	

	
	<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<div class="contact-form-head">
			<div class="contact-form-small-title"><?php echo JText::_('COM_CONTACT_FORM_SMALL_TITLE'); ?></div>
			<div class="contact-form-big-title"><?php echo JText::_('COM_CONTACT_FORM_BIG_TITLE'); ?></div>
		</div>
		<?php  echo $this->loadTemplate('form');  ?>
	<?php endif; ?>


	

</div>
