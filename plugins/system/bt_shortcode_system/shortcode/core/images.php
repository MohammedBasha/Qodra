<?php
/**
 * @package 	bt_article_gallery - BT Article Gallery 
 * @version		1.0
 * @created		July 2014
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

if (!class_exists('BTShortcodeImageHelper')) {
	class BTShortcodeImageHelper extends JObject {
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

		static function resize($imgSrc, $imgDest, $dWidth, $dHeight, $cropCenter = true, $quality = 80) {
			$info = getimagesize($imgSrc, $imageinfo);
			$sWidth = $info[0];
			$sHeight = $info[1];
			
			if ($sHeight / $sWidth > $dHeight / $dWidth) {
				if($cropCenter){
					$width = $sWidth;
					$height = round(($dHeight * $sWidth) / $dWidth);
					$sx = 0;
					$sy = round(($sHeight - $height) / 3);
				}else{
					$width = $sWidth;
					$sx = 0;
					$height = round(($dHeight * $sWidth) / $dWidth);
					$sy = 0;
				}
			}
			else {
				if($cropCenter){
					$height = $sHeight;
					$width = round(($sHeight * $dWidth) / $dHeight);
					$sx = round(($sWidth - $width) / 2);
					$sy = 0;
				}else{
					$height = $sHeight;
					$sy = 0;
					$width = round(($sHeight * $dWidth) / $dHeight);;
					$sx = 0;
				}
			}

			/*if (!$cropCenter) {
				// fit
				if($sHeight / $sWidth > $dHeight / $dWidth)
				$sx = 0;
				$sy = 0;
				$width = $sWidth;
				$height = $sHeight;
			}*/

			//echo "$sx:$sy:$width:$height";die();

			$ext = str_replace('image/', '', $info['mime']);
			$imageCreateFunc = self::getImageCreateFunction($ext);
			$imageSaveFunc = self::getImageSaveFunction(JFile::getExt($imgDest));

			$sImage = $imageCreateFunc($imgSrc);
			$dImage = imagecreatetruecolor($dWidth, $dHeight);

			// Make transparent
			if ($ext == 'png') {
				imagealphablending($dImage, false);
				imagesavealpha($dImage, true);
				$transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
				imagefilledrectangle($dImage, 0, 0, $dWidth, $dHeight, $transparent);
			}

			imagecopyresampled($dImage, $sImage, 0, 0, $sx, $sy, $dWidth, $dHeight, $width, $height);

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
		static function createImage($imgSrc, $imgDest, $width, $height, $cropCenter = true, $quality = 80) {
			if (JFile::exists($imgDest)) {
				$info = getimagesize($imgDest, $imageinfo);
				// Image is created
				if (($info[0] == $width) && ($info[1] == $height)) {
					return;
				}
			}
			self::resize($imgSrc, $imgDest, $width, $height, $cropCenter, $quality);
		}
	}
}
?>
