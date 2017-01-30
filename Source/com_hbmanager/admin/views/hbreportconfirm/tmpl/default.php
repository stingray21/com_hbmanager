<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = 'Europe/Berlin';


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbreport.xml');
?>
<h3><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TITLE')?></h3>
<dl>
	<dt><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_GAMEID')?></dt>
		<dd><?php echo $this->data['gameInfo']['gameId']?></dd>
	<dt><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_LEAGUE')?></dt>
		<dd><?php echo $this->data['gameInfo']['league']?></dd>
	<dt><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_DATE')?></dt>
		<dd><?php echo $this->data['gameInfo']['date']?></dd>
</dl>

<?php $teams = array('goalsHome', 'goalsAway');
foreach ($teams as $team) :
?>
<table>
	<tr>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_NAME')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TRIKOTNR')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TW')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TORE')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_7M')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_GELB')?></th>
		<th colspan="3"><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_2MIN')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_ROT')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_BEMERKUNG')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TEAMZSTR')?></th>
	</tr>

	<?php foreach ($this->data[$team] AS $row) : ?>
		
	<tr>
		<td><?php echo ($row['name'] != '') ? $row['name'] : '<b>'.$row['alias'].'</b>';?></td>
		<td><?php echo $row['trikotNr']?></td>
		<td><?php echo ($row['tw'] == 1) ? 'TW' : '';?></td>
		<td><?php echo $row['tore']?></td>
		<td><?php echo ($row['7m']+$row['tore7m'] != 0) ? $row['7m'].'/'.$row['tore7m'] : '';?></td>
		<td><?php echo $row['gelb']?></td>
		<td><?php echo $row['2min1']?></td>
		<td><?php echo $row['2min2']?></td>
		<td><?php echo $row['2min3']?></td>
		<td><?php echo $row['rot']?></td>
		<td><?php echo $row['bemerkung']?></td>
		<td><?php echo $row['teamZstr']?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php endforeach ?>


<table>
	<tr>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_INDEX')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_TIMESTRING')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_SCORE')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_TEAM')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_NAME')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_NUMBER')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_CATEGORY')?></th>
		<th><?php echo JText::_('COM_HBMANAGER_REPORTINPUT_CONFIRM_TEXT')?></th>
	</tr>

	<?php foreach ($this->data['action'] AS $row) : ?>
		
	<tr>
		<td><?php echo $row['actionIndex']?></td>
		<td><?php echo $row['timeString']?></td>
		<td><?php echo (!empty($row['scoreHome'])) ? $row['scoreHome'].':'.$row['scoreAway'] : ''?></td>
		<td><?php if ($row['team'] === -1) { echo 'H';}
				  elseif ($row['team'] === 1) { echo 'A'; }?></td>
		<td><?php echo $row['name']?></td>
		<td><?php echo ($row['number']) ? '('.$row['number'].')':'';?></td>
		<td><?php echo $row['category']?></td>
		<td><?php echo $row['text']?></td>
	</tr>
	<?php endforeach ?>
</table>


<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=addReport') 
		?>" method="post" id="addReport" name="addReport">

	<div class="fltlft">
	
		<fieldset class="adminform">
			
			<?php 
			echo $form->getInput('dataString','hbreport',$this->dataString);
			?>
			<input name="dataString" id="dataString" type="hidden" value"<?php echo $this->dataString;?>" />
			
			<input class="submit" type="submit" name="addReport_button" id="addReport_button" value="<?php echo JText::_('COM_HBMANAGER_REPORTINPUT_SUBMIT_ADDREPORT') ?>" />
		</fieldset>
	
	</div>
</form>	

