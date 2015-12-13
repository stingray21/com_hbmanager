<?php
defined('_JEXEC') or die;

class HbmanagerTableContacts extends JTable
{
//	var $spielIdHvw = null;
//	var $alias = null;
//	var $saison = null;
//	var $kuerzel = null;
//	var $trikotNr = null;
//	var $tw = null;
//	var $tore = null;
//	var $7m = null;
//	var $tore7m = null;
//	var $gelb = null;
//	var $2min1 = null;
//	var $2min2 = null;
//	var $2min3 = null;
//	var $rot = null;
//	var $bemerkung = null;
//	var $teamZstr = null;
	
	function __construct(&$db) 
	{
		$primaryKeys = array('id');
		parent::__construct( '#__contact_details', $primaryKeys, $db );
	} 
}
