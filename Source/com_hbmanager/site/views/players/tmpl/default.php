<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div id="hbplayers">

<h1><?php echo JText::_('COM_HBMANAGER_PLAYERS_HEADLINE') ?></h1>

<h1><?php echo $this->team->mannschaft; ?><span><?php echo $this->team->liga; ?></span></h1>
	

	<?php foreach ($this->players as $player) : ?>
	
		<div class="player">
			<div class="playerpic">
			<a href="<?php echo $this->picPath.$player->saison.'_'.$player->alias.'.jpg'?>">
				<img src="<?php echo $picPath.$player->saison.'_'.$player->alias.'.jpg'?>" id="<?php echo 'pic_'.$player->alias?>" alt="Bild <?php echo $player->name?>" />
			</a>
		</div>


		<?php if (!empty($player->trikotNr)) : ?>
			<div class="jerseyNr"><?php echo $player->trikotNr ?></div>
		<?php endif; ?>

		<a class="playername"><?php echo $player->name ?></a>

		<dl class="player">

		<?php if (!empty($player->alter)) : ?>
			<dt>Alter</dt>
			<dd><?php echo $player->alter ?></dd>
		<?php endif; ?>
		<?php if (!empty($player->positions)) : ?>
			<dt>Position</dt>
			<dd><?php echo $player->positions ?></dd>
		<?php endif; ?>
		</div>
	
	<?php endforeach; ?>


</div>
