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

		<?php
		if($this->params->get('largeimgprocess',-1)==-1){
			$width='100%';
			$height =$this->params->get('crop_height','400').'px';
		}
		else{
		$width =$this->params->get('crop_width','600').'px';
		$height =$this->params->get('crop_height','400').'px';
		}
		?>
		<ul>
			<?php foreach ($this->images as $image) :
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
					<?php if($image->youembed){?>	
								<div style="display:none"><img class="bt-mainimg" src="<?php echo $src_image_large; ?>" rel="<?php echo $src_image_large;?>" />	</div>
								<div class="bt-video"  style="max-width:<?php echo $width;?>; height:<?php echo $height;?>;background:#<?php echo $this->params->get('ss_background','000000');?>">
									<?php echo $image->youembed; ?>
								</div>
																
						<?php }
						else {?>							
								<img class="bt-mainimg" src="<?php echo $src_image_large; ?>" rel="<?php echo $src_image_large;?>"  alt="<?php echo $image->title; ?>"  />									
													
						<?php }?>			
						<div class="bt-caption">
							<div class="bt-caption-bg"></div>					
							<div class="bt-caption-content">
								
								<div class="bt-title">		
										<?php if($this->params->get('showtitle',1))
											echo $image->title; 									
										?>						
								</div>
								
								<div class="bt-introtext">
									<?php if($this->params->get('showdescription',1))
										 echo htmlspecialchars($image->youdesc); 								
									?>
								</div>	
								
								
							</div>
							
					</div>
				<?php endforeach; ?>
		</ul>
		
