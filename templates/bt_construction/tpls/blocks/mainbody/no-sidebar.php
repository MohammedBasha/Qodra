<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Mainbody 1 columns, content only
 */
?>

<div id="t3-mainbody" class="t3-mainbody">
	<div class="t3-mainbody-inner">
		<div class="container">
			<div class="row">

				<!-- MAIN CONTENT -->
				<div id="t3-content" class="t3-content col-xs-12">
					<div class="t3-content-inner">
						<?php if($this->hasMessage()) : ?>
						<jdoc:include type="message" />
						<?php endif ?>
						<jdoc:include type="component" />
					</div>
				</div>
				<!-- //MAIN CONTENT -->

			</div>
		</div> 
	</div> 
</div> 