<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

echo "\n\n".'<h1>'.JText::_('COM_HBMANAGER_OVERVIEW_DEFAULT_TITLE').'</h1>';

echo "\n\n".'<div id="hboverview">';

if (!empty($this->currGames))
{
	echo "\n".'<h2 class="current">';
	echo JText::_('COM_HBMANAGER_OVERVIEW_CURRENT_GAMES').'</h2>';
	
	$currTeam = null;
	echo "\n\n".'<div id="prevGames">';
	foreach ($this->currGames as $date => $games)
	{
		echo '<h3 class="date">';
		echo JHtml::_('date', $date, 'l, j. F Y', $tz)."\n";
		echo '</h3>';
		echo "\n".'<div class="games">';
		foreach ($games as $game)
		{
			//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";

			if ($currTeam != $game->mannschaft) {
				echo '<h4 class="team">';
				echo '<a class="teamLink" href="';
				echo JURI::base().'index.php/';
				if ($game->jugend === 'aktiv') {
					echo 'aktive';
				}
				else {
					echo 'jugend';
				}
				echo '/'.strtolower($game->kuerzel);
				echo '" alt="zur Mannschafts-Seite">';
				echo $game->mannschaft;
				echo '</a>';
				echo " <span>".$game->liga."</span>\n";
				echo '</h4>';
								
				echo '<div class="teamInfo">';
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				echo '<a id="btnShow_curr-'.$game->kuerzel.
					'" class="btnShowTable">'.
					JText::_('COM_HBMANAGER_OVERVIEW_STANDINGS').'</a>';
//				echo '<a id="report_'.$game->kuerzel.
//					'" class="linkReport">'.
//					JText::_('COM_HBMANAGER_OVERVIEW_REPORT').'</a>';
				echo '</div>'."\n";
				
				
				// standings
				$showDiff = true;
				echo '<table id="standings_curr-'.$game->kuerzel.'" data-state="hidden" class="HBminiStandings HBhighlight">';
				echo "<thead>";			
				echo "<tr><th></th><th class=\"textteam\">Mannschaft</th>";
				echo "<th>Sp.</th><th>S</th><th>U</th><th>N</th>";
				echo "<th colspan=\"3\" class=\"goals\">Tore</th>";
				echo ($showDiff) ? '<th>Diff.</th>' : '';
				echo "<th colspan=\"3\">Punkte</th>";
				echo "</tr>";
				echo "</thead>\n";

				echo "<tbody>";
					foreach ($game->standings as $row) {
						// row in HBtabelle table
						echo "<tr class=\"{$row->background}";
						if ($row->heimVerein) echo ' heim';
						echo "\">";
						echo "<td>{$row->platz}</td><td class=\"textteam\"><strong>{$row->mannschaft}</strong></td>";
						echo "<td>{$row->spiele}</td><td>{$row->s}</td><td>{$row->u}</td><td>{$row->n}</td>";
						echo "<td class=\"goals\">{$row->tore}</td><td class=\"sepaDots\">:</td><td class=\"goalsCon\">{$row->gegenTore}</td>";
						echo ($showDiff) ? '<td>'.$row->torDiff.'</td>' : '';
						echo "<td class=\"points\"><strong>{$row->punkte}</strong></td><td class=\"sepaDots\">:</td><td class=\"negPoints\"><strong>{$row->minusPunkte}</strong></td></tr>\n";
					}
				echo "</tbody>";
				echo "</table>\n";
				
				
				$currTeam = $game->mannschaft;
			}
			echo '<div class="game">';
			echo '<span class="time">';
			echo JHtml::_('date', $game->zeit, 'H:i', $tz);
			echo ' Uhr </span>';
			echo '<span class="home';
			if ($game->eigeneMannschaft === 1) echo ' own';
			echo '">'.$game->heim.'</span>';
			echo " - ";
			echo '<span class="away';
			if ($game->eigeneMannschaft === 2) echo ' own';
			echo '">'.$game->gast.'</span>';
			
			if ($game->toreHeim !== null)
			{
				echo '<span class="score">';
				echo '<span class="home';
				echo ($game->eigeneMannschaft === 1) ? ' own' : '';
				echo '">'.$game->toreHeim.'</span>';
				echo ":";
				echo '<span class="away';
				echo ($game->eigeneMannschaft === 2) ? ' own' : '';
				echo '">'.$game->toreGast.'</span>';
				echo '<span class="indicator '.$game->anzeige.'"></span>';
				echo '</span>';
			}
			echo '</div>';
			echo "\n";
		}
		echo "\n".'</div>'; // games
	}
	echo "\n\n".'</div>'; // currGames
}


if (!empty($this->prevGames))
{
	echo "\n".'<h2>'.JText::_('COM_HBMANAGER_OVERVIEW_RECENT_GAMES').'</h2>';
	
	$currTeam = null;
	echo "\n\n".'<div id="prevGames">';
	foreach ($this->prevGames as $date => $games)
	{
		echo '<h3 class="date">';
		echo JHtml::_('date', $date, 'l, j. F Y', $tz)."\n";
		echo '</h3>';
		echo "\n".'<div class="games">';
		foreach ($games as $game)
		{
			//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";

			if ($currTeam != $game->mannschaft) {
				echo '<h4 class="team">';
				echo '<a class="teamLink" href="';
				echo JURI::base().'index.php/';
				if ($game->jugend === 'aktiv') {
					echo 'aktive';
				}
				else {
					echo 'jugend';
				}
				echo '/'.strtolower($game->kuerzel);
				echo '" alt="zur Mannschafts-Seite">';
				echo $game->mannschaft;
				echo '</a>';
				echo " <span>".$game->liga."</span>\n";
				echo '</h4>';
			
				echo '<div class="teamInfo">';
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				echo '<a id="btnShow_prev-'.$game->kuerzel.
					'" class="btnShowTable">'.
					JText::_('COM_HBMANAGER_OVERVIEW_STANDINGS').'</a>';
//				echo '<a id="report_'.$game->kuerzel.
//					'" class="linkReport">'.
//					JText::_('COM_HBMANAGER_OVERVIEW_REPORT').'</a>';
				echo '</div>'."\n";
				
				
				// standings
				$showDiff = true;
				echo '<table id="standings_prev-'.$game->kuerzel.'" data-state="hidden" class="HBminiStandings HBhighlight">';
				echo "<thead>";			
				echo "<tr><th></th><th class=\"textteam\">Mannschaft</th>";
				echo "<th>Sp.</th><th>S</th><th>U</th><th>N</th>";
				echo "<th colspan=\"3\" class=\"goals\">Tore</th>";
				echo ($showDiff) ? '<th>Diff.</th>' : '';
				echo "<th colspan=\"3\">Punkte</th>";
				echo "</tr>";
				echo "</thead>\n";

				echo "<tbody>";
					foreach ($game->standings as $row) {
						// row in HBtabelle table
						echo "<tr class=\"{$row->background}";
						if ($row->heimVerein) echo ' heim';
						echo "\">";
						echo "<td>{$row->platz}</td><td class=\"textteam\"><strong>{$row->mannschaft}</strong></td>";
						echo "<td>{$row->spiele}</td><td>{$row->s}</td><td>{$row->u}</td><td>{$row->n}</td>";
						echo "<td class=\"goals\">{$row->tore}</td><td class=\"sepaDots\">:</td><td class=\"goalsCon\">{$row->gegenTore}</td>";
						echo ($showDiff) ? '<td>'.$row->torDiff.'</td>' : '';
						echo "<td class=\"points\"><strong>{$row->punkte}</strong></td><td class=\"sepaDots\">:</td><td class=\"negPoints\"><strong>{$row->minusPunkte}</strong></td></tr>\n";
					}
				echo "</tbody>";
				echo "</table>\n";
				
				
				$currTeam = $game->mannschaft;
			}
			
			echo '<div class="game">';
			echo '<span class="time">';
			echo JHtml::_('date', $game->zeit, 'H:i', $tz);
			echo ' Uhr </span>';
			echo '<span class="home';
			echo ($game->eigeneMannschaft === 1) ? ' own' : '';
			echo '">'.$game->heim.'</span>';
			echo " - ";
			echo '<span class="away';
			echo ($game->eigeneMannschaft === 2) ? ' own' : '';
			echo '">'.$game->gast.'</span>';
			
			if ($game->toreHeim !== null)
			{
				echo '<span class="score">';
				echo '<span class="home';
				echo ($game->eigeneMannschaft === 1) ? ' own' : '';
				echo '">'.$game->toreHeim.'</span>';
				echo ":";
				echo '<span class="away';
				echo ($game->eigeneMannschaft === 2) ? ' own' : '';
				echo '">'.$game->toreGast.'</span>';
				echo '<span class="indicator '.$game->anzeige.'"></span>';
				echo '</span>';
			}
			echo '</div>';
			echo "\n";
			
			
			// reports
//			if (empty($game->bericht)) {
//				echo '<p id="report_'.$game->kuerzel.'" data-state="hidden" class="report">';
//				echo 'Spielbericht blabla'.$game->bericht;
//				echo '</p>';
//			}
		}
		echo "\n".'</div>'; // games
	}
	echo "\n\n".'</div>'; // prevGames
}



//echo __FUNCTION__."<pre>"; print_r($this->nextGames); echo "</pre>";
if (!empty($this->nextGames))
{
	echo "\n".'<h2>'.JText::_('COM_HBMANAGER_OVERVIEW_UPCOMING_GAMES').'</h2>';
	
	$currTeam = null;
	echo "\n\n".'<div id="nextGames">';
	foreach ($this->nextGames as $date => $games)
	{
		echo '<h3 class="date">';
		echo JHtml::_('date', $date, 'l, j. F Y', $tz)."\n";
		echo '</h3>';
		echo "\n".'<div class="games">';
		foreach ($games as $game)
		{
			//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
			
			if ($currTeam != $game->mannschaft) {
				
				echo '<h4 class="team">';
				echo '<a class="teamLink" href="';
				echo JURI::base().'index.php/';
				if ($game->jugend === 'aktiv') {
					echo 'aktive';
				}
				else {
					echo 'jugend';
				}
				echo '/'.strtolower($game->kuerzel);
				echo '" alt="zur Mannschafts-Seite">';
				echo $game->mannschaft;
				echo '</a>';
				echo " <span>".$game->liga."</span>\n";
				echo '</h4>';
				
				echo '<div class="teamInfo">';
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				echo '<a id="btnShow_next-'.$game->kuerzel.
					'" class="btnShowTable">'.
					JText::_('COM_HBMANAGER_OVERVIEW_STANDINGS').'</a>';
				echo '</div>'."\n";
				
				// standings
				$showDiff = true;
				echo '<table id="standings_next-'.$game->kuerzel.'" data-state="hidden" class="HBminiStandings HBhighlight">';
				echo "<thead>";			
				echo "<tr><th></th><th class=\"textteam\">Mannschaft</th>";
				echo "<th>Sp.</th><th>S</th><th>U</th><th>N</th>";
				echo "<th colspan=\"3\" class=\"goals\">Tore</th>";
				echo ($showDiff) ? '<th>Diff.</th>' : '';
				echo "<th colspan=\"3\">Punkte</th>";
				echo "</tr>";
				echo "</thead>\n";

				echo "<tbody>";
					foreach ($game->standings as $row) {
						// row in HBtabelle table
						echo "<tr class=\"{$row->background}";
						if ($row->heimVerein) echo ' heim';
						echo "\">";
						echo "<td>{$row->platz}</td><td class=\"textteam\"><strong>{$row->mannschaft}</strong></td>";
						echo "<td>{$row->spiele}</td><td>{$row->s}</td><td>{$row->u}</td><td>{$row->n}</td>";
						echo "<td class=\"goals\">{$row->tore}</td><td class=\"sepaDots\">:</td><td class=\"goalsCon\">{$row->gegenTore}</td>";
						echo ($showDiff) ? '<td>'.$row->torDiff.'</td>' : '';
						echo "<td class=\"points\"><strong>{$row->punkte}</strong></td><td class=\"sepaDots\">:</td><td class=\"negPoints\"><strong>{$row->minusPunkte}</strong></td></tr>\n";
					}
				echo "</tbody>";
				echo "</table>\n";
				
				
				
				$currTeam = $game->mannschaft;
			}
			echo '<div class="game">';
			echo '<span class="time">';
			echo JHtml::_('date', $game->zeit, 'H:i', $tz);
			echo ' Uhr </span>';
			echo '<span class="home';
			if ($game->eigeneMannschaft === 1) echo ' own';
			echo '">'.$game->heim.'</span>';
			echo " - ";
			echo '<span class="away';
			if ($game->eigeneMannschaft === 2) echo ' own';
			echo '">'.$game->gast.'</span>';
			echo '</div>';
			echo "\n";
		}
		echo "\n".'</div>'; // games
	}
	echo "\n\n".'</div>'; // nextGames
}

echo "\n\n".'</div>'; // hboverview
