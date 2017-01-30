<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT.'/models/forms/reports.xml');

?>

<div>
	<h1><?php 
		echo $this->team->mannschaft; 
		?> <span><?php 
		echo $this->team->liga; 
		?></span></h1>
	
	<form id="updateGame" name="updateGame" action="" enctype="multipart/form-data" method="post">
		<select id="gameId" name="gameId">
			<?php foreach ($this->games AS $game) :?>
				<option value="<?php echo $game->key?>" <?php if($this->gameInfo['spielIdHvw'] == $game->key) echo ' selected';?>> <?php echo $game->value?></option>
			<?php endforeach ?>
		</select>
		<button type="submit">auswaehlen</button>
	    
	</form>

	

	<?php if($this->gameParts->report) : ?>	
	<h3><?php echo JText::_('COM_HBTEAM_REPORTS_TITLE_REPORT'); ?></h3>

	<div class="gameInfo">
		<p>
		<?php echo $this->gameInfo['hallenName'].' '.$this->gameInfo['stadt'].' | '; ?>
		<?php echo $this->gameInfo['datum'].' | '.$this->gameInfo['zeit']; ?>
		<?php echo ' | '.$this->gameInfo['spielIdHvw']; ?>
		</p>
		<p>
		<?php echo $this->gameInfo['heim'].' - '.$this->gameInfo['gast']; ?>
		<span><?php echo $this->gameInfo['toreHeim'].':'.$this->gameInfo['toreGast']; ?></span>	
		</p>
	</div>

	<p><?php echo $this->report->bericht ?></p>
	<?php endif ?> 


	<?php if($this->gameParts->actions) : ?>
	<h3><?php echo JText::_('COM_HBTEAM_REPORTS_TITLE_ACTIONS'); ?></h3>
	
	<div id="gamegraph" class="noselect"></div>
	<?php endif ?>
	</div>

	<?php if(!$this->gameParts->report && !$this->gameParts->actions) : ?>	

	<div class="gameInfo">
		<p>
		<?php echo $this->gameInfo['hallenName'].' '.$this->gameInfo['stadt'].' | '; ?>
		<?php echo $this->gameInfo['datum'].' | '.$this->gameInfo['zeit']; ?>
		<?php echo ' | '.$this->gameInfo['spielIdHvw']; ?>
		</p>
		<p>
		<?php echo $this->gameInfo['heim'].' - '.$this->gameInfo['gast']; ?>
		<span><?php echo $this->gameInfo['toreHeim'].':'.$this->gameInfo['toreGast']; ?></span>	
		</p>
	</div>
	<?php endif ?>

</div>