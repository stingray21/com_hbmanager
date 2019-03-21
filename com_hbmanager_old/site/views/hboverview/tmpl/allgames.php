<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

//JToolBarHelper::preferences('com_hbmanager');

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_ALL_TITLE').'</h3>';

echo '<div id="hboverview-all">';
foreach ($this->teams as $team)
{
	echo '<h4><b>'.$team->mannschaft.' </b>';
	//echo '('.$team->kuerzel.') ';
	echo '</h4>';
	
	echo '<p>';
	if (!empty($team->liga)) echo $team->liga.' ('.$team->ligaKuerzel.')';
	echo '</p>';
	
	echo '<p>';
	if(empty($team->hvwLink)) {
		echo 'keine HVW Daten';
	}
	else {
		echo '<a href="'.$team->hvwLink.'">'.$team->hvwLink.'</a>';
	}
	echo '</p>';
	
	if(!empty($team->schedule)) 
	{
		$background = false;
		echo "\n\t<table class=\"HBschedule HBhighlight\">\n";
		echo "\t\t<thead>\n";
		echo "\t\t<tr><th></th><th>Wann</th><th></th><th>Halle</th><th class=\"rightalign\">Heim</th><th></th><th class=\"leftalign\">Gast</th>";
		//echo "<th colspan=\"3\">Ergebnis</th><th>Bemerkung</th>";
		echo "</tr>\n";
		echo "\t\t</thead>\n\n";
	
		echo "\t\t<tbody>\n";
		foreach ($team->schedule as $row)
		{
			// row in HBschedule table
			echo "\t\t\t<tr class=\"".$row->background."\">";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datumZeit, 'D', $tz);
			echo "</td>";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datumZeit, 'd.m.y', $tz);
			echo "</td>";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datumZeit, 'H:i', $tz);
			echo " Uhr</td>";
			echo "<td>{$row->hallenNr}</td>";
			echo "<td class=\"rightalign";
			if ($row->mark === 1) echo ' heim';
			echo "\">{$row->heim}</td><td>-</td>";
			echo "<td class=\"leftalign";
			if ($row->mark === 2) echo ' heim';
			echo "\">{$row->gast}</td>";
//			echo "<td class=\"rightalign";
//			if ($row->mark === 1) echo ' heim';
//			echo "\">{$row->toreHeim}</td><td>:</td>";
//			echo "<td class=\"leftalign";
//			if ($row->mark === 2) echo ' heim';
//			echo "\">{$row->toreGast}</td>";
//			echo "<td>{$row->bemerkung}</td>";
//			echo "</td>";
			echo "</tr>\n";
		}
		echo "\t\t</tbody>\n\n";
		echo "\t</table>\n\n";
	}
	echo "\n";
}
echo "</div>";