<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

//echo '<p>Javascript</p>';
// Button
echo '<a id="hvwupdateall" class="hbbutton" './/'href="'.
		//JRoute::_('index.php?option=com_hbmanager&task=updateData&teamkey=all').
		'">'.JText::_('COM_HBMANAGER_DATA_UPDATE_ALL_BUTTON').'</a>';

echo '<div id="eggtimer"><img src="../media/com_hbmanager/images/eggtimer.gif" /></div>';

echo '<div class="clr"></div>';

//$user = JFactory::getUser();
//$timeZone = $user->getParam('timezone', 'UTC');
//echo 'time zone: '.$timeZone;

echo '<table id="hvwupdate">';
echo '<tr><th>Mannschaft</th><th>letztes Update</th><th></th></tr>'."\n";

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

foreach ($this->teams as $team)
{
	echo '<tr>';
	echo '<td><b>'.$team->mannschaft.' </b>('.$team->kuerzel.') </td>';
	
	
	echo '<td id="update_'.$team->kuerzel.'"';
	if(in_array($team->kuerzel, $this->updated)) 
			echo ' class="updated"';
	echo '>';
	if(empty($team->hvwLink)) echo 'keine HVW Daten';
	if (!empty($team->update)) 
		echo JHTML::_('date', $team->update , $datePattern);
	echo '</td>';
		
	if (!empty($team->hvwLink)) {
		echo '<td><a id="'.$team->kuerzel.'" class="hbbutton updatebutton" './/href="'.
			//JRoute::_('index.php?option=com_hbmanager&task=updateData&teamkey='.
			//$team->kuerzel).
			'"> UPDATE </a></td>';
	}
	echo '</tr>'."\n";
}

echo '</table>';
