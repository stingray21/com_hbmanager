<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

echo "\n\n".'<h1>'.JText::_('COM_HBMANAGER_OVERVIEW_DEFAULT_TITLE').'</h1>';

echo "\n\n".'<div id="hboverview">';

foreach ($this->gameDays as $gameDay)
{
	if (!empty($gameDay->games))
	{
		?>
		<div id="<?php echo $gameDay->shortVar;?>Games">	
		<h2><?php echo JText::_('COM_HBMANAGER_OVERVIEW_'.$gameDay->languageVar.'_GAMES')?></h2>
		<?php
		foreach ($gameDay->games as $date => $games)
		{
			?>
			<h3><?php echo JHtml::_('date', $date, 'l, j. F Y', $this->timezone)?></h3>
			<div class="gameday">
			
			<?php
			foreach ($games as $i => $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
				$lastTeam = ($i > 0) ? $games[$i-1]->mannschaft : null;
				if ($lastTeam != $game->mannschaft) {
					?>
					<div class="team-set">
					<h4><a href="<?php 
						echo JURI::base().'index.php/';
						echo ($game->jugend === 'aktiv') ? 'aktive' : 'jugend';
						echo '/'.strtolower($game->kuerzel);?>" alt="<?php 
						echo JText::_('COM_HBMANAGER_OVERVIEW_TO_TEAMSITE');?>"><?php 
						echo $game->mannschaft;?></a> <span><?php 
						echo $game->liga;?></span>
					</h4>

					<div>
					<a href="<?php echo $game->hvwLink;?>" target="_BLANK" alt="zur HVW-Seite" class="hvwlink">HVW</a>
					<a id="btnShow_curr-<?php echo $game->kuerzel?>" class="btnShowTable"><?php 
						echo JText::_('COM_HBMANAGER_OVERVIEW_STANDINGS');?></a>
					<?php
	//				echo '<a id="report_'.$game->kuerzel.
	//					'" class="linkReport">'.
	//					JText::_('COM_HBMANAGER_OVERVIEW_REPORT').'</a>';
					?>
					</div>

					<?php
				}
				?>
				<div class="gameInfo <?php echo ($game->toreHeim !== null) ? ' indicator '.$game->anzeige : '';	?>">
				<span class="time"><?php echo JHtml::_('date', $game->zeit, 'H:i', $this->timezone);?> Uhr </span>
				<span class="team">
					<span class="home<?php echo ($game->eigeneMannschaft === 1) ? ' own' : '';?>"><?php echo $game->heim;?></span>
					<span class="dash">-</span> <span class="away<?php echo ($game->eigeneMannschaft === 2) ? ' own' : '';?>"><?php echo $game->gast;?></span>
				</span>
				<span class="gameResult">
				<?php
				if ($game->toreHeim !== null)
				{
					?>
						<span class="<?php echo ($game->eigeneMannschaft === 1) ? ' own' : '';?>"><?php echo $game->toreHeim;?></span>
						<span class="dash">:</span> <span class="<?php echo ($game->eigeneMannschaft === 2) ? ' own' : '';?>"><?php echo $game->toreGast;?></span>
						<span class="indicator "></span>
					<?php
				}
				?>
				</span>
				</div>
				<?php
				$nextTeam = ($i < count($games)-1) ? $games[$i+1]->mannschaft : null;
				if ($nextTeam != $game->mannschaft) {
					?>
					<table id="standings_curr-<?php echo $game->kuerzel;?>" class="miniStandings" data-state="hidden">
						<thead>			
						<tr>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_RANK');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_TEAM');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_GAMES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_WINS');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_TIES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_LOSSES');?></th>
							<th colspan="3"><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_GOALS');?></th>
							<?php echo ($this->showDiff) ? '<th class="less4mobile">'.JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_DIFFERENCE').'</th>' : '';?>
							<th colspan="3"><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_POINTS');?></th>
						</tr>
						</thead>

						<tbody>
						<?php 
						foreach ($game->standings as $row) {
							// row in HBtabelle table
							?>
							<tr class="<?php echo ($row->heimVerein) ? ' home' : '';?>">
								<td><?php echo $row->platz;?></td>
								<td class="textteam"><?php echo $row->mannschaft;?></td>
								<td><?php echo $row->spiele;?></td>
								<td><?php echo $row->s;?></td>
								<td><?php echo $row->u;?></td>
								<td><?php echo $row->n;?></td>
								<td class="goals"><?php echo $row->tore;?></td>
								<td class="sepaDots">:</td>
								<td class="goalsCon"><?php echo $row->gegenTore;?></td>
								<?php echo ($this->showDiff) ? '<td class="less4mobile">'.$row->torDiff.'</td>' : '';?>
								<td class="points"><?php echo $row->punkte;?></td>
								<td class="sepaDots">:</td>
								<td class="negPoints"><?php echo $row->minusPunkte;?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
					</table>
				
				</div> <!-- div team-set -->
				<?php
					$currTeam = $game->mannschaft;
				}
				
			}
			
			?>
			</div>
			<?php
		}
		echo "\n\n".'</div>'; // games
		echo '<hr>';
	}
}

echo "\n\n".'</div>'; // hboverview
