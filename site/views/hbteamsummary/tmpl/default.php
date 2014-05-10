<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$picPath = JURI::Root().'hbdata/images/teams/';
?>

<div class="hbteam">
<h1>Mannschaften</h1>
<?php

foreach ($this->teams as $team) 
{
	//echo '=> view->players<br><pre>'; print_r($team); echo '</pre>';
	?>
	<div class="teamsummary">
		<div class="teamsummarypic">
		<a href="<?php echo JURI::Root().'index.php/'.$this->link.'/'.strtolower($team->kuerzel)?>">
			<img src="<?php 
			echo $picPath.$team->dateiname?>" id="<?php
			echo 'pic_'.$team->kuerzel?>" alt="Bild <?php 
			echo $team->mannschaft?>"  />
		</a>
	</div>
	
	<div class="teamsummaryinfo">
	<h3><?php echo $team->mannschaft; ?>
	<span><?php echo $team->liga; ?></span></h3>
	
	<dl class="training">

	<dt>Trainer</dt>
	<?php
	//echo "Trainer<pre>";print_r($team->trainer);echo "</pre>";
	foreach ($team->trainer as $curTrainer)
	{
		echo "<dd>";
		if(isset($curTrainer->name)) echo '<span class="trainerName">'.$curTrainer->name.'</span>';
		if(isset($curTrainer->contact)) echo " <br />(&nbsp;".$curTrainer->contact."&nbsp;)";
		echo "</dd>";
	}
	?>
	<dt class="times">Trainingszeiten</dt>
	<?php
	foreach ($team->trainings as $training) 
	{
		echo '<dd><span class="weekday">'.$training->tag.'</span> ';
		echo $training->beginn." - ".$training->ende." Uhr";
		if ($training->bemerkung != "") echo " (".$training->bemerkung.")";
		if (!empty($training->halleAnzeige)) echo " (".$training->halleAnzeige.")";
		echo "</dd>";
	}
	?>
	</dl>
	</div>
	
	<?php
//	echo '<a class="playername">';
//	echo $player->name;
//	echo '</a>';
//	echo '<dl class="player">';
//	//echo '<dt>Name</dt>';
//	//echo '<dd>'.$player->name.'</dd>';
//	if (!empty($player->alter)) {
//		echo '<dt>Alter</dt>';
//		echo '<dd>'.$player->alter.'</dd>';
//	}
//	if (!empty($player->positions)) {
//		echo '<dt>Position</dt>';
//		echo '<dd>'.$player->positions.'</dd>';
//	}
	echo '</div>';
}

?>
</div>