<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

$config = new JConfig();
$user = JFactory::getUser();
$userid = $user->id;

//// Button
//echo '<a id="addteams" class="hbbutton" href="'.
//		JRoute::_('index.php?option=com_hbmanager&task=addTeams').
//		'">'.JText::_('COM_HBMANAGER_TEAMS_ADD_TEAMS_BUTTON').'</a>';

// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbteammenus.xml');
?>
		
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=addTeamMenus') 
		?>" method="post" id="teamMenus" name="teamMenus">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMMENUS_FORM_TITLE'); ?>
			</legend>
			
			
			<table id="teamstable" name="teamstable">
				
				<?php
			//echo __FILE__.' - '.__LINE__.'<pre>';print_r($fields); echo'</pre>';
			echo '<thead><tr>';
			echo '<th></th>';
			foreach ($form->getFieldset('team') as $field) {
				echo '<th>';
				echo $field->label;
				echo '</th>';
			}
			echo '</tr></thead>'."\n\n";
			
			echo '<tbody>'."\n";
			
			//echo __FILE__.' - '.__LINE__.'<pre>';print_r($this->teams); echo'</pre>';
			foreach ($this->teams as $i => $team)
			{
				echo '<tr>'."\n";
				echo "\t".'<td>';
				echo $team->mannschaft;
				echo '</td>';
				foreach ($form->getFieldset('team') as $field) {
					//echo __FILE__.' - '.__LINE__.'<pre>';print_r($field->fieldname); echo'</pre>';
					echo "\t".'<td>';
					
					if (strpos($field->fieldname, 'add][') !== false ) {
						$value = isset($team->menus[$field->fieldname]) ? true : false;
					} else {
						$value = $team->{$field->fieldname};
					}
					$input = $form->getInput($field->fieldname, 'hbteammenus', $value );
					echo hbhelper::formatInput($input, $i);
					echo '</td>'."\n";
				}		
				echo '</tr>';
				echo "\n\n";
			}
			?>
			<tbody>
			</table>
			
			<div class="clr"></div>
			
			<input class="submit" type="submit" name="addTeamMenus_button" id="addTeamMenus_button" value="<?php 
				echo JText::_('COM_HBMANAGER_TEAMMENUS_SUBMIT_UPDATE_TEAMS') ?>" />
		
	
	</div>
</form>	


