<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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
		echo JHtml::_('date', $date, 'D, d.m.y', false)."\n";
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
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				$currTeam = $game->mannschaft;
			}
			echo '<div class="game">';
			echo '<span class="time">';
			echo $game->zeit;
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
				echo '<span class="score';
				if ($game->eigeneMannschaft === 1) echo ' own';
				echo '">'.$game->toreHeim.'</span>';
				echo ":";
				echo '<span class="score';
				if ($game->eigeneMannschaft === 2) echo ' own';
				echo '">'.$game->toreGast.'</span>';
				echo '<span class="indicator '.$game->anzeige.'"></span>';
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
		echo JHtml::_('date', $date, 'D, d.m.y', false)."\n";
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
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				$currTeam = $game->mannschaft;
			}
			echo '<div class="game">';
			echo '<span class="time">';
			echo $game->zeit;
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
				echo '<span class="score';
				if ($game->eigeneMannschaft === 1) echo ' own';
				echo '">'.$game->toreHeim.'</span>';
				echo ":";
				echo '<span class="score';
				if ($game->eigeneMannschaft === 2) echo ' own';
				echo '">'.$game->toreGast.'</span>';
				echo '<span class="indicator '.$game->anzeige.'"></span>';
			}
			echo '</div>';
			echo "\n";
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
		echo JHtml::_('date', $date, 'D, d.m.y', false)."\n";
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
				echo '<a class="hvwLink" href="'.$game->hvwLink.
					'" target="_BLANK" alt="zur HVW-Seite"> </a>';
				$currTeam = $game->mannschaft;
			}
			echo '<div class="game">';
			echo '<span class="time">';
			echo $game->zeit;
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
