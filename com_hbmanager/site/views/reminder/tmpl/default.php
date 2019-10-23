<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<h1><?php echo JText::_('COM_HBMANAGER_REMINDER_TITLE'); ?></h1>

<?php
// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();

?>
<div id="reminder">


</div>