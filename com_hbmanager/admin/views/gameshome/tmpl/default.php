<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();

?>
<div id="j-sidebar-container" class="span2">
	<?php 
	echo JHtmlSidebar::render(); 
	JToolBarHelper::preferences('com_hbmanager');
	?>
</div>
<div id="j-main-container" class="span10">

<div id="gameshome">

<?php
	// echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->games);echo'</pre>';
?>

<?php if (!empty($this->games)) : ?>

	<?php foreach ($this->games as $dayKey => $day) : 
		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($day);echo'</pre>';
	?>
		<?php foreach ($day as $gym) : ?>
		
			<h4><?php echo JHtml::_('date', $dayKey, 'D, d.m.Y', $tz).' - '.$gym[0]->gymName.', '.$gym[0]->town.' ('.$gym[0]->gymId.')' ?></h4>
			<table class="schedule">
			<?php foreach ($gym as $row) : ?>
				<tr>
					<!-- <td><?php echo $row->team?></td> -->
					<td><?php echo $row->leagueKey?></td>
					<td class="wann leftalign"><?php echo JHtml::_('date', $row->dateTime, 'H:i', $tz) ?> Uhr</td>
					<td class="rightalign"><?php echo $row->home ?></td>
					<td>-</td>
					<td class="rightalign"><?php echo $row->away ?></td>
				</tr>
			<?php endforeach; ?>
			</table>
		
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>
</div>

</div>

