<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
<div class="services_blogs <?php echo $this->pageclass_sfx;?>">
		
		
			<div class="row">


			<?php $leadingcount=0 ; ?>
			<?php if (!empty($this->lead_items)) : ?>
				<?php foreach ($this->lead_items as &$item) : ?>
					<div class="servicesBlogItem col-md-6 col-sm-6 col-xs-12">
						<?php
							$this->item = &$item;
							echo $this->loadTemplate('item');
						?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>


			<?php if (!empty($this->intro_items)) : ?>
				<?php foreach ($this->intro_items as $key => &$item) : ?>
					<div class="servicesBlogItem col-md-6 col-sm-6 col-xs-12">
						<?php
							$this->item = &$item;
							echo $this->loadTemplate('item');
						?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>


			</div>


<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
<?php  endif; ?>

</div>
