<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbdatastandings.php';	

class hbmanagerModelHbdata extends JModelLegacy
{	

    function __construct() 
    {
		parent::__construct();
		
		$this->names = self::getScheduleTeamNames();
		// set maximum execution time limit
		set_time_limit(90);

    }

    function getTeams()
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
								$db->qn('reihenfolge').' ASC');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
    }

    function updateDb($key = 'none')
    {
		//$start = time();
		if ($key != 'none')
		{
			$teams = self::getHvwTeams ($key);
			foreach ($teams as $team)
			{
				self::updateTeam($team->kuerzel);
			}
		}
		//echo $duration = (time() - $start). ' sec';	
		return;
    }

    protected function getHvwTeams ($teamkey)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('hvwLink').' IS NOT NULL');

		if ($teamkey != 'all') {
			// request only one team of DB
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		}
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		//echo '=> model->$updated <br><pre>'; print_r($teams); echo '</pre>';
		return $teams;
    }
	
	function getHvwTeamArray ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel, '.$db->q('false').' AS updated ');
		$query->from('hb_mannschaft');
		$query->where($db->qn('hvwLink').' IS NOT NULL');
		$db->setQuery($query);
		//$teams = $db->loadColumn();
		$teams = $db->loadObjectList();
		//echo '=> model->$updated <br><pre>'; echo $query; echo '</pre>';
		//echo '=> model->$updated <br><pre>'; print_r($teams); echo '</pre>';
		return $teams;
	}	
	
	private function updateStandingsChartData ($teamkey) {
		$chartModel = new HBmanagerModelHbdatastandings();
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($teamkey); echo '</pre>';
		$chartModel->updateStandingsChart($teamkey);
	}

	
}