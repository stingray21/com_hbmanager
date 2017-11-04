<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
$indicator = $this->show['schedule_params']['indicator'];
$reports = $this->show['schedule_params']['reports'];
?>

			<div class="hbschedule">
		
				<table>
					<thead>
						<tr>
							<th></th> 
							<th colspan="2"><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_WHEN')?></th>
							<th><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_GYM')?></th>
							<th><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_HOMETEAM')?></th>
							<th></th>
							<th><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_AWAYTEAM')?></th>
							<th colspan="<?php echo $indicator ? 4 : 3;?>"><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_RESULT')?></th>
							<?php echo $reports ? '<th> </th>' : '';?>
						</tr>
					</thead>
					
				
					<tbody>
					<?php foreach ($this->schedule as $row) : ?>
						<tr>
							<td><?php echo JHtml::_('date', $row->dateTime, 'D', $this->tz)?></td>
							<td><?php echo JHtml::_('date', $row->dateTime, 'j. M.', $this->tz)?></td>
							<td><?php echo JHtml::_('date', $row->dateTime, 'H:i', $this->tz)?> <?php echo JText::_('COM_HBMANAGER_TEAM_CLOCK')?></td>
							<td><?php echo $row->gymId ?></td>
							<td><?php echo $row->home ?></td>
							<td>-</td>
							<td><?php echo $row->away ?></td>
						<?php if ($row->comment == "abge..") : ?>
							<td colspan="3"><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_CANCELED') ?></td>
						<?php else : ?>
							<td class="<?php echo $row->heimspiel ? ' ownTeam' : '';?>"><?php echo $row->goalsHome;?></td>
							<td><?php echo ($row->goalsHome != '') ? ':' : '';?></td>
							<td class="<?php echo !$row->heimspiel ? ' ownTeam' : '';?>"><?php echo $row->goalsAway;?></td>
						<?php endif; ?>

						<?php if ($indicator) : ?>	
							<td>
							<?php if (!empty($row->toreHeim) && !empty($row->toreGast)) : ?>
								<span class="<?php echo $row->ampel ?>"></span>
							<?php endif; ?>
							</td>
						<?php endif; ?>

						<?php if ($reports) : ?> 
							<td>
								<?php if (!empty($row->report)) : ?>
								<a href="<?php echo $row->reportLink ?>">
									<img src="<?php JURI::root().'com_hbmanager/images/page_white_text.png' ?>" title="<?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_TO_REPORT') ?>" alt="<?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_TO_REPORT')?>" />
								</a>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						</tr>

					<?php endforeach; ?>
					</tbody>
				</table>
				<p><?php echo JText::_('COM_HBMANAGER_TEAM_UPDATE_DATE') ?>: <?php echo JHtml::_('date', $team->updateSchedule, 'd.m.y', $this->tz) ?> | 
					<?php echo JText::_('COM_HBMANAGER_TEAM_REF_LEAGUE') ?>: <a href="<?php echo HbmanagerHelper::get_hvw_page_url($team->leagueIdHvw) ?>" target="_BLANK"><?php echo $team->league ?> (<?php echo $team->leagueKey ?>)</a>
				</p>

			</div>
