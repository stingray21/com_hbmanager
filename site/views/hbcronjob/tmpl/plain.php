<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r (e)';

if (is_array($this->result)) {
	foreach ($this->result as $team)
	{
		echo "\n";
		echo $team->kuerzel.': ';
		echo JHtml::_('date', $team->updated, $datePattern, false)."\n";
	}
}
elseif ($this->result === "no update") {
	echo 'Kein Update';
}

//echo '<p>'.$this->time_elapsed.' µs</p>';
