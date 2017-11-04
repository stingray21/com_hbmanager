<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div id="hbhomesgames">

<h1><?php echo JText::_('COM_HBMANAGER_HOMEGAMES_HEADLINE') ?></h1>

<?php if (!empty($this->homegames)) : ?>

	<?php foreach ($this->homegames as $dayKey => $day) : ?>
	
		<?php foreach ($day as $gym): ?>
			<div id="tag_<?php echo $dayKey."_".$gym[0]->hallenNr ?>" class="gamedaytag<?php echo ($this->nextGameday[$dayKey]) ? ' showDay' : '' ?>">
				<div class="daybullet"></div>
				<h4 class="btnShowGameDay"><?php echo JHtml::_('date', $dayKey, 'D, d.m.Y', $tz) ?>
					<span> <?php echo $gym[0]->hallenName.', '.$gym[0]->stadt.' ('.$gym[0]->hallenNr.')'; ?> </span>
				</h4>
				<table id="table_<?php echo $dayKey."_".$gym[0]->hallenNr ?>" class="HBhomeSchedule">
				<?php foreach ($gym as $row): ?>
					<tr class="<?php echo $row->background ?>">
						<td><?php echo $row->mannschaft ?></td>
						<td><?php echo JHtml::_('date', $row->datumZeit, 'H:i', $tz) ?> Uhr</td>
						<td><?php echo $row->home ?></td>
						<td>-</td>
						<td><?php echo $row->away ?></td>
						<td><?php echo $row->goalsHome ?></td>
						<td><?php echo $row->goalsAway ?></td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		<?php endforeach; ?>

	<?php endforeach; ?>

<?php endif; ?>

</div>
