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
	
    function updateTeam($teamkey) 
    {
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($teamkey); echo '</pre>';
		$source = self::getSourceFromHVW( self::getHvwLink($teamkey) );
		self::setSeason($source['headline']);
		
		if (self::updateGamesInDB($teamkey, $source['schedule'], $source['leagueKey'])
				&& self::updateHvwStandingsInDB($teamkey, $source['standings'])
				&& self::updateDetailedStandingsInDB($teamkey) )
//		if (self::updateGamesInDB($teamkey, $source['schedule']) )
		{
			//TODO find better place for updating Standings Chart data
			self::updateStandingsChartData($teamkey);
		
			self::updateTimestamp ($teamkey);
			$this->updated[] = $teamkey;
			self::updateLog('schedule', $teamkey);
			return true;
		}
		return false;
    }
	
	
	private function updateStandingsChartData ($teamkey) {
		$chartModel = new HBmanagerModelHbdatastandings();
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($teamkey); echo '</pre>';
		$chartModel->updateStandingsChart($teamkey);
	}
	

    protected function updateTimestamp ($teamkey)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update('hb_mannschaft');
		$dateUTC = JFactory::getDate( )->toSql();
		//echo '<p>in DB: '.$date."</p>";
		$query->set($db->qn('update').' = '.$db->q($dateUTC));
		$query->where($db->qn('kuerzel').' = '.
								$db->q($teamkey));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$result = $db->query();

		return $result;
    }

    protected function updateLog($type, $teamkey)
    {	
		// function to log updates for cronjob
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->insert($db->qn('hb_updatelog'));
		$query->columns($db->qn(array('typ','kuerzel','datum')));

		$dateUTC = JFactory::getDate( )->toSql();
		$query->values($db->q($type).', '.$db->q($teamkey).', '.
				$db->q($dateUTC));
		//echo '=> model->$query <br><pre>'.$query.'</pre>';

		$db->setQuery($query);
		$result = $db->query();

		return $result;
    }

    function getUpdateDate($teamkey, $formatted = true)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('kuerzel', 'update')));
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
		//echo '=> model <br><pre>'; print_r($team); echo '</pre>';
		if ($formatted) {
			$format = 'D, d.m.Y - H:i:s \U\h\r';
			$date = JHtml::_('date', $team->update, $format, false);
		}
		else {
			$date = $team->update;
		}
		return $date;
    }


	
}