<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// echo JText::_('COM_HBMANAGER_REMINDER_TITLE')."\n\n"; 

// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $this->result ,1).'</pre>';

$tz = HbmanagerHelper::getHbTimezone();
$abbreviated = true;

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->prevgames);echo'</pre>'; 
?>
<style>
	* {
		font-family: sans-serif;
	}

	#holiday {
		color: red;
	}
</style>

<?php if (!empty($this->holidays)) : ?>
	<div id="holiday">
		<b><?php echo JText::_('COM_HBMANAGER_REMINDER_WARNING');?></b>
		<?php foreach ($this->holidays as $holiday) : ?>
			<p><b><?php echo $holiday->holiday; ?> am <?php echo JHTML::_('date', $holiday->date , 'l, d.m.Y', $tz); ?></b><br>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->prevgames)) : ?>
	<div id="articlePrevGames">
		<h2><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_PREV_GAMES');?></h2>
	<?php foreach ($this->prevgames as $date => $days) : ?>
		<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
		<?php foreach ($days as $game) : ?>
				<?php
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
				<i><?php 
					echo ($game->ownTeam === 1) ? '<b>' : '';
					echo $home;
					echo ($game->ownTeam === 1) ? '</b>' : '';
					?> - <?php 
					echo ($game->ownTeam === 2) ? '<b>' : '';
					echo $away;
					echo ($game->ownTeam === 2) ? '</b>' : '';
					?> | <?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?></i><br>
		<?php endforeach; ?>
		</p>
	<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php 
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->nextgames);echo'</pre>'; 
?>
<?php if (!empty($this->nextgames)) : ?>
	<div id="articleNextGames">
		<h2><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_NEXT_GAMES');?></h2>
	<?php foreach ($this->nextgames as $date => $days) : ?>
		<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
		<?php foreach ($days as $game) : ?>
				<?php
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<i><?php 
					if (isset($game->details)) echo JText::_('COM_HBMANAGER_PRINTREPORT_MULTIGAMES');
					echo JHTML::_('date', $game->dateTime , 'H:i', $tz); 
					echo JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK');?>&nbsp;in <?php echo $game->town ?> (<?php echo $game->gymName ?>)</i><br>
				<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
				<i><?php 
					echo ($game->ownTeam === 1) ? '<b>' : '';
					echo $home;
					echo ($game->ownTeam === 1) ? '</b>' : '';
					?> - <?php 
					echo ($game->ownTeam === 2) ? '<b>' : '';
					echo $away;
					echo ($game->ownTeam === 2) ? '</b>' : '';
					?></i><br>
				
		<?php endforeach; ?>
		</p>
	<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php 
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->homegames);echo'</pre>'; 
?>
<?php if (!empty($this->homegames)) : ?>
	<div id="articleHomeGames">
		<h2><?php 
			if (count($this->homegames) > 1) echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_HOME_GAMES_PLURAL');
			else echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_HOME_GAMES');
			?></h2>
	<?php foreach ($this->homegames as $date => $days) : ?>
		<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
		<?php foreach ($days as $gym) : ?>
			<i><?php echo $gym[0]->gymName ?>, <?php echo $gym[0]->town ?></i><br>
			<?php foreach ($gym as $game) : ?>
				<?php
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>

					<i><?php echo JHTML::_('date', $game->dateTime , 'H:i', $tz).JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK');?></i>&nbsp;<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
					<i><?php echo $home ?> - <?php echo $away ?></i><br>
				<?php endforeach; ?>
		<?php endforeach; ?>
		</p>
	<?php endforeach; ?>
	</div>
<?php endif; ?>
