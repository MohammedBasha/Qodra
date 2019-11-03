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
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * PortfolioCategory Form Field class for the bt_portfolio component
 */
class JFormFieldExtraLink extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'ExtraLink';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	
	protected function getInput() 
	{	
		$this->value =@unserialize($this->value);
		if(!is_array($this->value)){
			$this->value = array();
			$this->value[]='';
			$this->value[]='';
			$this->value[]='';
		}
		$html = '<input size="15" class="textbox" type="text" title="Ancho text" name="'.$this->name.'[]'.'" value="'.$this->value[0].'"> ';
		$html .= '<input class="textbox" type="text" title="Link" name="'.$this->name.'[]'.'" value="'.$this->value[1].'"> ';	
		$options = array();
		$options[] = JHtml::_('select.option', '_self', JText::_('COM_BT_PORTFOLIO_PORTFOLIO_SAME_WINDOW'));
		$options[] = JHtml::_('select.option', '_blank', JText::_('COM_BT_PORTFOLIO_PORTFOLIO_NEW_WINDOW'));
		$html .= JHtml::_('select.genericlist',$options,$this->name.'[]','','value','text',$this->value[2]);
		return $html;
	}
	
}
