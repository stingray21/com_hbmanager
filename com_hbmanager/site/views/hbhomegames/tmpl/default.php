<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_HOME_TITLE').'</h3>';

// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($this->nextGameday);echo '</pre';

echo '<div id="hbhomegames">';
//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->homegames);echo'</pre>';
if (!empty($this->homegames)) 
{
	foreach ($this->homegames as $dayKey => $day)
	{
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($day);echo '</pre';
		foreach ($day as $gym): ?>
			<div id="tag_<?php echo $dayKey."_".$gym[0]->hallenNr ?>" class="gamedaytag">
				<div class="daybullet <?php echo ($this->nextGameday[$dayKey]) ? 'arrow-down' : 'arrow-right';?>"></div>
				<h4 class="btnShowGameDay"><?php echo JHtml::_('date', $dayKey, 'D, d.m.Y', $tz) ?>
					<span> <?php echo $gym[0]->hallenName.', '.$gym[0]->stadt.' ('.$gym[0]->hallenNr.')'; ?> </span>
				</h4>
				<table id="table_<?php echo $dayKey."_".$gym[0]->hallenNr ?>" class="HBhomeSchedule" data-state="<?php echo ($this->nextGameday[$dayKey]) ? 'hidden' : 'visible';?>" <?php echo ($this->nextGameday[$dayKey]) ? ' style="display: table;"' : ''?>>
			<?php foreach ($gym as $row): 
				// row in HBschedule table ?>
				<tr class="<?php echo $row->background ?>">
					<td><?php echo $row->mannschaft ?></td>
				<?php
				//echo "<td class=\"wann leftalign\">";
				//echo JHtml::_('date', $row->datum, 'D', false);
				//echo "</td>";
				//echo "<td class=\"wann leftalign\">";
				//echo JHtml::_('date', $row->datum, 'd.m.y', false);
				//echo "</td>";
				echo "<td class=\"wann leftalign\">";
				echo JHtml::_('date', $row->datumZeit, 'H:i', $tz);
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
				echo "</tr>\n";?>
			<?php endforeach; ?>
				</table>
			</div>
		<?php endforeach; ?>
		<?php
	}
}
echo '</div>';