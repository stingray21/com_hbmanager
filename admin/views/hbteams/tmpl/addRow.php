<?php

$jinput = JFactory::getApplication()->input;
$newRowNr = $jinput->get('rowNr', 99);

$form = JForm::getInstance('myform', JPATH_ADMINISTRATOR.
					'/components/com_hbmanager/models'.
					'/forms/hbteams.xml');
//echo "<pre>";print_r($form);echo "</pre>";
$newRow = '<tr>';
	
$fields =  array( 'reihenfolge', 'kuerzel', 'mannschaft', 'name', 
				'nameKurz', 'ligaKuerzel', 'liga', 'geschlecht',
				'jugend', 'hvwLink');

foreach ($fields as $field) {
	//echo $field;
	$input = $form->getInput($field, 'hbteam');
	//echo "<pre>";print_r($input);echo "</pre>";
	if (!empty($input)) {
		$newRow .= '<td>';
		$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/',
							"name=\"$1[".$newRowNr."][$2]", $input);
		$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', 
							"id=\"$1_".$newRowNr."_$2", $input);
		$newRow .= $input;
		$newRow .= '</td>';
		$newRow .=  "\n";
	}
}
	
$newRow .= '</tr>';
$newRow .= "\n\n";

echo $newRow;