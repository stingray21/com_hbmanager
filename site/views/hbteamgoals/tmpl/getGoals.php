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
//print_r($this->gameId);
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