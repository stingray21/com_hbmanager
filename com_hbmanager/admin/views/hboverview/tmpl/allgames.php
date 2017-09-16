<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

JToolBarHelper::preferences('com_hbmanager');


// Button
echo '<a id="goback" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showOverview').
		'">'.JText::_('COM_HBMANAGER_OVERVIEW_BACK_BUTTON').'</a>';
// Button
//echo '<a id="showhome" class="hbbutton" href="'.
//		JRoute::_('index.php?option=com_hbmanager&task=showHomeGames').
//		'">'.JText::_('COM_HBMANAGER_OVERVIEW_HOME_BUTTON').'</a>';
//echo '<div class="clr"></div>';

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_ALL_TITLE').'</h3>';

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

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
		// TODO store different in DB
		//http://spo.handball4all.de/service/if_g_json.php?ca=1&cl=29109&cmd=ps&og=3
		$linkId = preg_replace('/.*ca=1&cl=(\d{4,6})&cmd=.*/', '$1', $team->hvwLink);
		$link = 'http://www.hvw-online.org/spielbetrieb/ergebnissetabellen/#/league?ogId=3&lId='.$linkId.'&allGames=1';
		echo '<a href="'.$link.'">'.$link.'</a>';
	}
	echo '</p>';
	
	if(!empty($team->schedule)) 
	{
		$background = false;
		echo "\n\t<table class=\"HBschedule HBhighlight\">\n";
		echo "\t\t<thead>\n";
		echo "\t\t<tr><th></th><th>Datum</th><th>Zeit</th><th class=\"rightalign\">Heim</th><th></th><th class=\"leftalign\">Gast</th>";
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
			echo JHtml::_('date', $row->datumZeit, 'd.m.Y', $tz);
			echo "</td>";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datumZeit, 'H:i', $tz);
			echo " Uhr</td>";
			//echo "<td>{$row->hallenNr}</td>";
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