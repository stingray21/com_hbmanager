<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pic = $this->picture;

?>
<h1><?php echo $this->msg; ?></h1>

<div class="teampic">
	<a href="<?php echo './hbdata/images/teams/'.$pic->filename ?>">
	<img src="<?php echo './hbdata/images/teams/'.$pic->filename?>" id="teampic_image" alt="<?php echo $pic->comment ?>"  />
	</a>
	
	<?php echo $pic->caption ?>
</div>
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