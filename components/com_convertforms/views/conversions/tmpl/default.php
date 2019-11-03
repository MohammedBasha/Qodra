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

use ConvertForms\Helper;

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$user       = JFactory::getUser();
$columns    = $this->state->get('filter.columns');
$formfields = Helper::getFormFields($this->state->get('filter.form_id'));

// Prepare form

$columns_new = [];

foreach ($columns as $key => $column)
{
    if (strpos('param_', $column) !== false)
    {   
        $columns_new[] = [
            'name' => $column,
            'type' => 'text'
        ];

        continue;
    }

    $t_ = str_replace('param_', '', $column);
}

JFactory::getDocument()->addStyleDeclaration('
    .js-stools .js-stools-container-filters .chzn-container.active:not(.chzn-with-drop) .chzn-single {
        border: 1px solid rgba(0,0,0,0.2);
    }
    .js-stools .js-stools-container-filters .chzn-container.active .chzn-single {
        border: 1px solid #2384D3;
    }
');

?>

<form action="<?php echo JRoute::_('index.php?option=com_convertforms&view=conversions'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

    <?php
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <table class="adminlist nrTable scroll table">
        <thead>
            <tr>
                <th width="2%" class="center"><?php echo JHtml::_('grid.checkall'); ?></th>
                <th width="3%" class="nowrap hidden-phone" align="center">
                    <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>
                <?php foreach ($columns as $key => $column) { ?>
                    <th class="nowrap col_<?php echo $column; ?>">
                        <?php 
                            $isParam = (strpos($column, 'param_') !== false);
                            $columnLabel = $isParam ? ucfirst(str_replace('param_', '', $column)) : 'COM_CONVERTFORMS_' . strtoupper($column);
                            echo JHtml::_('searchtools.sort', $columnLabel, 'a.' . $column, $listDirn, $listOrder); 
                        ?>
                    </th>                            
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($this->items)) { ?>
                <?php foreach($this->items as $i => $item): ?>
                    <?php 
                        $canChange = $user->authorise('core.edit.state', 'com_convertforms.conversion.' . $item->id);
                        $canEdit   = $user->authorise('core.edit',       'com_convertforms.conversion.' . $item->id);

                    ?>
                    <tr class="row<?php echo $i % 2; ?> <?php echo isset($item->params->sync_error) ? 'error' : '' ?>">
                        <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="center">
                            <div class="btn-group">
                                <?php 
                                    echo JHtml::_('jgrid.published', $item->state, $i, 'conversions.', $canChange); 
                               
                                    if ($canChange)
                                    {
                                        JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'conversions');
                                        echo JHtml::_('actionsdropdown.render', $this->escape($item->id));
                                    }
                                ?>
                            </div>
                        </td>
                        <?php $i = 0; foreach ($columns as $key => $column) { 
                                // Convert to lower case to always match the field in case it has been renamed.
                                $column = strtolower($column);
                                $params = [];

                                if (!is_null($item->params))
                                {
                                    foreach ($item->params as $key => $value)
                                    {
                                        $params[strtolower($key)] = $value;
                                    }
                                }

                                $isParam = (strpos($column, 'param_') !== false);
                                $columnName = $isParam ? str_replace('param_', '' , $column) : $column;
                                $value = false;
                                $col_class = !$isParam ? 'nowrap col_' . $column : $column;

                            ?>
                            <td class="<?php echo $col_class; ?>">
                                <?php 
                                    switch ($columnName)
                                    {
                                        case 'id':
                                            if ($canEdit)
                                            {
                                                $url = JRoute::_('index.php?option=com_convertforms&task=conversion.edit&id=' . $item->id);
                                                $value = '<a href="' . $url . '">' . $item->$columnName . '</a>';
                                            } else 
                                            {
                                                $value = $item->$columnName;
                                            }

                                            break;
                                        case 'user_username':
                                            if ($lead_user = JFactory::getUser($item->user_id))
                                            {
                                                $url = JURI::base() . '/index.php?option=com_users&task=user.edit&id=' . $user->id;
                                                $value = '<a href="' . $url . '">' . $lead_user->username . '</a>';
                                            }
                                            break;
                                        case 'user_id':
                                            $value = '';

                                            if ($item->user_id > 0)
                                            {
                                                $url = JURI::base() . '/index.php?option=com_users&task=user.edit&id=' . $user->id;
                                                $value = '<a href="' . $url . '">' . $user->id . '</a>';       
                                            }
                                            break;

                                        default:
                                            if ($isParam)
                                            {
                                                if (isset($params[$columnName]))
                                                {
                                                    $value = $params[$columnName];
                                                }
                                            } else 
                                            {
                                                if (isset($item->$columnName))
                                                {
                                                    $value = $item->$columnName;
                                                }
                                            }

                                            $value = ConvertForms\Helper::escape($value);

                                            break;
                                    }

                                    if (is_array($value))
                                    {
                                        $value = implode(',', $value);
                                    }
                                ?>

                                <?php echo nl2br($value); ?>

                                <?php if ($i == 0 && isset($item->params->sync_service) && isset($item->params->sync_error) && $key == 0) { ?>
                                    <span class="hasPopover icon icon-info" 
                                        data-placement="top"
                                        data-title="<?php echo JText::_("PLG_CONVERTFORMS_" . $item->params->sync_service . "_ALIAS"); ?>"
                                        data-content="<?php echo $item->params->sync_error ?>"
                                        style="color:red;">
                                    </span>
                                <?php } ?>

                                <?php $i++; ?>
                            </td>                            
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>  
            <?php } else { ?>
                <tr>
                    <td align="center" colspan="<?php echo count($columns) + 2 ?>">
                        <div align="center">
                            <?php echo ConvertForms\Helper::noItemsFound(); ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>  
        </tbody>
    </table>

        <div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>

        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>
<?php include_once(JPATH_COMPONENT_ADMINISTRATOR . '/layouts/footer.php'); ?>
