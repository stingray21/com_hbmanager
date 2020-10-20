<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
$indicator = $this->show['schedule_params']['indicator'];
$reports = $this->show['schedule_params']['reports'];
JHTML::_('bootstrap.tooltip');
?>

			<div class="hbschedule">
				<table>
					<thead>
						<tr>
							<th class="hidden-phone"></th> 
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
						<?php //echo __FILE__.' ('.__LINE__.'):<pre>';print_r($row);echo'</pre>';
						$gymDesc = $row->street.'<br>'.$row->zip.' '.$row->town.'<br>'.$row->adhesive;
						$gymLink = 'https://www.google.de/maps/search/'.urlencode($row->gymName).'+'.urlencode($row->zip).'+'.urlencode($row->town);
						?>
						<tr>
							<td class="hidden-phone right"><?php echo JHtml::_('date', $row->dateTime, 'D', $this->tz)?></td>
							<td><span><span class="hidden-phone"><?php echo JHtml::_('date', $row->dateTime, 'j. M.', $this->tz)?></span></span></td>
							<td><span><span class="hidden-phone"><?php echo HbmanagerHelper::getformatedTime($row->dateTime, $this->tz, JText::_('COM_HBMANAGER_TEAM_CLOCK'))?></span>
							 	<span class="visible-phone"><?php echo JHtml::_('date', $row->dateTime, 'd.m.', $this->tz)?> <?php echo HbmanagerHelper::getformatedTime($row->dateTime, $this->tz)?></span></span>
							</td>
							<td><span><?php echo JHTML::tooltip($gymDesc, $row->gymName, '', '<span class="visible-desktop gym">'.$row->gymName.' ('.$row->gymId.')</span>'.'<span class="hidden-desktop">'.$row->gymId.'</span>', $gymLink) ?></span></td>
							<td class="right <?php echo $row->homegame ? 'ownTeam' : '';?>"><span><span class="hidden-phone"><?php echo $row->home ?></span><span class="visible-phone"><?php echo $row->home_abbr ?></span></span></td>
							<td>-</td>
							<td class="<?php echo !$row->homegame ? 'ownTeam' : '';?>"><span><span class="hidden-phone"><?php echo $row->away ?></span><span class="visible-phone"><?php echo $row->away_abbr ?></span></span></td>
						<?php if ($row->comment == "abgesetzt") : ?>
							<td colspan="3" class="comment"><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_CANCELED') ?></td>
						<?php else : ?>
							<td class="<?php echo $row->homegame ? 'ownTeam' : '';?>"><?php echo $row->goalsHome;?></td>
							<td><?php echo ($row->goalsHome != '') ? ':' : '';?></td>
							<td class="<?php echo !$row->homegame ? 'ownTeam' : '';?>"><?php echo $row->goalsAway;?></td>
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
								<a href="<?php echo $row->reportLink ?>" title="<?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_TO_REPORT')?>" alt="<?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE_TO_REPORT')?>">
									<span class="icon-file-2"> </span>
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
