		
		
		
		<?php 
			
			$doc		= JFactory::getDocument();
			$lang 		= JFactory::getLanguage();
			$app		= JFactory::getApplication();
			$menu 		= $app->getMenu();
			$menuItem 	= $menu->getActive();
			if ($menu->getActive() != $menu->getDefault($lang->getTag())) : ?>	
			
			<?php if (!$this->countModules('background_slideshow')) : ?>
				<div id="page-heading" class="page-heading">
					<div class="page-heading-inner">
						<div class="container">
							<div class="pageheading-title">
								<div class="pageheading-title-inner">
									<?php 
									if ((JRequest::getCmd('view')=='portfolio')) {
										echo $doc->getTitle();
									}else {
										if($menuItem ){
											$page_heading = ($menuItem->params->get('page_heading')) ? $menuItem->params->get('page_heading') : $menuItem->title;
											echo $page_heading;
										}else{
											echo $doc->getTitle();
										}
									}
									?>	
								</div>
							</div>
						</div>
					</div>	
				</div>	
				
			<?php endif; ?>
		<?php endif; ?>