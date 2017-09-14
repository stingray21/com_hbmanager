<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pic = $this->picture;
?>
<div id="hbteam">

<h1><?php echo $this->team->mannschaft; ?>
 <span><?php echo $this->team->liga; ?></span></h1>
<?php

//echo __FILE__.__LINE__.'<a href="'.$this->model->getImage('500').'">'.$this->model->getImage('500').'</a>';

if (!empty($this->model->getImage('800')) AND file_exists($this->model->getImage('1200')))
{
?>
<div id="teampic">
	<a href="<?php echo $this->model->getImage('1200') ?>" target="_BLANK">
	<img src="<?php echo $this->model->getImage('800')?>" id="teampic_image" alt="<?php echo $pic->comment ?>"  />
	</a>
	
	<?php 
	if (!empty($this->team->liste))
	{
		?>
		<dl>
			<?php 
			foreach ($this->team->liste as $line)
			{
				?>
				<dt><?php echo $line['titel'];?></dt>
				<dd><?php echo $line['namen'];?></dd>
				<?php
			}
			?>
		</dl>
		<?php
	}
?>	
</div>
<?php
}


	$modules =& JModuleHelper::getModules('HBteam');
	foreach ($modules as $module){
		echo JModuleHelper::renderModule($module);
	}
	

if ($this->standingsChart) {
	echo '<div id="chart"></div>';
}
?>	
</div>

