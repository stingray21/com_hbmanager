<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelOfficials extends HBmanagerModelHBmanager
{	

	public function __construct($config = array())
	{		
		
		parent::__construct($config);
	}



}

