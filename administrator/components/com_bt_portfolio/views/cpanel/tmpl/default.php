<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		3.0.6
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$i = 0;
?>
<?php if(!$this->legacy): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
<?php endif;?>	
<div class="adminform">
	<div class="cpanel-left">
		<div class="cpanel">
		<?php foreach ($this->buttons as $button){
			$i ++;
		?>
			<div class="icon-wrapper">
				<div class="icon">
					<a class="<?php echo $button['class']?>" rel="<?php echo $button['rel']?>" href="<?php echo $button['link']; ?>">
						<img src="<?php echo $button['image']?>" />
						<span><?php echo $button['text']; ?></span></a>
				</div>
			</div>
		<?php
			if($i%3 == 0) echo '<br clear="all">';
		} ?>
		</div>
	</div>
	<div class="cpanel-right">
	<div class="bt-description">
		<fieldset class="adminform">
			<h3>BT Portfolio component for Joomla 2.5 & 3.x</h3>
			<a href="http://bowthemes.com" target="_blank"><img src="<?php echo JURI::root().'components/com_bt_portfolio/assets/icon/portfolio.png';?>"></a>
			<p align="justify">
                BT Portfolio is a Joomla component which allows you to present your favorite projects on your website. BT Portfolio, indeed, is built to help users manage the portfolio playlist with further description, photo gallery and other extra fields included.
			</p>
			<br clear="both" />
			<h3>Components features:</h3>
			<ul class="list-style">
				<li>Multi-level management of categories.</li>
				<li>Ability to manage every single photo album under each portfolio and to select your featured photo for the album.</li>
				<li>Good management of voting and review.</li>
				<li>Ability to well manage the extra fields in accordance with category and portfolio items.</li>
				<li>Excellent management of layout templates (Easy to add or modify custom templates).</li>
				<li>Auto photo cropping and resizing.</li>
				<li>Friendly SEF supported.</li>
				<li>Free breadcrumbs supported.</li>
				<li>"Search" Plugin included.</li>
				<li>"Article" Plugin included (which allows users to insert projects into articles).</li>
				<li>Easy layout adjustment ( in Details or Thumbnails).</li>
				<li>Responsive layout supported</>
				<li>Fully compatible with Joomla!2.5 & 3.x</li>
				<li>Cross Browser Support: IE7+, Firefox 2+, Safari 3+, Chrome 8+, Opera 9+.</li>
				<li>Video tutorials and forum support provided.</li>
			</ul>
			<br />
			<br />
			<p>
				<b>Your current versions is 3.0.9. <a target="_blank" href="http://bowthemes.com">Find our latest versions now</a></b>
			</p>

			<p>
				<b><a target="_blank" href="http://bowthemes.com/showcase/joomla-templates.html">Bow Themes Joomla templates</a> |
				<a target="_blank" href="http://bowthemes.com/showcase/joomla-extensions.html">Bow Themes Joomla extension</a></b>
			</p>

		</fieldset>
	</div>
	</div>
</div>
</div>