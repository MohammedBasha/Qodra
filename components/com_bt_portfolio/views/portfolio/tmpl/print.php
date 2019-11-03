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

// Get default image
$default_image = '';
foreach ($this->images as $image){
	if($image->default){
		$default_image = $image->filename;
		break;
	}
}
$default_image = Bt_portfolioHelper::getPathImage($this->item->id,'large',$default_image,$this->category->id);

// get review summary:
$review_summary = '';
if ($this->params->get('allow_voting')){
	//$review_summary .= '<span style="float: left; margin-right: 10px;">'.JText::_('COM_BT_PORTFOLIO_VOTE_IT').'</span>';
	$review_summary .= Bt_portfolioHelper::getRatingPanel($this->item->id, $this->item->vote_sum, $this->item->vote_count,0);
}
if ($this->params->get('comment_system') == 'none' && $this->params->get('allow_comment')){

	$review_summary .= '<span class="review_count">'.$this->item->review_count . ' ' ;
	$review_summary .= ' ' ;
	$review_summary .= $this->item->review_count > 1 ? JText::_('COM_BT_PORTFOLIO_REVIEWS') : JText::_('COM_BT_PORTFOLIO_REVIEW');
	$review_summary .= '</span>';
}
$review_summary .= '<div class="clr"></div>';

// Get extra fields
$extra_fields = '';
//$extra_fields = '<div class="btp-detail-extrafields">';
foreach ($this->item->extra_fields as $field){
	if(count($field) ==0) continue;
	$extra_fields .= '<div class="extrafield-row '.preg_replace("/[^a-zA-Z0-9]/", "", strtolower($field->name)).'" >';
	$extra_fields .= '<span style="font-weight:bold" class="extrafield-title">'.$field->name.':</span>';
	
	switch($field->type){
		case'link':							
			if($field->value[0]){
				$itrolink =$field->value[0];						
			}else{
				$itrolink =$field->value[1];
			}
			$extra_fields .= ' <span class="extrafield-value"><a href="'.$field->value[1].'"target="'.$field->value[2].'">'.$itrolink.'</a></span>';
			break;
		case'image':
			$extra_fields .= ' <span class="extrafield-value"><img src="'.JURI::root().$field->value.'"></span>';
			break;
		case'measurement':	
			$extra_fields .= ' <span class="extrafield-value">'.$field->value.' '.$field->default_value[1].'</span>';
			break;
		case'dropdown':
			$extra_fields .= ' <span class="extrafield-value">'.$field->value[0].'</span>';
			break;
		default:
			$extra_fields .= ' <span class="extrafield-value">'.$field->value.'</span>';
			break;
	}
	$extra_fields .= '</div>';
}
//$extra_fields .= '</div>';

// get comment
$comment = array('','','','','','');
$i = 0;
foreach ($this->comment['data'] as $item){
	$i++;
	$comment[$i] = '<div class="btp-comment-item">';
	$comment[$i] .= '<div class="'.($item->admin? 'comment-admin':'').'">';
	$comment[$i] .= '<div class="btp-comment-item-head">';
	if($item->image){
		$comment[$i] .= '<div class="comment-avatar">';
		$comment[$i] .= '<a href="'.$item->link.'"><img src="'.$item->image.'" /></a>';
		$comment[$i] .= '</div>';
	}
	$comment[$i] .= '<div class="in-content-comment">';
	$comment[$i] .= '<div class="comment-info">';
	$comment[$i] .= '<span class="comment-author">'.$item->name.'</span>';
	$comment[$i] .= '<span class="comment-created">'.JText::sprintf('COM_BT_PORTFOLIO_CREATED_ON', JHtml::_('date', $item->created, JText::_('F d, Y')), JHTML::_('date',$item->created, JText::_('g:i a'))).'</span>';
	$comment[$i] .= '</div>';
	if ($this->params->get('show_title')) {
		$comment[$i] .= '<div class="comment-title"> '.$item->title.'</div>';
	}
	$comment[$i] .=  '<div class="btp-comment-item-content">'.htmlspecialchars($item->content).'</div>';
	$comment[$i] .=  '</div>';
	$comment[$i] .=  '</div>';
	$comment[$i] .=  '</div>';
	$comment[$i] .=  '</div>';
	$comment[$i] .=  '<div class="clr"></div>';
}

$print_button = Bt_portfolioHelper::getPrintButton(0,$this->item->id);

// Process layout
$html = $print_button . $this->params->get('printing_layout');
$array_key =array(
			'{PORTFOLIO_TITLE}',
			'{REVIEW_SUMMARY}',
			'{PORTFOLIO_IMAGE}',
			'{EXTRA_FIELDS}',
			'{DESCRIPTION}',
			'{COMMENT_1}',
			'{COMMENT_2}',
			'{COMMENT_3}',
			'{COMMENT_4}',
			'{COMMENT_5}'
			);
$array_text = array();
$array_text[] = $this->item->title;
$array_text[] = $review_summary;
$array_text[] = '<img src="'.$default_image.'" />';
$array_text[] = $extra_fields;
$array_text[] = $this->item->full_description;
$array_text[] = $comment[1];
$array_text[] = $comment[2];
$array_text[] = $comment[3];
$array_text[] = $comment[4];
$array_text[] = $comment[5];
$html = str_replace($array_key,$array_text,$html );
$html .="<style>body{background:none!important;}</style>";
echo $html;
?>
