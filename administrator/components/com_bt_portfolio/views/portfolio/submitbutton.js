Joomla.submitbutton = function(task)
{
	jQuery('#image-configs input').each(function(){
			if(this.value==Joomla.JText._('JGLOBAL_USE_GLOBAL','Use global')){
				this.value = "";
			}
	})
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = $$('form.form-validate');
			for (var i=0;i<forms.length;i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}
 
		if (isValid)
		{
			Joomla.submitform(task, document.getElementById('portfolio-form'));
			return true;
		}
		else
		{
			alert(Joomla.JText._('COM_BT_PORTFOLIO_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}
