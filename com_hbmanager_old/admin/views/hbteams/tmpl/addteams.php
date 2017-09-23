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

$model = $this->model;
// Button
echo '<a class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showTeams').
		'">'.JText::_('COM_HBMANAGER_BACK').'</a>';

// Button
echo '<a id="addteams" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=addTeams&getHvwData=1').
		'">'.JText::_('COM_HBMANAGER_TEAMS_ADD_TEAMS_UPDATEHVW_BUTTON').'</a>';


// get the JForm object
$form = JForm::getInstance('myform', 
		JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/hbteams.xml');
?>
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=showTeams')
		?>" method="post" id="addTeam" name="addTeam">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_TEAMS_SELECT_TEAM'); ?>
			</legend>
			
			<?php 
// echo __FILE__.' - '.__LINE__.'<pre>';print_r($this->leagues); echo'</pre>';
if (!empty($this->leagues)) 
{
	echo "\n\n".'<table id="teamstable" name="teamstable">'."\n";

	$fields = array('staffel','staffelName', 'url', 'geschlecht',
		'jugend','saison', 'mannschaftenTabelle', 'mannschaftenSpielplan');
	//echo __FILE__.' - '.__LINE__.'<pre>';print_r($this->leagues); echo'</pre>';
	echo '<tr>';
	echo '<th></th>';
	foreach ($fields as $key) {
		echo '<th>';
		echo $form->getLabel($key, 'hbAddTeam');
		echo '</th>';
		echo "\n";
	}
	echo '</tr>'."\n\n";

	foreach ($this->leagues as $i => $team)
	{
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($team); echo'</pre>';
		echo '<tr>'."\n";
		
		echo '<td>';
		echo hbhelper::formatInput($form->getInput('includeTeam', 'hbAddTeam', 
					$team->select['mannschaftenSpielplan'] !== false), $i);
		echo '</td>';
		
		$team =  (array) $team;
		foreach ($fields as $key) {			
			$input = $form->getInput($key, 'hbAddTeam', $team[$key]);		
			if (isset($team['select'][$key]) ) {
				$input = $model->getOptions($input, $team[$key], 
					$team['select'][$key]);
			}
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
	echo "\n\n".'</table>'."\n\n";
}
			
			
//				<?php 
//				$i = 0;
//				foreach ($this->leagues as $team)
//				{
//					
//					if (preg_match("/Geisl/",$team->rankingTeams)) {
//						echo '<tr>';
//						
//						$team = (array) $team;
//						
//						echo '<td>';
//						$checked = preg_match("/Geisl/",$team['rankingTeams']);
//						echo hbhelper::formatInput($form->getInput('includeTeam', 
//								'hbAddTeam', $checked), $i);
//						echo '</td>';
//						echo "\n";
//						
//						foreach (array('staffel','staffelName','staffelLink',
//							'geschlecht','jugend','saison') as $value)
//						{
//							echo '<td>';
//							echo hbhelper::formatInput($form->getInput($value, 
//								'hbAddTeam', $team[$value]), $i);
//							echo '</td>';
//							echo "\n";
//						}
//						
//						echo '<td>';
//						$input = hbhelper::formatInput($form->getInput('rankingName', 
//								'hbAddTeam'), $i);
//						echo $model->selectHomeTeam($input, $team['rankingTeams'], 
//								"/Geisl/");
//						echo '</td>';
//						echo "\n";
//						
//						echo '<td>';
//						$input = hbhelper::formatInput($form->getInput('scheduleName', 
//								'hbAddTeam'), $i);
//						echo $model->selectHomeTeam($input, $team['scheduleTeams'], 
//								"/Geisl/");
//						echo '</td>';
//						echo "\n";
//						echo '</tr>';
//						echo "\n\n";
//						$i++;
//					}
//				}
				?>

			<div class="clr"></div>
			<input class="submit" type="submit" name="addTeams_button" id="addTeams_button" value="<?php 
					echo JText::_('COM_HBMANAGER_TEAMS_SUBMIT_ADD_TEAMS') ?>" />
		</fieldset>
	
	</div>
</form>