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
JFormHelper::loadFieldClass('list');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/' . 'models');

class JFormFieldConvertForms extends JFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return    array   An array of JHtml options.
     */
    protected function getOptions()
    {
        $model = JModelLegacy::getInstance('Forms', 'ConvertFormsModel', array('ignore_request' => true));
        $model->setState('filter.state', 1);

        $convertforms = $model->getItems();
        $options = array();

        foreach ($convertforms as $key => $convertform)
        {
            $options[] = JHTML::_('select.option', $convertform->id, $convertform->name);
        }   

        return array_merge(parent::getOptions(), $options);
    }
}