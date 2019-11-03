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
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form
    action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=categories'); ?>"
    method="post" name="adminForm" id="adminForm">
	<?php if(!$this->legacy): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_BT_PORTFOLIO_SEARCH_IN_TITLE');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_BT_PORTFOLIO_SEARCH_IN_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BT_PORTFOLIO_SEARCH_IN_TITLE'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
<?php else : ?>
	<div id="j-main-container">
    <div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
			</label> <input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			<button type="submit" class="btn">
			<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button"
				onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
		<div class="filter-select fltrt">
                <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php
                    echo JHtml::_('select.options', Bt_portfolioHelper::getPublishedOptions(), 'value', 'text', $this->state->get(
                                    'filter.published'));
                    ?>
                </select> 
                <select name="filter_parent_id" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
                    <?php
                    echo JHtml::_('select.options', Bt_portfolioHelper::getCategoryOptions(), 'value', 'text', $this->state->get(
                                    'filter.parent_id'));
                    ?>
                </select>
                <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')); ?>
                </select>
                <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
                </select>
		</div>
	</fieldset>
<?php endif;?>	
    <div class="clr"></div>

    <table class="adminlist table table-striped">
        <thead>
            <tr>
                <th width="1%"><input type="checkbox" name="checkall-toggle"
                                      value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                                      onclick="Joomla.checkAll(this)" />
                </th>
                <th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th><?php echo JHtml::_('grid.sort', 'Parent', 'parent_title', $listDirn, $listOrder); ?>
                </th>
                <th width="8%"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                </th>


                <th width="12%"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                    <?php if ($saveOrder) : ?> <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'categories.saveorder');
                        ?> <?php endif; ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
                </th>


                <th width="8%">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th width="1%" class="nowrap"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>

        </tfoot>
        <tbody>
            <?php
            foreach ($this->items as $i => $item) :
                $item->max_ordering = 0; //??
                $ordering = ($listOrder == 'a.ordering');
                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                $canChange = true;
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td ><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                <td>
                    <?php if ($item->checked_out) : ?> <?php
                    echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin
                    );
                    ?> 
                    <?php endif; ?> 
                    <a style="width: 20px" href="<?php echo JRoute::_('index.php?option=com_bt_portfolio&task=category.edit&id=' . $item->id); ?>">
                        <?php echo $this->escape($item->title); ?> 
                    </a>
                    <p class="smallsub">
                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                    </p>
                </td>
                <td >
                    <?php if ($item->parent_id) echo '<a href="index.php?option=com_bt_portfolio&view=categories&filter_parent_id=' . $item->parent_id . '">' . $this->escape($item->parent_title) . '</a>'; ?>
                </td>
                <td><?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', $canChange, 'cb'); ?>
                </td>
                <td class="order">
                    <?php if ($canChange) : ?> <?php if ($saveOrder) : ?>
                    <?php if ($listDirn == 'asc') : ?> 
                    <span>
                        <?php
                            echo $this->pagination->orderUpIcon($i, ($item->parent_id == @$this->items[$i - 1]->parent_id), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering);
                        ?>
                    </span>
                    <span>
                        <?php
                        echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->parent_id == @$this->items[$i + 1]->parent_id), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering);
                        ?>
                    </span> 
                    <?php elseif ($listDirn == 'desc') : ?> 
                    <span>
                        <?php
                        echo $this->pagination->orderUpIcon($i, ($item->parent_id == @$this->items[$i - 1]->parent_id), 'categories.orderdown', 'JLIB_HTML_MOVE_UP', $ordering);
                        ?>
                    </span> 
                    <span>
                        <?php
                        echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->parent_id == @$this->items[$i + 1]->parent_id), 'categories.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering);
                        ?>
                    </span> 
                        <?php endif; ?> 
                    <?php endif; ?> 
                    <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>                
                            <input type="text" name="order[]" size="5" style="width: 20px"
                                   value="<?php echo $item->ordering; ?>" <?php echo $disabled ?>
                                   class="text-area-order" /> <?php else : ?> <?php echo $item->ordering; ?>
                               <?php endif; ?>
                    </td>
                    <td >
                        <?php echo $this->escape($item->access_level); ?>
                    </td>


                    <td>
                        <?php if ($item->language == '*'): ?>
                            <?php echo JText::alt('JALL', 'language'); ?>
                        <?php else: ?>
                            <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo (int) $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" /> <input type="hidden"
                                                            name="boxchecked" value="0" /> <input type="hidden"
                                                            name="filter_order" value="<?php echo $listOrder; ?>" /> <input
                                                            type="hidden" name="filter_order_Dir"
                                                            value="<?php echo $listDirn; ?>" />
                                                            <?php echo JHtml::_('form.token'); ?>
    </div>
    </div>
</form>
