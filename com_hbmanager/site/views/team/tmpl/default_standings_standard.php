<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
// $ = $this->show['standings_params'][''];
?>

			<div class="hbstandings">

				<table>
					<thead>
						<tr>
							<th><span class="hidden-phone"><?php echo JText::_('COM_HBMANAGER_STANDINGS_RANK');?></span></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_TEAM');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_GAMES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_WINS');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_TIES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_LOSSES');?></th>
							<th colspan="3" class="hidden-phone"><?php echo JText::_('COM_HBMANAGER_STANDINGS_GOALS');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_STANDINGS_GOALDIFFERENCE');?></th>
							<th colspan="3"><?php echo JText::_('COM_HBMANAGER_STANDINGS_POINTS');?></th>
						</tr>
					</thead>
					
					<tbody>
						<?php
						foreach ($this->standings as $row) {
							// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($row);echo'</pre>';
							?>
							<tr>
								<td class="number"><?php echo $row->rank ?></td>
								<td><span class="hidden-phone"><?php echo $row->team ?></span><span class="visible-phone"><?php echo $row->teamname_short ?></span></td>
								<td class="number"><?php echo $row->games ?></td>
								<td class="number"><?php echo $row->wins ?></td>
								<td class="number"><?php echo $row->ties ?></td>
								<td class="number"><?php echo $row->losses ?></td>
								<td class="hidden-phone right"><?php echo $row->goalsPos ?></td>
								<td class="hidden-phone dots">:</td>
								<td class="hidden-phone left"><?php echo $row->goalsNeg ?></td>
								<td class="number"><?php echo $row->goalsDiff ?></td>
								<td class="number right"><?php echo $row->pointsPos ?></td>
								<td class="dots">:</td>
								<td class="number left"><?php echo $row->pointsNeg ?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>

				<p><?php echo JText::_('COM_HBMANAGER_TEAM_UPDATE_DATE') ?>: <?php echo JHtml::_('date', $team->updateStandings, 'd.m.y', $this->tz) ?> | 
					<?php echo JText::_('COM_HBMANAGER_TEAM_REF_LEAGUE') ?>: <a href="<?php echo HbmanagerHelper::get_hvw_page_url($team->leagueIdHvw) ?>" target="_BLANK"><?php echo $team->league ?> (<?php echo $team->leagueKey ?>)</a>
				</p>

			</div>