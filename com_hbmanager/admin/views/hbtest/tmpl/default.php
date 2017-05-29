<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = true; //true: user-time, false:server-time

JToolBarHelper::preferences('com_hbmanager');

// Button
//echo '<a id="hvwupdateall" class="hbbutton" href="'.
//		JRoute::_('index.php?option=com_hbmanager&task=updateData&teamkey=all').
//		'">'.JText::_('COM_HBMANAGER_DATA_UPDATE_ALL_BUTTON').'</a>';
//
//echo '<div class="clr"></div>';

echo '<h3>no JavaScript</h3>';



//echo '<a id="hvwupdateall" href="'.
//		JRoute::_('index.php?option=com_hbmanager&task=showData').
//		'">'.JText::_('COM_HBMANAGER_DATA_SHOW_JS_LINK').'</a>';