<?php
defined('_JEXEC') or die;

class HbmanagerTableContacts extends JTable
{

	function __construct(&$db) 
	{
		$primaryKeys = array('id');
		parent::__construct( '#__contact_details', $primaryKeys, $db );
	} 
}
