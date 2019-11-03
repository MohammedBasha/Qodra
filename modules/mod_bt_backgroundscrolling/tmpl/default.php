<?php
/**
 * @package 	BT BACKGROUND SCROLLING HTML
 * @version		1.0.0
 * @created		Jan 2014
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;
?>

<div id="bgscrolling_<?php echo $module->id ?>" class="bgscrolling<?php echo $moduleclass_sfx ?>" style="height:<?php echo $params->get('height'); ?>px;background-image:url(<?php echo $params->get('background'); ?>)">
	<?php echo $params->get('text');?>
</div>
<style>.bgscrolling{background-repeat:no-repeat;background-attachment:fixed;background-size:100% auto!important;overflow:hidden;}</style>
<script type="text/javascript">(function ($){$(document).ready(function ($){$('#bgscrolling_<?php echo $module->id ?>').parallax("50%", <?php echo $params->get('speedFactor',0.1) ?>);});})(jQuery);</script>