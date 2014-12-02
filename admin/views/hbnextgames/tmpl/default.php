<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).
		'/components/com_hbmanager/css/default.css');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
				'/models/forms/hbdates.xml');
?>

<form class="hbmanager form-validate" action="<?php 
	JRoute::_('index.php?option=com_hbmanager&task=showJournal') 
	?>" method="post" id="datesForm" name="datesForm">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php 
				echo JText::_('COM_HBMANAGER_DATE_SETTINGS');
				?>
			</legend>
			<dl>
				<dt>
				<?php echo $form->getLabel('date', 'hbDates'); ?>
				</dt>
				<dd>
				<?php 
				if (isset($this->dates['date'])) $date = $this->dates['date'];
				else $date = null;
				echo $form->getInput('date', 'hbDates', 
						strftime("%d.%m.%Y", strtotime($date))); 
				?>
				</dd>
			</dl>
			<div class="clr"></div>	
			
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_NEXT_GAMES');?></h3>
			
			<dl>
				<dt>
				<?php
					echo $form->getLabel('nextStart', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('nextStart', 'hbdates', 
							$this->dates['nextStart']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('nextEnd', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('nextEnd', 'hbdates', 
							$this->dates['nextEnd']);
				?>
				</dd>
			</dl>
			
			<div class="clr"></div>
			<input class="submit" type="submit" name="date_button" id="date_button" value="<?php 
				echo JText::_('COM_HBMANAGER_DATE_UPDATE_BUTTON');?>"/>
		</fieldset>
	
	</div>
</form>	
<div class="clr"></div>

<?php
$form = JForm::getInstance('myformgames', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbnextgames.xml');
?>
<form class="hbmanager form-validate" action="<?php
	echo JRoute::_('index.php?option=com_hbmanager&task=showNextGames') 
	?>" method="post" id="gamesForm" name="gamesForm">

<div class="fltlft spiele">

		<fieldset class="adminform">
			<legend>
				<?php 
					echo JText::_('Kommende Spiele'); 
				?>
			</legend>

<?php 
$i = 0;
foreach ($this->games as $key => $value)
{
	echo '<h3>'.strftime("%A, %d.%m.%Y (KW%V)", strtotime($key)).'</h3>'."\n"; 

	foreach ($value as $game)
	{
		echo '<div class="spieleSpiel">'."\n";
		
			echo '<h4>'.$game->mannschaft.' ('.$game->ligaKuerzel.')</h4>'."\n";
			
			echo '<div class="spieleInfos">'."\n";
				echo '<dl>'."\n";
					echo '<dt>';
						echo '<label>Spiel-Infos</label>';
					echo '</dt>'."\n";
					echo '<dd>'."\n"; 
						echo '<table>'."\n";
							echo '<tr><th>'.$game->heim.'</th></tr>'."\n";
							echo '<tr><th>'.$game->gast.'</th></tr>'."\n";
						echo '</table>'."\n";
						echo '<p>SpielNr.: '.$game->spielIdHvw.'</p>'."\n"; 
						echo '<p>'.
								strftime("%d.%m.%Y", strtotime($game->datum)).
								' um '.$game->zeit.' Uhr</p>'."\n";
						echo '<p>Hallennr.: '.$game->hallenNr.'</p>'."\n";
						echo '<p>'.$game->bemerkung.'</p>'."\n";
					echo '</dd>'."\n"; 
				echo '</dl>'."\n"; 
			echo '</div>'."\n";

			echo '<div class="spieleVorschau">'."\n";
				echo hbhelper::formatInput($form->getInput('spielIdHvw', 
						'hbnextgames', $game->spielIdHvw), $i)."\n";
				echo '<dl>'."\n";
					echo '<dt>';
						echo $form->getLabel('vorschau', 'hbnextgames');
					echo '</dt>'."\n";
					echo '<dd>';
					echo hbhelper::formatInput($form->getInput('vorschau', 
							'hbnextgames', $game->vorschau), $i);
					echo '</dd>'."\n";
				echo '</dl>'."\n";
			echo '</td>';
			echo '</div>'."\n";

			echo '<div class="spieleZusatz">'."\n";
				echo '<dl>'."\n";
					echo '<dt>';
						echo $form->getLabel('treffOrt', 'hbnextgames');
					echo '</dt>'."\n";
					echo '<dd>';
						echo hbhelper::formatInput($form->getInput('treffOrt', 
								'hbnextgames', $game->treffOrt), $i);		
					echo '</dd>'."\n";
					echo '<dt>';
						echo $form->getLabel('treffZeit', 'hbnextgames');
					echo '</dt>'."\n";
					echo '<dd>';
						echo hbhelper::formatInput($form->getInput('treffZeit', 
								'hbnextgames', $game->treffZeit), $i);		
					echo '</dd>'."\n";
				echo '</dl>'."\n";
			echo '</div>'."\n";
		echo '</div>'."\n";

		$i++;
	}

}
?>
<div class="clr"></div>

			<input type="hidden" name="hbdates[nextStart]" id="hbdates[nextStart]" value="<?php echo $this->dates['nextStart']?>" />
			<input type="hidden" name="hbdates[nextEnd]" id="hbdates[nextEnd]" value="<?php echo $this->dates['nextEnd']?>" />
			
			<input type="hidden" name="userid" id="userid" value="<?php echo $userid?>" />
			<?php
			if (isset($_REQUEST["Itemid"])) {
				echo '<input type="hidden" name="Itemid" id "Itemid" value="'.$_REQUEST["Itemid"].'" />';
			}
			?>
			<input class="submit" type="submit" name="update_button" id="update_button" value="Berichte speichern" />
			<input class="submit" type="submit" name="article_button" id="article_button" value="Artikel einstellen" />
		</fieldset>
		
	</div>
	
</form>	
