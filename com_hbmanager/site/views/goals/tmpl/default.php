<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
?>

<script type="text/javascript">
	var teamkey = '<?php echo $this->team->teamkey ?>';
	var season = '<?php echo $this->season ?>';		
	var gameId = '<?php echo $this->selectedGameId ?>';		
</script>

<div id="goals">

<?php if (!empty($team)) : ?>

<h1><?php echo JText::_('COM_HBMANAGER_GOALS_HEADLINE') ?> | <?php echo $team->team; ?> <span><?php echo $team->league; ?></span></h1>

<div id="playertable">
	
	<div id="gameSelect">
		<div v-on="mouseover: showSelection(), mouseout: hideSelection()" class="menu">
			<div id="selectedGame" class="game" v-show="showSelectorFlag"> 
				<span class="date">{{games[selectedGame].date}}</span>
				<span class="gameName">{{games[selectedGame].game}}</span>
				<span class="result">{{games[selectedGame].result}}</span>
			</div>
			<ul v-show="showSelectionFlag">
				<li v-repeat="game : games" class="game" id="game-{{$index}}" v-class="played: (game.result != null), show: (game.show == 1), selectedGame: ($index == selectedGame)">
					<div v-on="click: selectGame($index), mouseover: indicateGame($index), mouseout: removeIndication($index)"> 
						<span class="date">{{formatDate(game.date)}}</span>
						<span class="gameName">{{game.game}}</span>
						<span class="result">{{game.result}}</span>

					</div>
				</li>
			</ul>
		</div>
	</div>

	<?php JHTML::_('bootstrap.tooltip');?>
	
	<table>
		<thead>
			<tr>
				<th colspan="1"></th>
				<th colspan="5" id="currentGame"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_CURRENT_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_CURRENT_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_CURRENT_SHORT'));?>
				</th>
				<th class="buffer sofar"></th>
				<th colspan="7" class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_DESC'),
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_SHORT'));?>
				</th>
				<th class="buffer total"></th>
				<th colspan="7" class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_DESC'),
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_SHORT'));?>
				</th>
				<th id="toggleSwitch"><-></th>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HBMANAGER_GOALS_NAME');?></th>

				<th><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_GOALS_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_GOALS_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_GOALS_SHORT'));?>
				</th>
				<th><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_PENALTY_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_PENALTY_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_PENALTY_SHORT'));?>
				</th>
				<th><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_YELLOW_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_YELLOW_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_YELLOW_SHORT'));?>
				</th>
				<th><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_2MIN_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_2MIN_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_2MIN_SHORT'));?>
				</th>
				<th><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_RED_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_RED_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_RED_SHORT'));?>
				</th>

				<th class="buffer sofar"></th>

				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GAMES_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GAMES_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GAMES_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GOALS_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GOALS_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_GOALS_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_AVERAGE_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_AVERAGE_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_AVERAGE_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_PENALTY_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_PENALTY_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_PENALTY_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_YELLOW_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_YELLOW_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_YELLOW_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_2MIN_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_2MIN_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_2MIN_SHORT'));?>
				</th>
				<th class="sofar"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_RED_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_RED_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_SOFAR_RED_SHORT'));?>
				</th>

				<th class="buffer total"></th>

				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GAMES_DESC'),
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GAMES_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GAMES_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GOALS_DESC'),
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GOALS_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_GOALS_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_AVERAGE_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_AVERAGE_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_AVERAGE_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_PENALTY_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_PENALTY_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_PENALTY_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_YELLOW_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_YELLOW_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_YELLOW_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_2MIN_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_2MIN_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_2MIN_SHORT'));?>
				</th>
				<th class="total"><?php echo JHTML::tooltip(JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_RED_DESC'), 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_RED_NAME'), '', 
						JText::_('COM_HBMANAGER_GOALS_TABLE_TOTAL_RED_SHORT'));?>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr  v-repeat="player: players" v-class="grayout : player.played != 1">
				<td class="name">{{ player.name }}{{ checkGoalie(player.goalie) }}</td>

				<td class="goals">{{ player.goals }}</td>
				<td>{{ checkPenalty(player.penaltyRatio, player.played) }}</td>
				<td>{{ checkZero(player.yellow) }}</td>
				<td>{{ checkZero(player.suspensionGame) }}</td>
				<td>{{ checkZero(player.red) }}</td>

				<td class="buffer sofar"></td>

				<td class="sofar">{{ player.gamesSoFar }}</td>
				<td class="sofar">{{ player.goalsSoFar }}</td>
				<td class="sofar">{{ player.averageSoFar }}</td>
				<td class="sofar">{{ checkPenalty(player.penaltyRatioSoFar, player.played, player.penaltyPercentSoFar) }}</td>
				<td class="sofar">{{ player.yellowSoFar }}</td>
				<td class="sofar">{{ player.suspensionSoFar }}</td>
				<td class="sofar">{{ player.redSoFar }}</td>

				<td class="buffer total"></td>

				<td class="total">{{ player.games }}</td>
				<td class="total">{{ player.goalsTotal }}</td>
				<td class="total">{{ player.averageTotal }}</td>
				<td class="total">{{ checkPenalty(player.penaltyRatioTotal, 1, player.penaltyPercentTotal) }}</td>
				<td class="total">{{ player.yellowTotal }}</td>
				<td class="total">{{ player.suspensionTotal }}</td>
				<td class="total">{{ player.redTotal }}</td>
			</tr>
	</tbody>
	</table>

</div>

	<?php if($this->goalGraph) : ?>

	<h3><?php echo JText::_('COM_HBMANAGER_GOALS_TITLE_ACTIONS'); ?></h3>

		<?php if ($this->chartmodes != null) : ?>
			<fieldset id="hbgoalchart_chartmode" class="radio" >
			<?php foreach($this->chartmodes as $mode) : ?>
					<div class="chartmodebox">
						<input type="radio" id="hbgoalchart_chartmode_<?php echo $mode ?>" name="hbgoalchart_mode" value="<?php echo $mode ?>" <?php 
						echo ($this->defaultChartMode == $mode)	? ' checked="checked"' : ''; ?> />
						<label for="hbgoalchart_mode_<?php echo $mode?>" ><?php echo JText::_('COM_HBMANAGER_GOALS_GOALCHART_MODE_'.strtoupper($mode)) ?></label>
					</div>
			<?php endforeach ?>
			</fieldset>
		
			<div id="chartgoals"></div>
		<?php else : ?>
			<p><?php echo JText::_('COM_HBMANAGER_GOALS_GOALCHART_NODATA') ?></p>
		<?php endif ?>
	<?php endif ?>



<?php else : ?>
	<h1><?php echo JText::_('COM_HBMANAGER_TEAM_NO_TEAM')?></h1>
<?php endif; ?>

</div>