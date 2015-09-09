<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$datePattern = 'D, d.m.Y, H:i \U\h\r';
$timePattern = 'H:i \U\h\r';

echo '<h1>'.JTEXT::_('COM_HBCURRENTGAMES_TITLE').'</h1>'."\n\n";
echo "\n".'<div id="HBcurrentGames">';
if (!empty($this->prevGames))
{	
	echo '<h3>';
	echo 'Letzte Spiele';
	echo '</h3>';
	
	foreach ($this->prevGames as $game)
	{
		echo "\n\t\t\t";
		echo '<dl class="currentGames">';
		echo '<dt>';
		echo $game->mannschaft;
		echo '</dt>';
		echo '<dd>';
		echo '<span class="heim">'.$game->heim.'</span>';
		echo '<span class="gast">- '.$game->gast.'</span>';
		echo '<span class="toreHeim">'.$game->toreHeim.'</span>';
		echo '<span class="toreGast">:'.$game->toreGast.'</span>';
		echo '</dd>';
		echo '</dl>';
	}
}


if (!empty($this->nextGames))
{	
	echo '<h3>';
	echo 'Kommende Spiele';
	echo '</h3>';
	
	foreach ($this->nextGames as $game)
	{
		echo "\n\t\t\t";
		echo '<dl class="currentGames">';
		echo '<dt>';
		echo $game->mannschaft;
		echo '</dt>';
		echo '<dd>';
		echo '<span class="zeit">';
		echo JHTML::_('date', $game->datum.' '.$game->uhrzeit , $datePattern, 'UTC');
		echo '</span>';
		echo '<span class="heim">'.$game->heim.'</span>';
		echo '<span class="gast">'.$game->gast.'</span>';
		echo '<br />'.$game->hallenName.
			' in'.
			//' '.$game->plz.
			' '.$game->stadt.
			' ('.$game->hallenNummer.')';
		echo '</dd>';
		echo '</dl>';
	}
}

if (!empty($this->homeGames))
{	
	echo '<h3>';
	echo 'Heimspieltag';
	echo '</h3>';
	
	foreach ($this->homeGames as $game)
	{
		echo "\n\t\t\t";
		echo '<dl class="currentGames">';
		echo '<dt>';
		echo $game->mannschaft;
		echo '</dt>';
		echo '<dd>';
		echo '<span class="zeit">';
		echo JHTML::_('date', $game->uhrzeit , $timePattern, 'UTC');
		echo '</span>';
		echo '<span class="heim">'.$game->heim.'</span>';
		echo '<span class="gast">'.$game->gast.'</span>';
		echo '</dd>';
		echo '</dl>';
	}
}

echo '</div>'."\n";
//[kuerzel] => mJD2
//[spielID] => 136
//[spielIdHvw] => 72931
//[hallenNummer] => 7014
//[datum] => 2014-10-03
//[uhrzeit] => 10:00:00
//[heim] => TSV Geislingen 2	
//[gast] => SG Tail/Trucht
//[toreHeim] => 
//[toreGast] => 
//[bemerkung] =>  
//[mannschaftID] => 46
//[reihenfolge] => 9
//[mannschaft] => männliche D-Jugend 2
//[name] => TSV Geislingen 2
//[nameKurz] => TSV Geislingen 2
//[ligaKuerzel] => mJD-KLA
//[liga] => Kreisliga A
//[geschlecht] => m
//[jugend] => 1
//[hvwLink] => http://www.hvw-online.org/?A=g_class&id=39&orgID=11&score=18351&all=1
//[updateTabelle] => 2014-09-20 23:24:02
//[updateSpielplan] => 2014-09-20 23:24:05