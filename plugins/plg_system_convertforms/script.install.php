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

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemConvertFormsInstallerScript extends PlgSystemConvertFormsInstallerScriptHelper
{
	public $name = 'CONVERTFORMS';
	public $alias = 'convertforms';
	public $extension_type = 'plugin';
	public $show_message = false;

	public function onAfterInstall()
	{
		// Render Plugin should be always ordered after Jooml Framework plugins 
		// such as T3 Framework, YT Framework and jQueryEasy
    	$this->pluginOrderAfter(array(
			"t3",
        	"jat3",
        	"jqueryeasy",
        	"yt",
        	"nrframework"
		));
	}

}
