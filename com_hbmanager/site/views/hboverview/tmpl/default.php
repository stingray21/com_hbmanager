<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

echo "\n\n".'<h1>'.JText::_('COM_HBMANAGER_OVERVIEW_DEFAULT_TITLE').'</h1>';

echo "\n\n".'<div>';

foreach ($this->gameDays as $gameDay)
{
	if (!empty($gameDay->games))
	{
		echo "\n".'<h2>';
		echo JText::_('COM_HBMANAGER_OVERVIEW_'.$gameDay->languageVar.'_GAMES').'</h2>';

		$currTeam = null;
		echo "\n\n".'<div id="'.$gameDay->shortVar.'Games">';
		
		foreach ($gameDay->games as $date => $games)
		{
			echo '<h3>';
			echo JHtml::_('date', $date, 'l, j. F Y', $this->timezone)."\n";
			echo '</h3>';
			echo "\n".'<div>';
			
			foreach ($games as $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";

				if ($currTeam != $game->mannschaft) {
					?>
					<h4><a href="<?php 
						echo JURI::base().'index.php/';
						echo ($game->jugend === 'aktiv') ? 'aktive' : 'jugend';
						echo '/'.strtolower($game->kuerzel);?>" alt="<?php 
						echo JText::_('COM_HBMANAGER_OVERVIEW_TO_TEAMSITE');?>"><?php 
						echo $game->mannschaft;?></a> <span><?php 
						echo $game->liga;?></span>
					</h4>

					<div>
					<a href="<?php echo $game->hvwLink;?>" target="_BLANK" alt="zur HVW-Seite">HVW</a>
					<a id="btnShow_curr-<?php$game->kuerzel?>" class="btnShowTable"><?php 
						echo JText::_('COM_HBMANAGER_OVERVIEW_STANDINGS');?></a>
					<?php
	//				echo '<a id="report_'.$game->kuerzel.
	//					'" class="linkReport">'.
	//					JText::_('COM_HBMANAGER_OVERVIEW_REPORT').'</a>';
					?>
					</div>

					<table id="standings_curr-<?php echo $game->kuerzel;?>" data-state="hidden">
						<thead>			
						<tr>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_RANK');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_TEAM');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_GAMES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_WINS');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_TIES');?></th>
							<th><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_LOSSES');?></th>
							<th colspan="3"><?php echo JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_GOALS');?></th>
							<?php echo ($this->showDiff) ? '<th>'.JText::_('COM_HBMANAGER_OVERVIEW_MINITABLE_DIFFERENCE').'</th>' : '';?>
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
								<td><?php echo $row->mannschaft;?></td>
								<td><?php echo $row->spiele;?></td>
								<td><?php echo $row->s;?></td>
								<td><?php echo $row->u;?></td>
								<td><?php echo $row->n;?></td>
								<td><?php echo $row->tore;?></td>
								<td>:</td>
								<td><?php echo $row->gegenTore;?></td>
								<?php echo ($this->showDiff) ? '<td>'.$row->torDiff.'</td>' : '';?>
								<td><?php echo $row->punkte;?></td>
								<td>:</td>
								<td><?php echo $row->minusPunkte;?></td>
							</tr>
							<?php
						}
					?>
					</tbody>
					</table>

					<?php
					$currTeam = $game->mannschaft;
				}
				?>
				<div>
				<span><?php echo JHtml::_('date', $game->zeit, 'H:i', $this->timezone);?> Uhr </span>
				<span class="<?php echo ($game->eigeneMannschaft === 1) ? ' own' : '';?>"><?php echo $game->heim;?></span>
				 - <span class="<?php echo ($game->eigeneMannschaft === 2) ? ' own' : '';?>"><?php echo $game->gast;?></span>
				<?php
				if ($game->toreHeim !== null)
				{
					?>
					<span>
						<span class="<?php echo ($game->eigeneMannschaft === 1) ? ' own' : '';?>"><?php echo $game->toreHeim;?></span>
						 - <span class="<?php echo ($game->eigeneMannschaft === 2) ? ' own' : '';?>"><?php echo $game->toreGast;?></span>
						<span class="indicator <?php echo $game->anzeige;?>"></span>
					</span>
					<?php
				}
				?>
				</div>
				<?php
			}
			
			?>
			</div>
			<?php
		}
		echo "\n\n".'</div>'; // games
	}
}

echo "\n\n".'</div>'; // hboverview
