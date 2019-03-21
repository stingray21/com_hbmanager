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

<div id="gamereports">

<?php if (!empty($team)) : ?>

<h1><?php echo JText::_('COM_HBMANAGER_GAMEREPORTS_HEADLINE') ?> | <?php echo $team->team; ?> <span><?php echo $team->league; ?></span></h1>


<form id="updateGame" name="updateGame" action="" enctype="multipart/form-data" method="post">
	<select id="gameSelect" name="gameId">
		<?php foreach ($this->games AS $game) :?>
			<option value="<?php echo $game->gameIdHvw?>" <?php if($this->selectedGameId == $game->gameIdHvw) echo ' selected';?>> <?php echo $game->date?> | <?php echo $game->home?> - <?php echo $game->away?> (<?php echo $game->gameIdHvw?>) | <?php echo $game->goalsHome?>:<?php echo $game->goalsAway?></option>
		<?php endforeach ?>
	</select>
    
</form>


<?php if(count($this->games) > 0) : ?>	

	<h3><?php echo JText::_('COM_HBMANAGER_GAMEREPORTS_TITLE_GAME'); ?></h3>

	<?php foreach($this->games as $game) : ?>
	<?php $showClass = ($this->selectedGameId != $game->gameIdHvw) ? ' hidden' : ''; ?>
	<div id="gameReport_<?php echo $game->gameIdHvw ?>"  class="gameInfo <?php echo $showClass ?>"  >
		
		<p>
		<?php echo $game->gymName.' '.$game->town.' | '; ?>
		<?php echo $game->date.' | '.$game->time; ?>
		<?php echo ' | '.$game->gameIdHvw; ?>
		<br>
		<?php echo $game->home.' - '.$game->away; ?>
		<span><?php echo $game->goalsHome.':'.$game->goalsAway; ?></span>	
		</p>
		
		<?php if(!empty($game->report)) : ?>	
		<h3><?php echo JText::_('COM_HBMANAGER_GAMEREPORTS_TITLE_REPORT'); ?></h3>
		<p><?php echo $game->report ?></p>	
		<?php endif ?>
	</div>

	<?php endforeach ?>

	<?php if($this->gameGraph) : ?>
	<div id="gamegraph-box">
		<h3><?php echo JText::_('COM_HBMANAGER_GAMEREPORTS_TITLE_ACTIONS'); ?></h3>

		<div id="gamegraphframe" class="noselect"></div>
	</div>
	<?php endif ?>


<?php endif ?>


<?php else : ?>
<h1><?php echo JText::_('COM_HBMANAGER_TEAM_NO_TEAM')?></h1>
<?php endif; ?>

</div>