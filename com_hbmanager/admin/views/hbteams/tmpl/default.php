<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;

// Button
echo '<a id="addteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=addTeams').
		'">'.JText::_('COM_HBMANAGER_TEAMS_ADD_TEAMS_BUTTON').'</a>';

// Button
echo '<a id="deleteteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=deleteTeams').
		'">'.JText::_('COM_HBMANAGER_TEAMS_DELETE_TEAMS_BUTTON').'</a>';


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbteams.xml');
?>
		
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams') 
		?>" method="post" id="updateTeams" name="updateTeams">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_FORM_TITLE'); ?>
			</legend>
			
			
			<table id="teamstable" name="teamstable">
				
				<?php 
			$fields = array('reihenfolge', 'kuerzel', 'mannschaft', 'name', 'nameKurz', 
					'ligaKuerzel', 'liga', 'geschlecht', 'jugend', 'hvwLink');
			//echo __FILE__.' - '.__LINE__.'<pre>';print_r($this->teams); echo'</pre>';
			echo '<tr>';
			foreach ($fields as $key) {
				echo '<th>';
				echo $form->getLabel($key, 'hbteam');
				echo '</th>';
				echo "\n";
			}
			echo '</tr>'."\n\n";
			
			foreach ($this->teams as $i => $team)
			{
				echo '<tr>';

				$team =  (array) $team;
				foreach ($fields as $key) {					
					$team[$key] = str_replace("http://www.hvw-online.org/?A","?A", $team[$key]);
					$input = $form->getInput($key, 'hbteam', $team[$key]);
					if (!empty($input)) {
						echo '<td>';
						echo hbhelper::formatInput($input, $i);
						echo '</td>';
						echo "\n";
					}
				}
				echo '</tr>';
				echo "\n\n";
			}

			?>
			</table>
			
			<?php 
			// Button
			echo '<a id="addcustomteam" name="addcustomteam" class="hbbutton">+</a>';
			?>
			
			<div class="clr"></div>
			
			<input class="submit" type="submit" name="updateTeams_button" id="updateTeams_button" value="<?php 
				echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_UPDATE_TEAMS') ?>" />
		
	
	</div>
</form>	


