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

<div id="gamesall">
<?php if (!empty($this->games)) : ?>

	<?php foreach ($this->games as $team) : ?>

		<h4><?php echo $team->team ?></h4>
		<p><?php echo $team->league ?> <?php echo (!empty($team->leagueKey)) ? '('.$team->leagueKey.')' : ''; ?></p>
		<!-- <p><?php echo $team->email ?></p> -->
		<?php $link = HbmanagerHelper::get_hvw_page_url($team->leagueIdHvw) ?>
		<p><?php echo (!empty($team->leagueIdHvw)) ? '<a href="'.$link.'" target="_BLANK">'.$link.'</a>' : JText::_('COM_HBMANAGER_GAMESALL_NO_DATA'); ?></p>
		
		<?php if (!empty($team->games)) : ?>
		
			<table class="schedule">
				<thead>
					<tr>
						<th></th>
						<th>Datum</th>
						<th>Zeit</th>
						<!-- <th>Halle</th> -->
						<th class="rightalign">Heim</th>
						<th></th>
						<th class="leftalign">Gast</th>
					</tr>
				</thead>
				
				<tbody>	
				<?php foreach ($team->games as $game) : ?>

					<tr class="">
					<td class="leftalign"><?php echo JHtml::_('date', $game->dateTime, 'D', $tz); ?></td>
					<td class="leftalign"><?php echo JHtml::_('date', $game->dateTime, 'd.m.Y', $tz); ?></td>
					<td class="leftalign"><?php echo JHtml::_('date', $game->dateTime, 'H:i', $tz); ?> Uhr</td>
					<!-- <td><?php echo $game->gymId ?></td> -->
					<td class="rightalign <?php echo ($game->ownTeam === 1) ? ' heim' : ''?>"><?php echo $game->home ?></td>
					<td>-</td>
					<td class="leftalign <?php echo ($game->ownTeam === 2) ? ' heim' : ''?>"><?php echo $game->away ?></td>
					</tr>

				<?php endforeach; ?>
				</tbody>
			</table>
		
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
</div>

</div>