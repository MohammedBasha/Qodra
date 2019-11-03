<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die("Restricted access");
 
class plgSystemBt_shortcode_system extends JPlugin
{
	// for admin
	public function onBeforeRender()
	{
		require_once (dirname ( __FILE__ ) . '/shortcode/core/generator.php');
		
		# load language
		$lang = JFactory::getLanguage();
		$extension = 'plg_system_bt_shortcode_system';
		$base_dir = JPATH_ADMINISTRATOR;
		$language_tag = 'en-GB';
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);
		
		#generator list shortcodes
		if(JRequest::getVar('btaction')=== "bt_shortcode_system" ){
			 global $shortcode_tags;
			 Bt_shortcodeHelper::init();
			if(JRequest::getVar('task')=== 'listview'){
			  	# Reading xml 
			    $xml = Bt_shortcodeHelper::loadXml();
			    
			    # generate html shortcode list
			    $shortcodeList = '';
			   
			    foreach($xml->children() as $shortcode){
			    	if((string)$shortcode->show === 'yes'){
						$shortcodeList .= 
						'<span data-group="'.$shortcode->group.'" data-desc="'.$shortcode->desc.'" title="'.JText::_($shortcode->desc).'" data-shortcode="'.$shortcode->tagcode.'" data-name="'.JText::_($shortcode->name).'" >'
								.'<i class="fa fa-'.$shortcode->icon.'"></i>'
								.JText::_($shortcode->name)
						.'</span>';
			    	}
			    }
			    
			   # featch head
			   echo '<!DOCTYPE html><html><head>' . Bt_shortcodeHelper::fetchHead() . '</head><body>';
			   
			   # generate html code
			   	echo 
			   	'<div id="bt-generator">'
			   		.'<input id="bt-generator-search" type="text" placeholder="'.JText::_('SEARCH_SHORTCODE').'" value="" name="bt-generator-search">'
			   		.'<div id="bt-generator-filter">'
			   			.'<strong>'.JText::_('FILTER_BY_TYPE').' </strong>'
			   			.'<a data-filter="all" href="#">'.JText::_('ALL').'</a>'
			   			.'<a data-filter="content" href="#">'.JText::_('CONTENT').'</a>'
			   			.'<a data-filter="box" href="#">'.JText::_('BOX').'</a>'
			   			.'<a data-filter="media" href="#">'.JText::_('MEDIA').'</a>'
			   			.'<a data-filter="gallery" href="#">'.JText::_('GALLERY').'</a>'
			   			.'<a data-filter="data" href="#">'.JText::_('DATA').'</a>'
			   			.'<a data-filter="other" href="#">'.JText::_('OTHER').'</a>'
			   		.'</div>'
			   		.'<div id="bt-generator-choices" class="bt-generator-clearfix">'
			   			.$shortcodeList
			   		.'</div>'
			   		.'<div id="bt-generator-settings">'
			   		.'</div>'
			   		.'<input id="bt-generator-selected" type="hidden" value="">'
			   		.'<input id="plg-url" type="hidden" value="'. JURI::base().'">'
			   		.'<input type="hidden" name="bt-compatibility-mode-prefix" id="bt-compatibility-mode-prefix" value="'.Bt_shortcodeHelper::getPrefix().'" />'
			   	.'</div>';
				echo '</body></html>';
			   	die();
		 	 }elseif(JRequest::getVar("task") === "settings"){
		 	 	# setting attribute of shortcode
		  		Bt_shortcodeHelper::settings();
		  	}elseif(JRequest::getVar("task") ==='preview'){
		  		# preview shortcode 
		  		Bt_shortcodeHelper::preview();
		  	}elseif(JRequest::getVar("task") ==='images'){
		  		$path = JRequest::getVar('folder');
		  		# get images
		  		Bt_shortcodeHelper::getImages($path);
		  	}elseif (JRequest::getVar("task")==="upload"){
		  		#upload images
		  		Bt_shortcodeHelper::uploadImages();
		  	}elseif(JRequest::getVar("task")==="imagescat"){
		  		# get images from cat
		  		Bt_shortcodeHelper::getCatImages(JRequest::getVar('cats'),JRequest::getVar('limit'),JRequest::getVar('prefix'));
		  	}elseif(JRequest::getVar("task")==="add_preset"){
		  		#get var
		  		$shortcode = JRequest::getVar("shortcode");
		  		$id = JRequest::getVar("id");
		  		$name = JRequest::getVar("name");
		  		$settings = JRequest::getVar("settings");
		  		# add preset
		  		Bt_shortcodeHelper::add_preset($shortcode, $id,$name,$settings);
		  	}elseif(JRequest::getVar("task")==="remove_preset"){
		  		$id = JRequest::getVar('id');
		  		$shortcode = JRequest::getVar('shortcode');
		  		# remove preset
		  		Bt_shortcodeHelper::remove_preset($shortcode, $id);
		  	}elseif(JRequest::getVar("task")==="load_preset"){
		  		$id = JRequest::getVar('id');
		  		$shortcode = JRequest::getVar('shortcode');
		  		#load preset
		  		Bt_shortcodeHelper::load_preset($shortcode, $id);
		  	}elseif(JRequest::getVar("task")==="icon"){
		  		Bt_shortcodeHelper::icons();
		  	}
		}
	}
}
