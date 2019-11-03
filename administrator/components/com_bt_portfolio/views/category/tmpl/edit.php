<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		3.0.3
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
$component_params = JComponentHelper::getParams("com_bt_portfolio");
?>
<form
    action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="portfolio-form"
    enctype="multipart/form-data" class="form-validate <?php echo (!Bt_portfolioLegacyHelper::isLegacy() ? 'isJ30' : 'isJ25') ?>">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#details">Details</a></li>
		<li><a href="#options">Options</a></li>
		<li><a href="#layout">Layout</a></li>
		<li><a href="#metadata">Metadata</a></li>

	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane active">
			<div class="width-100 fltlft ">
				<fieldset class="adminform">
					<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('details') as $field) : ?>
							<li class="control-group"><?php
						echo $field->label;
						if ($field->type == "Editor")
							echo '<div class="clr"></div>';
						echo $field->input;
						?>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</div>
		</div>
		<div id="options" class="tab-pane">
			<div class="width-100 fltlft ">
			<fieldset class="adminform">
				<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('advanced') as $field) : ?>
						<li class="control-group"><?php
							echo $field->label;
							if ($field->type == "Editor")
								echo '<div class="clr"></div>';
							echo $field->input;
							?>
						</li>
						<?php endforeach; ?>
				</ul>
            </fieldset>
			</div>
		</div>
		<div id="layout" class="tab-pane">
			<div class="width-100 fltlft ">
			<fieldset class="adminform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset('layout') as $field) : ?>
					<li class="control-group"><?php
					echo $field->label;
					if ($field->type == "Editor")
						echo '<div class="clr"></div>';
					echo $field->input;
					?>
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			</div>
		</div>
		<div id="metadata" class="tab-pane">
			<div class="width-100 fltlft ">
			 <fieldset class="adminform">
				<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset('metadata') as $field) : ?>
						<li class="control-group"><?php
						echo $field->label;
						if ($field->type == "Editor")
							echo '<div class="clr"></div>';
						echo $field->input;
						?>
						</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
 <script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.tab-content .tab-pane').hide();
		jQuery('.tab-content .active').show();
		jQuery('.nav-tabs li a').click(function(){
		   var li = jQuery(this).parent();
		   if(jQuery(li).hasClass('active')) return false;
		   var tab = jQuery(this).attr('href');
		   jQuery('.nav-tabs li.active').removeClass('active');
		   jQuery(li).addClass('active');
		   
		   jQuery('.tab-pane.active').hide().removeClass('active');
		   jQuery(tab).addClass('active').show();
		   return false;
		});
	});
</script>
