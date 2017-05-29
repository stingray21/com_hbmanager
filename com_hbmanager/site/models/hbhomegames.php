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
	
	public function getNextGameday () {
		$homeGames = parent::getHomeGames();
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($homeGames);echo '</pre';
		$today = date('Y-m-d');
		$beforeToday = true;
		foreach ($homeGames as $key => $value) {
			$nextGameday[$key] = false;
			if ($beforeToday && ($today<$key)) {
				$nextGameday[$key] = true;
				$beforeToday = false;
			}
		}
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($showTable);echo '</pre';
		return $nextGameday;
	}
}