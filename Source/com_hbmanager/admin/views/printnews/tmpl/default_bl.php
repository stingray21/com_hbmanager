<?php 
	$tz = HbmanagerHelper::getHbTimezone();
	$abbreviated = true;
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->prevgames);echo'</pre>'; 
	?>
	<p>
	<?php if (!empty($this->prevgames)) : ?>
		<b><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_RESULT');?></b><br>
		<?php foreach ($this->prevgames as $date => $days) : ?>
			<?php foreach ($days as $game) : ?>
				<?php
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<span class="game">
					<?php $teamname = (strcmp($game->youth,'aktiv')===0) ? $game->team : $game->teamkey ; 
						$teamname = preg_replace('/(-| )(1)$/', '$1', $teamname);
						$teamname = preg_replace('/(-| )(\d{1,2})?$/', '$2', $teamname);
						// $teamname = preg_replace('/(-| )(1)$/', '$1', $teamname);
						echo $teamname;
						?>: 
					<?php echo $home ?> - <?php echo $away ?>&nbsp;&nbsp;<?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?><br>
				</span>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php 
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->nextgames);echo'</pre>'; 
	?>
	
	<?php if (!empty($this->nextgames)) : ?>
		<b><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_FORCAST');?></b><br>
		<?php foreach ($this->nextgames as $date => $days) : ?>
			<b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
			<?php foreach ($days as $game) : ?>
				<?php
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<span class="game">
					<?php 
						if (strcmp($game->youth,'aktiv')===0) {
							$teamname = $game->team;
							$teamname = preg_replace('/ \d$/', '', $teamname);
						} else {
							$teamname = $game->leagueKey;
							$teamname = preg_replace('/^((m|w|g)J[A-F]([4,6]\+1))-.*/', '$1', $teamname);
						} 
						preg_match('/\d$/', $game->name, $team);
						// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($match);echo'</pre>';
						$teamNum = isset($match[0]) ? $match[0] : '';
						
						echo $teamname.$teamNum;
						?>: 
					<?php 
						if (isset($game->details)) echo JText::_('COM_HBMANAGER_PRINTREPORT_MULTIGAMES');
						echo HbmanagerHelper::getformatedTime($game->dateTime, $tz);
						echo JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK');
					?>
					<?php echo $game->gymName ?>, <?php echo $game->town ?>,
					 <?php echo $home ?> - <?php echo $away ?><br>
				</span>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endif; ?>


	<?php 
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->homegames);echo'</pre>'; 
	?>
	<?php if (!empty($this->homegames)) : ?>
		<?php foreach ($this->homegames as $date => $days) : ?>
			<?php foreach ($days as $gym) : ?>
			<b><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_HOME_GAMES_GYM');?> <?php echo $gym[0]->gymName ?>, <?php echo $gym[0]->town ?></b><br>
			<b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
				<?php foreach ($gym as $game) : ?>
					<?php
					$home = $game->home;
					$away = $game->away;  
					if ($abbreviated) {
						$home = $game->homeAbbr;
						$away = $game->awayAbbr;
					}
					?>
					<span class="game">
						<?php $teamname = (strcmp($game->youth,'aktiv')===0) ? $game->team : $game->teamkey ; 
							$teamname = preg_replace('/(-| )(1)$/', '$1', $teamname);
							$teamname = preg_replace('/(-| )(\d{1,2})?$/', '$2', $teamname);
							// $teamname = preg_replace('/(-| )(1)$/', '$1', $teamname);
							echo $teamname;
							?>: 
						<?php 
							if (isset($game->details)) echo JText::_('COM_HBMANAGER_PRINTREPORT_MULTIGAMES');
							echo JHTML::_('date', $game->dateTime , 'H:i', $tz); 
							echo JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK');
						?> 
						<?php echo $home ?> - <?php echo $away ?><br>
					</span>
				<?php endforeach; ?>
			<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endif; ?>
</p>
