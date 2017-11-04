<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div id="hbgoals">

<h1><?php echo JText::_('COM_HBMANAGER_GOALS_HEADLINE') ?></h1>

<div>
<h1><?php echo $this->team->mannschaft; ?>
 <span><?php echo $this->team->liga; ?></span></h1>
	
	<div>
	
		<h3><?php echo JText::_('COM_HBTEAM_GOALS_SCORER'); ?></h3>	


		<div id="playertable">
			
			<div id="gameSelect">
				<div v-on="mouseover: showSelection(), mouseout: hideSelection()" class="menu">
					<div class="game selectedGame" v-show="showSelectorFlag"> 
						<span class="date">{{games[selectedGame].date}}</span>
						<span class="gameName">{{games[selectedGame].game}}</span>
						<span class="result">{{games[selectedGame].result}}</span>
					</div>
					<ul v-show="showSelectionFlag">
						<li v-repeat="game : games">
							<div class="game" v-class="played: (game.result != null), show: (game.show == 1), selected: ($index == selectedGame)" v-on="click: selectGame($index), mouseover: indicateGame($index), mouseout: removeIndication($index)"> <span class="date">{{game.date}}</span>
			 <span class="gameName">{{game.game}}</span>
			 <span class="result">{{game.result}}</span>

							</div>
						</li>
					</ul>
				</div>
			</div>
		
			<div>	
				<table>
					<thead><tr>
							<?php JHTML::_('bootstrap.tooltip');?>
							<th><?php echo JText::_('COM_HBTEAM_GOALS_NAME');?></th>
							<th><?php echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_DESC'), 
									JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_NAME'), '', 
									JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_SHORT'));?>
							</th>
							<th><?php echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_DESC'),
									JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_NAME'), '', 
									JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_SHORT'));?>
							</th>
							<th><?php echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_DESC'),
									JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_NAME'), '', 
									JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_SHORT'));?>
							</th>
							<th><?php echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_DESC'), 
									JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_NAME'), '', 
									JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_SHORT'));?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr  v-repeat="player: players" v-class="notPlayed: player.g == ''">
							<td class="name">{{ player.name }}</td>
							<td class="goals">{{ player.g }}</td>
							<td>{{ player.ga }}</td>
							<td>{{ player.t }}</td>
							<td>{{ player.r }}</td>
						</tr>
				</tbody>
				</table>
			</div>

			
		</div>


<div class="clr"></div>
<?php 
	//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->chartmodes); echo '</pre>';
	if ($this->chartmodes != null) {
?>
	
	<form class="goalchart">
	
		<label id="hbgoalchart_chartmode-lbl" for="hbgoalchart_chartmode" class="">
			<?php echo JText::_('COM_HBTEAM_GOALCHART_TITLE'); ?>
		</label>
		<fieldset id="hbgoalchart_chartmode" class="radio" >
		<?php
			foreach($this->chartmodes as $mode) {
				echo "\t\t\t<div>".'<input type="radio" id="hbgoalchart_chartmode_'.$mode.
						'" name="hbgoalchart_mode" value="'.$mode.'"';
				if ($this->defaultChartMode == $mode)	echo ' checked="checked"';
				echo ' />'."\n";
				echo '<label for="hbgoalchart_mode_'.$mode.'" >'.
						JText::_('COM_HBTEAM_GOALCHART_MODE_'.strtoupper($mode)).'</label></div>';
			}
		?>
		</fieldset>	

	</form>
	
	<div id="chartgoals">
	
	</div>
<?php
} else {
	echo '<p>'.JText::_('COM_HBTEAM_GOALCHART_NODATA').'</p>';
}
?>
	
</div>
</div>


</div>
