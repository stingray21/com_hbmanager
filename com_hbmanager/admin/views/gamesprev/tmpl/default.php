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

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
// $form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
// 				'/models/forms/hbdates.xml');

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';

?>
<div id="gamesprev">
	<div id="j-sidebar-container" class="span2">
	    <?php 
		echo JHtmlSidebar::render(); 
		JToolBarHelper::preferences('com_hbmanager');
		?>
	</div>


	<div id="j-main-container" class="span10">
		
	<?php
	// get the JForm object
	JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
	$form = JForm::getInstance('gamedateform', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/gamedates.xml');
	?>

		<form action="" method="post" id="gamedatesForm" name="gamedatesForm">
				<fieldset class="gamedates">
					<legend>
						<?php echo JText::_('COM_HBMANAGER_GAMEDATES_LEGEND');	?>
					</legend>
					
					<dl>
						<dt>
							<?php echo $form->getLabel('nextStart', 'gameDates'); ?>
						</dt>
						<dd>
							<?php echo $form->getInput('nextStart', 'gameDates', $this->dates['nextStart']); ?>
						</dd>
						
						<dt>
							<?php echo $form->getLabel('nextEnd', 'gameDates'); ?>
						</dt>
						<dd>
							<?php echo $form->getInput('nextEnd', 'gameDates', $this->dates['nextEnd']); ?>
						</dd>
					</dl>

					<input class="btn" type="submit" name="date_button" id="date_button" value="<?php echo JText::_('COM_HBMANAGER_GAMEDATES_UPDATE_BUTTON');?>"/>
				</fieldset>
		</form>	


	<?php 
	$form = JForm::getInstance('formgamesprev', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/gamesprev.xml');
	$i = 0;
	?>
		<form action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=games.saveReport') ?>" method="post" id="adminForm" name="adminForm">

			<?php if (!empty($this->games)) : ?>
				<?php foreach ($this->games as $date => $day) :
					//$link = JRoute::_('index.php?option=com_hbmanager&task=team.edit&teamId=' . $row->teamId);
				?>
					<h3><?php echo JText::_('COM_HBMANAGER_GAMES_DAY_HEADLINE').' '.JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></h3>
					<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th width="1%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_NUM'); ?>
							</th>
							<th width="5%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_TIME'); ?>
							</th>
							<th width="14%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_TEAM'); ?>
							</th>
							<th width="10%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_SCORE'); ?>
							</th>
							<th width="2%">
								<?php ?>
							</th>
							<th width="">
								<?php echo JText::_('COM_HBMANAGER_GAMES_REPORT'); ?>
							</th>
							<th width="10%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_PLAYERLIST'); ?>
							</th>
							<th width="10%">
								<?php echo JText::_('COM_HBMANAGER_GAMES_EXTRA'); ?>
							</th>
							<th width="2%">
								<?php  ?>
							</th>
						</tr>
						</thead>
						<tbody>
							<?php if (!empty($day)) : ?>
								<?php foreach ($day as $day_i => $game) : ?>
									<tr>
										<td><?php echo HbmanagerHelper::formatInput($form->getInput('includeToNews', 'gamesprev', null), $i)?>
											<?php echo HbmanagerHelper::formatInput($form->getInput('gameIdHvw', 'gamesprev', $game->gameIdHvw), $i)?>
											<?php echo HbmanagerHelper::formatInput($form->getInput('season', 'gamesprev', $game->season), $i)?>	
											<?php echo HbmanagerHelper::formatInput($form->getInput('reportID', 'gamesprev', $game->reportID), $i)?>	
										</td>
										<td><?php echo JHTML::_('date', $game->dateTime , 'H:i', $tz); ?></td>
										<td><?php echo $game->team; ?> <br> (<?php echo $game->teamkey; ?>) <br> <?php echo $game->gameIdHvw ?> <br><br> <?php echo $game->league ?><br><span><?php echo $game->leagueKey ?></span><br>
											<a href="<?php echo HbmanagerHelper::get_hvw_page_url($game->leagueIdHvw); ?>" title="<?php echo JText::_('COM_HBMANAGER_GAMES_HVWLINK'); ?>" target="_BLANK"><?php echo $game->leagueIdHvw; ?></a>
										</td>
										<td><?php echo $game->home; ?> <br>
											<?php echo $game->away; ?> 
										</td>
										<td><?php echo $game->goalsHome; ?><br>
											<?php echo $game->goalsAway; ?>
										</td>
										<td class="report"><?php echo HbmanagerHelper::formatInput($form->getInput('report', 'gamesprev', $game->report), $i) ?></td>
										<td><?php echo HbmanagerHelper::formatInput($form->getInput('playerlist', 'gamesprev', $game->playerList), $i) ?></td>
										<td><?php echo HbmanagerHelper::formatInput($form->getInput('extra', 'gamesprev', $game->extra), $i) ?></td>
										<td>
											<?php  ?>
										</td>
									</tr>
								<?php $i++; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				<?php endforeach; ?>
			<?php endif; ?>


			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="userid" id="userid" value="<?php echo $this->user->id?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>