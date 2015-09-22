<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbgoals.xml');


if (!empty($this->confirmation)) {
	//echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($this->confirmation); echo'</pre>';
	
	echo '<h3>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TITLE').'</h3>';
	echo '<dl class="confirmImport">';
		echo '<dt>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_SPIELIDHVW').'</dt> '
				. '<dd>'.$this->confirmation[0]['spielIdHvw'].'</dd>';
		echo '<dt>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_KUERZEL').'</dt> '
				. '<dd>'.$this->confirmation[0]['kuerzel'].'</dd>';
		echo '<dt>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_SAISON').'</dt> '
				. '<dd>'.$this->confirmation[0]['saison'].'</dd>';
	echo '</dl>';
	echo '<table id="goalsConfirm">';
	echo '<tr>';
	
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_NAME').'</th>';
	//echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_KUERZEL').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TRIKOTNR').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TW').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TORE').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_7M').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_GELB').'</th>';
	echo '<th colspan="3">'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_2MIN').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_ROT').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_BEMERKUNG').'</th>';
	echo '<th>'.JText::_('COM_HBMANAGER_GOALSINPUT_CONFIRM_TEAMZSTR').'</th>';
	echo '</tr>';
	
	foreach ($this->confirmation AS $row) {
		//echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($row); echo'</pre>';
		echo '<tr>';
		//echo '<td>'.$row['spielIdHvw'].'</td>';
		echo '<td>';
		echo ($row['name'] != '') ? $row['name'] : '<b>'.$row['alias'].'</b>';
		echo '</td>';
		//echo '<td>'.$row['saison'].'</td>';
		//echo '<td>'.$row['kuerzel'].'</td>';
		echo '<td>'.$row['trikotNr'].'</td>';
		echo '<td>';
		echo ($row['tw'] == 1) ? 'TW' : '';
		echo '</td>';
		echo '<td>'.$row['tore'].'</td>';
		echo '<td>';
		echo ($row['7m']+$row['tore7m'] != 0) ? $row['7m'].' / '.$row['tore7m'] : '';
		echo '</td>';
		echo '<td>'.$row['gelb'].'</td>';
		echo '<td>'.$row['2min1'].'</td>';
		echo '<td>'.$row['2min2'].'</td>';
		echo '<td>'.$row['2min3'].'</td>';
		echo '<td>'.$row['rot'].'</td>';
		echo '<td>'.$row['bemerkung'].'</td>';
		echo '<td>'.$row['teamZstr'].'</td>';
		echo '</tr>';
	}
	echo '</table>';
}



?>
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=addGoals') 
		?>" method="post" id="addGoals" name="addGoals">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_GOALSINPUT_FORM_TITLE'); ?>
			</legend>
			
				<?php
			//echo __FILE__.' - '.__LINE__.'<pre>';print_r($fields); echo'</pre>';
			
			foreach ($form->getFieldset('goals') as $field) {
				echo $field->label;
				echo $field->input;
			}
			?>
			<div class="clr"></div>
			
			<input class="submit" type="submit" name="addGoals_button" id="addGoals_button" value="<?php 
				echo JText::_('COM_HBMANAGER_GOALSINPUT_SUBMIT_ADDGOALS') ?>" />
		
	
	</div>
</form>	


