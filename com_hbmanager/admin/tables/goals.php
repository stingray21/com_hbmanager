<?php
defined('_JEXEC') or die;

class HbmanagerTableGoals extends JTable
{
	
	function __construct(&$db) 
	{
		$primaryKeys = array('season', 'gameIdHvw', 'alias', 'number');
		parent::__construct( '#__hb_game_player', $primaryKeys, $db );
	} 
}
