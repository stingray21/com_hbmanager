<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div id="" class="">
<?php
echo '<h1>'.JText::_('COM_HBTEAM_TEAMS').'</h1>'."\n";


foreach ($this->teams as $team) 
{
	//echo '=> view->players<br><pre>'; print_r($team); echo '</pre>';
	?>
	<div>
		<div>
			<a href="<?php echo JURI::Root().'index.php/'.$this->link.'/'.strtolower($team->kuerzel)?>">
				<img src="<?php 
				echo $this->picPath.$team->dateiname?>" id="<?php
				echo 'pic_'.$team->kuerzel?>" alt="Bild <?php 
				echo $team->mannschaft?>"  />
			</a>
		</div>
		
		<div>
			<a href="<?php echo JURI::Root().'index.php/'.$this->link.'/'.strtolower($team->kuerzel)?>">
				<h3><?php echo $team->mannschaft; ?>
				<span><?php echo $team->liga; ?></span></h3>
			</a>

			<dl>

			<dt><?php echo JText::_('COM_HBTEAM_COACH');?></dt>
			<?php
			//echo "Trainer<pre>";print_r($team->trainer);echo "</pre>";
			foreach ($team->trainer as $curTrainer)
			{
				echo "<dd>";
				if(isset($curTrainer->name)) echo '<span class="trainerName">'.$curTrainer->name.' </span>';
				if(isset($curTrainer->contact)) {
					echo '<span class="trainerContact">';
					foreach ($curTrainer->contact as $contact)
					{
						echo '<br/>'.$contact;
					}
					echo '</span>';
				}
				echo "</dd>";
			}
			?>
			<dt><?php echo JText::_('COM_HBTEAM_TRAINING_TIMES');?></dt>
			<dd>
			<div>
			<?php
			foreach ($team->trainings as $training) 
			{
				echo '<div>';
				echo '<span>'.$training->tag.'</span> ';
				echo '<span>'.$training->beginn."</span> ";
				echo '- ';
				echo '<span>'.$training->ende."</span> Uhr";
				if (!empty($training->halleAnzeige)) {
					echo ' <span>';
					echo $training->halleAnzeige;
					echo '</span>';
				}
				if ($training->bemerkung != "") {
					echo '<span>';
					echo " (".$training->bemerkung.")";
					echo '</span>';
				}
				echo '<div>'."\n";
			}
			?>
			</div>
			</dd>
			</dl>
			
		</div>
	</div>
	<?php
}

?>
</div>