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

JFactory::getDocument()->addScriptDeclaration("
	Joomla.checkAll = function(box) 
	{
		var state = false;
		// console.log(box);
		if (box.checked) state = true;
			
		var els = document.getElementsByClassName('selectBox');
		// console.log(els);
		[].forEach.call(els, function (el) {
			el.getElementsByTagName('input')[0].checked = state;
		});
	}
");

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
// $form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
// 				'/models/forms/hbdates.xml');

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';

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
							<?php echo $form->getLabel('prevStart', 'gameDates'); ?>
						</dt>
						<dd>
							<?php echo $form->getInput('prevStart', 'gameDates', $this->dates['prevStart']); ?>
						</dd>
						
						<dt>
							<?php echo $form->getLabel('prevEnd', 'gameDates'); ?>
						</dt>
						<dd>
							<?php echo $form->getInput('prevEnd', 'gameDates', $this->dates['prevEnd']); ?>
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

			
			<input type="hidden" name="gameDates[prevStart]" id="gameDates[prevStart]" value="<?php echo $this->dates['prevStart']?>" />
			<input type="hidden" name="gameDates[prevEnd]" id="gameDates[prevEnd]" value="<?php echo $this->dates['prevEnd']?>" />

			<?php if (!empty($this->games)) : ?>
				<?php foreach ($this->games as $date => $day) :
					//$link = JRoute::_('index.php?option=com_hbmanager&task=team.edit&id=' . $row->id);
				?>
					<h3><?php echo JText::_('COM_HBMANAGER_GAMES_DAY_HEADLINE').' '.JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></h3>
					<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th width="1%" class="selectBox">
								<?php echo JText::_('COM_HBMANAGER_GAMES_NUM'); ?>
								<input name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="<?php echo JText::_('JGLOBAL_CHECK_ALL')?>" type="checkbox">
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
										<td class="selectBox"><?php echo HbmanagerHelper::formatInput($form->getInput('includeToNews', 'gamesprev', null), $i)?>
											<?php echo HbmanagerHelper::formatInput($form->getInput('gameIdHvw', 'gamesprev', $game->gameIdHvw), $i)?>
											<?php echo HbmanagerHelper::formatInput($form->getInput('season', 'gamesprev', $game->season), $i)?>	
											<?php echo HbmanagerHelper::formatInput($form->getInput('reportID', 'gamesprev', $game->reportID), $i)?>	
										</td>
										<td><?php echo HbmanagerHelper::getformatedTime($game->dateTime, $tz); ?></td>
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