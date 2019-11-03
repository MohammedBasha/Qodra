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
JHTML::_('behavior.modal');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$user = JFactory::getUser();

?>

<form action="<?php echo JRoute::_('index.php?option=com_convertforms&view=forms'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container">
        <?php
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>
        <table class="adminlist nrTable table">
            <thead>
                <tr>
                    <th class="text-center" width="2%"><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="3%" class="nowrap hidden-phone" align="center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('searchtools.sort', 'NR_NAME', 'a.name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="13%" class="text-center">
                        <?php echo JText::_('COM_CONVERTFORMS_CAMPAIGN') ?>
                    </th>
                    <th width="13%" class="text-center">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CONVERTFORMS_LEADS', 'leads', $listDirn, $listOrder); ?>
                    </th>
                    <th width="13%" class="text-center">
                        <?php echo JHtml::_('searchtools.sort', 'COM_CONVERTFORMS_ISSUES', 'issues', $listDirn, $listOrder); ?>
                    </th>
                    <th width="20%" class="text-center"><?php echo JText::_("COM_CONVERTFORMS_ACTIONS"); ?></th>
                    <th width="5%" class="nowrap text-center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->items)) { ?>
                    <?php foreach($this->items as $i => $item): ?>
                        <?php 
                            $canChange = $user->authorise('core.edit.state', 'com_convertforms.form.' . $item->id);
                            $hasLeads  = $item->leads + $item->issues;
                            $leadsURL  = JURI::base() . 'index.php?option=com_convertforms&view=conversions&filter.period&filter.form_id='. $item->id;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="text-center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'forms.', $canChange); ?>
                                    <?php
                                    if ($canChange)
                                    {
                                        JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'forms');
                                        JHtml::_('actionsdropdown.' . 'duplicate', 'cb' . $i, 'forms');
                                               
                                        echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo JRoute::_('index.php?option=com_convertforms&task=form.edit&id='.$item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>"><?php echo $this->escape($item->name); ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <?php 
                                    if (isset($item->campaign))
                                    {
                                        echo ConvertForms\Helper::getCampaign($item->campaign)->name;
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php $badgeClass = $item->leads ? 'success' : '' ?>
                                <a href="<?php echo $leadsURL ?>&filter.state=1">
                                    <span class="badge badge-<?php echo $badgeClass; ?> hasPopover" data-placement="top" data-content="<?php echo JText::sprintf("COM_CONVERTFORMS_FORM_LEADS", $item->leads) ?>">
                                        <?php echo $item->leads; ?>
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                <?php if ($item->issues > 0) { ?>
                                <a href="<?php echo $leadsURL ?>&filter.state=0">
                                    <span class="badge badge-important hasPopover" data-placement="top" data-content="<?php echo JText::sprintf("COM_CONVERTFORMS_ISSUES_DETECTED", $item->issues) ?>">
                                        <?php echo $item->issues; ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <ul class="item-icons">
                                    <li>
                                        <a class="hasPopover <?php echo (!$hasLeads) ? "disabled" : "" ?>" 
                                            data-placement="top"
                                            data-content="<?php echo JText::_("COM_CONVERTFORMS_VIEW_LEADS") ?>"
                                            href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=conversions&filter.form_id=<?php echo $item->id ?>&filter.state"><span class="icon icon-users"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="hasPopover" 
                                            data-placement="top"
                                            data-content="<?php echo JText::_("JTOOLBAR_DUPLICATE") ?>"
                                            href="javascript://" onclick="listItemTask('cb<?php echo $i; ?>', 'forms.duplicate')">
                                            <span class="icon icon-copy"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="hasPopover"
                                            data-placement="top"
                                            data-content="<?php echo JText::_("COM_CONVERTFORMS_FORM_CREATE_MODULE") ?>"
                                            href="<?php echo JURI::base() ?>index.php?option=com_modules&task=module.add&eid=<?php echo $this->moduleID ?>">
                                            <span class="icon icon-cube"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="hasPopover copyToClipboard"
                                            data-clipboard="{convertforms <?php echo $item->id ?>}"
                                            data-placement="top"
                                            data-content="<?php echo JText::sprintf("COM_CONVERTFORMS_FORM_CLIPBOARD_SHORTCODE", "{convertforms ".$item->id."}") ?>"
                                            href='#'>
                                            <span class="icon icon-link"></span>
                                        </a>
                                    </li>
                                </ul>
                            </td>
                            <td class="text-center"><?php echo $item->id ?></td>
                        </tr>
                    <?php endforeach; ?>  
                <?php } else { ?>
                    <tr>
                        <td align="center" colspan="9">
                            <div align="center">
                                <?php echo ConvertForms\Helper::noItemsFound("forms"); ?>
                                - 
                                <a href="javascript://" onclick="Joomla.submitbutton('form.add')"><?php echo JText::_("COM_CONVERTFORMS_CREATE_NEW") ?></a>   
                            </div>
                        </td>
                    </tr>
                <?php } ?>        
            </tbody>
            <tfoot>
    			<tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr>
            </tfoot>
        </table>
        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>

<?php include_once(JPATH_COMPONENT_ADMINISTRATOR."/layouts/footer.php"); ?>

<script>
    jQuery(function($) {
        $(".copyToClipboard").click(function() {
            $data = $(this).data("clipboard");

            copyTextToClipboard($data, function(success) {
                if (success)  {
                    alert("Shortcode " + $data + " copied to clipboard");
                }
            });

            return false;
        });
    })

    Joomla.submitbutton = function(task) {
        if (task == 'form.add') {
            url = "index.php?option=com_convertforms&view=templates&tmpl=component"
            SqueezeBox.open(url, { handler: 'iframe', size: {x: 1100, y: 635}}); 
        } else {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    }

    function copyTextToClipboard(text, callback) {
        var textArea = document.createElement("textarea");
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.background = 'transparent';
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();

        try {
            var success = document.execCommand('copy');
            callback(success);
        } catch (err) {
            callback(false);
        }

        document.body.removeChild(textArea);
    }
</script>