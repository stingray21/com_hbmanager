<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_SITE.'/models/hboverview.php';

class hbmanagerModelHbhomegames extends HBmanagerModelHboverview
{	
	
	function __construct() 
	{
		parent::__construct();
		
	}
	
}