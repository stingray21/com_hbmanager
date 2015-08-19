<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = true; //true: user-time, false:server-time

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
		echo JHTML::_('date', $team->update , 'D, d.m.Y - H:i:s \U\h\r', $tz);
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

echo '<a id="hvwupdateall" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showData&nojs=1').
		'">'.JText::_('COM_HBMANAGER_DATA_SHOW_NOJS_LINK').'</a>';