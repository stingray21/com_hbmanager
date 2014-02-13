<?php
//$teamkey = 'M1';
$jinput = JFactory::getApplication()->input;
$teamkey = $jinput->get('teamkey');

$this->model->updateTeam($teamkey);

$this->model->getUpdateDate($teamkey);
echo $newRow = "updated";