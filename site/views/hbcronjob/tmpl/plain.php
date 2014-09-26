<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

if (is_array($this->result)) {
	foreach ($this->result as $team)
	{
		echo "\n";
		echo $team->kuerzel."\n";
		echo 'T: '.$team->ranking."\n";
		echo 'S: '.$team->ranking."\n";
	}
}
elseif ($this->result === "no update") {
	echo 'Kein Update';
}

//echo '<p>'.$this->time_elapsed.' µs</p>';
