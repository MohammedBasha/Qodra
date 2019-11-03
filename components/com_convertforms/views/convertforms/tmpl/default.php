<?php

/**
 * @package         Convert Forms
 * @version         2.3.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;

jimport('joomla.filesystem.file');

$downloadKey = NRFramework\Functions::getDownloadKey();

if (JComponentHelper::getParams('com_convertforms')->get('show_update_notification', true))
{
	echo \NRFramework\HTML::checkForUpdates('com_convertforms');
}

$canAccessOptions     = Helper::authorise('core.admin');
$canAccessForms       = Helper::authorise('convertforms.forms.manage');
$canAccessSubmissions = Helper::authorise('convertforms.submissions.manage');
$canAccessCampaigns   = Helper::authorise('convertforms.campaigns.manage');

?>

<div class="row-fluid dashboard">
	<span class="span8">
		<div class="row-fluid">
			<div class="clearfix">
				<ul class="nr-icons clearfix">
					<?php if ($canAccessForms) { ?>
					<li>
						<a href="javascript: newForm()">
							<span class="icon-pencil-2"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_NEW_FORM") ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=forms">
							<span class="icon-list-2"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_FORMS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessCampaigns) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=campaigns">
							<span class="cf-icon-megaphone"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_CAMPAIGNS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessSubmissions) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=conversions">
							<span class="icon-users"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_CONVERSIONS") ?></span>
						</a>
					</li>
					<?php } ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=addons">
							<span class="icon-puzzle"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_ADDONS") ?></span>
						</a>
					</li>
					<?php if ($canAccessForms) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=forms&layout=import">
							<span class="icon-box-remove"></span>
							<span><?php echo JText::_("NR_IMPORT") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessOptions) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_config&view=component&component=com_convertforms&path=&return=<?php echo MD5(JURI::base()."index.php?option=com_convertforms") ?>">
							<span class="icon-options"></span>
							<span><?php echo JText::_("JOPTIONS") ?></span>
						</a>
					</li>
					<?php } ?>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/convert-forms/docs" target="_blank">
							<span class="icon-info"></span>
							<span><?php echo JText::_("NR_KNOWLEDGEBASE")?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<?php if ($canAccessSubmissions) { ?>
		<div class="row-fluid" style="margin-top:10px;">
			<div class="span6">
				<div class="well nr-well-white">
				<h3><?php echo JText::_("COM_CONVERTFORMS_LEADS") ?></h3>
				<?php include "panel.stats.php"; ?>
				</div>
			</div>
			<div class="span6">
				<div class="well nr-well-white">
					<h3>Latest Submissions</h3>
					<?php include "latest.leads.php"; ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</span>
	<span class="span4">
		<?php if (!$downloadKey) { ?>
			<div class="alert alert-danger">
				<h3><?php echo JText::_("NR_DOWNLOAD_KEY_MISSING") ?></h3>
				<p><?php echo JText::sprintf("NR_DOWNLOAD_KEY_HOW", "<b>".JText::_("COM_CONVERTFORMS")."</b>"); ?></p>
				<p><a class="btn btn-small btn-success" href="<?php echo JURI::base() ?>index.php?option=com_plugins&view=plugins&filter_search=novarain">
					<?php echo JText::_("NR_DOWNLOAD_KEY_UPDATE")?>
				</a>
				</p>
			</div>
		<?php } ?>

		<?php echo JHtml::_('bootstrap.startAccordion', "info", array('active' => 'slide0')); ?>

		<!-- Information Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_INFORMATION"), 'slide0'); 
			include "panel.info.php";	
			echo JHtml::_('bootstrap.endSlide');
		?>

		<!-- Documentation Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_KNOWLEDGEBASE"), 'slide1'); 
			include "panel.docs.php";
			echo JHtml::_('bootstrap.endSlide');
		?>

		<!-- Translations Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_HELP_WITH_TRANSLATIONS"), 'slide2'); 
			include "panel.translations.php";
			echo JHtml::_('bootstrap.endSlide');
		?>

		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</span>
</div>
<hr>
<?php include_once(JPATH_COMPONENT_ADMINISTRATOR . '/layouts/footer.php'); ?>

<script>
	function newForm() {
        url = 'index.php?option=com_convertforms&view=templates&tmpl=component';
        SqueezeBox.open(url, { handler: 'iframe', size: {x: 1100, y: 635}});
    }
</script>
