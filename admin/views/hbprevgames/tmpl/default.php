<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = false; //true: user-time, false:server-time

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
						JHtml::_('date', $date, 'd.m.y', $tz));
				?>
				</dd>
			</dl>
			<div class="clr"></div>	
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_PREV_GAMES');?></h3>
			<dl>
				<dt>
				<?php
					echo $form->getLabel('prevStart', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('prevStart', 'hbdates', 
							$this->dates['prevStart']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('prevEnd', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('prevEnd', 'hbdates', 
							$this->dates['prevEnd']);
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
$form = JForm::getInstance('myformgames', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/hbprevgames.xml');
?>
<form class="hbmanager form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=showPrevGames') ?>" method="post" id="gamesForm" name="gamesForm">

<div class="fltlft spiele">

		<fieldset class="adminform">		
			<legend>
				<?php 
				echo JText::_('COM_HBMANAGER_PREVGAMES_HEADLINE');
				?>
			</legend>

<?php 
		$i = 0;
		foreach ($this->games as $key => $value)
		{
			echo '<h3>'.JHtml::_('date', $key, 'l, j. F', $tz).'</h3>';
			
			foreach ($value as $game)
			{
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				
				echo '<div class="spieleSpiel">'."\n";
				
					echo '<h4>'.$game->mannschaft.' ('.$game->ligaKuerzel.')</h4>'."\n";
					
					echo '<div class="spieleInfos">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo '<label>Spiel-Infos</label>';
							echo '</dt>'."\n";
							echo '<dd>'."\n"; 
								echo '<table>'."\n";
									echo '<tr><th>'.$game->heim.'</th><td>'.$game->toreHeim.'</td></tr>'."\n";
									echo '<tr><th>'.$game->gast.'</th><td>'.$game->toreGast.'</td></tr>'."\n";
								echo '</table>'."\n";
								echo '<p>SpielNr.: '.$game->spielIdHvw.'</p>'."\n"; 
								echo '<p>'.
									JHtml::_('date', $game->datum, 'd.m.y', $tz).
									' um '.
									JHtml::_('date', $game->zeit, 'H:i', $tz).
									' Uhr</p>'."\n";
								echo '<p>Hallennr.: '.$game->hallenNr.'</p>'."\n";
								echo '<p>'.$game->bemerkung.'</p>'."\n";
							echo '</dd>'."\n"; 
						echo '</dl>'."\n"; 
					echo '</div>'."\n";
						
					echo '<div class="spieleBericht">'."\n";
						echo hbhelper::formatInput($form->getInput('spielIdHvw', 'hbprevgames', $game->spielIdHvw), $i)."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('bericht', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
							echo hbhelper::formatInput($form->getInput('bericht', 'hbprevgames', $game->bericht), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</td>';
					echo '</div>'."\n";
		
					echo '<div class="spieleZusatz">'."\n";
						echo '<dl>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielerliste', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('spielerliste', 'hbprevgames', $game->spielerliste), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('zusatz', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('zusatz', 'hbprevgames', $game->zusatz), $i);		
							echo '</dd>'."\n";
						echo '</dl>'."\n";
						echo '<dl class="spieleTore">'."\n";
							echo '<dt>';
								echo $form->getLabel('halbzeitstand', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('halbzeitstand', 'hbprevgames', $game->halbzeitstand), $i);		
							echo '</dd>'."\n";
							echo '<dt>';
								echo $form->getLabel('spielverlauf', 'hbprevgames');
							echo '</dt>'."\n";
							echo '<dd>';
								echo hbhelper::formatInput($form->getInput('spielverlauf', 'hbprevgames', $game->spielverlauf), $i);
							echo '</dd>'."\n";
						echo '</dl>'."\n";
					echo '</div>'."\n";
				echo '</div>'."\n";
				
				$i++;
			}
			
		}	
	
?>
<div class="clr"></div>
			<input type="hidden" name="hbdates[prevStart]" id="hbdates[prevStart]" value="<?php echo $this->dates['prevStart']?>" />
			<input type="hidden" name="hbdates[prevEnd]" id="hbdates[prevEnd]" value="<?php echo $this->dates['prevEnd']?>" />
			

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
