<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<h1><?php echo $this->msg; ?></h1>

<?php
	$modules =& JModuleHelper::getModules('HBtrainingInComponent');
	foreach ($modules as $module){
		echo JModuleHelper::renderModule($module);
	}
	$modules =& JModuleHelper::getModules('HBscheduleInComponent');
	foreach ($modules as $module){
		echo JModuleHelper::renderModule($module);
	}
	
	$modules =& JModuleHelper::getModules('HBstandingsInComponent');
	foreach ($modules as $module){
		echo JModuleHelper::renderModule($module);
	}
	
	$modules =& JModuleHelper::getModules('HBHVWlinkInComponent');
	foreach ($modules as $module){
		echo JModuleHelper::renderModule($module);
	}
	
?>