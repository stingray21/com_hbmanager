<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

if (is_array($this->result)) {
	foreach ($this->result as $team)
	{
		echo "\n";
		echo '<p><b>'.$team->kuerzel.'</b><br/>';
		echo 'Tabelle: '.$team->ranking.'<br/>';
		echo 'Spielplan: '.$team->ranking.'</p>';
	}
}
elseif ($this->result === "no update") {
	echo '<p><b>Kein Update</b><br/>';
}


//echo '<p>'.$this->time_elapsed.' µs</p>';
