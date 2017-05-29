<?php
defined('_JEXEC') or die;

class HbmanagerTableActions extends JTable
{
	
	function __construct(&$db) 
	{
		$primaryKeys = array('season', 'spielIdHvw','actionIndex');
		parent::__construct( 'hb_spielbericht_details', $primaryKeys, $db );
	} 
}
