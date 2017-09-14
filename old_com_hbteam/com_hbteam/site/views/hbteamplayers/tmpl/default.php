<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>



<div>
	
<?php 
if (isset($this->noTeamMessage))
{
?>
	<div>
		<p><?php echo $this->noTeamMessage; ?></p>
	</div>
<?php 
} else {
?>

	<h1><?php echo $this->team->mannschaft; ?>
	 <span><?php echo $this->team->liga; ?></span></h1>
	<?php

	foreach ($this->players as $player) 
	{
		//echo '=> view->players<br><pre>'; print_r($player); echo '</pre>';
		?>
		<div class="player">
			<div class="playerpic">
			<a href="<?php echo $this->picPath.$player->saison.'_'.$player->alias.'.jpg'?>">
				<img src="<?php 
				echo $picPath.$player->saison.'_'.$player->alias.'.jpg'?>" id="<?php
				echo 'pic_'.$player->alias?>" alt="Bild <?php 
				echo $player->name.'"  />';
			echo '</a>';
		echo '</div>';


		if (!empty($player->trikotNr)) {
			echo '<div class="jerseyNr">'.$player->trikotNr.' </div>';
		}

		echo '<a class="playername">';
		echo $player->name;
		echo '</a>';
		echo '<dl class="player">';
		//echo '<dt>Name</dt>';
		//echo '<dd>'.$player->name.'</dd>';
		if (!empty($player->alter)) {
			echo '<dt>Alter</dt>';
			echo '<dd>'.$player->alter.'</dd>';
		}
		if (!empty($player->positions)) {
			echo '<dt>Position</dt>';
			echo '<dd>'.$player->positions.'</dd>';
		}
		echo '</div>';
	}
}
?>
</div>