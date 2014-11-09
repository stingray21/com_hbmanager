<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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
		echo '<a href="'.$team->hvwLink.'">'.$team->hvwLink.'</a>';
	}
	echo '</p>';
	
	if(!empty($team->schedule)) 
	{
		$background = false;
		echo "\n\t<table class=\"HBschedule HBhighlight\">\n";
		echo "\t\t<thead>\n";
		echo "\t\t<tr><th colspan=\"3\">Wann</th><th>Halle</th><th class=\"rightalign\">Heim</th><th></th><th class=\"leftalign\">Gast</th><th colspan=\"3\">Ergebnis</th><th>Bemerkung</th>";
		echo "</tr>\n";
		echo "\t\t</thead>\n\n";
	
		echo "\t\t<tbody>\n";
		foreach ($team->schedule as $row)
		{
			// switch color of background
			$background = !$background;
			// check value of background
			switch ($background) {
				case true: $backgroundColor = 'odd'; break;
				case false: $backgroundColor = 'even'; break;
			}
		
			// row in HBschedule table
			echo "\t\t\t<tr class=\"{$backgroundColor}\">";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datum, 'D', false);
			echo "</td>";
			echo "<td class=\"wann leftalign\">";
			echo JHtml::_('date', $row->datum, 'd.m.y', false);
			echo "</td>";
			echo "<td class=\"wann leftalign\">".substr($row->uhrzeit,0,5)." Uhr</td>";
			echo "<td>{$row->hallenNummer}</td>";
			echo "<td class=\"rightalign";
			if ($row->mark === 1) echo ' heim';
			echo "\">{$row->heim}</td><td>-</td>";
			echo "<td class=\"leftalign";
			if ($row->mark === 2) echo ' heim';
			echo "\">{$row->gast}</td>";
			echo "<td class=\"rightalign";
			if ($row->mark === 1) echo ' heim';
			echo "\">{$row->toreHeim}</td><td>:</td>";
			echo "<td class=\"leftalign";
			if ($row->mark === 2) echo ' heim';
			echo "\">{$row->toreGast}</td>";
			echo "<td>{$row->bemerkung}</td>";
			echo "</td></tr>\n";
		}
		echo "\t\t</tbody>\n\n";
		echo "\t</table>\n\n";
	}
	echo "\n";
}