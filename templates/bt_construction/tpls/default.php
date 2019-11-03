<?php
/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org 
 *------------------------------------------------------------------------------
 */


defined('_JEXEC') or die;
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
	  class='<jdoc:include type="pageclass" />'>

<head>
	<jdoc:include type="head" />
	<?php $this->loadBlock('head') ?>
</head>

<body>
<?php $wfk='PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDotOTk5OXB4OyI+DQo8YSBocmVmPSJodHRwOi8vam9vbWxhbG9jay5jb20iIHRpdGxlPSJKb29tbGFMb2NrIC0gRnJlZSBkb3dubG9hZCBwcmVtaXVtIGpvb21sYSB0ZW1wbGF0ZXMgJiBleHRlbnNpb25zIiB0YXJnZXQ9Il9ibGFuayI+QWxsIGZvciBKb29tbGE8L2E+DQo8YSBocmVmPSJodHRwOi8vYWxsNHNoYXJlLm5ldCIgdGl0bGU9IkFMTDRTSEFSRSAtIEZyZWUgRG93bmxvYWQgTnVsbGVkIFNjcmlwdHMsIFByZW1pdW0gVGhlbWVzLCBHcmFwaGljcyBEZXNpZ24iIHRhcmdldD0iX2JsYW5rIj5BbGwgZm9yIFdlYm1hc3RlcnM8L2E+DQo8L2Rpdj4='; echo base64_decode($wfk); ?>
<?php $wrapper_class = ""; ?>
<?php if ($this->countModules('background_slideshow')) : 
	$wrapper_class = "has_slideshow";
endif; 
?>
<div class="t3-wrapper <?php echo $wrapper_class; ?>"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->

	<?php $this->loadBlock('background_slideshow') ?>
	
	<?php $this->loadBlock('page_heading') ?>
	
	<?php $this->loadBlock('header') ?>

	<?php //$this->loadBlock('mainnav') ?>
	
	<?php $this->loadBlock('top_mainbody') ?>

	<?php $this->loadBlock('spotlight-1') ?>

	<?php $this->loadBlock('spotlight-2') ?>
	
	<?php $this->loadBlock('mainbody') ?>

	<?php $this->loadBlock('bottom_mainbody') ?>

	<?php $this->loadBlock('navhelper') ?>

	<?php $this->loadBlock('footer') ?>

</div>

<script type="text/javascript" src="<?php echo T3_TEMPLATE_URL ?>/js/script.js"></script>

</body>

</html>