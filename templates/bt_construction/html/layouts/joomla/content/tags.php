<?php
/**
 * @package     Joomla.Cms
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
use Joomla\Registry\Registry;


JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

?>
<?php if (!empty($displayData)) : ?>
	<div class="tags">
		<?php foreach ($displayData as $i => $tag) : ?>
			<?php if (in_array($tag->access, JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id')))) : ?>

				<?php $tagParams = new Registry($tag->params); ?>
				<?php $link_class = $tagParams->get('tag_link_class'); ?>

				<span class="<?php echo $link_class; ?> tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i ?>" itemprop="keywords">
					<span class="tag-label">
						<?php echo substr($tag->path, 0, strpos($tag->path, '/')); ?>: 
					</span>
					<span class="tag-info">
						<?php echo $this->escape($tag->title); ?>
					</span>
				</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
