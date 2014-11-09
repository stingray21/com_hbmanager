<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');


// Button
echo '<a id="goback" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showOverview').
		'">'.JText::_('COM_HBMANAGER_OVERVIEW_BACK_BUTTON').'</a>';
// Button
//echo '<a id="showall" class="hbbutton" href="'.
//		JRoute::_('index.php?option=com_hbmanager&task=showAllGames').
//		'">'.JText::_('COM_HBMANAGER_OVERVIEW_ALL_BUTTON').'</a>';
//echo '<div class="clr"></div>';

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_HOME_TITLE').'</h3>';

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y';

foreach ($this->homegames as $dayKey => $day)
{
	echo "<h4>".JHtml::_('date', $dayKey, $datePattern, false)."</h4>";
	
	foreach ($day as $gym)
	{
		echo '<p>';
		echo $gym[0]->hallenName.', '.$gym[0]->stadt.' ('.$gym[0]->hallenNummer.')';
		echo '</p>';
		$background = false;
		echo "\t\t<table class=\"HBhomeSchedule\">\n";
		foreach ($gym as $row)
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
			echo "<td>{$row->mannschaft}</td>";
			//echo "<td class=\"wann leftalign\">";
			//echo JHtml::_('date', $row->datum, 'D', false);
			//echo "</td>";
			//echo "<td class=\"wann leftalign\">";
			//echo JHtml::_('date', $row->datum, 'd.m.y', false);
			//echo "</td>";
			echo "<td class=\"wann leftalign\">".substr($row->uhrzeit,0,5)." Uhr</td>";
			//echo "<td>{$row->ligaKuerzel}</td>";
			//echo "<td>{$row->hallenNummer}</td>";
			echo "<td class=\"rightalign";
			echo "\">{$row->heim}</td><td>-</td>";
			echo "<td class=\"leftalign";
			echo "\">{$row->gast}</td>";
			//echo "<td class=\"rightalign";
			//echo "\">{$row->toreHeim}</td><td>:</td>";
			//echo "<td class=\"leftalign";
			//echo "\">{$row->toreGast}</td>";
			//echo "<td>{$row->bemerkung}</td>";
			//echo "</td>";
			echo "</tr>\n";
		}
		echo "\t\t</table>\n";
	}
}
	