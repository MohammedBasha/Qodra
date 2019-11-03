<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


		
		<div class="bt_social_icon <?php echo $params->get('moduleclass_sfx') ?>">
				<?php if( trim($params->get('pre_text')) ){ ?>
					<span class="socialIcon_pretext"><?php echo $params->get('pre_text') ?></span>
				<?php } ?>	
				
			<div class="iconLinks">	
				<?php if( trim($params->get('link_facebook')) ){ ?>
					<a class="iconLink icon_fb"  href="<?php echo $params->get('link_facebook') ?>" ><i class="fa fa-facebook"></i></a>
				<?php } ?>
				<?php if( trim($params->get('link_twitter')) ){ ?>
					<a class="iconLink icon_tt"  href="<?php echo $params->get('link_twitter') ?>" ><i class="fa fa-twitter"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_linkedin')) ){ ?>
					<a class="iconLink icon_in"  href="<?php echo $params->get('link_linkedin') ?>" ><i class="fa fa-linkedin"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_youtube')) ){ ?>
					<a class="iconLink icon_yt"  href="<?php echo $params->get('link_youtube') ?>" ><i class="fa fa-youtube"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_google_plus')) ){ ?>
					<a class="iconLink icon_gg"  href="<?php echo $params->get('link_google_plus') ?>" ><i class="fa fa-google-plus"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_skype')) ){ ?>
					<a class="iconLink icon_sk"  href="skype:<?php echo $params->get('link_skype') ?>?chat" ><i class="fa fa-skype"></i></a>
				<?php } ?>
				<?php if( trim($params->get('link_pinterest')) ){ ?>
					<a class="iconLink icon_pt"  href="<?php echo $params->get('link_pinterest') ?>" ><i class="fa fa-pinterest"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_dribbble')) ){ ?>
					<a class="iconLink icon_dr"  href="<?php echo $params->get('link_dribbble') ?>" ><i class="fa fa-dribbble"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_xing')) ){ ?>
					<a class="iconLink icon_xi"  href="<?php echo $params->get('link_xing') ?>" ><i class="fa fa-xing"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_flickr')) ){ ?>
					<a class="iconLink icon_fl"  href="<?php echo $params->get('link_flickr') ?>" ><i class="fa fa-flickr"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_tumblr')) ){ ?>
					<a class="iconLink icon_tu"  href="<?php echo $params->get('link_tumblr') ?>" ><i class="fa fa-tumblr"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_behance')) ){ ?>
					<a class="iconLink icon_be"  href="<?php echo $params->get('link_behance') ?>" ><i class="fa fa-behance"></i></a>
				<?php } ?>	
				<?php if( trim($params->get('link_vk')) ){ ?>
					<a class="iconLink icon_vk"  href="<?php echo $params->get('link_vk') ?>" ><i class="fa fa-vk"></i></a>
				<?php } ?>	
			</div>	
				



		</div>







