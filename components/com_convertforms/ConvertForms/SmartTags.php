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

namespace ConvertForms;

use ConvertForms\Helper;
use ConvertForms\Form;

defined('_JEXEC') or die('Restricted access');

class SmartTags
{
	public static $smartTags = array(
        'submission.id'          => '',
        'submission.date'        => '',
        'submission.campaign_id' => '',
        'submission.form_id'     => '',
        'submission.visitor_id'  => '',
        'all_fields'       => ''
    );

	public static function get()
    {
        return self::$smartTags;
    }

    public static function prepare($lead = null, $form_id = null)
    {
        $tags = array();

        if (!is_object($lead) && is_int($lead))
        {
            // Add component include paths for models and tables
            $path = JPATH_ADMINISTRATOR . '/components/com_convertforms/';
            \JModelLegacy::addIncludePath($path . 'models');
            \JTable::addIncludePath($path . 'tables');

            $model = \JModelLegacy::getInstance('Conversion', 'ConvertFormsModel', array('ignore_request' => true));
            $lead = $model->getItem($lead);
        }

        if (is_object($lead))
        {
            $form_id = is_null($form_id) ? $lead->form_id : $form_id;

            $tags = [
                'submission' => [
                    'id'          => $lead->id,
                    'date'        => $lead->created,
                    'campaign_id' => $lead->campaign_id,
                    'form_id'     => $lead->form_id,
                    'visitor_id'  => $lead->visitor_id
                ]
            ];

            // Support old Lead Smart Tags as well
            $tags['lead'] = $tags['submission'];

            $all_fields = '';

            if (is_array($lead->params))
            {
                foreach ($lead->params as $key => $value)
                {
                    // Skip integration wide fields
                    if (strpos($key, 'sync_') !== false)
                    {
                        continue;
                    }

                    if (is_array($value))
                    {
                        $value = implode(', ', $value);
                    } else 
                    {
                        // Escape HTML
                        $value = Helper::escape($value);
                        $value = nl2br($value);
                    }

                    $tags['field'][$key] = $value;
                    $all_fields .= '<div><strong>' . self::getFormFieldLabelOrKey($form_id, $key) . '</strong>: ' . $value . '</div>';
                }

                $tags['']['all_fields'] = $all_fields;
            }
        }

        $tags['submissions']['count'] = $form_id ? Helper::getFormLeadsCount($form_id) : '0';
        
        // Support old Lead Smart Tags as well
        $tags['leads']['count'] = $tags['submissions']['count'];

        return $tags;
    }

    public static function replace($string, $lead = null, $form_id = null)
    {
        $smartTags = new \NRFramework\SmartTags();

        $localTagsGroups = self::prepare($lead, $form_id);

        foreach ($localTagsGroups as $key => $localTagsGroup)
        {
            $prefix = empty($key) ? null : $key . '.';
            $smartTags->add($localTagsGroup, $prefix);
        }

        $result = $smartTags->replace($string);

        // Temporary fix for duplicate site URL.
        $result = self::fixDuplicateSiteURL($result);
        
        return $result;
    }

    /**
     *  In TinyMCE we are forcing absolute URLs (relative_urls=false). This means that the editors prefixes
     *  all 'src' and 'href' properties with the site's base URL. Since the File Upload field stores the full absolute URL in the database
     *  we are end up with invalid URLs, like in the example below:
     *  
     *  http://www.site.com/http://www.site.com/images/uploaded_file.png
     *   
     *  The line below is a temporary and dirty solution to our problem.
     *  We may need to consider storing just the path of the uploaded file to the database instead. Eg: images/path/file.png
     *
     * @param  string $string
     *
     * @return string
     */
    private static function fixDuplicateSiteURL($subject)
    {
        $base_url = \JURI::root();

        if (is_string($subject))
        {
            return str_replace($base_url . $base_url, $base_url, $subject);
        }

        if (is_array($subject))
        {
            foreach ($subject as $key => &$item)
            {
                if (!is_string($item))
                {
                    continue;
                }
    
                $item = str_replace($base_url . $base_url, $base_url, $item);
            }
        }

        return $subject;
    }

    private static function getFormFieldLabelOrKey($form_id, $field_key)
    {
        $form = Form::load($form_id);

        if (is_array($form['fields']))
        {
            foreach ($form['fields'] as $key => $field)
            {
                if ($field_key != $key)
                {
                    continue;
                }
    
                // Found
                if (isset($field['label']) && !empty($field['label']))
                {
                    return $field['label'];
                }
            }
        }

        return ucfirst($field_key);
    }
}

?>