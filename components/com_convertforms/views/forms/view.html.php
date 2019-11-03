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
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Forms View Class
 */
class ConvertFormsViewForms extends JViewLegacy
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    public function display($tpl = null) 
    {
		// Access check.
        ConvertForms\Helper::authorise('convertforms.forms.manage', true);

        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->config        = JComponentHelper::getParams('com_convertforms');

        ConvertForms\Helper::addSubmenu('forms');
        $this->sidebar = JHtmlSidebar::render();
        $this->moduleID = NRFramework\Extension::getID('mod_convertforms', 'module');

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {

        $canDo = ConvertForms\Helper::getActions();
        $state = $this->get('State');
        $viewLayout = JFactory::getApplication()->input->get('layout', 'default');

        if ($viewLayout == 'import')
        {
            JFactory::getDocument()->setTitle(JText::_('COM_CONVERTFORMS') . ': ' . JText::_('NR_IMPORT_ITEMS'));
            JToolbarHelper::title(JText::_('COM_CONVERTFORMS') . ': ' . JText::_('NR_IMPORT_ITEMS'));
            JToolbarHelper::back();
        }
        else
        {
            JToolBarHelper::title(JText::_('COM_CONVERTFORMS') . ": " . JText::_('COM_CONVERTFORMS_FORMS'));

            if ($canDo->get('core.create'))
            {
                JToolbarHelper::addNew('form.add');
            }
            
            if ($canDo->get('core.edit'))
            {
                JToolbarHelper::editList('form.edit');
            }

            if ($canDo->get('core.create'))
            {
                JToolbarHelper::custom('forms.duplicate', 'copy', 'copy', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit.state') && $state->get('filter.state') != 2)
            {
                JToolbarHelper::publish('forms.publish', 'JTOOLBAR_PUBLISH', true);
                JToolbarHelper::unpublish('forms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            if ($canDo->get('core.delete') && $state->get('filter.state') == -2)
            {
                JToolbarHelper::deleteList('', 'forms.delete', 'JTOOLBAR_EMPTY_TRASH');
            }
            else if ($canDo->get('core.edit.state'))
            {
                JToolbarHelper::trash('forms.trash');
            }

            if ($canDo->get('core.create'))
            {
                JToolbarHelper::custom('forms.export', 'box-add', 'box-add', 'NR_EXPORT');
                JToolbarHelper::custom('forms.import', 'box-remove', 'box-remove', 'NR_IMPORT', false);
            }

            if ($canDo->get('core.admin'))
            {
                JToolbarHelper::preferences('com_convertforms');
            }
        }

        JToolbarHelper::help("Help", false, "http://www.tassos.gr/joomla-extensions/convert-forms/docs");
    }
}