<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div>
<h1><?php echo $this->team->mannschaft; ?>
 <span><?php echo $this->team->liga; ?></span></h1>
	
	<div>
		
		<div id="goals-player">
	
		<h3><?php echo JText::_('COM_HBTEAM_GOALS_SCORER');?></h3>	
		
<div data-teamkey="<?php echo $this->teamkey;?>" data-season="<?php echo $this->season;?>">	
	<table>
		<thead><tr>
				<th><?php echo JText::_('COM_HBTEAM_GOALS_NAME');?></th>
				<th><?php JHTML::_('behavior.tooltip');
				echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_DESC'), 
						JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_NAME'), '', 
						JText::_('COM_HBTEAM_GOALS_TABLE_GOALS_SHORT'));?>
				</th>
				<th><?php JHTML::_('behavior.tooltip');
				echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_DESC'),
						JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_NAME'), '', 
						JText::_('COM_HBTEAM_GOALS_TABLE_GAMES_SHORT'));?>
				</th>
				<th><?php JHTML::_('behavior.tooltip');
				echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_DESC'),
						JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_NAME'), '', 
						JText::_('COM_HBTEAM_GOALS_TABLE_TALLY_SHORT'));?>
				</th>
				<th><?php JHTML::_('behavior.tooltip');
				echo JHTML::tooltip(JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_DESC'), 
						JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_NAME'), '', 
						JText::_('COM_HBTEAM_GOALS_TABLE_AVERAGE_SHORT'));?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php

foreach ($this->players as $player) 
{
	//echo __FILE__.' - '.__LINE__.'<pre>';print_r($player); echo'</pre>';
	?>
			<tr <?php echo ($player->tore === null) ? ' class="notPlayed"' : '';?>>
				<td class="name"><?php echo $player->name;
					echo ($player->tw == true or $player->twposition) ? ' (TW)' : '';?></td>
				<td class="goals"><?php echo $player->tore;
					echo ($player->tore7m != 0) ? '/'.$player->tore7m : '';?></td>
				<td><?php echo $player->spiele; ?></td>
				<td><?php echo $player->toregesamt;?></td>
				<td><?php echo $player->quote;?></td>
			</tr>
				<?php 
}
?>
	</tbody>
	</table>
</div>
</div>

<div id="goals-games">
	<div data-teamkey="<?php echo $this->teamkey;?>" style="display: hidden"></div>
	<div id="<?php echo $this->season;?>" style="display: hidden"></div>
	
	<table>
<?php
foreach ($this->games as $game) 
{
	//echo __FILE__.' - '.__LINE__.'<pre>';print_r($game); echo'</pre>';
	?>
	<tr id="<?php echo $game->spielIdHvw;?>" class="gamebutton<?php 
			echo ($game->spielIdHvw === $this->gameId) ? ' selected' : '';
			echo ($game->show = 0) ? ' grayout' : '';?>" >
		<td><?php echo JHtml::_('date', $game->datum, 'd. M.', false);
		?></td>
		<td><?php echo $game->gameName;?></td>
		<td><?php echo $game->toreHeim.':'.$game->toreGast;?></td>
	</tr>
	<?php
}

?>
	</table>
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