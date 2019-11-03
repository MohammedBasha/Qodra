<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author Windy
 */
defined('_JEXEC') or die ;

jimport('joomla.application.component.controller');

if (version_compare(JVERSION, '3.0', 'ge'))
{
    class BtPortfolioController extends JControllerLegacy
    {
    }

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
    class BtPortfolioController extends JController
    {
       
    }
}
else
{
    class BtPortfolioController extends JController
    {
    }
}

?>
