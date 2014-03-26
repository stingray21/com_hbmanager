<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div class="hbteamhome">
<h1><?php echo $this->team->mannschaft; ?>
<span><?php echo $this->team->liga; ?></span></h1>

<div id="scorerTable" data-teamkey="<?php echo $this->teamkey;?>" data-season="<?php echo $this->season;?>">
	<div class="goalsgame">Tore im Spiel 
		<span class="goalsgame"><?php 
		echo $this->players[0]->heim;
		echo ' - ';
		echo $this->players[0]->gast;
		?></span>
		<span class="goalsgameresult"><?php 
		echo $this->players[0]->toreHeim;
		echo ':';
		echo $this->players[0]->toreGast;
		?></span>
	</div>
	
	<table class="goals">
		<tr><th class="name">Name</th><th>Tore</th><th>Spiele</th><th>gesamt</th><th>Quote</th></tr>
<?php

foreach ($this->players as $player) 
{
	//echo '=> view->players<br><pre>'; print_r($player); echo '</pre>';
	echo '<tr><td class="name">';
	echo $player->name;
	if ($player->tw == true) {
		echo ' (TW)';
	}
	echo '</td><td>';
	echo $player->tore;
	if ($player->davon7m != null) {
		echo '/'.$player->davon7m;
	}
	echo '</td><td>';
	echo $player->spiele;
	echo '</td><td>';
	echo $player->toregesamt;
	echo '</td><td>';
	echo $player->quote;
	echo '</td></tr>';
}
?>
	</table>
</div>


<div class="moreGames">
	<div data-teamkey="<?php echo $this->teamkey;?>" style="display: hidden"></div>
	<div class="dataSaison" id="<?php echo $this->season;?>" style="display: hidden"></div>
	
	<span>Spiel auswählen
	<table class="moreGames">
<?php
foreach ($this->games as $game) 
{
	?>
	<tr class="<?php
	if ($game->tore !== null) echo 'gamebutton';
		?>" id="<?php echo $game->spielIDhvw;?>">
		<td class="date"><?php 
		echo JHtml::_('date', $game->datum, 'd.m.y', false);
		?></td>
		<td class="moreGamesTeams"><?php 
		echo $game->heim;
		echo '</td><td class="moreGamesTeams">- </td><td class="moreGamesTeams">';
		echo $game->gast;
		?></span></td>
		<td><span class="moreGamesResult"><?php 
		echo $game->toreHeim;
		echo ':';
		echo $game->toreGast;
		?></span></td>
	</tr>
	<?php
}
?>
	</table>
</div>
	
	<div id="chartgoals">

	</div>
	
	<div id="chartgoalstotal">

	</div>
</div>