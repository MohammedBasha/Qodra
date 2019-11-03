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
// No direct access
defined('_JEXEC') or die;

jimport( 'joomla.filesystem.file' );

if(!class_exists('BTImageHelper'))
{
	class BTImageHelper extends JObject {
		static function getImageCreateFunction($type) {
			switch ($type) {
				case 'jpeg':
				case 'jpg':
					$imageCreateFunc = 'imagecreatefromjpeg';
					break;

				case 'png':
					$imageCreateFunc = 'imagecreatefrompng';
					break;

				case 'bmp':
					$imageCreateFunc = 'imagecreatefrombmp';
					break;

				case 'gif':
					$imageCreateFunc = 'imagecreatefromgif';
					break;

				case 'vnd.wap.wbmp':
					$imageCreateFunc = 'imagecreatefromwbmp';
					break;

				case 'xbm':
					$imageCreateFunc = 'imagecreatefromxbm';
					break;

				default:
					$imageCreateFunc = 'imagecreatefromjpeg';
			}

			return $imageCreateFunc;
		}

		static function getImageSaveFunction($type) {
			switch ($type) {
				case 'jpeg':
					$imageSaveFunc = 'imagejpeg';
					break;

				case 'png':
					$imageSaveFunc = 'imagepng';
					break;

				case 'bmp':
					$imageSaveFunc = 'imagebmp';
					break;

				case 'gif':
					$imageSaveFunc = 'imagegif';
					break;

				case 'vnd.wap.wbmp':
					$imageSaveFunc = 'imagewbmp';
					break;

				case 'xbm':
					$imageSaveFunc = 'imagexbm';
					break;

				default:
					$imageSaveFunc = 'imagejpeg';
			}

			return $imageSaveFunc;
		}

		static function resize($imgSrc, $imgDest, $dWidth, $dHeight, $crop = true,$crop_pos = 'c', $quality = 100) {
			$info = getimagesize($imgSrc, $imageinfo);
			$sWidth = $info[0];
			$sHeight = $info[1];
			//$ratio1 = $sWidth/$sHeight;
			//$ratio2 = $dWidth/$dHeight;

			if (!$crop) {
				$sx = 0;
				$sy = 0;
				$width = $sWidth;
				$height = $sHeight;
			}
			else{
				if ($sHeight / $sWidth > $dHeight / $dWidth) {
					$width = $sWidth;
					$height = round(($dHeight * $sWidth) / $dWidth);
				}
				else {
					$height = $sHeight;
					$width = round(($sHeight * $dWidth) / $dHeight);
				}
				switch($crop_pos){
					case 'tl':	
						$sx = 0;
						$sy = 0;
						break;
					case 'tm':	
						$sx = round(($sWidth - $width) / 2);
						$sy = 0;
						break;
					case 'tr':	
						$sx = $sWidth - $width;
						$sy = 0;
						break;
					case 'bl':
						$sx = 0;
						$sy =$sHeight - $height;
						break;	
					case 'bm':	
						$sx = round(($sWidth - $width) / 2);
						$sy = $sHeight - $height;
						break;
					case 'br':	
						$sx = $sWidth - $width;
						$sy = $sHeight - $height;
						break;	
					default:
						if ($sHeight / $sWidth > $dHeight / $dWidth) {
							$sx = 0;
							$sy = round(($sHeight - $height) / 2);
						}
						else {
							$sx = round(($sWidth - $width) / 2);
							$sy = 0;
						}
					break;
				}
			
			
			}
			if ($crop ==2){
				$width = $dWidth;
				$height = round($width * $sHeight /$sWidth);
				if($height > $dHeight){
					$height = $dHeight;
					$width = round($height * $sWidth /$sHeight);
				}
				$dWidth  = $width;
				$dHeight = $height;
				$width = $sWidth;
				$height= $sHeight;
				$sx = 0;
				$sy = 0;
			}
			
			

			$ext = str_replace('image/', '', $info['mime']);
			$imageCreateFunc = self::getImageCreateFunction($ext);
			$imageSaveFunc = self::getImageSaveFunction(JFile::getExt($imgDest));

			$sImage = $imageCreateFunc($imgSrc);
			$dImage = imagecreatetruecolor($dWidth, $dHeight);

			// Make transparent
			if ($ext == 'png') {
				imagealphablending($dImage, false);
				imagesavealpha($dImage,true);
				$transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
				imagefilledrectangle($dImage, 0, 0, $dWidth, $dHeight, $transparent);
			}

			imagecopyresampled($dImage, $sImage, 0, 0, $sx, $sy, $dWidth, $dHeight, $width, $height);

			// Initialise variables.
			$FTPOptions = JClientHelper::getCredentials('ftp');
			if ($FTPOptions['enabled'] == 1)
			{
				// Connect the FTP client
				jimport('joomla.client.ftp');
				$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], array(), $FTPOptions['user'], $FTPOptions['pass']);				
				ob_start();
				if ($ext == 'png') {
					$imageSaveFunc($dImage, null, 9);
				}
				else if ($ext == 'gif'){
					$imageSaveFunc($dImage, null);
				}
				else {
					$imageSaveFunc($dImage, null, $quality);
				}
				$buffer = ob_get_contents();
				ob_end_clean();
				// Translate path for the FTP account and use FTP write buffer to file
				$imgDest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $imgDest), '/');
				$ret = $ftp->write($imgDest, $buffer);
				//$ftp->chmode($imgDest,0755);
			}
			else
			{
				if ($ext == 'png') {
					$imageSaveFunc($dImage, $imgDest, 9);
				}
				else if ($ext == 'gif') {
					$imageSaveFunc($dImage, $imgDest);
				}
				else {
					$imageSaveFunc($dImage, $imgDest, $quality);
				}
			}
		}
		static function createImage($imgSrc, $imgDest, $width, $height, $crop = true, $quality = 100) {
			if (JFile::exists($imgDest)) {
				$info = getimagesize($imgDest, $imageinfo);
				// Image is created
				if (($info[0] == $width) && ($info[1] == $height)) {
					return;
				}
			}
			self::resize($imgSrc, $imgDest, $width, $height, $crop, $quality);

		}
	}
}
?>
