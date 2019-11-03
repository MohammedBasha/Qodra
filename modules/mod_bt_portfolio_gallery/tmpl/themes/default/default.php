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
$document->addStyleSheet(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/css/mod_bt_portfolio_gallery.css');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/js/jquery.mixitup.js');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/js/modernizr.min.js');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/js/classie.js');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/js/grid3d.js');
$document->addScript(JUri::root() . 'modules/mod_bt_portfolio_gallery/tmpl/themes/' . $params->get('theme', 'default') . '/js/mod_bt_portfolio_gallery.js');
$document->addScriptDeclaration(
		'var modPortfolioCfg = {siteURL: "' . JUri::base() . '", item_min_w:' . $params->get('item_min_width', 300) . '}'
);

?>
<?php if ($list):?>
    <div id="mod-bt-portfolio-gallery" class="mod-bt-portfolio-gallery <?php if($moduleclass_sfx) echo $moduleclass_sfx;?>">
        <?php if ($params->get('show_cat_filter', 1) == 1): ?>
            <div class="filters">
                <ul>
                    <li class="filter active" data-filter="all">All</li>
                    <?php foreach ($cats as $cat) : ?>
                        <?php if ($params->get('hide_empty_category', 1) == 1): ?>
                            <?php if ($cat->item_count > 0): ?>
                                <li class="filter" data-filter="<?php echo '.' . $cat->alias; ?>" data-parent="<?php echo $cat->parent_id; ?>" data-id="<?php echo $cat->id; ?>"><?php echo $cat->title; ?></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class="filter" data-filter="<?php echo '.' . $cat->alias; ?>" data-parent="<?php echo $cat->parent_id; ?>" data-id="<?php echo $cat->id; ?>"><?php echo $cat->title; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <section class="grid3d vertical" id="grid3d">
            <div class="container grid-wrap">
                <div class="row grid" id="grid">
                    <?php foreach ($list as $port) : ?>
                        <?php
                        $class_name = '';
                        foreach ($port->cats as $cat) {
                            $class_name .= $cat->alias . ' ';
                        }
                        ?>
                        <figure class="mix <?php echo $class_name; ?> col-sm-6 col-md-3" data-item-id="<?php echo $port->id; ?>">
                            <img src="<?php echo modBtPortfolioGalleryHelper::getItemImage($params, $port); ?>" alt="img">
                            <figcaption>
                                <div class="table">
                                    <div class="table-cell">
                                        <div class="title"><?php echo $port->title; ?></div>
                                        <div class="line"></div>
                                        <?php if ($params->get('show_category', 1) != 0): ?>
                                            <ul class="terms">
                                                <?php foreach ($port->cats as $cat): ?> 
                                                    <?php if ($params->get('show_category', 1) == 1): ?>
                                                        <li><?php echo $cat->title; ?></li>
                                                    <?php else: ?>
                                                        <li><a href="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=portfolios&catid=' . $cat->id); ?>"><?php echo $cat->title; ?></a></li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <?php if ($params->get('show_des', 0) == 1): ?>
                                            <div class="item-des">
                                                <?php echo $port->description;?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div><!-- /grid-wrap -->
            <div class="mod-portfolio-content">
                <?php foreach ($list as $port) : ?>
                    <div id="item-<?php echo $port->id; ?>" class="hide">
                    </div>
                <?php endforeach; ?>
                <div class="loading"></div>
                <span class="icon close-content"></span>
            </div>
        </section>
    </div>
<?php else: ?>
    <?php echo JText::_('No Item'); ?>
<?php endif; ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.mod-bt-portfolio-gallery .terms a').click(function(e){
		location.href = jQuery(this).attr('href');
		return false;
	});
});
</script>