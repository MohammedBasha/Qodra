<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
jimport('joomla.html.parameter');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/images.php';
class JFormFieldAjax extends JFormField {

	protected $type = 'ajax';
	protected $saveDir;
	private $imageTempSaveDir = '';
	private $thumbSaveDir = '';
	private $videolargeDir = '';
	private $videothumbDir = '';
	private $videossthumbDir = '';
	private $videoOriginalDir = '';
	protected $params;
	private $result = array('success' => false, 'message' => '', 'data' => null);	
	private $videoYoutube = array();
	private $videoVimeo = array();
	private $videodailymotion = array();
	private $sessions;
	
	protected function getInput() {
	$this->sessions = JFactory::getSession();
	$this->params = JComponentHelper::getParams('com_bt_portfolio');
	$this->saveDir =JPATH_SITE .'/'. $this->params->get('images_path','images/bt_portfolio').'/';
	$this->createDir($this->saveDir);
	$pid =JRequest::getVar('id');
    $this->createDir($this->saveDir . $pid . '/');
	$this->imageTempSaveDir = $this->saveDir . '/tmp';
    $this->createDir($this->imageTempSaveDir);
	$this->videoOriginalDir = $this->saveDir . 'tmp' . '/original/';
    $this->createDir($this->videoOriginalDir);	
	$this->videothumbDir = $this->saveDir . 'tmp' . '/thumb/';
    $this->createDir($this->videothumbDir);
	$this->videossthumbDir = $this->saveDir . 'tmp' . '/ssthumb/';
    $this->createDir($this->videossthumbDir);
	$this->videolargeDir = $this->saveDir . 'tmp' . '/large/';
    $this->createDir($this->videolargeDir);	
		
		 if (JRequest::get('post') && JRequest::getString('action')) {
           $obLevel = ob_get_level();
			while ($obLevel > 0 ) {
				ob_end_clean();
				$obLevel --;
            }
            echo self::doPost();
            exit;
        }
	}
	private function doPost() {	
	  if (JRequest::getString('action') == 'get_video') {
         if (JRequest::getString('from') == 'youtube') {
             $method = $_POST['method'];
				if ($method == "playlist_video") {
                    $url_decode = base64_decode($_POST['data']);
                    $url = rtrim($url_decode, "/");
					if (filter_var($url, FILTER_VALIDATE_URL)) {
                            $parse_url_1 = explode("?", $url);
                            $parse_url = explode("&", $parse_url_1[1]);
                            foreach ($parse_url as $value) {							
                                $param = explode('=', $value);									
                                if ($param[0] == 'v') {									
                                    $videos = array();
                                    $video_id = $param[1];
                                    $this->result['success'] = TRUE;
                                    $this->result['message'] = JText::_('Video had been get');
                                    $this->result['data'] = 'video|' . $video_id;
                                    break;
                                }
                                if ($param[0] == "list") {
                                    $playlist_id = $param[1];

                                    $this->result['success'] = TRUE;
                                    $this->result['message'] = JText::_('Video in playlist had been get');
                                    $this->result['data'] = 'playlist|' . $playlist_id;
                                    break;
                                }
                            }
                        }					
					
                       else {
                            $this->result['success'] = FALSE;
                            $this->result['message'] = JText::_('Youtube url invalid');
                        }
                        return json_encode($this->result);
                 }				 
				if ($method == 'getvideos') {
						$data = $_POST['data'];
                        $parse_data = explode("|", $data);

                        if ($parse_data[0] == "playlist") {
                            $videos = $this->youtubeGetVideoFromPlaylist($parse_data[1]);
                            if (count($videos) > 0) {
                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_('All video had been got');
                                $this->result['data'] = $videos;
                            } else {
                                $this->result['message'] = JText::_('Can\'t get video from playlist');
                            }
                        } else if ($parse_data[0] == "video") {
                            $this->result['success'] = TRUE;
                            $this->result['message'] = JText::_('Video had been get');
                            $this->result['data'] = $parse_data[1];
                        }
						return json_encode($this->result);
                       
                    }
					if($method =='getvideo'){
					   $video_id = $_POST['videoid'];						   
                       $youtubeGetVideo = $this->youtubeGetVideo($video_id);
                       return $youtubeGetVideo;
					}
			 
		 }
        //vimeo
		 if (JRequest::getString('from') == 'vimeo') {
              $method = $_POST['method'];
				if ($method == "playlist_video") {
				$url_decode = base64_decode($_POST['data']);
                $url = rtrim($url_decode, "/");				
				if (filter_var($url, FILTER_VALIDATE_URL)) {
					 $parse_url = explode("/", $url);
					 $is_album = $parse_url[count($parse_url) - 2];
                      if ($is_album == "album") {
                          $album_id = $parse_url[count($parse_url) - 1];
                          $videos = $this->vimeoGetVideoFromAlbum($album_id);
                          $this->result['success'] = TRUE;
                          $this->result['message'] = JText::_('All video had been get');
                          $this->result['data'] = "album_" . $album_id;
                      } else {
						 $video_id = $parse_url[count($parse_url) - 1];		 
						 if (is_numeric($video_id)) {
							$this->result['success'] = TRUE;
							$this->result['message'] = JText::_('Video had been got');
							$this->result['data'] = "video_" .$video_id;
							$this->result['url'] =$url; 						
						  } else {
							 $this->result['message'] = JText::_('Video vimeo invalid');
							}
					    }
					}
					return json_encode($this->result);
				}
				   if ($method == "getvideos") {
                        $data = $_POST['data'];
                        $parse_data = explode('_', $data);
                        if ($parse_data[0] == "album") {
                            $videos = $this->vimeoGetVideoFromAlbum($parse_data[1]);
                            if (count($videos) > 0) {
                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_('All video had been got');
                                $this->result['data'] = $videos;
                            } else {
                                $this->result['message'] = JText::_('Can\'t get video from album');
                            }
                        } else if ($parse_data[0] == "video") {
                            $this->result['success'] = TRUE;
                            $this->result['message'] = JText::_('Video had been got');
                            $this->result['data'] = $parse_data[1];
                        } else {
                            $this->result['message'] = JText::_('Can\'t get video data');
                        }
                        return json_encode($this->result);
                    }

				if ($method == 'getvideo') {
					$video_id = $_POST['videoid'];		
					$vimeoGetVideo = $this->vimeoGetVideo($video_id);
					return $vimeoGetVideo;
					
				}
		}
		 if (JRequest::getString('from') == 'dailymotion') {
              $method = $_POST['method'];
				if ($method == "playlist_video") {
				$url_decode = base64_decode($_POST['data']);
                $url = rtrim($url_decode, "/");				
				if (filter_var($url, FILTER_VALIDATE_URL)) {
					 $parse_url = explode("/", $url);
					 $is_playlist = $parse_url[3];
					if($is_playlist == 'playlist'){
						$output = parse_url($url);
						$url_parse= $output['path'];
						$parts = explode('/',$url_parse);
						$parts = explode('_',$parts[2]);
						$playlist_id= $parts[0];						
                         $this->result['success'] = TRUE;
                         $this->result['message'] = JText::_('Video in playlist had been get');
                         $this->result['data'] = 'playlist|' . $playlist_id;
					} else if ($is_playlist == "video") {
						$output = parse_url($url);
						$url_parse= $output['path'];
						$parts = explode('/',$url_parse);
						$parts = explode('_',$parts[2]);
						$video_id= $parts[0];
						$this->result['success'] = TRUE;
						$this->result['message'] = JText::_('Video had been got');
						$this->result['data'] ='video|' .$video_id;
						$this->result['url'] =$url; 
						}
					}
					return json_encode($this->result);
				}
			if($method == 'getvideos'){
				 $data = $_POST['data'];
                 $parse_data = explode('|', $data);
                 if ($parse_data[0] == "playlist") {
						$videos = $this->daylimotionGetVideoFromList($parse_data[1]);
                       if (count($videos) > 0) {
                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_('All video had been got');
                                $this->result['data'] = $videos;
                         } else {
                                $this->result['message'] = JText::_('Can\'t get video from album');
                       }
				 
				}else if ($parse_data[0] == "video") {
                            $this->result['success'] = TRUE;
                            $this->result['message'] = JText::_('Video had been got');
                            $this->result['data'] = $parse_data[1];
                        } else {
                            $this->result['message'] = JText::_('Can\'t get video data');
                        }
                        return json_encode($this->result);
				
				
			}			
			
			if ($method == 'getvideo') {
				$video_id = $_POST['videoid'];
				$dailymotionGetVideo = $this->dailymotionGetVideo($video_id);
				return $dailymotionGetVideo;
			}
			
		}
	
		}
		if (JRequest::getString('action') == 'get_image') {
			$data = $_POST['data'];			
			$image =JURI::root().$data;
			return $image;
		}
	}
	   private function youtubeGetVideoFromPlaylist($playlistId) {
        $pl_info = @simplexml_load_file("http://gdata.youtube.com/feeds/api/playlists/" . $playlistId."?max-results=50");			
        $list_video = array();
        if ($pl_info) {
            $videos = $pl_info->entry;			
            foreach ($videos as $video) {
                $video_link = $video->link;
                foreach ($video_link as $link) {
                    if ($link['rel'] == "related") {
                        $parser_link = explode("/", $link['href']);
                        $videoid = $parser_link[count($parser_link) - 1];
                        if ($videoid != "") {
                            $list_video[] = $videoid;
                        }
                    }
                }
            }
        }

        return $list_video;
    }
	 private function vimeoGetVideoFromAlbum($albumId) {
        $dataURL = 'http://vimeo.com/api/v2/album/' . $albumId . '/videos.xml';
        $videos = @simplexml_load_file($dataURL);
        $list_video = array();
        if ($videos) {
            foreach ($videos as $video) {
                $videoid = (string) $video->id;
                $list_video[] = $videoid;
            }
        }

        return $list_video;
    }
	 private function daylimotionGetVideoFromList($playlistId) {
	 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.dailymotion.com/playlist/".$playlistId."/videos?limit=50");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$results = curl_exec($ch);
		curl_close($ch);
		$videos = json_decode($results);		
        $list_video = array();
        if ($videos) {
            foreach ($videos->list as $video) {			
                $videoid = (string) $video->id;
                $list_video[] = $videoid;
            }
        }

        return $list_video;
    }
	private function youtubeGetVideo($video_id) {
	
        $hashedName = md5('youtube-'. strtotime("now") .$video_id);
        $filename = $hashedName . '.jpg';
		$pid =JRequest::getVar('id');		
        $videoObj = NULL;
       
            $videoObj = new stdClass();
            $video_image = 'http://img.youtube.com/vi/' . $video_id . '/0.jpg';
            $urlFeed = 'http://gdata.youtube.com/feeds/api/videos/' . $video_id;
            $video = @simplexml_load_file($urlFeed);
			
            if ($video) {
                if (copy($video_image, $this->videoOriginalDir . '/' . $hashedName . '.jpg')) {
					if (copy($video_image, $this->videothumbDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videothumbDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videothumbDir . "/{$filename}", $this->params->get('thumb_width', 336), $this->params->get('thumb_height', 180), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
					if (copy($video_image, $this->videolargeDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videolargeDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videolargeDir . "/{$filename}", $this->params->get('crop_width', 600), $this->params->get('crop_height', 400), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
					if (copy($video_image, $this->videossthumbDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videossthumbDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videossthumbDir . "/{$filename}", $this->params->get('ss_thumb_width', 70), $this->params->get('ss_thumb_height', 40), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
                        $videoObj->name = (string) $video->title;
                        $videoObj->description = (string) $video->description;
                        $videoObj->id_video = $video_id;
                        $videoObj->thumb_video = $filename;
                        $videoObj->source_video = 'Youtube Server';
                        $videoObj->video_type = 'video';                   
						$this->sessionStore('video_youtube', $videoObj, $this->videoYoutube);
                        $rs_html = $this->createHTML($hashedName, $videoObj->thumb_video, $videoObj->name, 'video_youtube');						
                        $this->result['success'] = TRUE;						
						$this->result['title'] =(string) $video->title;
						$this->result['description'] =(string) $video->content;
						$this->result['embed'] =(string)('<iframe  src="http://www.youtube.com/embed/'.trim($video_id).'?autoplay=1&showinfo=0&wmode=transparent&feature=player_detailpage" frameborder="0" wmode="Opaque" allowfullscreen></iframe>');
                        $this->result['message'] = JText::_('Video has been get successfully!');
                        $this->result['data'] = $rs_html;
                        $this->result['url'] = 'http://www.youtube.com/watch?v='.$video_id;
						
                    
				}
                 else {
                    $this->result['message'] = JText::_('Video not found');
                }
            } else {
                $this->result['message'] = JText::_('This video not found.');
            }
        
		
        return json_encode($this->result);
    }
	private function vimeoGetVideo($video_id) {
        $hashedName = md5('vimeo-'. strtotime("now"). $video_id);
        $filename = $hashedName . '.jpg';
        $videoObj = NULL;       
        $video = @simplexml_load_file("http://vimeo.com/api/v2/video/".$video_id.".xml");		
            if ($video) {				
                $video_image = (string) $video->video->thumbnail_large;
                $videoObj = new stdClass();
                $content = file_get_contents($video_image);
                file_put_contents($this->videoOriginalDir . '/' . $hashedName . '.jpg', $content);
                file_put_contents($this->videothumbDir . '/' . $hashedName . '.jpg', $content);
                file_put_contents($this->videolargeDir . '/' . $hashedName . '.jpg', $content);
                file_put_contents($this->videossthumbDir . '/' . $hashedName . '.jpg', $content);
                  if (file_exists($this->videothumbDir . '/' . $hashedName . '.jpg')) {
                    BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videothumbDir . "/{$filename}", $this->params->get('thumb_width', 128), $this->params->get('thumb_height', 96));
					}
				if (file_exists($this->videossthumbDir . '/' . $hashedName . '.jpg')) {
                    BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videossthumbDir . "/{$filename}", $this->params->get('ss_thumb_width', 70), $this->params->get('ss_thumb_height', 40));
					}
				 if (file_exists($this->videolargeDir . '/' . $hashedName . '.jpg')) {
                    BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videolargeDir . "/{$filename}", $this->params->get('crop_width', 600), $this->params->get('crop_height', 400));
					}
                    $videoObj->name = (string) $video->video->title;
                    $videoObj->id_video = $video_id;
                    $videoObj->thumb_video =  $filename;
                    $videoObj->source_of_media = 'Vimeo Server';
                    $videoObj->media_type = 'video';

                    $this->sessionStore('video_vimeo', $videoObj, $this->videoVimeo);

                    $rs_html = $this->createHTML($hashedName, $videoObj->thumb_video, $videoObj->name, 'video_vimeo');

                    $this->result['success'] = TRUE;
					$this->result['title'] =(string) $video->video->title;
					$this->result['description'] =(string)$video->video->description;
					$this->result['embed'] =(string)('<iframe  src="http://player.vimeo.com/video/'.$video_id.'?autoplay=1&title=0" frameborder="0"  webkitAllowFullScreen mozallowfullscreen allowFullScreen allowfullscreen></iframe>');
                    $this->result['message'] = JText::_('Video has been get successfully!');
                    $this->result['url'] = 'https://vimeo.com/'.$video_id;
                    $this->result['data'] = $rs_html;
                
            }
			else{
			 $this->result['message'] = JText::_('This video not found.');
			}
      
        return json_encode($this->result);
    }
	private function dailymotionGetVideo($video_id) {
	
        $hashedName = md5('dailymotion-'. strtotime("now") .$video_id);
        $filename = $hashedName . '.jpg';
		$pid =JRequest::getVar('id');		
        $videoObj = NULL;       
        $videoObj = new stdClass();
        $video_image = 'http://www.dailymotion.com/thumbnail/video/' . $video_id ;		
        $description =get_meta_tags("http://www.dailymotion.com/video/".$video_id.'?fields=title');
		$urlFeed=simplexml_load_file("http://www.dailymotion.com/api/oembed?url=http://www.dailymotion.com/video/".$video_id."&format=xml");
		$title= $urlFeed->title;     
		$desc = ($description['description']); 
            if ($urlFeed) {
                if (copy($video_image, $this->videoOriginalDir . '/' . $hashedName . '.jpg')) {
					if (copy($video_image, $this->videothumbDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videothumbDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videothumbDir . "/{$filename}", $this->params->get('thumb_width', 336), $this->params->get('thumb_height', 180), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
					if (copy($video_image, $this->videolargeDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videolargeDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videolargeDir . "/{$filename}", $this->params->get('crop_width', 600), $this->params->get('crop_height', 400), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
					if (copy($video_image, $this->videossthumbDir . '/' . $hashedName . '.jpg')) {				
						if (file_exists($this->videossthumbDir . '/' . $hashedName . '.jpg')) {
							BTImageHelper::resize($this->videoOriginalDir . "/{$filename}", $this->videossthumbDir . "/{$filename}", $this->params->get('ss_thumb_width', 70), $this->params->get('ss_thumb_height', 40), $this->params->get('thumbimgprocess', 1),$this->params->get('crop-position', 'c'), $this->params->get('jpeg-compression', 100));
						}
					}
                        $videoObj->name = (string) $title;
                        $videoObj->description = (string) $desc;
                        $videoObj->id_video = $video_id;
                        $videoObj->thumb_video = $filename;
                        $videoObj->source_video = 'dailymotion Server';
                        $videoObj->video_type = 'video';                   
						$this->sessionStore('video_dailymotion', $videoObj, $this->videodailymotion);
                        $rs_html = $this->createHTML($hashedName, $videoObj->thumb_video, $videoObj->name, 'video_dailymotion');						
                        $this->result['success'] = TRUE;						
						$this->result['title'] =(string) $title;
						$this->result['description'] =(string) $desc;
						$this->result['embed'] =(string)('<iframe frameborder="0" src="http://www.dailymotion.com/embed/video/'.trim($video_id).'?autoplay=1&logo=0&info=0"></iframe>');
                        $this->result['message'] = JText::_('Video has been get successfully!');
                        $this->result['data'] = $rs_html;  
						$this->result['url'] = 'http://www.dailymotion.com/video/'.$video_id;					
                    
				}
                 else {
                    $this->result['message'] = JText::_('Video not found');
                }
            } else {
                $this->result['message'] = JText::_('This video not found.');
            }
        
		
        return json_encode($this->result);
    }
	 private function createDir($dir_name) {
        if (!is_dir($dir_name)) {
            mkdir($dir_name, 0777);
            chmod($dir_name, 0777);
        }
    }
	private function sessionStore($session_name, $value, $temp) {
        $temp = $this->sessions->get($session_name);
        $temp[] = $value;
        $this->sessions->set($session_name, $temp);
    }
	
	 private function checkFileInSession($session_name, $file_path) {
        $is_exist = false;
        $objFiles = $this->sessions->get($session_name);
        if ($objFiles != null) {
            foreach ($objFiles as $objFile) {
                if ((string) $objFile->id_video == $file_path) {
                    $is_exist = TRUE;
                    break;
                }
            }
        }
        return $is_exist;
    }
	
	 private function createHTML($id, $m_path, $name, $session_name) {
        $html = array();		
        $html[] = '<li>';
		$html[]=  '<input class="input-default" title="Make default" name="default_image" type="radio" value="' . $m_path . '" />';
        $html[] = '<img class="img-thumb" src="'.JURI::root() . $this->params->get('images_path','images/bt_portfolio').'/tmp'.'/thumb/' . $m_path . '"/>';
		$html[] = '<input type="hidden" name="image_id[]" value="0" />';
        $html[] = '<input type="hidden" name="image_filename[]" value="' . $m_path . '" /><br/>';             
        $html[] ='<a href="javascript:void(0)" class="edit" onclick="editImage(this)">Edit</a>';
		$html[] ='<a href="javascript:void(0)" class="remove" onclick="removeImage(this)" >Remove</a>';
        $html[] = '</li>';
        return implode($html);
    } 
	 
	
}
?>