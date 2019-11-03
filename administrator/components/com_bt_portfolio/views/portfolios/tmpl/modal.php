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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
$function	= JRequest::getCmd('function', 'jSelectPortfolio');
?>
<form action="<?php echo JRoute::_('index.php?option=com_bt_portfolio&view=portfolios&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>'); ?>"
	method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if(!$this->legacy): ?>
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
					<i class="icon-remove"></i></button>
			</div>
			<input onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('0', '<?php echo $this->escape(addslashes(JText::_('COM_CONTENT_SELECT_AN_ARTICLE'))); ?>', null, null);" class="btn" type="button" value="<?php echo JText::_('JNONE'); ?>" />
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters">
			<select name="filter_published" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
				</option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getPublishedOptions(), 'value', 'text', $this->state->get(
'filter.published'));?>
			</select> <select name="filter_featured" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('COM_BT_PORTFOLIO_SELECT_FEATURED');?>
				</option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getFeaturedOptions(), 'value', 'text', $this->state->get(
'filter.featured'));?>
			</select>

			<select name="filter_catid" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getCategoryOptions(), 'value', 'text', $this->state->get(
'filter.catid'));?>

			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>
	<?php else: ?>
	<fieldset id="filter-bar">
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
			<select name="filter_published" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
				</option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getPublishedOptions(), 'value', 'text', $this->state->get(
'filter.published'));?>
			</select> <select name="filter_featured" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
				<?php echo JText::_('COM_BT_PORTFOLIO_SELECT_FEATURED');?>
				</option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getFeaturedOptions(), 'value', 'text', $this->state->get(
'filter.featured'));?>
			</select>

			<select name="filter_catid" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', Bt_portfolioHelper::getCategoryOptions(), 'value', 'text', $this->state->get(
'filter.catid'));?>

			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>

		</div>
	</fieldset>
	<?php endif; ?>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th><?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?></th>

				<th>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th><?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<th><?php echo JHtml::_('grid.sort', 'COM_BT_PORTFOLIO_VOTE_SUM_LABEL', 'vote_sum/vote_count', $listDirn, $listOrder); ?>
				</th>
				<th>
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
		$ordering	= ($listOrder == 'a.ordering');
		if ($item->language && JLanguageMultilang::isEnabled()) {
			$tag = strlen($item->language);
			if ($tag == 5) {
				$lang = substr($item->language, 0, 2);
			}
			elseif ($tag == 6) {
				$lang = substr($item->language, 0, 3);
			}
			else {
				$lang = "";
			}
		}
		elseif (!JLanguageMultilang::isEnabled()) {
			$lang = "";
		}
		$image_url = '';
		$url_portfolio = JURI::root().'index.php?option=com_bt_portfolio&view=portfolio&id='.$item->id.':'.$item->alias.'&catid_rel='.$item->category_id.':'.$item->category_alias;
		if($item->image){
			$image_url = JURI::root().'index.php?option=com_bt_portfolio&task=portfolio.viewimage&src='.base64_encode($item->id.'|'.$item->image.'|'.($this->params->get('thumbimgprocess',1)==-1?'original':'thumb'));
			$img_title =$item->title .'::'. '<img style="width:200px;" src="'.$image_url.'" alt = "'.JText::_("COM_BT_PORTFOLIO_PORTFOLIO_NO_IMAGE").'">';
		}
		else
		$img_title =$item->title .'::'.JText::_("COM_BT_PORTFOLIO_PORTFOLIO_NO_IMAGE");
		$desc = $this->escape(addslashes($item->description));
		$desc = str_replace(chr(13), " ", $desc);
		$desc = str_replace(chr(10), " ", $desc);

		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><span class="editlinktip hasTip" title="<?php echo htmlspecialchars($img_title);?>">
				<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>(<?php echo $item->id ?>,'<?php echo JRequest::getVar('el') ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape(addslashes($image_url)); ?>', '<?php echo $desc; ?>', '<?php echo $url_portfolio; ?>', '<?php echo $this->escape($lang); ?>');">
					<?php echo $this->escape($item->title); ?> </a>
					</span>
				</td>
				<td class="center"><?php echo str_replace(',',', ',$this->escape($item->category_title));?></td>
				<td class="order"><?php echo $item->ordering;?></td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center nowrap"><?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center"><?php echo (int) $item->hits; ?>
				</td>
				<td class="center"><?php echo $item->vote_count? round($item->vote_sum/$item->vote_count,1):0; ?>
				</td>
				<td class="center">
					<?php if ($item->language=='*'):?>
						<?php echo JText::alt('JALL','language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center"><?php echo (int) $item->id; ?>
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
</form>
