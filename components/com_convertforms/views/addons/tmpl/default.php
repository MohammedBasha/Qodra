<?php

/**
 * @package         Convert Forms
 * @version         2.3.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.popover');

?>

<div class="row-fluid">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container">
        
        <div class="cf-addons-container">
            <h2>
                <?php echo JText::_("COM_CONVERTFORMS") ?>
                <?php echo JText::_("COM_CONVERTFORMS_ADDONS") ?>
            </h2>
            <p><?php echo JText::_("COM_CONVERTFORMS_ADDONS_DESC") ?></p>
            <div class="cf-addons">
                <?php foreach ($this->availableAddons as $key => $item) { ?>
                    <div class="cf-addon">
                        <div class="cf-addon-wrap">
                            <div class="cf-addon-img">
                                <img alt="<?php echo $item["label"]; ?>" src="<?php echo $item["image"]; ?>"/>
                            </div>
                            <div class="cf-addon-text">
                                <h3><?php echo $item["label"]; ?></h3>
                                <?php echo $item["description"]; ?>
                            </div>
                            <div class="cf-addon-action text-center">

                                <?php if ($item["comingsoon"]) { ?>
                                    Coming Soon
                                <?php } else { ?>

                                    
                                    
                                    <a href="<?php echo $item["url"] ?>" class="btn btn-danger" target="_blank">
                                        <span class="icon icon-lock"></span>
                                        <?php echo JText::_("NR_UPGRADE_TO_PRO"); ?>
                                    </a>
                                    

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="cf-addon">
                    <div class="cf-addon-wrap">
                        <div class="cf-addon-img">
                            <a target="_blank" target="_blank" href="https://www.tassos.gr/contact">
                                <img alt="<?php echo $item["description"]; ?>" src="http://static.tassos.gr/images/integrations/addon.png"/>
                            </a>
                        </div>
                        <div class="cf-addon-text">
                            <h3><?php echo JText::_("COM_CONVERTFORMS_ADDONS_MISSING_ADDON") ?></h3>
                            <?php echo JText::_("COM_CONVERTFORMS_ADDONS_MISSING_ADDON_DESC") ?>
                        </div>
                        <div class="cf-addon-action text-center">
                            <a class="btn btn-primary" target="_blank" href="https://www.tassos.gr/contact"><?php echo JText::_("NR_CONTACT_US")?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once(JPATH_COMPONENT_ADMINISTRATOR."/layouts/footer.php"); ?>
