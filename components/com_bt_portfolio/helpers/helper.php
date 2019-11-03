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
defined('_JEXEC') or die;

/**
 * Bt_portfolio component helper.
 */
abstract class Bt_portfolioHelper {

	public static function addSiteScript() {
		$document = JFactory::getDocument();
		$header = $document->getHeadData();
		$params = JComponentHelper::getParams("com_bt_portfolio");
		JHTML::_('behavior.framework');
		$loadJquery = true;
		foreach ($header['scripts'] as $scriptName => $scriptData) {
			if (substr_count($scriptName, '/jquery')) {
				$loadJquery = false;
				break;
			}
		}
		if ($loadJquery) {
			$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.min.js');
		}
		
		$document->addScriptDeclaration('btShowcase = new BT.Showcase(\'' . JURI::root() . '\');');
		$document->addStyleSheet(JURI::root() . 'components/com_bt_portfolio/assets/css/global.css');
		$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/default.js');
		$document->addScript(JURI::root() . 'components/com_bt_portfolio/assets/js/jquery.easing.1.3.js');
		$document->addStyleSheet(COM_BT_PORTFOLIO_THEME_URL . 'css/style.css');
	}
	public static function getRatingPanel($portfolioId, $rating_sum, $rating_count, $canRate = true) {
		$width = 15;
		$height = 15;
		$numOfStar = 5;

		if ($rating_count == 0)
			$rating = 0;
		else
			$rating = ($rating_sum / $rating_count);

		$backgroundWidth = $numOfStar * $width;
		$currentWidth = round($rating * $width);

		$html = '<div class="btp-rating-container-' . $portfolioId . '"><div class="btp-rating-background" style="width: ' . $backgroundWidth . 'px"><div class="btp-rating-current" style="width: ' . $currentWidth . 'px"></div>';

		if ($canRate) {
			for ($i = $numOfStar; $i > 0; $i--) {
				$starWidth = $width * $i;
				$html .= '<a onclick="javascript:btShowcase.rate(' . $portfolioId . ', ' . $i . ')" href="javascript:void(0);" style="width:' . $starWidth . 'px"></a>';
			}
		}

		$html .= '</div>';

		$html .= '<div class="btp-rating-notice">' . sprintf(JText::_('COM_BT_PORTFOLIO_RATING_TEXT'), $rating, $rating_count) . '</div>';

		$html .= '</div>';

		return $html;
	}
	public static function getSocialShare($social_buttons)
	{
		$document = JFactory::getDocument();
		//$document->addScriptDeclaration('var switchTo5x=true;');
		$document->addScript('http://w.sharethis.com/button/buttons.js');
		//$document->addScriptDeclaration('stLight.options({publisher: "349b1eef-d8c5-4c16-81a7-2dcc762843eb"});');
		//$document->addScriptDeclaration('stLight.options({publisher: ""});');
		if (!is_array($social_buttons))
		{
			$social_buttons = array($social_buttons);
		}
		foreach ($social_buttons as $button)
		{
			switch ($button)
			{
				case 1:
					//echo "<span class='st_twitter_hcount' displayText='Tweet' st_via='YourTwitterHandleName' st_msg='#YourHashTag and #YourOtherHashTag'></span>";
					echo "<span class='st_twitter_hcount' displayText='Tweet'></span> ";
					
					break;
				case 2:
					echo "<span class='st_plusone_hcount' displayText='Google +1'></span>";
					break;
				case 3:
					echo "<span class='st_linkedin_hcount' displayText='LinkedIn'></span>";
					break;
				case 4:
					echo "<span class='st_email_hcount' displayText='Email'></span>";
					break;
				case 5:
					echo "<span class='st_facebook_hcount' displayText='Facebook'></span>";
					break;
				case 6:
					echo "<span class='st_fbsend_hcount' displayText='Facebook Send'></span>";
					break;
				case 7:
					echo "<span class='st_fblike_hcount' displayText='Facebook Like'></span>";
					break;
				case 8:
					echo "<span class='st_fbrec_hcount' displayText='Facebook Recommend'></span>";
					break;
				case 9:
					echo "<span class='st_pinterest_hcount' displayText='Pinterest'></span>";
					break;
			}
		}
	}
	public static function getFacebookComment($facebook_app_id,$number_comments,$commmentbox_width){
		$language = JFactory::getLanguage();
		$params = JComponentHelper::getParams("com_bt_portfolio");
		$locales = $language->getLocale();
		$uri =  JFactory::getURI();
		$locale = str_replace('-', '_', substr($locales[0], 0, 5));
		$html = '<fb:comments data-colorscheme="'.$params->get('commmentbox_colorscheme','light').'" href="'.$uri->toString().'" simple="1" numposts="'.$number_comments.'" width="'.$commmentbox_width.'"></fb:comments>
			<div id="fb-root"></div>
			<script>
			  window.fbAsyncInit = function() {
			   FB.init({
			     appId: \''.$facebook_app_id.'\',
			     status: true,
				 cookie: true,
			     xfbml: true
			   });
			 };
			  (function() {
			    var e = document.createElement(\'script\');
			    e.type = \'text/javascript\';
			    e.src = document.location.protocol + \'//connect.facebook.net/'.$locale.'/all.js\';
			    e.async = true;
			    document.getElementById(\'fb-root\').appendChild(e);
			   }());
			</script>';
		if($commmentbox_width == 'auto'){
			$html.='<style type="text/css">.fb_iframe_widget iframe, .fb_iframe_widget, .fb_iframe_widget span { width:100% !important; }</style>';
		}
		return $html;
	}
	public static function getDisqusComment($shortname){
		// Output code
		$html = " <div id='disqus_thread'></div>
		<script type='text/javascript'>
		var disqus_shortname = '".$shortname."';

		(function() {
		var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
		</script>
		<noscript>Please enable Javascript to view the <a href='http://disqus.com/?ref_noscript'>comments powered by Disqus.</a></noscript>
		<a href='http://disqus.com' class='dsq-brlink'>comments powered by <span class='logo-disqus'>Disqus</span></a>";
		return $html;
	}
	public static function checkComponent($name) {
		$path = JPATH_ADMINISTRATOR . '/components/' . $name;
		if (is_dir($path)) {
			return true;
		}
		else {
			return false;
		}
	}
	public static function getPathImage($itemId,$imageType,$imageName,$catid){
		$params = JComponentHelper::getParams("com_bt_portfolio");
		$watermask = $params->get('wm-enabled');
		$path = $params->get('images_path','images/bt_portfolio');
		// zoomtype
		
		if($params->get('zoom_image_type','large') =='large' && $imageType=='original'){
			$imageType = 'large';
		}
		if(($imageType == 'large' && $params->get('largeimgprocess')=='-1') 
		|| ($imageType == 'thumb' && $params->get('thumbimgprocess')=='-1')){
			$imageType= 'original';
		}
		
		if( $imageType == 'ssthumb' || ( !$params->get('wm-thumb',1) && $imageType == 'thumb')){
			$watermask = false;
		}
		
		if(!in_array('all',$params->get('wm-categories',array('all'))) && !in_array($catid,$params->get('wm-categories',array('all'))) ){
			$watermask = false;
		}
		if(!$imageName){
			return COM_BT_PORTFOLIO_THEME_URL . 'images/no-image.jpg';
		}
		if(!$watermask){
			$imagePath = JURI::root() . $path. '/' . $itemId . '/'.$imageType.'/' . $imageName;
		}
		else{
			$imagePath = JRoute::_('index.php?option=com_bt_portfolio&task=portfolio.viewimage&src='.base64_encode($itemId.'|'.$imageName.'|'.$imageType));
		}

		return $imagePath;
	}
	public static function showWmImage(){

		list($itemId,$imageName,$imageType) = explode('|',base64_decode(JRequest::getVar('src')));
		$params = JComponentHelper::getParams("com_bt_portfolio");
		$path = $params->get('images_path','images/bt_portfolio');
		$source = JPATH_ROOT.'/'.$path.'/'. $itemId.'/'.$imageType.'/'.$imageName;
		
		if($params->get('wm-enabled') && $imageType!='ssthumb'){
			require_once JPATH_COMPONENT . '/helpers/watermask/watermask.php';
			$options = BtWaterMask::getWaterMarkOptions();
			$options['padding'] = $params->get('wm-padding',$options['padding']);
			$options['font'] = $params->get('wm-font')? JPATH_COMPONENT . '/helpers/watermask/fonts/'.$params->get('wm-font').'.ttf':$options['font'];
			$options['text'] = $params->get('wm-text',$options['text']);
			$options['image']= $params->get('wm-image')? JPATH_ROOT.'/'.$params->get('wm-image'):$options['image'];
			$options['type'] = $params->get('wm-type',$options['type']);
			$options['fcolor'] = $params->get('wm-fcolor',$options['fcolor']);
			$options['fsize'] = $params->get('wm-fsize',$options['fsize']);
			$options['bg'] = $params->get('wm-bg',$options['bg']);
			$options['bgcolor'] = $params->get('wm-bgcolor',$options['bgcolor']);
			$options['factor'] = $params->get('wm-factor',$options['factor']);
			$options['position'] = $params->get('wm-position',$options['position']);
			$options['opacity'] = $params->get('wm-opacity',$options['opacity']);
			$options['rotate'] = $params->get('wm-rotate',$options['rotate']);
			BtWaterMask::createWaterMark($source,$options);

		}else{
		$size = getimagesize($source);
		$imagetype = $size[2];
		 switch($imagetype) {
			  case(1):
				header('Content-type: image/gif');
				$image = imagecreatefromgif($source);
				imagegif($image);
				break;

			  case(2):
			  	$image = imagecreatefromjpeg($source);
				header('Content-type: image/jpeg');
				imagejpeg($image);
				break;

			  case(3):
				header('Content-type: image/png');
				$image = imagecreatefrompng($source);
				imagepng($image);
				break;

			  case(6):
				header('Content-type: image/bmp');
				$image = imagecreatefrombmp($source);
				imagewbmp($image);
				break;

			  }
		}
		exit;
	}
	public static function getPrintButton($type,$portfolio_id){
		$print_icon = COM_BT_PORTFOLIO_THEME_URL . 'images/print-button.gif';
		if($type == 0){
			return '<div class="print_button"><a onclick="window.print();return false;" href="#"><img alt="Print" src="'.$print_icon.'"></a></div>';
		}
		else{
			$print_url = JRoute::_('index.php?tmpl=component&option=com_bt_portfolio&view=portfolio&layout=print&id='.$portfolio_id);
			return '<a class="print_button" rel="nofollow" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" title="Print" href="'.$print_url.'"><img alt="Print" src="'.$print_icon.'"></a>';
		}	
	}
	public static function extractUrl($string){
		$regex = "/\<iframe.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/Us";
		preg_match($regex, $string, $matches);
		$images = (count($matches)) ? $matches : array();
		if (count($images)) {
			return $images[1];
		}
		return '';
	}
}
