<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelPrintNews', JPATH_COMPONENT_ADMINISTRATOR . '/models/printnews.php');

class HBmanagerModelPrintreport extends HBmanagerModelPrintNews
{	

	public function __construct($config = array())
	{		
		parent::__construct($config);
		
		
	}

	
}

