<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hbmanager
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();

// JFactory::getDocument()->addScriptDeclaration('
// 	Joomla.submitbutton = function(task) 
// 	{
// 		if (task == "teamdata.update")
// 		{
// 			updateCheckedTeams();
// 		}
// 	}
// ');

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
// $form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
// 				'/models/forms/hbdates.xml');

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';

?>
<div id="gamedetails">
	<div id="j-sidebar-container" class="span2">
	    <?php 
		echo JHtmlSidebar::render(); 
		JToolBarHelper::preferences('com_hbmanager');
		?>
	</div>


	<div id="j-main-container" class="span10">
		

	<?php 
	$form = JForm::getInstance('formgamedetails', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/gamesprev.xml');
	$i = 0;
	?>
		<form action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=gamedetails.saveReport') ?>" method="post" id="adminForm" name="adminForm">

			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th width="1%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_NUM'); ?>
					</th>
					<th width="5%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_DATE'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_TEAM'); ?>
					</th>
					<th width="5%"></th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_GAME_ID'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_MATCH'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_REPORT_LINK'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_IMPORT'); ?>
					</th>
					<th width="">
						<?php  ?>
					</th>
				</tr>
				</thead>
				<tbody>
							
			<?php if (!empty($this->games)) : ?>
				<?php foreach ($this->games as $game) :
					//$link = JRoute::_('index.php?option=com_hbmanager&task=team.edit&teamId=' . $row->teamId);
				?>
					<tr class="<?php 
								echo ((!empty($game->reportHvwId) OR isset($game->importFilename)) AND empty($game->timeString)) ? '' : 'hidden';
								?>">
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('gameIdHvw', 'gamesprev', $game->gameIdHvw), $i)?>
							<?php echo HbmanagerHelper::formatInput($form->getInput('season', 'gamesprev', $game->season), $i)?>
						</td>
						<td><?php echo JHTML::_('date', $game->dateTime , 'd.m.Y', $tz); ?></td>
						<td><?php echo $game->team; ?></td>
						<td><a href="<?php echo HbmanagerHelper::get_hvw_page_url($game->leagueIdHvw); ?>" title="<?php echo JText::_('COM_HBMANAGER_GAMES_HVWLINK'); ?>" target="_BLANK"><?php echo $game->leagueKey ?><?php //echo $game->teamkey; ?></a></td>
						<td><?php echo $game->gameIdHvw ?></td>
						<td><?php echo $game->home; ?> - <?php echo $game->away; ?></td>
						<td align="center">
							<a class="btn btn-micro hasTooltip" href="<?php echo HbmanagerHelper::get_hvw_report_url($game->reportHvwId); ?>" title="<?php echo JText::_('COM_HBMANAGER_GAMES_HVWLINK'); ?>" target="_BLANK" title="Download pdf" data-original-title="Download pdf">
								<span class="icon-download" aria-hidden="true"></span> <?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_BTN_DOWNLOAD'); ?>
							</a>
						</td>
						<td><?php if (isset($game->importFilename)) : ?>
							<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="importGameBtn('<?php echo $game->gameIdHvw; ?>');" title="" data-original-title="Update team">
								<span class="icon-signup" aria-hidden="true"></span> <?php echo JText::_('COM_HBMANAGER_GAMEDETAILS_BTN_IMPORT'); ?>
							</a>		
							<?php endif; ?>
						</td>
						<td>
							<?php  ?>
						</td>
					</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
				</tbody>
			</table>
	<?php endif; ?>


			<input type="hidden" name="task" value=""/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>