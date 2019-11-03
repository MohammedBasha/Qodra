<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();
?>


<?php $serviceDetailRight = JModuleHelper::getModules('serviceDetailRight'); ?>
<?php 
$classColLeft = '';
$classColRight = '';
if($serviceDetailRight){
	$classColLeft = 'col-xs-12 col-sm-8  col-md-9';
	$classColRight = 'col-xs-12 col-sm-4  col-md-3';
} else {
	$classColLeft = 'col-xs-12';
	$classColRight = '';
}

?>
<div class="servicesDetail <?php echo $this->pageclass_sfx?>">
<div class="servicesDetail">


<div class="row">
	<div class="<?php echo $classColLeft; ?> servicesDetailLeft">
		
		<div class="servicesDetailLeftBody">
			<?php if ($params->get('show_title')) : ?>
				<h2 class="servicesDetail_title">
					<span><?php echo $this->escape($this->item->title); ?></span>
				</h2>
			<?php endif; ?>


			<?php  if (isset($images->image_fulltext) and !empty($images->image_fulltext)) : ?>
			<div class="img-fulltext">
			<img src="<?php echo htmlspecialchars($images->image_fulltext); ?>" />
			</div>
			<?php endif; ?>

			<div class="servicesDetail_text"><?php echo str_replace ($this->item->introtext, '', $this->item->text) ?></div>
		</div>
		
		<?php $modules = JModuleHelper::getModules('serviceDetail'); ?>
		<?php if($modules){	?>
				<div class="serviceDetailModules">	
					<?php foreach ( $modules as $mod) {	?>
						<div class="serviceDetailModule">	
							<?php echo  JModuleHelper::renderModule ($mod, array('style'=>'T3Xhtml')); ?>
						</div>
					<?php } ?>
				</div>
		<?php } ?>


	</div>
	
	
	<?php if($serviceDetailRight){	?>
		<div class="<?php echo $classColRight; ?> servicesDetailRight">
			<?php foreach ( $serviceDetailRight as $mod) {	?>
				<div class="servicesDetailRight_module">	
					<?php echo  JModuleHelper::renderModule ($mod, array('style'=>'T3Xhtml')); ?>
				</div>
			<?php } ?>
		
		</div>
	<?php } ?>
	
	
</div>
</div>




</div>
