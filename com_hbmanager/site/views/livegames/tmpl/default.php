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

<?php foreach ($this->games as $date => $day) : ?>
	<h2><?php echo JHtml::_('date', $date, 'l, j. F', $this->tz)?></h2>
	<table>
	<?php foreach ($day as $game) : ?>
		<tr>
			<td class="date"><?php //echo $game['gDate'].' um ';?><?php echo $game['gTime'];?><span class=""> Uhr</span></td>
			<td class="team"><strong><?php echo $game['team'];?></strong><br>
				<span class="hidden-phone"><?php echo $game['league'];?></span><span class="visible-phone"><?php echo $game['leagueKey'];?></span></td>
			<!-- <td>Spielbeginn: <?php echo $game['dateTime'];?> Uhr</td> -->
			<td class="game"><?php echo $game['gHomeTeam'];?> - <?php echo $game['gGuestTeam'];?></td>
			<td class="gym"><?php echo JHTML::tooltip($game['gGymnasiumName'].', '.$game['gGymnasiumTown'], 
						JText::_('COM_HBMANAGER_LIVEGAMES_GYM'), '',  
						'<span class="icon-home large-icon"> </span>');?>
				<?php echo $game['shortGym'];?>
			</td>
			<td class="result"><?php 
				if (!empty(trim($game['gHomeGoals'].$game['gGuestGoals']))) {
					echo $game['gHomeGoals'];
					echo ':';
					echo $game['gGuestGoals'];
				}
				?></td>
			<td class="live">
				<?php if ($game['live']) : ?>
				<a href="./index.php?option=com_hbmanager&view=ticker&token=<?php echo $game['gToken'];?>" class="btn"><span class="icon-play"></span></a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endforeach;?>
<?php else : ?>
<p><?php echo JText::_('COM_HBMANAGER_LIVEGAMES_NO_GAMES')?></p>
<?php endif; ?>
</div>
