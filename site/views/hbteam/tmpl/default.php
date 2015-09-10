<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pic = $this->picture;
$picPath = './hbdata/images/teams/2014-2015/600px/';
$picPathFull = './hbdata/images/teams/2014-2015/3000px/';
?>
<div class="hbteam">

<h1><?php echo $this->team->mannschaft; ?>
 <span><?php echo $this->team->liga; ?></span></h1>
<?php

if (!empty($pic->filename) AND file_exists($picPath.$pic->filename))
{
?>
<div>
	<a href="<?php echo $picPathFull.$pic->filename ?>" target="_BLANK">
	<img src="<?php echo $picPath.$pic->filename?>" id="teampic_image" alt="<?php echo $pic->comment ?>"  />
	</a>
	
	<?php 
	if (!empty($pic->caption))
	{
		?>
		<dl>
			<?php 
			foreach ($pic->caption as $caption)
			{
				?>
				<dt><?php echo $caption->headline;?></dt>
				<dd><?php echo $caption->content;?></dd>
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
	
?>
</div>

