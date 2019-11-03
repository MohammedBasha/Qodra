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
JHtml::_('behavior.formvalidation');
JHtml::_('jquery.ui', array('core', 'sortable'));

JFactory::getDocument()->addScript(JURI::root(true) . "/media/editors/tinymce/tinymce.min.js");

NRFramework\Functions::addMedia("convertforms.editor.sys.css","com_convertforms");
NRFramework\Functions::addMedia("cookie.js", "com_convertforms");

$fonts = new NRFonts();
JFactory::getDocument()->addScriptDeclaration('var ConvertFormsGoogleFonts = '. json_encode($fonts->getFontGroup('google')));

$tabState      = JFactory::getApplication()->input->cookie->get("ConvertFormsState" . $this->item->id, 'fields');
$tabStateParts = explode("-", $tabState);
$tabActive     = $tabStateParts[0];

// Smart Tags Box
echo NRFramework\HTML::smartTagsBox();

JFactory::getDocument()->addStyleDeclaration('
    .has-smarttags.is_textarea .st_trigger {
        bottom:0;
        top:auto;
    }
');


NRFramework\HTML::renderProOnlyModal();


?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'form.cancel' || document.formvalidator.isValid(document.id('adminForm')))
        {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    }
</script>

<div class="nrEditor" data-root="<?php echo JURI::root(); ?>">
    <div class="nrEditorOptions">
        <form action="<?php echo JRoute::_('index.php?option=com_convertforms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-vertical" pk="<?php echo (int) $this->item->id ?>">
            <div class="tabs-left">
                <?php 
                    echo JHtml::_('bootstrap.startTabSet', 'sections', array('active' => $tabActive));

                    foreach ($this->tabs as $key => $tab)
                    {
                        $tabName  = $key;
                        $tabLabel = JText::_($tab["label"]);

                        echo JHtml::_('bootstrap.addTab', 'sections', $tabName, '<span data-label="' . $tabLabel . '" class="' . $tab["icon"] . '"></span>');

                        $panelActive = $tabActive == $key ? $tabState : "";

                        echo JHtml::_('bootstrap.startAccordion', $tabName, array('active' => $panelActive));
                        echo "<h2>" . $tabLabel . "</h2>";

                        $single = count($tab["fields"]) == 1 ? true : false;

                        foreach ($tab["fields"] as $key => $field)
                        {
                            if ($single)
                            {
                                echo '<div class="accordion-inner"> ' . $this->form->renderFieldset($field["name"]) . '</div>';
                                continue;
                            }

                            echo JHtml::_('bootstrap.addSlide', $tabName, JText::_($field["label"]), $tabName.'-' . $field["name"], $field["name"]);

                            $fieldset = $this->form->renderFieldset($field["name"]);
                            JFactory::getApplication()->triggerEvent('onConvertFormsBackendFormPrepareFieldset', [$field["name"], &$fieldset]);
                            echo $fieldset;

                            echo JHtml::_('bootstrap.endSlide');
                        }

                        echo JHtml::_('bootstrap.endAccordion');
                        echo JHtml::_('bootstrap.endTab');
                    }

                    echo JHtml::_('bootstrap.endTabSet');
                ?>
                <input type="hidden" name="task" value="form.edit" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
    </div>
    <div class="nrEditorPreview hidden-phone">
        <div class="nrEditorTools">
            <div class="l nrEditorTabs">
                <ul class="nrNav">
                    <li class="nrCheckbox">
                        <input value="1" type="checkbox" id="preview-successmsg">
                        <label for="preview-successmsg"><?php echo JText::_("COM_CONVERTFORMS_PREVIEW_SUCCESS") ?></label>
                    </li>
                    <li class="nrCheckbox">
                        <input value="1" type="checkbox" id="preview-loader">
                        <label for="preview-loader"><?php echo JText::_("COM_CONVERTFORMS_PREVIEW_WORKING_INDICATOR") ?></label>
                    </li>
                </ul>
            </div>
            <!--<div class="m nrEditorDevices text-center">
                <a href="#"
                    data-placement="bottom" 
                    data-title="<?php echo JText::_("COM_CONVERTFORMS_DEVICE_DESKTOP") ?>"
                    data-content="<?php echo JText::_("COM_CONVERTFORMS_PREVIEW_SIZE_DESKTOP") ?>"
                    data-class="cf-large-up"
                    class="hasPopover cf-icon-desktop active">
                </a>
                <a href="#"
                    data-width="768"
                    data-placement="bottom"
                    data-title="<?php echo JText::_("COM_CONVERTFORMS_DEVICE_TABLET") ?>"
                    data-content="<?php echo JText::_("COM_CONVERTFORMS_PREVIEW_SIZE_TABLET") ?>"
                    data-class="cf-medium-only"
                    class="hasPopover cf-icon-tablet">
                </a>
                <a href="#"
                    data-width="320"
                    data-placement="bottom"
                    data-title="<?php echo JText::_("COM_CONVERTFORMS_DEVICE_MOBILE") ?>"
                    data-content="<?php echo JText::_("COM_CONVERTFORMS_PREVIEW_SIZE_MOBILE") ?>"
                    data-class="cf-small-only"
                    class="hasPopover cf-icon-mobile">
                </a>
            </div>-->
            <div class="r"></div>
        </div>
        <div class="nrEditorPreviewContainer"></div>
        <div class="loader"></div>
    </div>
</div>


