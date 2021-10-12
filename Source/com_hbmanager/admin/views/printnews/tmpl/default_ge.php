<?php 
	$tz = HbmanagerHelper::getHbTimezone();
	$abbreviated = true;
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->prevgames);echo'</pre>'; 
	?>
	<?php if (!empty($this->prevgames)) : ?>
		<div id="articlePrevGames">
			<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_PREV_GAMES');?></u></b></p>
		<?php foreach ($this->prevgames as $date => $days) : ?>
			<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
			<?php foreach ($days as $game) :
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<span class="game">
					<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
					<i><?php echo $home ?> - <?php echo $away ?> | <?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?></i><br>
				</span>
			<?php endforeach; ?>
			</p>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>


	<?php 
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->reports);echo'</pre>'; 
	?>
	<?php if (!empty($this->reports)) : ?>
		<div id="articleReports">
			<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_REPORTS');?></u></b></p>
		<?php foreach ($this->reports as $game) : ?>

			<p class="report">
				<b><?php echo $game->team ?></b> (<?php echo $game->league ?>)<br>
				<i><?php echo $game->home ?> - <?php echo $game->away ?> | <?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?></i><br>
				<?php if (!empty($game->report)) : ?>
				<?php echo $game->report; ?><br>
				<?php endif; ?>
				<?php if (!empty($game->playerList)) : ?>
				Es spielten: <br>
				<?php echo $game->playerList; ?><br>
				<?php endif; ?>
				<?php if (!empty($game->extra)) : ?>
				<?php echo $game->extra; ?><br>
				<?php endif; ?>
			</p>

		<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php 
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->nextgames);echo'</pre>'; 
	?>
	<?php if (!empty($this->nextgames)) : ?>
		<div id="articleNextGames">
			<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_NEXT_GAMES');?></u></b></p>
		<?php foreach ($this->nextgames as $date => $days) : ?>
			<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
			<?php foreach ($days as $game) : 
				$home = $game->home;
				$away = $game->away;  
				if ($abbreviated) {
					$home = $game->homeAbbr;
					$away = $game->awayAbbr;
				}
				?>
				<span class="game">
					<i><?php 
						if (isset($game->details)) echo JText::_('COM_HBMANAGER_PRINTREPORT_MULTIGAMES');
						echo HbmanagerHelper::getformatedTime($game->dateTime, $tz); 
						echo JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK');?>&nbsp;in <?php echo $game->town ?> (<?php echo $game->gymName ?>)</i><br>
					<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
					<i> <?php echo $home ?> - <?php echo $away ?></i><br>
					
				</span>
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
			<p class="headline"><b><u><?php 
				if (count($this->homegames) > 1) echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_HOME_GAMES_PLURAL');
				else echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_HOME_GAMES');
				?></u></b></p>
		<?php foreach ($this->homegames as $date => $days) : ?>
			<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
			<?php foreach ($days as $gym) : ?>
				<i><?php echo $gym[0]->gymName ?>, <?php echo $gym[0]->town ?></i><br>
				<?php foreach ($gym as $game) : 
					$home = $game->home;
					$away = $game->away;  
					if ($abbreviated) {
						$home = $game->homeAbbr;
						$away = $game->awayAbbr;
					}
					?>
					<span class="game">
						<i><?php 
						echo HbmanagerHelper::getformatedTime($game->dateTime, $tz, '&nbsp;'.JText::_('COM_HBMANAGER_PRINTREPORT_CLOCK')); ?>
						</i>&nbsp;<?php echo $game->teamShort ?> (<?php echo $game->leagueKey ?>): 
						<i><?php echo $home ?> - <?php echo $away ?></i><br>
					</span>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</p>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>


	<?php 
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->reports);echo'</pre>'; 
	?>
	<?php if (!empty($this->pregames)) : ?>
		<div id="articlePregames">
			<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_HEADLINE_PREGAMES');?></u></b></p>
		<?php foreach ($this->pregames as $game) : ?>

			<p class="report">
				<b><?php echo $game->team ?></b> (<?php echo $game->league ?>)<br>
				<i><?php echo $game->home ?> - <?php echo $game->away ?></i><br>
				<?php if (!empty($game->pregame)) : ?>
				<?php echo $game->pregame; ?><br>
				<?php endif; ?>
				<?php if (!empty($game->meetupLoc)) : ?>
				<?php echo $game->meetupLoc; ?><br>
				<?php endif; ?>
				<?php if (!empty($game->meetupTime)) : ?>
				<?php echo $game->meetupTime; ?><br>
				<?php endif; ?>
			</p>

		<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<p><b>Es gelten die 3G Regeln.</b></p>