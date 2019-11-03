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
	<div class="pmshow" style="display:none; max-width:<?php echo $width;?> ">
	<div id="btportfoliomedia">		
		<div class="bt-sliders">
			<div class="bt-window">
			<?php foreach( $this->images as $i => $row ): ?>

				<?php
				$src_image_large = Bt_portfolioHelper::getPathImage($row->item_id,'large',$row->filename,$this->category->id);
				$src_image_original = Bt_portfolioHelper::getPathImage($row->item_id,'original',$row->filename,$this->category->id);
				$src_image_thumb = Bt_portfolioHelper::getPathImage($row->item_id,'ssthumb',$row->filename,$this->category->id);	
				
				$rel = '{handler:\'iframe\'}';
				$src_image_original = Bt_portfolioHelper::extractUrl($row->youembed);
				if(!$src_image_original){
					$src_image_original = Bt_portfolioHelper::getPathImage($row->item_id,'original',$row->filename,$this->category->id);
					$rel = '{handler:\'image\'}';
				}
				?>
			<div class="bt-slide" style="background:#<?php echo $this->params->get('sm_background','000000');?>;">					
					<?php if($row->youembed){?>	
								<div style="display:none"><img class="bt-mainimg" src="<?php echo $src_image_large; ?>" rel="<?php echo $src_image_large;?>" />	</div>
								<div class="bt-video"  style="max-width:<?php echo $width;?>; height:<?php echo $height;?>;background:#<?php echo $this->params->get('sm_background','000000');?>">
									<?php if($i==0) echo $row->youembed; ?>
								</div>
								<input type="hidden" class="embedvideo" value="<?php echo htmlspecialchars($row->youembed); ?>"/>									
						<?php }
						else {?>							
								<img class="bt-mainimg" src="<?php echo $src_image_large; ?>" rel="<?php echo $src_image_large;?>"  alt="<?php echo $row->title; ?>"  />									
													
						<?php }?>	
				<?php if($this->params->get('showtitle',1) || $this->params->get('showdescription',1)): ?>				
				<div class="bt-caption">
					<div class="bt-caption-bg"></div>					
					<div class="bt-caption-content">
						<div class="bt-title">		
								<?php if($this->params->get('showtitle',1))
									echo $row->title; 									
								?>						
						</div>
						<div class="bt-introtext">
							<?php if($this->params->get('showdescription',1))
								 echo htmlspecialchars($row->youdesc); 								
							?>
						</div>	
					</div>
				</div>
				<?php endif; ?>
				<?php if ($this->params->get('show_zoom_image',1) && !$row->youembed):	?>
						<a class="btp-zoom-image" onclick="return openModalBox(this,<?php echo $rel ?>)"   title="<?php echo JText::_('COM_BT_PORTFOLIO_ZOOM_IN'); ?>" href="<?php echo $src_image_original; ?>"><?php echo JText::_('COM_BT_PORTFOLIO_ZOOM_IN'); ?></a>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
			</div>
		</div>
			<div class="shadowvideo" >
				<img class="imageshadow"src="<?php echo JURI::root() .'components/com_bt_portfolio/themes/default/images/bottomshadow-110-95-0.png' ?>">
			</div>
		<?php if($this->params->get('sm_next_back',1) && count($this->images)>1): ?>
			<div class="bt-handles handles-next">
				<div class="next"></div>
			</div>
			<div class="bt-handles handles-pre">
				<div class="prev"></div>
			</div>
		<?php endif;?>
		<center>
		<div class="bt-footernav">
			<div class="bt-navpipe">
				<?php foreach( $this->images as $i => $row ): ?>
					<?php 
					$src_image_original = Bt_portfolioHelper::getPathImage($row->item_id,'original',$row->filename,$this->category->id);
					$src_image_thumb = Bt_portfolioHelper::getPathImage($row->item_id,'ssthumb',$row->filename,$this->category->id); 
					
					?>				
				<div <?php if(!$this->params->get('showthumb',1)) echo 'style="display:none"';  ?> class="bt-nav <?php echo $i==0 ? 'bt-nav-first' : (($i==count($this->images)-1) ? 'bt-nav-last' : ''); ?>">
					<?php if($this->params->get('showthumb',1)): ?>	
						<?php
							$ss_thumb_width = $this->params->get('ss_thumb_width','70');
							$ss_thumb_height = $this->params->get('ss_thumb_height','40');
						?>
						<div class="bt-thumb" rel="<?php echo $row->title; ?>"><img src="<?php echo $src_image_thumb; ?>" width="<?php echo $ss_thumb_width ?>" height="<?php echo $ss_thumb_height ?>"/></div>						
					<?php endif; ?>	
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		</center>
	</div>
</div>
<script type="text/javascript">	
		$B('#btportfoliomedia').btsliders({		
			autoPlay:0,
			hoverPause: <?php echo $this->params->get('pause_hover',1) ?>,
			easing: '<?php echo $this->params->get('easing','jswing')?>',
			slideSpeed: <?php echo (int)$this->params->get('effect_time', '500')?>,
			interval:<?php echo $this->params->get('interval', 5)*1000 ?>,
			effect:'<?php echo $this->params->get('veffect-slide'); ?>' // slide or fade		
		});
</script>
		
