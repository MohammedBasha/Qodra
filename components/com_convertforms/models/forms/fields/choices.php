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

require_once JPATH_PLUGINS . '/system/nrframework/helpers/field.php';

class JFormFieldNR_Choices extends NRFormField
{
    /**
     *  Default Choices
     *
     *  @var  array
     */
    private $defaultChoices = array(
        1 => array("label" => "First Choice"),
        2 => array("label" => "Second Choice"),
        3 => array("label" => "Third Choice")
    );

    /**
     *  Get Input HTML
     *
     *  @return  string
     */
    protected function getInput()
    {
        $this->addMedia();

        $choiceType = $this->get("choicetype", "dropdown");

        // Settings
        $showValuesFieldName = $this->name . '[showvalues]';
        $showValuesFieldChecked = isset($this->value["showvalues"]) ? "checked" : "";

        // Choices
        $choices = $this->getChoices();
        $nextid  = max(array_keys($choices)) + 1;

        $html[] = '
            <div id="nr_choices_' . $this->id . '" class="nr_choices" data-min="1" data-fieldname="' . $this->name . '" data-nextid="' . $nextid . '">
        ';

        foreach ($choices as $key => $value)
        {
        	// Skip empty choices
        	if (!isset($value["label"]) || empty($value["label"]))
        	{
        		continue;
        	}

        	$choiceName  = $this->name . '[choices][' . $key . ']';
        	$checked     = (isset($value["default"]) && (bool) $value["default"] === true) ? "checked" : "";
            $choiceValue = isset($value["value"]) ? $value["value"] : "";
            $choiceLabel = $value["label"];

			$html[] = '
				<div class="nr-choice-item" data-id=' . $key . '>
                    <div>
	    			    <input tabindex="-1" 
                            class="nr-choice-default norender" 
                            type="'.($choiceType == "dropdown" ? "radio" : "checkbox") .'" 
                            name="' . $choiceName . '[default]" 
                            value="1" '.$checked .'>
                    </div>
                    <div class="nr-choice-sort">
	    			    <span class="icon-menu-3"></span>
                    </div>
	    			<div class="nr-choice-input">
                        <input placeholder="Enter Label" class="nr-choice-label" name="' . $choiceName . '[label]" value="'.$choiceLabel.'" type="text"/>
                        <input '.(!$showValuesFieldChecked ? "style=\"display:none;\"" : "").' placeholder="Enter Value" class="nr-choice-value" name="' . $choiceName . '[value]" value="'.$choiceValue.'" type="text"/>
                    </div>
                    <div class="nr-choice-control">
					    <a tabindex="-1" href="#" class="nr-choice-add"><span class="icon-plus"></span></a>
					    <a tabindex="-1" href="#" class="nr-choice-remove"><span class="icon-minus"></span></a>
                    </div>
	    		</div>
	    	';
        }

        // Add settings fields
        $html[] = '
            </div>
            <div class="nr-choice-settings">
                <input value"1" class="showvalues" type="checkbox" id="' . $showValuesFieldName . '" name="' . $showValuesFieldName . '" '.$showValuesFieldChecked.'>
                <label for="' . $showValuesFieldName . '">Show values</label>
            </div>
        ';

        return implode(" ", $html);
    }

    /**
     *  Get Field Choices
     *
     *  @return  array  
     */
    private function getChoices()
    {
        // Setup some default choices if we don't have saved data
        if (!isset($this->value) || !isset($this->value["choices"]) || count($this->value["choices"]) == 0)
        {
            return $this->defaultChoices;
        }

        return $this->value["choices"];
    }

    /**
     *  Adds CSS and JavaScript files to DOM
     */
    private function addMedia()
    {
        JHtml::_('jquery.framework');
        JHtml::_('jquery.ui', array('core', 'sortable'));

        $path = JURI::base(true) . '/components/com_convertforms/models/forms/fields/';

        JFactory::getDocument()->addScript($path . 'choices.js');
        JFactory::getDocument()->addStyleSheet($path . 'choices.css');
    }
}