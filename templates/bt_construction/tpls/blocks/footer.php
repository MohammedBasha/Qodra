<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- FOOTER -->
<footer id="t3-footer" class="wrap t3-footer">

	<?php if ($this->checkSpotlight('footnav', 'footer-1, footer-2, footer-3, footer-4')) : ?>
		<!-- FOOT NAVIGATION -->
		<div class="footerBlock_module">
			<div class="container">
				<?php $this->spotlight('footnav', 'footer-1, footer-2, footer-3, footer-4') ?>
				
			</div>
		</div>
		<!-- //FOOT NAVIGATION -->
	<?php endif ?>
		
		<div class="container" style="position:relative;">
			<a class="backToTop" href="#"><i class="fa fa-angle-double-up"></i></a>
			<script type='text/javascript'>
				jQuery(document).ready(function(){
					jQuery('a.backToTop').click(function(){
						jQuery('html, body').animate({scrollTop:0}, 'slow');
						return false;
					 });
				});
			</script>
		</div>
		
	<section class="t3-copyright">
		<div class="container">
			<div class="menuFooter">
				<jdoc:include type="modules" name="<?php $this->_p('menu_footer') ?>" />
			</div>
			<div class="copyRight">
				<jdoc:include type="modules" name="<?php $this->_p('copyright_footer') ?>" />
			</div>
			<div style="clear:both;"></div>
		</div>
	</section>

</footer>
<!-- //FOOTER -->