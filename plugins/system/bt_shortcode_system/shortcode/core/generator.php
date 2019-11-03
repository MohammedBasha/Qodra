<?php
// no direct access
defined('_JEXEC') or die;
require_once (dirname ( __FILE__ ) . '/field-type.php');
require_once (dirname ( __FILE__ ) . '/shortcode-api.php');
require_once (dirname ( __FILE__ ) . '/images.php');
//Get to process link
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

class Bt_shortcodeHelper{
	
	public static $icons = array( 'glass', 'music', 'search', 'envelope-o', 'heart', 'star', 'star-o', 'user', 'film', 'th-large', 'th', 'th-list', 'check', 'times', 'search-plus', 'search-minus', 'power-off', 'signal', 'cog', 'trash-o', 'home', 'file-o', 'clock-o', 'road', 'download', 'arrow-circle-o-down', 'arrow-circle-o-up', 'inbox', 'play-circle-o', 'repeat', 'refresh', 'list-alt', 'lock', 'flag', 'headphones', 'volume-off', 'volume-down', 'volume-up', 'qrcode', 'barcode', 'tag', 'tags', 'book', 'bookmark', 'print', 'camera', 'font', 'bold', 'italic', 'text-height', 'text-width', 'align-left', 'align-center', 'align-right', 'align-justify', 'list', 'outdent', 'indent', 'video-camera', 'picture-o', 'pencil', 'map-marker', 'adjust', 'tint', 'pencil-square-o', 'share-square-o', 'check-square-o', 'arrows', 'step-backward', 'fast-backward', 'backward', 'play', 'pause', 'stop', 'forward', 'fast-forward', 'step-forward', 'eject', 'chevron-left', 'chevron-right', 'plus-circle', 'minus-circle', 'times-circle', 'check-circle', 'question-circle', 'info-circle', 'crosshairs', 'times-circle-o', 'check-circle-o', 'ban', 'arrow-left', 'arrow-right', 'arrow-up', 'arrow-down', 'share', 'expand', 'compress', 'plus', 'minus', 'asterisk', 'exclamation-circle', 'gift', 'leaf', 'fire', 'eye', 'eye-slash', 'exclamation-triangle', 'plane', 'calendar', 'random', 'comment', 'magnet', 'chevron-up', 'chevron-down', 'retweet', 'shopping-cart', 'folder', 'folder-open', 'arrows-v', 'arrows-h', 'bar-chart-o', 'twitter-square', 'facebook-square', 'camera-retro', 'key', 'cogs', 'comments', 'thumbs-o-up', 'thumbs-o-down', 'star-half', 'heart-o', 'sign-out', 'linkedin-square', 'thumb-tack', 'external-link', 'sign-in', 'trophy', 'github-square', 'upload', 'lemon-o', 'phone', 'square-o', 'bookmark-o', 'phone-square', 'twitter', 'facebook', 'github', 'unlock', 'credit-card', 'rss', 'hdd-o', 'bullhorn', 'bell', 'certificate', 'hand-o-right', 'hand-o-left', 'hand-o-up', 'hand-o-down', 'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up', 'arrow-circle-down', 'globe', 'wrench', 'tasks', 'filter', 'briefcase', 'arrows-alt', 'users', 'link', 'cloud', 'flask', 'scissors', 'files-o', 'paperclip', 'floppy-o', 'square', 'bars', 'list-ul', 'list-ol', 'strikethrough', 'underline', 'table', 'magic', 'truck', 'pinterest', 'pinterest-square', 'google-plus-square', 'google-plus', 'money', 'caret-down', 'caret-up', 'caret-left', 'caret-right', 'columns', 'sort', 'sort-asc', 'sort-desc', 'envelope', 'linkedin', 'undo', 'gavel', 'tachometer', 'comment-o', 'comments-o', 'bolt', 'sitemap', 'umbrella', 'clipboard', 'lightbulb-o', 'exchange', 'cloud-download', 'cloud-upload', 'user-md', 'stethoscope', 'suitcase', 'bell-o', 'coffee', 'cutlery', 'file-text-o', 'building-o', 'hospital-o', 'ambulance', 'medkit', 'fighter-jet', 'beer', 'h-square', 'plus-square', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-double-down', 'angle-left', 'angle-right', 'angle-up', 'angle-down', 'desktop', 'laptop', 'tablet', 'mobile', 'circle-o', 'quote-left', 'quote-right', 'spinner', 'circle', 'reply', 'github-alt', 'folder-o', 'folder-open-o', 'smile-o', 'frown-o', 'meh-o', 'gamepad', 'keyboard-o', 'flag-o', 'flag-checkered', 'terminal', 'code', 'reply-all', 'mail-reply-all', 'star-half-o', 'location-arrow', 'crop', 'code-fork', 'chain-broken', 'question', 'info', 'exclamation', 'superscript', 'subscript', 'eraser', 'puzzle-piece', 'microphone', 'microphone-slash', 'shield', 'calendar-o', 'fire-extinguisher', 'rocket', 'maxcdn', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-circle-down', 'html5', 'css3', 'anchor', 'unlock-alt', 'bullseye', 'ellipsis-h', 'ellipsis-v', 'rss-square', 'play-circle', 'ticket', 'minus-square', 'minus-square-o', 'level-up', 'level-down', 'check-square', 'pencil-square', 'external-link-square', 'share-square', 'compass', 'caret-square-o-down', 'caret-square-o-up', 'caret-square-o-right', 'eur', 'gbp', 'usd', 'inr', 'jpy', 'rub', 'krw', 'btc', 'file', 'file-text', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'thumbs-up', 'thumbs-down', 'youtube-square', 'youtube', 'xing', 'xing-square', 'youtube-play', 'dropbox', 'stack-overflow', 'instagram', 'flickr', 'adn', 'bitbucket', 'bitbucket-square', 'tumblr', 'tumblr-square', 'long-arrow-down', 'long-arrow-up', 'long-arrow-left', 'long-arrow-right', 'apple', 'windows', 'android', 'linux', 'dribbble', 'skype', 'foursquare', 'trello', 'female', 'male', 'gittip', 'sun-o', 'moon-o', 'archive', 'bug', 'vk', 'weibo', 'renren', 'pagelines', 'stack-exchange', 'arrow-circle-o-right', 'arrow-circle-o-left', 'caret-square-o-left', 'dot-circle-o', 'wheelchair', 'vimeo-square', 'try', 'plus-square-o' );
	
	// load shortcode function
	public static function init(){
		global $shortcode_tags;
		$prefix = self::getPrefix();
		$shortcodes = self::loadXml();
		foreach($shortcodes->children() as $shortcode){
			$func = array('Bt_Shortcodes',(string)$shortcode->tagcode);
			add_shortcode($prefix.(string)$shortcode->tagcode, $func);
		}
	}
	// process ajax setting ajax
	public static  function settings(){
 		// get data request
 		$shortcodeTag = JRequest::getVar('shortcode');
 		$shortcodeList = self::loadXml();
 		foreach ($shortcodeList->children() as $shortcode){
 			if($shortcode->tagcode == $shortcodeTag){
 				$shortcodeSelected = $shortcode;
 				break;
 			}
 		}
 		// render header
 		$return = '<div id="bt-generator-breadcrumbs">';
 		$return .= '<a href="javascript:void(0);" class="bt-generator-home" title="Click to return to the shortcodes list">'.JText::_("ALL_SHORTCODE").'</a> &rarr;' ;
 		$return .= '<span>' .JText::_($shortcodeSelected->name ). '</span> <small>' . JText::_($shortcodeSelected->desc ). '</small>';
		$return .= '<a href="javascript:void(0);" class="alignright button bt-generator-toggle-preview"><i class="fa fa-eye"></i>'.JText::_('LIVE_PREVIEW').'</a>';
		$return .= '<div class="bt-generator-clear"></div>';
 		$return .= '</div>';
 		
 		// render attributes 
 		if($shortcodeSelected->atts && count($shortcodeSelected->atts->children())){
 			foreach($shortcodeSelected->atts->children() as $attr){
 				//wrapper
 				$return .='<div class="bt-generator-attr-container">';
 				//title
 				$return .= '<h5>'.JText::_($attr->name).'</h5>';
 				// field type
 				$return .= call_user_func(array('Bt_shortcode_fieldType',(string)$attr->type),$attr->getName(), $attr);
 				if($attr->desc){
 					$return .='<div class="bt-generator-attr-desc">'.JText::_($attr->desc).'</div>';
 				}
 				$return .= '</div>';
 			}
 		}
 		//render wrapping shortcode
 		if($shortcodeSelected->type=='single'){
 			$return .='<input type="hidden" name="bt-generator-content" id="bt-generator-content" value="false" />';
 		}else{
 			$return .= '<div class="bt-generator-attr-container"><h5>'.JText::_('CONTENT').'</h5><textarea name="bt-generator-content" id="bt-generator-content" rows="5">' . str_replace(array('%prefix_','__'), self::getPrefix(),$shortcodeSelected->content )  . '</textarea></div>';
 		}
		$return .= '<div id="bt-generator-preview"></div>';
		$return .= '<div class="bt-generator-actions bt-generator-clearfix">';
		$return .= '<a href="javascript:void(0);" class="button button-primary bt-generator-insert"><i class="fa fa-check"></i>'.JText::_('INSERT_SHORCODE').'</a>';
		$return .= '<a href="javascript:void(0);" class="button bt-generator-toggle-preview"><i class="fa fa-eye"></i>'.JText::_('LIVE_PREVIEW').'</a>';
		$return .= '<a href="javascript:void(0);" class="button button-secondary btn-up-to-top"><i class="fa fa-arrow-up"></i>' . JText::_('GO_UP') . '</a>';
		$return .= self::preset($shortcodeTag);
		$return .= '</div>';

		echo  $return;
		die();
 	}
 	public static function preset($shortcode){
 		return '<div class="bt-generator-presets alignright" data-shortcode="'.$shortcode.'">
					<a href="javascript:void(0);" class="button button-primary bt-gp-button"><i class="fa fa-bars"></i> '.JText::_('PRESET').'</a>
					<div class="bt-preset-popup">
						<div class="bt-preset-head">
							<a href="javascript:void(0);" class="button button-small button-primary bt-preset-new"> '.JText::_('SAVE_CURRENT_PRESET').'</a>
						</div>
						<div class="bt-presets-list">'. self::presets_list($shortcode).'</div>
					</div>
				</div>'; 
 	} 
 	public static function presets_list($shortcode){
 
		// Get presets
		$db = JFactory::getDbo();
		// create table if not exists
 		$createTableQuery = "CREATE TABLE IF NOT EXISTS `#__presets_bt_shortcode` (`shortcode` varchar(255) NOT NULL, `presets` text , PRIMARY KEY(`shortcode`)) ;";
 		$db->setQuery($createTableQuery);
 		$db->query(); 
 		// get presets
 		$presets = self::getPresets($shortcode);
		$return = '';
		// Presets has been found
		if ( is_array( $presets ) && count( $presets ) ) {
			// Print the presets
			foreach( $presets as $preset ) {
				 $return.= '<span data-id="' . $preset->id . '"><em>' .  $preset->preset_name  . '</em> <i class="fa fa-times"></i></span>';
			}
			// Hide default text
			 $return .= '<b style="display:none">'.JText::_('PRESET_NOT_FOUND').'</b>' ;
		}
		// Presets doesn't found
		else   $return .= '<b>'.JText::_('PRESET_NOT_FOUND').'</b>';
 		return $return;
 	}
 	/**
 	 * 
 	 * Load preset list
 	 * @param String $shortcode shortcode name
 	 */
 	public static function load_preset($shortcode,$id){
 		$presets = self::getPresets($shortcode);
 		foreach($presets as $preset){
 			if($preset->id === $id){
 				$settings = json_encode($preset->settings);
 				break;
 			}
 		}
 		echo $settings;
 		die();
 		
 	}
 	
 	/**
 	 * 
 	 * Add new preset
 	 * @param String $shortcode shortcode name
 	 * @param Array $preset data preset
 	 */
 	public static function add_preset($shortcode,$id, $name,$settings){
 		// get list preset
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 		$query->select('*');
 		$query->from($db->quoteName('#__presets_bt_shortcode'));
 		$query->where($db->quoteName('shortcode').' LIKE '. $db->quote($shortcode));
 		$db->setQuery($query);
 		$obj = $db->loadObject();
 		// generator data
 		$preset = new stdClass();
 		$preset->preset_name= $name;
 		$preset->settings = $settings;
 		$preset->id= $id;
 		if($obj != null){
	 		$presets = json_decode(($obj->presets));
 		}else{
 			$presets = array();
 		}
 		$presets[] = $preset;
 		$newPresets = new stdClass();
 		$newPresets->shortcode = $shortcode;
 		$newPresets->presets = json_encode($presets);
 		if($obj != null){
 			//update row
 			$result = JFactory::getDbo()->updateObject('#__presets_bt_shortcode', $newPresets, 'shortcode');
 		}else{
			// insert new row
			$result =  	JFactory::getDbo()->insertObject('#__presets_bt_shortcode', $newPresets, 'shortcode');		
 		}
 		die();
 	}
 	
 	/**
 	 * 
 	 * Remove preset
 	 * @param String $shortcode name of shortcode
 	 * @param String $preset_id id of preset
 	 */
 	public static function remove_preset($shortcode,$preset_id){
 		// get presets list 
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 		$query->select('*');
 		$query->from($db->quoteName('#__presets_bt_shortcode'));
 		$query->where($db->quoteName('shortcode').' LIKE '. $db->quote($shortcode));
 		$db->setQuery($query);
 		$result = $db->loadObject();
		$presets = json_decode($result->presets);
		$newPresets = array();
		foreach($presets as $preset){
			if($preset->id != $preset_id){
				$newPresets[] = $preset;
			}
		}
		$obj = new stdClass();
 		$obj->shortcode = $shortcode;
 		$obj->presets = json_encode($newPresets);
 		$result = JFactory::getDbo()->updateObject('#__presets_bt_shortcode', $obj, 'shortcode');
 		die();
 		
 	}
 	
 	public static  function getPresets($shortcode){
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 		$query->select('*');
 		$query->from($db->quoteName('#__presets_bt_shortcode'));
 		$query->where($db->quoteName('shortcode').' LIKE '. $db->quote($shortcode));
 		$db->setQuery($query);
 		$result = $db->loadObject();
 		$presets = array();
 		if($result != null)
		$presets = json_decode($result->presets);
		return $presets;
 	}
 	// process ajax  preview request
 	public static function preview(){
 		global $shortcode_tags;
 		// call shortcode api to generator html 
 		$shortcode = JRequest::getVar('shortcode','','post','string',JREQUEST_ALLOWRAW);
 		echo do_shortcode($shortcode);
 		die();
 	}
 	
  	public static  function loadXml(){
		// Reading xml file
		$xmlfile = JPATH_SITE .'/plugins/system/bt_shortcode_system/shortcode/data/data.xml';
		$xml = JFactory::getXML($xmlfile,true);
		return $xml;
  	}
  	public static function getPrefix(){
  		$plugin = JPluginHelper::getPlugin('system','bt_shortcode_system');
		$param  = new JRegistry($plugin->params);
		return $param->get('prefix','bt_');
  	}
  	
  	public  static  function fetchHead(){
  		 // set url file load 
		
  		$assetUrl 		 = JURI::root(). 'plugins/system/bt_shortcode_system/shortcode/assets/'  ;
		$urlAwesomefont  = $assetUrl.'css/font-awesome.min.css';
		$urlBootstrap	 = $assetUrl. 'css/bootstrap.css';
		$urlAdminCss 	 = $assetUrl.'css/generator.css';
		$urlGeneratorJs  = $assetUrl.'js/bt-generator.js';
		$jqueryUrl 		 = $assetUrl.'js/jquery.js';
		$jqueryUI		 = $assetUrl.'js/jquery-ui-1.10.4.js';
		$uploadJS		 = $assetUrl.'js/fileuploader.js';
		$simpleSliderUrl = $assetUrl.'js/simpleslider.js';
		$farbtasticJs 	 = $assetUrl.'js/farbtastic.js';
		$farbtasticCss 	 = $assetUrl.'css/farbtastic.css';
		$mootoolJs 		 = JURI::root() . 'media/system/js/mootools-core.js';
		$mootoolMoreJs 	 = JURI::root() . 'media/system/js/mootools-more.js';
		$modalCSS 		 = JURI::root() . 'media/system/css/modal.css';
		$modalJs 		 = JURI::root() . 'media/system/js/modal.js';
		
		
	   	// load style for page 
	  	$return  = '<link type="text/css" href="'.$urlAwesomefont.'" rel="stylesheet"/>';
	  	$return .= '<link type="text/css" href="'.$urlBootstrap .'" rel="stylesheet"/>';
	   	$return .= '<link type="text/css" href="'.$urlAdminCss.'" rel="stylesheet"/>';
	   	$return .= '<link type="text/css" href="'.$farbtasticCss.'" rel="stylesheet"/>';
		$return .= '<link type="text/css" href="'.$modalCSS.'" rel="stylesheet"/>';
	   	$return .= '<script type="text/javascript" src="'.$jqueryUrl.'"></script>';
	   	$return .= '<script type="text/javascript" src="'.$jqueryUI.'"></script>';
	   	$return .= '<script type="text/javascript" src="'.$uploadJS.'"></script>';
	   	$return .= '<script type="text/javascript" src="'.$simpleSliderUrl.'"></script>';
	   	$return .= '<script type="text/javascript" src="'.$farbtasticJs.'"></script>';
	   	$return .= '<script type="text/javascript" src="'.$urlGeneratorJs.'"></script>';
		$return .= '<script type="text/javascript" src="'.$mootoolJs.'"></script>';
		$return .= '<script type="text/javascript" src="'.$mootoolMoreJs.'"></script>';
		$return .= '<script type="text/javascript" src="'.$modalJs.'"></script>';
		$return .='<script type="text/javascript">'
				.' function jInsertFieldValue(value, id){'
				.'			jQuery("#" + id).val(value).trigger("change");'
				.'}'
				. ' function jModalClose() {SqueezeBox.close();}'
				.'</script>';
	   	
	   	return  $return;
  	}	
 	
  	public static function icons(){
  		$icons= array();
  		foreach (self::$icons as $icon){
  			$icons[] = '<i class="fa fa-' . $icon . '" title="' . $icon . '"></i>';
  		}
  		echo implode('',$icons); 
  		die();
  	}
  	
  	/**
  	 * Get list sub dir in images folder
  	 */
 	public static function getSubDirs($path, $level, &$dirList){
 		if(count($dirList)== 0){
	 		$obj = new stdClass();
	 		$obj->level= '';
	 		$obj->path= $path;
	 		array_push($dirList,$obj);
 		}
 		$realPath = JPATH_SITE.'/'.$path.'/*';
 		$directories = glob($realPath , GLOB_ONLYDIR);
 		if(count($directories) > 0){
 			foreach ($directories as $dir){
 				$obj = new stdClass();
 				$obj->level = $level;
 				$obj->path = $path.'/'.basename($dir);
 				array_push($dirList ,$obj);
 				self::getSubDirs($obj->path, $level.'-', $dirList);
 			}
 		}
 	}
 	/**
 	 * Get all images in path
 	 */
  	public static function getImages($path){
  		//check dir is exists
  		if(!is_dir(JPATH_SITE.'/'.$path)){
  			echo "DIR_FALSE";
  			die();
  		}

  		$imagesList = array();

		$realPath =  JPATH_SITE.'/'.$path;
		$images = scandir($realPath);

		foreach ($images as $image){
			if(preg_match('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i',$image)){
				array_push($imagesList, $path.'/'.$image);
			}
		}
  		// check images 
  		if(count($imagesList)==0){
	  		echo "IMAGE_NULL";
	  	}else{
	  		echo json_encode($imagesList);
	  	}
  		die();
  	}
  	/** Upload images */
  	public static function uploadImages(){
  		$fileName = JRequest::getVar('qqfile');
  		$imagesUrl = 'images/bt-shortcode/upload/';
  		$imagesDir = JPATH_ROOT.'/images/bt-shortcode/';
  		if(!file_exists($imagesDir)){
	  		mkdir($imagesDir,0755);
	  	}
		$imagesDir = JPATH_ROOT.'/images/bt-shortcode/upload/';
		if(!file_exists($imagesDir)){
	  		mkdir($imagesDir,0755);
	  	}
		
	  	file_put_contents($imagesDir.$fileName, file_get_contents('php://input'));
	  	
  		if(file_exists($imagesDir.$fileName)){
			echo json_encode(array('success'=>1,'fileUrl'=>$imagesUrl.$fileName));
		}
  		else{
			echo json_encode(array('success'=>0) );
		}
			
	  	die();
  	}
  	
  	/** resize images */
  	public static function resizeImage($originalPath,$thumbPath,$width,$height){
  			$imageObj = new JImage($originalPath);
  			$properties = JImage::getImageFileProperties($originalPath);
  			$resizedImage = $imageObj->resize($width,$height, true);
  			$mime = $properties->mime;
			if ($mime == 'image/jpeg')
			{
			    $type = IMAGETYPE_JPEG;
			}
			elseif ($mime = 'image/png')
			{
			    $type = IMAGETYPE_PNG;
			}
			elseif ($mime = 'image/gif')
			{
			    $type = IMAGETYPE_GIF;
			}
  			$resizedImage->toFile($thumbPath, $type);	
  	}
  	/**
  	 * 
  	 * Create select tag html
  	 * @param unknown_type $args
  	 */
  	public static function select($args){
  		$args = self::parse_args( $args, array(
				'id'       => '',
				'name'     => '',
				'class'    => '',
				'multiple' => '',
				'size'     => '',
				'disabled' => '',
				'selected' => '',
				'none'     => '',
				'options'  => array(),
				'style' => '',
				'format'   => 'keyval', // keyval/idtext
				'noselect' => '' // return options without <select> tag
			) );
  		$options = array();
		if ( !is_array( $args['options'] ) ) $args['options'] = array();
		if ( $args['id'] ) $args['id'] = ' id="' . $args['id'] . '"';
		if ( $args['name'] ) $args['name'] = ' name="' . $args['name'] . '"';
		if ( $args['class'] ) $args['class'] = ' class="' . $args['class'] . '"';
		if ( $args['style'] ) $args['style'] = ' style="' .  $args['style']  . '"';
		if ( $args['multiple'] ) $args['multiple'] = ' multiple="multiple"';
		if ( $args['disabled'] ) $args['disabled'] = ' disabled="disabled"';
		if ( $args['size'] ) $args['size'] = ' size="' . $args['size'] . '"';
		if ( $args['none'] && $args['format'] === 'keyval' ) $args['options'][0] = $args['none'];
		if ( $args['none'] && $args['format'] === 'idtext' ) array_unshift( $args['options'], array( 'id' => '0', 'text' => $args['none'] ) );
		// keyval loop
		// $args['options'] = array(
		//   id => text,
		//   id => text
		// );
		if ( $args['format'] === 'keyval' ) foreach ( $args['options'] as $id => $text ) {
				$options[] = '<option value="' . (string) $id . '">' . (string) $text . '</option>';
			}
		// idtext loop
		// $args['options'] = array(
		//   array( id => id, text => text ),
		//   array( id => id, text => text )
		// );
		elseif ( $args['format'] === 'idtext' ) foreach ( $args['options'] as $option ) {
				if ( isset( $option['id'] ) && isset( $option['text'] ) )
					$options[] = '<option value="' . (string) $option['id'] . '">' . (string) $option['text'] . '</option>';
			}
		$options = implode( '', $options );
		$options = str_replace( 'value="' . $args['selected'] . '"', 'value="' . $args['selected'] . '" selected="selected"', $options );
		return ( $args['noselect'] ) ? $options : '<select' . $args['id'] . $args['name'] . $args['class'] . $args['multiple'] . $args['size'] . $args['disabled'] . $args['style'] . '>' . $options . '</select>';
  	}
  	/**
  	 *Get all Category 
  	 * 
  	 */
  	public static function getCategories(){
  		jimport( 'joomla.application.categories' );
		$categories = JCategories::getInstance('Content');
		$options = array(); 
		$root = $categories->get('root');
		$checked = array();
		self::dfs($options,$root);
		$cats= array();
		foreach($options as $option){
			$cats[$option->id] = $option->title;
		}
		
		return $cats;
  	}
  	public static function dfs(&$options,$cat){
  		array_push($options,$cat);
  		$childs = $cat->getChildren();
  		if(!empty($childs)){
  			foreach ($childs as $child){
  				self::dfs($options,$child);		
  			}
  		}
  	}
  	
	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * This function is used  to allow for  array
	 * to be merged into another array.
	 *
	 * @param string|array $args Value to merge with $defaults
	 * @param array $defaults Array that serves as the defaults.
	 * @return array Merged user defined values with defaults.
	 */
	public static function parse_args( $args, $defaults = '' ) {
		if ( is_object( $args ) )
			$r = get_object_vars( $args );
		elseif ( is_array( $args ) )
			$r =& $args;
	
		if ( is_array( $defaults ) )
			return array_merge( $defaults, $r );
		return $r;
	}
	
	/**
	 * 
	 * Get slides for slider ( image and all property ) 
	 * @param Array $arg some attr of slider( source type,...)
	 * @return Array $slides
	 */
	public static function get_slides($args){
		# get source type
		if($args['source']==='none') return ;
		foreach(array('media','category') as $type ){
			if(strpos( trim( $args['source'] ), $type . ':' )=== 0){
				$args['source'] = array(
						'type' => $type,
						'val'  => (string) trim( str_replace( array( $type . ':', ' ' ), '', $args['source'] ), ',' )
					);
				break;
			}
		}
		if($args['source']['type'] ==='media'){
			$images = (array) explode( ';', $args['source']['val'] );
			 
			$slides = array();
			$originalUrl = JURI::root().'/images/bt-shortcode/original/';
			foreach($images  as $image){
				$image = str_ireplace("'", '"', $image); 
				$image = json_decode($image); 
				$slide['image'] = $image->url;
				$slide['caption'] = $image->title;
				$slide['link']= (isset($image->link))? $image->link :'';
				$slides[] = $slide;
			}
			return $slides;
		}
		
		// images from category 
		if($args['source']['type'] ==='category'){
			
		}
	}
	
	/**
	 * 
	 * Get all image in article of category
	 * @param Array $categories list categories 
	 * @param Int $limit limit of image
	 * @param String $prefix Prefix of shortcode
	 */
	public  static function getCatImages($catsSelected,$limit,$prefix){
			$cats = array();
			jimport( 'joomla.application.categories' );
			$categories = JCategories::getInstance('Content');
			
			
			$cats_ids = implode(',',$catsSelected);  
			// get article & url image in article
			
			$db = JFactory::getDbo();
	
	        // Get an instance of the generic articles model
	        $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
	
	        $model->setState('params', new JRegistry);
	
	        $model->setState('list.select', 'a.urls, a.images, a.fulltext, a.id, a.title, a.alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' . ' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' . ' a.hits, a.featured,' . ' LENGTH(a.fulltext) AS readmore');
	        // Set the filters based on the module params
	
	        $model->setState('list.start', 0);
	        //$model->setState('list.limit', (int)$args['limit']  );
	        $model->setState('filter.published', 1);
	
	        // Access filter
			$userId = JFactory::getUser()->get('id');
	        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
	        $authorised = JAccess::getAuthorisedViewLevels($userId);
	        $model->setState('filter.access', $access); 
	        $model->setState('filter.category_id', $cats_ids);
	        $items = $model->getItems();
	        $slides = array();
			foreach ($items as &$item) {
				// break if limited
				//if(count($slides)>= $limit) break;
				// setting for route link
	            $item->slug = $item->id . ':' . $item->alias;
	            $item->catslug = $item->catid ;
	
				// item link
				$item->link = '';
	            if ($access || in_array($item->access, $authorised)) {
					// We know that user has the privilege to view the article
					//Item link
					if(!$item->link) $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, JFactory::getLanguage()->getTag()));
				}
				else {
					$item->link = JRoute::_('index.php?option=com_users&view=login');
				}
	
				// escape html characters
				$item->title = htmlspecialchars($item->title);
				
				//Get image tag & url image 
				$imageUrl = '';
				if($item->images){
					$item->images = json_decode($item->images);
					$imageUrl = $item->images->image_intro;
					if(!$imageUrl){
						$imageUrl = $item->images->image_fulltext;
					}
				}
				if(!$imageUrl){
				
					if($item->fulltext)
						preg_match_all('/<img[^>]+>/i',$item->fulltext, $imgTags);
					else 
						preg_match_all('/<img[^>]+>/i',$item->introtext, $imgTags);
				
				
					for ($i = 0; $i < count($imgTags[0]); $i++) {
						// break if limited
						//if(count($slides)>= $limit) break;
						// get the source string
						preg_match('/src="([^"]+)/i',$imgTags[0][$i], $imgage);
						//remove opening 'src=' tag, can`t get the regex right
						$imageUrl = str_ireplace( 'src="', '',  $imgage[0]);
						break;
						
					}
				}
				if($imageUrl){
					$slide['image'] = $imageUrl;
					$slide['link'] = $item->link;
					$slide['caption'] = $item->title;
					$slides[] = $slide;
				}
	        }
	        $imagesTag = '';
	        foreach($slides as $slide){
	        	$imageTag ='['.$prefix.'image src="'.$slide['image'].'" title="'.$slide['caption'].'" link="'.$slide['link'].'"]';
	        	$imagesTag .= $imageTag;
	        }
	        echo $imagesTag;
			die;
	}
	/**
	 * 
	 * Get categories hasn't category children  
	 * @param Array $leafCats  contain category return 
	 * @param Object $currenCat 
	 */
	public static function getLeafCats(&$leafCats,$currenCat){
		$childs = $currenCat->getChildren();
	 	if(!empty($childs)){
			foreach ($childs as $child){
				self::getLeafCats($leafCats,$child);		
			}
		}else{
			array_push($leafCats, $currenCat);
		}
	}
	
	public static function createThumb($id, $sourceImage, $width = 150, $height = 150 , $cropCenter, $quality = 80){
		
		if(strpos($sourceImage, JURI::root()) > -1){
			$sourceImage = str_replace(JURI::root() , JPATH_ROOT .'/' , $sourceImage);
		}else{
			$sourceImage = JPATH_ROOT .'/'. $sourceImage;
		}
		$sourceImage = urldecode($sourceImage);
		$imageName = substr($sourceImage, strrpos($sourceImage, '/') + 1);
		// check thumb folder
		$thumbFolder = JPATH_ROOT . '/images/bt-shortcode';
		if(!file_exists($thumbFolder)){
			mkdir($thumbFolder,0775);
		}  
		
		$thumbFolder .= '/' . $id ;
		if(!file_exists($thumbFolder)){
			mkdir($thumbFolder,0775);
		}
		
		$thumbFolder .= '/' . $width . "x" . $height;
		if(!file_exists($thumbFolder)){
			mkdir($thumbFolder,0775);
		}
		
		if (file_exists($sourceImage)) {	
			$thumbName = $width . "x" . $height . "-" . $imageName;
			if (!file_exists($thumbFolder . '/' . $thumbName)) {
				//create thumb
				BTShortcodeImageHelper::createImage($sourceImage, $thumbFolder . '/' . $thumbName, $width, $height, $cropCenter, $quality);
			}
			$thumbUrl = JURI::root().'images/bt-shortcode/' . $id . '/' . $width . "x" . $height . '/';
			return  $thumbUrl.$thumbName;
		}else{
			return false;
		}
  	}
	
}