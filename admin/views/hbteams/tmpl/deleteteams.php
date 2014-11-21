<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;

// Button
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showTeams').
		'">'.JText::_('COM_HBMANAGER_BACK').'</a>';

// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.'/models'.
		'/forms/hbteams.xml');
?>
		
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams')
				?>" method="post" id="updateTeams" name="updateTeams">


	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_SELECT_TEAM'); ?>
			</legend>
			
			
			<table id="teamstable" name="teamstable" class="deleteTeams" >
			
				<tr>	
					<?php 
echo '<th>'.$form->getLabel('deleteTeam','hbDeleteTeam').'</th>'."\n";
$fields = array('reihenfolge', 'kuerzel', 'mannschaft', 'name', 'nameKurz', 
		'ligaKuerzel', 'liga', 'geschlecht', 'jugend', 'hvwLink');
//echo __FILE__.' - '.__LINE__.'<pre>';print_r($this->teams); echo'</pre>';
foreach ($fields as $key) {
	echo '<th>';
	echo $form->getLabel($key, 'hbteam');
	echo '</th>';
	echo "\n";
}
					?>
				</tr>
				<?php 
foreach ($this->teams as $i => $team)
	{
		echo '<tr>';
		
		echo '<td>';
			echo hbhelper::formatInput($form->getInput('deleteTeam', 
					'hbDeleteTeam'), $i);
			echo hbhelper::formatInput($form->getInput('kuerzel', 
					'hbDeleteTeam', $team->kuerzel), $i);
		echo '</td>';
		echo "\n";
		
		$team =  (array) $team;
		foreach ($fields as $key) {					
			$team[$key] = str_replace("http://www.hvw-online.org/?A","?A", $team[$key]);
			echo '<td>';
			echo $team[$key];
			echo '</td>';
			echo "\n";
		}
		echo '</tr>';
		echo "\n\n";
	}

			?>
			</table>

			<div class="clr"></div>
			<input class="submit" type="submit" name="deleteTeams_button" id="deleteTeams_button" value="<?php 
					echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_DELETE_TEAMS') ?>" />
		</fieldset>
	
	</div>
</form>	
