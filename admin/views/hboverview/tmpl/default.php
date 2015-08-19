<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// Button
echo '<a id="showall" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showAllGames').
		'">'.JText::_('COM_HBMANAGER_OVERVIEW_ALL_BUTTON').'</a>';
// Button
echo '<a id="showhome" class="hbbutton" href="'.
		JRoute::_('index.php?option=com_hbmanager&task=showHomeGames').
		'">'.JText::_('COM_HBMANAGER_OVERVIEW_HOME_BUTTON').'</a>';
echo '<div class="clr"></div>';

echo '<h3>'.JText::_('COM_HBMANAGER_OVERVIEW_TITLE').'</h3>';

//$datePattern = "%A, %d.%m.%Y &nbsp;&nbsp;%H:%M:%S Uhr";
$datePattern = 'D, d.m.Y - H:i:s \U\h\r';

foreach ($this->teams as $team)
{
	echo '<h4><b>'.$team->mannschaft.' </b>';
	//echo '('.$team->kuerzel.') ';
	echo '</h4>';
	
	echo '<p>';
	if (!empty($team->liga)) echo $team->liga.' ('.$team->ligaKuerzel.')';
	echo '</p>';
	
	echo '<p>';
	if(empty($team->hvwLink)) {
		echo 'keine HVW Daten';
	}
	else {
		echo '<a href="'.$team->hvwLink.'">'.$team->hvwLink.'</a>';
	}
	echo '</p>';
	
	echo "\n";
}