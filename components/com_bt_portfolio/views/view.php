<?php

defined('_JEXEC') or die ;

jimport('joomla.application.component.view');

if (version_compare(JVERSION, '3.0', 'ge'))
{
    class BTView extends JViewLegacy
    {
    }

}
else
{
    class BTView extends JView
    {
    }

}
?>
