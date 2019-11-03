<?php

/**
 * @package         Convert Forms
 * @version         2.3.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;
use NRFramework\Cache;
use Joomla\Registry\Registry;

class Form {

    /**
     *  Returns a form object from database
     *
     *  @param   Integer  $id  The Form ID
     *
     *  @return  object       The Form object
     */
    public static function load($id)
    {
        if (!$id)
        {
            return;
        }

        $hash = 'convertforms_ ' . $id;
        if (Cache::has($hash))
        {
            return Cache::get($hash);
        }

        // Get a db connection.
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
         
        $query->select("*")
            ->from($db->quoteName('#__convertforms'))
            ->where($db->quoteName('id') . ' = '. (int) $id)
            ->where($db->quoteName('state') . ' = 1');
         
        $db->setQuery($query);

        if (!$form = $db->loadAssoc())
        {
            return;
        }

        $form['params'] = json_decode($form['params'], true);

        // Make 3rd party developer's life easier by setting field's name as the array key for faster code manipulation through PHP scripts.
        foreach ($form['params']['fields'] as $key => $field)
        {
            $name = isset($field['name']) ? $field['name'] : $key;
            unset($form['params']['fields'][$key]);
            $form['params']['fields'][$name] = $field;
        }

        $form['fields'] = $form['params']['fields'];
        unset($form['params']['fields']);

        return Cache::set($hash, $form);
    }

    /**
     * Run user-defined PHP scripts on certain form events.
     * 
     * The available events are:
     * 
     * formprepare:         Called on form data prepare.
     * formdisplay:         Called on form display.
     * formsubmission:      Called on form process.
     * afterformprocess:    Called after the form has been processed and the submission is saved into the database.
     *
     * @param   Integer $form_id        The form's ID
     * @param   String  $script_name    The script name to run
     * @param   Array   $payload        The data passed as argument to the PHP script. By reference.
     *
     * @return  void
     */
    public static function runPHPScript($form_id, $script_name, &$payload)
    {
        // Only on the front-end
        if (\JFactory::getApplication()->isAdmin())
        {
            return;
        }

        if (!$form = self::load($form_id))
        {
            return;
        }

        // Abort, if the script is not found
        if (!isset($form['params']['phpscripts'][$script_name]))
        {
            return;
        }

        // Abort, if the script is empty
        if (!$php_script = $form['params']['phpscripts'][$script_name])
        {
            return;
        }

        if (!isset($payload['form']))
        {
            $payload['form'] = $form;
        }

        $payload['script_name'] = $script_name;

        try
        {
            $executer = new \NRFramework\Executer($php_script, $payload);
            $executer->run();
        } catch (\Throwable $th)
        {
            $error = $th->getMessage() . ' - ' . $th->getFile() . ' on line ' . $th->getLine();
           
            // Log error
            Helper::triggerError($error, 'PHP Script', $form_id);

            // Re throw exception
            throw new \Exception($th->getMessage());
        }
    }
}