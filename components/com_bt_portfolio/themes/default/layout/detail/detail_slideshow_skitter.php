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
// no direct access
defined('_JEXEC') or die;

?>
		<!-- Title -->
		

		<!-- Slide show -->			
			<div class="box_skitter box_skitter_large" style="background:#<?php echo $this->params->get('ss_background','000000');?>;">
				<ul>
					<?php foreach ($this->images as $i => $image) :						
						$src_image_large = Bt_portfolioHelper::getPathImage($image->item_id,'large',$image->filename,$this->category->id);
						$src_image_original = Bt_portfolioHelper::getPathImage($image->item_id,'original',$image->filename,$this->category->id);
						$src_image_thumb = Bt_portfolioHelper::getPathImage($image->item_id,'ssthumb',$image->filename,$this->category->id);
						
						$rel = '{handler:\'iframe\'}';
						$src_image_original = Bt_portfolioHelper::extractUrl($image->youembed);
						if(!$src_image_original){
							$src_image_original = Bt_portfolioHelper::getPathImage($image->item_id,'original',$image->filename,$this->category->id);
							$rel = '{handler:\'image\'}';
						}
					?>
					<li>
					
						<img class="block" 	src="<?php echo $src_image_large; ?>" rel="<?php echo $src_image_thumb;?>" alt="<?php echo $image->title; ?>"/>
						<div class="label_text">
							 <?php if ($this->params->get('show_zoom_image',1)):	?>
								<a class="btp-zoom-image" onclick="return openModalBox(this,<?php echo $rel ?>)"   title="<?php echo JText::_('COM_BT_PORTFOLIO_ZOOM_IN'); ?>" href="<?php echo $src_image_original; ?>"><?php echo JText::_('COM_BT_PORTFOLIO_ZOOM_IN'); ?></a>
	                        <?php endif; ?>
							<?php if ($this->params->get('show_url') && $this->item->url):	?>
				                <a target="_blank" title="<?php echo JText::_('COM_BT_PORTFOLIO_VISIT_SITE'); ?>" class="visit-site" href="<?php  echo $this->item->url; ?>">
				                	<?php echo JText::_('COM_BT_PORTFOLIO_VISIT_SITE'); ?>
				                </a>
					        <?php endif; ?>
							
						</div>			
						
					</li>
					<?php endforeach; ?>
				</ul>
		   </div>		
		
		
		
		
		

