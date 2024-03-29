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

/**
 *  Conversions Class
 */
class ConvertFormsModelConversions extends JModelList
{
    /**
     *  Default table columns
     *
     *  @var  array
     */
    public $default_columns = array(
        'id',
        'created', 
        'user_id',
        'user_username',
        'visitor_id',
        'form_name',
        'campaign_name',
        'param_leadnotes'
    );

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     *
     * @see        JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'created', 'a.created',
                'search',
                'campaign_id', 'a.campaign_id',
                'form_id', 'a.form_id',
                'created_from', 'created_to',
                'columns', 'a.columns',
                'period', 'a.period'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        // Get the previously set form ID before populating the State.
		$session = JFactory::getSession();
		$registry = $session->get('registry');
        $previous_form_id = $registry->get($this->context . '.filter.form_id');

        // List state information.
        parent::populateState($ordering, $direction);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $state);
        
        $formID = $this->getUserStateFromRequest($this->context . '.filter.form_id', 'filter_form_id', $this->getLastFormID());
        $this->setState('filter.form_id', $formID);
        
        $campaignID = $this->getUserStateFromRequest($this->context . '.filter.campaign_id', 'filter_campaign_id');
        $this->setState('filter.campaign_id', $campaignID);

        $period = $this->getUserStateFromRequest($this->context . '.filter.period', 'filter_period');
        $this->setState('filter.period', $period);
        
        $columns = $this->getUserStateFromRequest($this->context . '.filter.columns', 'filter_columns');

        $sameForm = ($previous_form_id == $formID);

        // Get form fields from the database when the user has switched to another form using
        // the search filters or when the filters frorm has been reset.
        if (!$sameForm || is_null($columns) || empty($columns))
        {
            $columns = $this->getColumns($formID);
            // Pre-select the first 8 only
            $columns = array_slice($columns, 0, 8);
        }

        $this->setState('filter.columns', $columns);
    }

    /**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.6.1
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
        if (!isset($data->filter))
        {
            $data->filter = [];
        } else 
        {
            if (is_object($data->filter))
            {
                $data->filter = (array) $data->filter;
            }
        }

        $data->filter['form_id'] = $this->getState('filter.form_id');
        $data->filter['columns'] = $this->getState('filter.columns');

		parent::preprocessForm($form, $data, $group);
	}

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields from the item table
        $query
            ->select('a.*')
            ->from('#__convertforms_conversions a');
        
        // Filter State
        $filter = $this->getState('filter.state');

        if ($filter != 'skip')
        {
            if (is_numeric($filter))
            {
                $query->where('a.state = ' . ( int ) $filter);
            }
            else if ($filter == '')
            {
                $query->where('( a.state IN (0,1,2))');
            }
        }

        // Join Campaigns Table
        if ($this->getState('filter.join_campaigns') != 'skip')
        {
            $query->select("b.name as campaign_name");
            $query->join('LEFT', $db->quoteName('#__convertforms_campaigns', 'b') . ' ON 
                (' . $db->quoteName('a.campaign_id') . ' = ' . $db->quoteName('b.id') . ')');
        }

        // Join Forms Table
        if ($this->getState('filter.join_forms') != 'skip')
        {
            $query->select("c.name as form_name");
            $query->join('LEFT', $db->quoteName('#__convertforms', 'c') . ' ON 
                (' . $db->quoteName('a.form_id') . ' = ' . $db->quoteName('c.id') . ')');
        }

        // Filter the list over the search string if set.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . ( int ) substr($search, 3));
            } else 
            {
                $query->where('a.params LIKE "%' . $search . '%" ');
            }
        }

        // Filter by ID
        if ($id = $this->getState('filter.id'))
        {
            $query->where('a.id IN (' . implode(', ', (array) $id) . ')');
        }

        // Filter by Campaign ID
        if ($campaign_id = $this->getState('filter.campaign_id'))
        {
            $query->where('a.campaign_id IN (' . implode(', ', (array) $campaign_id) . ')');
        }

        // Filter Form
        if ($form_id = $this->getState('filter.form_id'))
        {
            $query->where('a.form_id = ' . $db->q($form_id));
        }

        // Period
        if ($period = $this->getState('filter.period'))
        {
            switch ($period)
            {
                case 'today':
	    		    $query->where('DATE(a.created) = CURRENT_DATE');
	    		    break;
	    	    case 'yesterday':
	    		    $query->where('DATE(a.created) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)');
                    break;
                case 'this_week':
                    $query->where('YEARWEEK(a.created, 1) = YEARWEEK(CURDATE(), 1)');
                    break;
                case 'this_month':
                    $query->where('MONTH(a.created) = MONTH(CURRENT_DATE)');
                    $query->where('YEAR(a.created) = YEAR(CURRENT_DATE)');
                    break;
                case 'this_year':
                    $query->where('YEAR(a.created) = YEAR(CURRENT_DATE)');
                    break;
                case 'last_week':
                    $query->where('YEARWEEK(a.created, 1) = YEARWEEK(NOW() - INTERVAL 1 WEEK, 1)');
                    break;
                case 'last_month':
                    $query->where('MONTH(a.created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
                    $query->where('YEAR(a.created) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)');
                    break;
                case 'last_year':
                    $query->where('YEAR(a.created) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)');
                    break;
                default:
                    // Filter created date (from)
                    if ($created_from = $this->getState('filter.created_from'))
                    {
                        $query->where('DATE(a.created) >= ' . $db->q($created_from));
                    }
            
                    // Filter created date (to)
                    if ($created_to = $this->getState('filter.created_to'))
                    {
                        $query->where('DATE(a.created) <= ' . $db->q($created_to));
                    }
                    break;
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
    
    /**
     *  Exports leads in CSV file
     *  Leads can be exported by passing either row IDs or Campaign IDs
     *
     *  @param   array    $ids             Array of row IDs
     *  @param   array    $campaign_ids    Array of Campaign IDs
     *
     *  @return  void
     */
    public function export($ids = array(), $campaign_ids = array())
    {
        $filename = '';
        
        if (is_array($ids) && count($ids))
        {
            $this->setState('filter.id', $ids);
        }

        if (is_array($campaign_ids) && count($campaign_ids))
        {
            $filename = '_Campaign' . $campaign_ids[0];
            $this->setState('filter.state', 1);
            $this->setState('filter.campaign_id', $campaign_ids);
        } else 
        {
            $this->setState('filter.state', 'skip');
        }

        $this->setState('filter.join_campaigns', 'skip');
        $this->setState('filter.join_forms', 'skip');

        $rows = $this->getItems();

        $this->prepareRowsForExporting($rows);

        $columns = array_keys($rows[0]);

        // Create the downloadable file
        $this->downloadFile('ConvertForms_Submissions_' . $filename . '_' . date('Y-m-d') . '.csv');
        
        $excel_security = (bool) JComponentHelper::getParams('com_convertforms')->get('excel_security', true);
        echo $this->createCSV($rows, $columns, $excel_security);

        die();
    }

    /**
     *  Prepares rows with their custom fields for exporting
     *
     *  @param   object  &$rows  The rows object
     *
     *  @return  void
     */
    private function prepareRowsForExporting(&$rows)
    {
        $columns = array(
            'id',
            'created',
            'name',
            'email'
        );

        $rows_ = [];

        // Find custom fields per row
        foreach ($rows as $key => $row)
        {
            $rows_[$key]['id'] = $row->id;
            $rows_[$key]['created'] = (string) $row->created;

            foreach ($row->params as $pkey => $param)
            {
                $pkey = trim(strtolower($pkey));

                if (strpos($pkey, 'sync_') !== false)
                {
                    continue;
                }

                $value = is_array($param) ? implode(', ', $param) : $param;

                $rows_[$key][$pkey] = $value;

                if (!in_array($pkey, $columns))
                {
                    array_push($columns, $pkey);
                }
            }
        }

        $rowsNew = array();

        // Fix rows based on columns
        foreach ($columns as $ckey => $column)
        {
            foreach ($rows_ as $rkey => $row)
            {
                $value = isset($row[$column]) ? $row[$column] : '';
                $rowsNew[$rkey][$column] = $value;
            }
        }

        $rows = $rowsNew;
    }

    /**
     *  Create the CSV file
     *
     *  CSV Injection info: https://vel.joomla.org/articles/2140-introducing-csv-injection
     *
     *  @param   array    $rows            CSV Rows to print
     *  @param   array    $columns         CSV Columns to print
     *  @param   boolean  $excel_security  If enabled, certain row values will be prefixed by a tab to avoid any CSV injection
     *
     *  @return  mixed
     */
    public function createCSV($rows, $columns, $excel_security = true)
    {
        ob_start();
        $output = fopen('php://output', 'w');

        // Support UTF-8 on Microsoft Excel
        fputs( $output, "\xEF\xBB\xBF" );

        fputcsv($output, $columns);

        foreach ($rows as $key => $row)
        {
            // Prevent CSV Injection
            if ($excel_security)
            {
                foreach ($row as $rowKey => &$rowValue)
                {
                    $firstChar = substr($rowValue, 0, 1);

                    // Prefixe values starting with a =, +, - or @ by a tab character
                    if (in_array($firstChar, array('=', '+', '-', '@')))
                    {
                        $rowValue = '    ' . $rowValue;
                    }
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        return ob_get_clean();
    }

    /**
     *  Sets propert headers to document to force download of the file
     *
     *  @param   string  $filename  Filename
     *
     *  @return  void
     */
    function downloadFile($filename)
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    /**
     *  [getItems description]
     *
     *  @return  object
     */
    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item)
        {
            $items[$key]->params = json_decode($item->params);

            // Add timezone offset to created date
            $tz = new \DateTimeZone(JFactory::getApplication()->getCfg('offset', 'GMT'));
            $item->created = JFactory::getDate($item->created)->setTimeZone($tz);
        }
        
        return $items;
    }

    private function getLastFormID()
    {
        $model = JModelLegacy::getInstance('Forms', 'ConvertFormsModel', ['ignore_request' => true]);
        $model->setState('filter.state', '1');
        $model->setState('list.limit', 1);
        $model->setState('list.direction', 'asc');

        $forms = $model->getItems();
        return $forms[0]->id;
    }

    public function getColumns($form_id)
    {
        // Form Fields
        $form_fields = array_map(function($value) {
            return 'param_' . $value;
        }, ConvertForms\FieldsHelper::getFieldsByForm($form_id));

        // Global Columns
        $default_columns = $this->default_columns;

        // Set ID and Date Submitted as the first 2 columns
        $columns = array_merge(array_slice($this->default_columns, 0, 2), $form_fields, array_slice($this->default_columns, 2, count($this->default_columns)));

        return $columns;
    }
}
