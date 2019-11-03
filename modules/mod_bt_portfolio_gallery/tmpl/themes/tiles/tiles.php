<?php
/**
 * @package 	mod_bt_media_gallery - BT Media Gallery Module
 * @version		1.0
 * @created		Aug 2013
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/tiles/css/tilesGallery.css');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/tiles/js/tilesGallery.js');
require_once(JPATH_ROOT.'/components/com_bt_portfolio/router.php');
?>
<?php if ($list):?>
    <div id="mod-bt-portfolio-gallery-<?php echo $module->id?>" class="tiles-gallery mod-bt-portfolio-gallery <?php if($moduleclass_sfx) echo $moduleclass_sfx;?>">
		<div class="tiles-wrapper">
			<?php foreach ($list as $p) : ?>
			<?php 
			$Itemid = BTFindItemID($p->catid,$p->id);
			$Itemid = $Itemid? '&Itemid='.$Itemid:'';
			$p->link = JRoute::_("index.php?option=com_bt_portfolio&view=portfolio&id=" . $p->id .':'.$p->alias .'&catid_rel='.$p->catid.':'.$p->category_alias.$Itemid);
			?>
			
			<div class="tile">
				<div class="tile-image">
					<img src="<?php echo modBtPortfolioGalleryHelper::getItemImage($params, $p); ?>" alt="<?php $p->title?>">
				</div>
				<div class="tile-caption">
					<div class="tile-caption-2">
						<div class="tile-caption-wrapper">
							<h3><?php echo $p->title?></h3>
							<?php if ($params->get('show_des', 0)): ?>
							<div class="item-des">
								<?php echo $p->description;?>
							</div>
							<?php endif; ?>
							<a class="readmore" href="<?php echo $p->link?>" ><?php echo JText::_('BT_PORTFOLIO_GALLERY_READ_MORE')?></a>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>	
			
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#mod-bt-portfolio-gallery-<?php echo $module->id?>').tilesGallery({columns: 3, rows: 3});
		});
		</script>
	</div>
<?php else: ?>
    <?php echo JText::_('BT_PORTFOLIO_GALLERY_NO_IMAGE'); ?>
<?php endif; ?>
