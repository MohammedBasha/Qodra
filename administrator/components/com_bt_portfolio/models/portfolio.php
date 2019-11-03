<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		3.0.3
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Portfolio Model
 */
class Bt_portfolioModelPortfolio extends JModelAdmin
{	
	protected $images_path;
	protected $params;
	/**
	 * Class constructor.
	 *
	 * @param	array	$config	A named array of configuration variables.
	 *
	 * @return	JControllerForm
	 * @since	1.6
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->params = JComponentHelper::getParams($this->option);
		$this->images_path = JPATH_SITE .'/'. $this->params->get('images_path','images/bt_portfolio').'/';		
		$this->thumbimgprocess = $this->params->get('thumbimgprocess', 1);
		$this->largeimgprocess = $this->params->get('largeimgprocess', -1);		
		$this->crop_width =$this->params->get('crop_width', 600);
		$this->crop_height = $this->params->get('crop_height', 400);
		$this->thumb_width = $this->params->get('thumb_width', 336);
		$this->thumb_height = $this->params->get('thumb_height', 180);
		$this->ssthumb_width = $this->params->get('ss_thumb_width', 70);
		$this->ssthumb_height = $this->params->get('ss_thumb_height', 40);
		$this->crop_pos = $this->params->get('crop-position', 'c'); 
		$this->jpeg_com = $this->params->get('jpeg-compression', 100);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 */

	public function getTable($type = 'Portfolio', $prefix = 'Bt_portfolioTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bt_portfolio.portfolio', 'portfolio', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;

	}

	public function featured($pks, $value = 0)
	{
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
		try
		{
			$db = $this->getDbo();

			$db->setQuery('UPDATE #__bt_portfolios AS a' . ' SET a.featured = ' . (int) $value . ' WHERE a.id IN (' . implode(',', $pks) . ')');
			if (!$db->query())
			{
				throw new Exception($db->getErrorMsg());
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bt_portfolio.edit.portfolio.data', array());
		if (empty($data) || JRequest::getVar('layout')=='edit_extrafields')
		{
			$data = $this->getItem();
			$data->vote_sum = $data->vote_count ? number_format($data->vote_sum / $data->vote_count, 1) : 0;
			$data->vote_sum .= '/5';
			$data->vote_sum .= ' (' . $data->vote_count . ' votes)';
		}
		return $data;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		return $condition;
	}

	function getImages()
	{

		$db = $this->getDbo();
		$id = $this->getState($this->getName() . '.id', 0 );

		$db->setQuery('Select * from #__bt_portfolio_images WHERE id > 0 and item_id = ' . $id . ' order by ordering');
		return $db->loadObjectList();

	}
	function findKeyOld($id, $array)
	{
		foreach ($array as $key => $item)
		{
			if ($item->id == $id)
			{
				return $key;
			}
		}
		return -1;
	}
	function findPortOld($id, $array)
	{
		foreach ($array as $key => $item)
		{
			if ($item->portfolio_id == $id)
			{
				return $key;
			}
		}
		return -1;
	}
	function Portflioids(){
		$db = $this->getDbo();
		$db->setQuery('Select portfolio_id  from #__bt_portfolio_extrafields_values ');
		return $db->loadObjectList();
	}
	function getType($id){
		$db = $this->getDbo();
		$db->setQuery('Select type  from #__bt_portfolio_extrafields WHERE id  = ' . $id . ' order by ordering');
		return $db->loadObjectList();
	
	}
	function checkDiffImg($img, $image_order)
	{
		foreach ($image_order as $ordering => $id)
		{
			if ($img->id == $id)
			{
				if ($img->ordering == $ordering)
					return -1;
				else
					return $ordering;
			}
		}
		return -2;
	}
	public function save($data)
	{

		$db = $this->getDbo();

		$extrafields = JRequest::getVar('extra_fields', array(), 'post', 'array');		
		// Alter the title for save as copy
		if (JRequest::getVar('task') == 'save2copy') {
			list($title,$alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
			$data['title']	= $title;
			$data['alias']	= $alias;
		}
		if($data['url'] && substr_count($data['url'],'http') ==0){
			$data['url'] = 'http://'.$data['url'];
		}
		if (!parent::save($data))
		{
			return false;
		}

		// process image gallery
		if (JRequest::getVar("task") == 'save2copy')
			return true;

		$pid = $this->getState($this->getName() . '.id');		
		$path_image_orig = $this->images_path. $pid . '/original/';		
		$path_image_thumb = $this->images_path . $pid . '/thumb/';
		$path_image_ssthumb =$this->images_path . $pid . '/ssthumb/';
		$path_image_large = $this->images_path . $pid . '/large/';
		$this->prepareFolders($pid);
		$this->cleanFiles('tmp');
		
		$registry = new JRegistry();
		$registry->loadArray($data['params']);
		$this->overrideConfigs($registry);	

		// reorder or delete old image
		$imageIds = JRequest::getVar('image_id', array(), 'POST', 'array');
		$imageTitles = JRequest::getVar('image_title', array(), 'POST', 'array');
		$imageNames = JRequest::getVar('image_filename', array(), 'POST', 'array');
		$imageOlds = self::getImages();
		$video = JRequest::getVar('video');		
		$videoarray =json_decode(base64_decode($video));		
		$IdportOld =self::Portflioids();		
		foreach($extrafields as $key=> $extrafield){			
			$IdOld = self::findPortOld($pid, $IdportOld);
			$type = self::getType($key);
			if($type[0]->type =='measurement' || $type[0]->type =='dropdown'){
			if(is_array($extrafield)) $extrafield =implode(',',$extrafield);
			if ($IdOld >= 0)
			{					
			$query='UPDATE  #__bt_portfolio_extrafields_values SET  value=\''.($extrafield).'\'WHERE portfolio_id =\'' . $pid.'\' AND extrafields_id=\'' . ($key) . '\'';
			$db->setQuery($query);
			$db->query();			
			
			}else{
				$query='INSERT INTO  #__bt_portfolio_extrafields_values VALUES(\'' . ($pid) . '\',\'' . $key . '\', \''.($extrafield).'\')';
				$db->setQuery($query);
				$db->query();
			}
			}
		}
		
		foreach ($imageIds as $key => $id)
		{
			$keyOld = self::findKeyOld($id, $imageOlds);
			if ($keyOld >= 0)
			{
				$query = 'UPDATE #__bt_portfolio_images SET title=\'' . $db->escape($videoarray[$key]->title) . '\', youid=\''.($db->escape($videoarray[$key]->youid)).'\', youdesc=\''.($db->escape($videoarray[$key]->youdesc)).'\', youembed=\''.($db->escape($videoarray[$key]->youembed)).'\', ordering =' . $key . ' WHERE id =' . $id;
				$db->setQuery($query);
				$db->query();
				unset($imageOlds[$keyOld]);
			}
			else
			{
				if ($id == 0)
				{
					$query = "INSERT INTO #__bt_portfolio_images SET " . "item_id = '" . $pid . "'," . "title = '" . $db->escape($videoarray[$key]->title). "'," . "filename = '" . $db->escape($imageNames[$key]) . "',". " youid= '" .$db->escape($videoarray[$key]->youid)."',". " youdesc= '" .$db->escape($videoarray[$key]->youdesc)."',". " youembed= '" .$db->escape($videoarray[$key]->youembed)."',`default`=0, ordering=" . $key;
					$db->setQuery($query);
					$db->query();					
				
					$tmp = $this->images_path.'tmp/original/'. $imageNames[$key];
					JFile::move($tmp, $path_image_orig . $imageNames[$key]);
				
					$tmp = $this->images_path.'tmp/large/'. $imageNames[$key];
					JFile::move($tmp, $path_image_large . $imageNames[$key]);

					$tmp = $this->images_path.'tmp/thumb/'. $imageNames[$key];
					JFile::move($tmp, $path_image_thumb . $imageNames[$key]);
					
					$tmp = $this->images_path.'tmp/ssthumb/'. $imageNames[$key];
					JFile::move($tmp, $path_image_ssthumb . $imageNames[$key]);
				}
			}
		}
		foreach ($imageOlds as $img)
		{
			
			@unlink($path_image_orig . $img->filename);
			
			@unlink($path_image_large . $img->filename);
			@unlink($path_image_thumb . $img->filename);
			@unlink($path_image_ssthumb . $img->filename);

			$query = 'DELETE FROM #__bt_portfolio_images WHERE id =' . $img->id;
			$db->setQuery($query);
			$db->query();
		}
		

		// update default image
		$defaul_imageName = JRequest::getVar('default_image', '');

		$query = 'UPDATE #__bt_portfolio_images SET `default` = 0 WHERE item_id =' . $pid;
		$db->setQuery($query);
		$db->query();
		$query = 'UPDATE #__bt_portfolio_images SET `default` = 1 WHERE filename =\'' .$db->escape($defaul_imageName) . '\'';
		$db->setQuery($query);
		$db->query();

		//Save image
		$order = count($image_order);
		for ($i = 0; $i < sizeof($_FILES["images"]["name"]); $i++)
		{
			if ($_FILES["images"]["name"][$i] != "")
			{

				$imgSource = $_FILES["images"]["tmp_name"][$i];
				$info = getimagesize($imgSource);
				$imageExt = str_replace('image/', '', $info['mime']);
				$imageName = md5($pid . strtotime("now") . $_FILES["images"]["name"][$i]) . '.' . $imageExt;
				JFile::upload($imgSource, $path_image_orig . $imageName);
				if($this->largeimgprocess!=-1)
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_large . $imageName, $this->crop_width, $this->crop_height, $this->largeimgprocess, $this->crop_pos, $this->jpeg_com);
				if($this->thumbimgprocess!=-1)
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_thumb . $imageName, $this->thumb_width, $this->thumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_ssthumb . $imageName, $this->ssthumb_width, $this->ssthumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
			
				$imageTitle = $_FILES["images"]["name"][$i];
				$query = "INSERT INTO #__bt_portfolio_images SET " . "item_id = '" . $pid . "'," . "title = '" . $imageTitle . "'," . "filename = '" . $imageName . "',`default`=0, ordering=" . $order;
				$db->setQuery($query);
				$db->query();
				$order++;
			}
		}
		// set default image
		$db->setQuery('Select count(*) from #__bt_portfolio_images WHERE `default` = 1 and item_id = ' . $pid);
		if (!$db->loadResult())
		{
			$query = 'UPDATE #__bt_portfolio_images SET `default` = 1 WHERE ordering = 0 and item_id =' . $pid;
			$db->setQuery($query);
			$db->query();
		}
		
		$query = 'UPDATE #__bt_portfolios SET youembed =  (SELECT youembed FROM #__bt_portfolio_images  WHERE `default` = 1 AND item_id =' . $pid.' ) ,image =  (SELECT filename FROM #__bt_portfolio_images  WHERE `default` = 1 AND item_id =' . $pid.' ) WHERE id =' . $pid;
		$db->setQuery($query);
		$db->query();
		return true;
	}
	
	// delete function
	public function delete(&$pks){
		// IF delete successfully
		if(parent::delete($pks)){
		
			// DELETE IMAGES
			foreach($pks as $pk){
				$path_image = $this->images_path. $pk;
				JFolder::delete($path_image);
				$db = & $this->getDbo();
				$query = 'DELETE FROM #__bt_portfolio_images WHERE item_id = ' . $pk;
				$db->setQuery($query);
				$db->query();
			}
			
		}
	}
	
	// upload function
	function upload()
	{	
		$db = JFactory::getDBO();
        $id = JRequest::getInt('id');
		$query= "SELECT params from #__bt_portfolios where id=$id";
		$db->setQuery($query);
		$params = $db->loadResult();
		
		if($params){
			$registry = new JRegistry();
			$registry->loadString($params);
			$this->overrideConfigs($registry);	
		}
		$pid = 'tmp';
		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

		
		$path_image_orig = $this->images_path. $pid . '/original/';	
		$path_image_thumb =  $this->images_path. $pid . '/thumb/';
		$path_image_ssthumb =  $this->images_path. $pid . '/ssthumb/';
		$path_image_large =  $this->images_path. $pid . '/large/';
		$this->prepareFolders($pid);
		
		$validated = true;
		$objFile = new stdClass();

		$imgSource = $_FILES["Filedata"]["tmp_name"];
		$info = getimagesize($imgSource);
		$imageExt = str_replace('image/', '', $info['mime']);
		$imageName = md5($pid . strtotime("now") . $_FILES["Filedata"]["name"]) . '.' . $imageExt;
		if (in_array($imageExt, $allowedExtensions))
		{

			if (!JFile::upload($imgSource, $path_image_orig . $imageName))
			{
				$result['message'] = 'Could not save!';
				$validated = false;
			}
			else
			{
				
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_large . $imageName, $this->crop_width, $this->crop_height, $this->largeimgprocess, $this->crop_pos, $this->jpeg_com);
				
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_thumb . $imageName, $this->thumb_width, $this->thumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
				BTImageHelper::resize($path_image_orig . $imageName, $path_image_ssthumb . $imageName, $this->ssthumb_width, $this->ssthumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
				
				$objFile->filename = $imageName;
				$objFile->title = $_FILES['Filedata']['name'];
			}

		}
		else
		{
			$result['message'] = 'File extension invalid';
			$validated = false;
		}
		if ($validated)
		{
			$result["success"] = true;
			$result["files"] = $objFile;
		}
		$obLevel = ob_get_level();
		while ($obLevel > 0 ) {
			ob_end_clean();
			$obLevel --;
		}
		echo json_encode($result);
	}
	function prepareFolders($pid)
	{
		self::createFolder( $this->images_path);
		self::createFolder($this->images_path . $pid . '/');
		$original = $this->images_path . $pid . '/original/';
		
		self::createFolder($original);		
		self::createFolder($this->images_path . $pid . '/large/');
		self::createFolder($this->images_path . $pid . '/thumb/');
		self::createFolder($this->images_path . $pid . '/ssthumb/');	
	}
	function createFolder($path){
		if (!is_dir($path))
		{
			JFolder::create($path, 0755);
			$html = '<html><body bgcolor="#FFFFFF"></body></html>';
			JFile::write($path.'index.html', $html);
		}
	}
	function cleanFiles($pid)
	{		
		
		$path_image_orig = $this->images_path. $pid . '/original/';		
		$path_image_thumb = $this->images_path. $pid . '/thumb/';
		$path_image_ssthumb = $this->images_path. $pid . '/ssthumb/';
		$path_image_large = $this->images_path. $pid . '/large/';
		$files = array();
		if(JFolder::exists($path_image_orig)){
			$files = JFolder::files($path_image_orig, '.', true);
		}
		foreach ($files as $file)
		{
			if (file_exists($path_image_orig . $file))
			{
				if (strtotime('now') - filemtime($path_image_orig . $file) > 24 * 3600)
				{					
					JFile::delete($path_image_orig . $file);					
					JFile::delete($path_image_thumb . $file);
					JFile::delete($path_image_ssthumb . $file);
					JFile::delete($path_image_large . $file);
				}
			}
		}

	}
	function rebuild(){
		$building = JRequest::getVar('building');
        $session = JFactory::getSession();
        $db = JFactory::getDBO();
        if ($building) {
            $images = $session->get('rebuild-images');
            $output = $session->get('rebuild-output');
        } else {

            $db->setQuery('Select im.id,im.filename,im.title as image_title,im.item_id,pf.title,pf.params from #__bt_portfolio_images as im inner join #__bt_portfolios as pf on im.item_id = pf.id order by pf.title desc');
            $images = $db->loadObjectList();
            $output = '<h3>Rebuild thumbnail & large images of portfolios:</h3>';
            $output .= '<b>Total: ' . count($images) . ' images</b><hr />';
        }
        if (count($images)) {
			$image = array_pop($images);
            $session->set('rebuild-images', $images);	
			$registry = new JRegistry();
			$registry->loadString($image->params);
			$this->overrideConfigs($registry);		
            $path_image_thumb = $this->images_path. $image->item_id . '/thumb/';
            $path_image_ssthumb = $this->images_path. $image->item_id . '/ssthumb/';
            $path_image_large = $this->images_path. $image->item_id . '/large/';
			
			
				$path_image_orig = $this->images_path. $image->item_id . '/original/';
				if(!is_file($path_image_orig . $image->filename) && is_file($path_image_large . $image->filename)){
					JFile::copy($path_image_large . $image->filename, $path_image_orig . $image->filename);	
				}
				
			
            if (!is_file($path_image_orig . $image->filename)) {
                @unlink($path_image_large . $image->filename);
                @unlink($path_image_thumb . $image->filename);
                @unlink($path_image_ssthumb . $image->filename);
                $query = 'DELETE FROM #__bt_portfolio_images WHERE id =' . $image->id;
                $db->setQuery($query);
                $db->query();
                $output .= $image->title . ':<i> ' . $image->image_title . '</i><font style="color:red"> - Image not found! Removed!</font><br />';
            } else {
                $this->prepareFolders($image->item_id);     
				if($this->largeimgprocess!=-1)		
				BTImageHelper::resize($path_image_orig . $image->filename, $path_image_large . $image->filename, $this->crop_width, $this->crop_height, $this->largeimgprocess, $this->crop_pos, $this->jpeg_com);
                if($this->thumbimgprocess!=-1)
				BTImageHelper::resize($path_image_orig . $image->filename, $path_image_thumb . $image->filename, $this->thumb_width, $this->thumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
                BTImageHelper::resize($path_image_orig . $image->filename, $path_image_ssthumb . $image->filename, $this->ssthumb_width, $this->ssthumb_height, $this->thumbimgprocess, $this->crop_pos, $this->jpeg_com);
               
				$output .= '' . $image->title . ':<i> ' . $image->image_title . '</i><br />';
            }
            echo '<html><head><title>Rebuilding images...</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta HTTP-EQUIV="REFRESH" content="0; url=index.php?option=com_bt_portfolio&task=portfolios.rebuild&tmpl=component&building=true;"></head>';
            echo '<body>' . $output . '...</body></html>';
            $session->set('rebuild-output', $output);
            exit;
        } else {
           return $output;
		}
	}
	function generateNewTitle($categoryid,$alias, $title)
	{
		// Alter the title & alias
		$catTable = JTable::getInstance('Portfolio', 'Bt_portfolioTable');
		while ($catTable->load(array('alias'=>$alias))) {
			$m = null;
			if (preg_match('#-(\d+)$#', $alias, $m)) {
				$alias = preg_replace('#-(\d+)$#', '-'.($m[1] + 1).'', $alias);
			} else {
				$alias .= '-2';
			}
			if (preg_match('#\((\d+)\)$#', $title, $m)) {
				$title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
			} else {
				$title .= ' (2)';
			}
		}

		return array($title, $alias);
	}
	function overrideConfigs($params){
		$this->crop_width =$params->get('crop_width', $this->crop_width);
		$this->crop_height = $params->get('crop_height', $this->crop_height);
		$this->thumb_width = $params->get('thumb_width', $this->thumb_width);
		$this->thumb_height = $params->get('thumb_height', $this->thumb_height);
		$this->ssthumb_width = $params->get('ss_thumb_width', $this->ssthumb_width);
		$this->ssthumb_height = $params->get('ss_thumb_height', $this->ssthumb_height);
	
	
	}
	
}
