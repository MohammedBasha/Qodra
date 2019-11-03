<?php
/**
 * @version		$Id: pagination.php 17298 2010-05-27 14:58:59Z infograf768 $
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 *	Input variable $list is an array with offsets:
 *		$list[prefix]		: string
 *		$list[limit]		: int
 *		$list[limitstart]	: int
 *		$list[total]		: int
 *		$list[limitfield]	: string
 *		$list[pagescounter]	: string
 *		$list[pageslinks]	: string
 *
 * pagination_list_render
 *	Input variable $list is an array with offsets:
 *		$list[all]
 *			[data]		: string
 *			[active]	: boolean
 *		$list[start]
 *			[data]		: string
 *			[active]	: boolean
 *		$list[previous]
 *			[data]		: string
 *			[active]	: boolean
 *		$list[next]
 *			[data]		: string
 *			[active]	: boolean
 *		$list[end]
 *			[data]		: string
 *			[active]	: boolean
 *		$list[pages]
 *			[{PAGE}][data]		: string
 *			[{PAGE}][active]	: boolean
 *
 * pagination_item_active
 *	Input variable $item is an object with fields:
 *		$item->base	: integer
 *		$item->prefix	: string
 *		$item->link	: string
 *		$item->text	: string
 *
 * pagination_item_inactive
 *	Input variable $item is an object with fields:
 *		$item->base	: integer
 *		$item->prefix	: string
 *		$item->link	: string
 *		$item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	$html = "<div class=\"list-footer\">\n";

	$html .= "\n<div class=\"limit\">".JText::_('JGLOBAL_DISPLAY_NUM').$list['limitfield']."</div>";
	$html .= $list['pageslinks'];
	$html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

	$html .= "\n<input type=\"hidden\" name=\"" . $list['prefix'] . "limitstart\" value=\"".$list['limitstart']."\" />";
	$html .= "\n</div>";

	return $html;
}

function pagination_list_render($list)
{
	// Initialise variables.
	/*$html = '<ul>';
	$html .= '<li class="pagination-start">'.$list['start']['data'].'</li>';
	$html .= '<li class="pagination-prev">'.$list['previous']['data'].'</li>';
	foreach($list['pages'] as $page) {
		$html .= '<li>'.$page['data'].'</li>';
	}
	$html .= '<li class="pagination-next">'. $list['next']['data'].'</li>';
	$html .= '<li class="pagination-end">'. $list['end']['data'].'</li>';
	$html .= '</ul>';
	*/
		
	//$html = "<span class=\"pagination\">";
	$html = '<ul class="my-pagination">';
		//$html .= '<li class="pagination-start general">' . $list['start']['data'] . '</li>';
		$html .= '<li class="pagination-prev general">' . $list['previous']['data'] . '</li>';
		foreach ($list['pages'] as $page)
		{
			$html .= '<li class="page-number">' . $page['data'] . '</li>';
		}
		$html .= '<li class="pagination-next general">' . $list['next']['data'] . '</li>';
		//$html .= '<li class="pagination-end general">' . $list['end']['data'] . '</li>';
		$html .= '</ul>';
	return $html;
}

function pagination_item_active(&$item) {
	return "<a title=\"" . $item->text . "\" href=\"" . $item->link . "\" class=\"pagenav\"><span>" . $item->text . "</span></a>";
}

function pagination_item_inactive(&$item) {
	return "<span class=\"pagenav\"><span>" . $item->text . "</span></span>";
}
?>