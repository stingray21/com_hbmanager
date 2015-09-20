<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_HBGYMS_TITLE');?></h1>

<?php
if ($this->showMap == true) {
	echo '<div id="map-canvas"></div>'."\n";
}

JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT.'/models/forms/teams.xml');
?>
		
<form class="form-validate" action="" method="post" id="updateGyms" name="updateGyms">
	<fieldset class="adminform">
		<?php
		echo $form->getLabel('teamauswahl', 'hbteams');	
		echo $form->getInput('teamauswahl', 'hbteams');			
		?>
	</fieldset>
</form>	

<h2 name="hallenAuswahl" id="hallenAuswahl"><?php echo JText::_('COM_HBGYMS_GYM_ALLGYMS');?></h2>

<table id="hallenvztbl" class="hallenvz">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_HBGYMS_GYM_NUMBER');?></th>
			<th><?php echo JText::_('COM_HBGYMS_GYM_NAME');?></th>
			<th><?php echo JText::_('COM_HBGYMS_GYM_ADDRESS');?></th>
			<th></th>
			<th><?php echo JText::_('COM_HBGYMS_GYM_PHONE');?></th>
			<th><?php echo JText::_('COM_HBGYMS_GYM_GLUE');?></th>
		</tr>
	</thead>
	
	<tbody>
<?php
foreach ($this->gyms as $gym)
{
	$destination = implode(' ',array($gym->plz, $gym->stadt, $gym->strasse));
	$link = 'https://maps.google.com/maps?saddr='.urlencode($this->start).'&daddr='.urlencode($destination).'&ie=UTF8';
	
	?>
	<tr id="gym<?php echo $gym->hallenNr?>" name="<?php echo $gym->hallenNr?>"<?php 
		echo ($this->focus == $gym->hallenNr) ? ' class="highlighted"' : '';
		?>>
		<td><?php echo $gym->hallenNr?></td>
		<td><?php echo $gym->hallenName?><br />(<?php echo $gym->kurzname?>)</td>
		<td class="link"><a href="<?php echo $link?>"><?php
			echo $gym->plz.' '.$gym->stadt;
			echo !empty($gym->strasse) ? '<br />'.$gym->strasse : '';?></a></td>
	<?php // 	echo '<td class="map"><a href="'.$link.'">'.JHtml::image('com_hbhallenvz/google-maps-standing.png', 'Google maps link', array('height' => 32) , true).'</a></td>';?>
		<td><a href="<?php echo $link?>" target="_BLANK"><span class="google-map-icon"></span></a></td>
		<td><?php echo $gym->telefon?></td>
		<td><?php echo $gym->haftmittel?></td>
	</tr>
<?php
}
?>	
	</tbody>
</table>
