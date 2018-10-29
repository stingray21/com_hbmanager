<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Fields 
// gID, sGID, gNo, live, gToken, gAppid, gDate, gTime, 
// gGymnasiumID, gGymnasiumNo, gGymnasiumName, gGymnasiumPostal, gGymnasiumTown, gGymnasiumStreet, 
// gHomeTeam, gGuestTeam, gHomeGoals, gGuestGoals, gHomeGoals_1, gGuestGoals_1, gHomePoints, gGuestPoints, gComment, gReferee,
// team, name, shortName, league, leagueIdHvw, sex, youth, dateTime
?>

<h1><?php echo JText::_('COM_HBMANAGER_LIVEGAMES_HEADLINE') ?></h1>

<div id="livegames">

<?php if(!empty($this->games)) : ?>	

<table>
	<?php foreach ($this->games as $game) : ?>
		<tr>
			<td><strong><?php echo $game['team'];?></strong> | <?php echo $game['league'];?></td>
			<td><?php echo $game['gDate'];?> um <?php echo $game['gTime'];?> Uhr</td>
			<!-- <td>Spielbeginn: <?php echo $game['dateTime'];?> Uhr</td> -->
			<td><?php echo $game['gHomeTeam'];?> - <?php echo $game['gGuestTeam'];?></td>
			<td>
				<?php if ($game['live']) : ?>
				<a href="./index.php?option=com_hbmanager&view=ticker&token=<?php echo $game['gToken'];?>" class="btn"><span class="icon-play"></span>Ticker</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach;?>
</table>
<?php else : ?>
<p><?php echo JText::_('COM_HBMANAGER_LIVEGAMES_NO_GAMES')?></p>
<?php endif; ?>
</div>
