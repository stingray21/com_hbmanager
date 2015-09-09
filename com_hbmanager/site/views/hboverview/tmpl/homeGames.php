<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_HOME_TITLE').'</h3>';

echo '<div id="hboverview">';
if (!empty($this->homegames)) 
{
	foreach ($this->homegames as $dayKey => $day)
	{
		echo "<h4>".JHtml::_('date', $dayKey, 'D, d.m.Y', $tz)."</h4>";

		foreach ($day as $gym)
		{
			echo '<h5>';
			echo $gym[0]->hallenName.', '.$gym[0]->stadt.' ('.$gym[0]->hallenNr.')';
			echo '</h5>';
			echo "\t\t<table class=\"HBhomeSchedule\">\n";
			foreach ($gym as $row)
			{
				// row in HBschedule table
				echo "\t\t\t<tr class=\"".$row->background."\">";
				echo "<td>{$row->mannschaft}</td>";
				//echo "<td class=\"wann leftalign\">";
				//echo JHtml::_('date', $row->datum, 'D', false);
				//echo "</td>";
				//echo "<td class=\"wann leftalign\">";
				//echo JHtml::_('date', $row->datum, 'd.m.y', false);
				//echo "</td>";
				echo "<td class=\"wann leftalign\">";
				echo JHtml::_('date', $row->zeit, 'H:i', $tz);
				echo " Uhr</td>";
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
}
echo '</div>';