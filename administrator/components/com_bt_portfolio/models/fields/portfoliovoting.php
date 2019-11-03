<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');


class JFormFieldPortfolioVoting extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'PortfolioVoting';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		
		$id = preg_replace("/[^\w]/","",$this->name);
		
		$params = JComponentHelper::getParams("com_bt_portfolio" );
		
		return Bt_portfolioHelper::getVoting($id,$this->value,true,JURI::root().'components/com_bt_portfolio/themes/'.$params->get("theme",'default'));
	}
}
