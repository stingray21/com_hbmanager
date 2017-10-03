<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HbManager Model
 *
 * @since  0.0.1
 */
class HBmanagerModelTeamdata extends JModelList
{
	private $tz = false; //true: user-time, false:server-time
 	protected $season;
 	protected $ownTeamNames;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$this->season = HbmanagerHelper::getCurrentSeason();
		$this->ownTeamNames = self::getOwnTeamNames();

		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'team',
				'teamkey'
			);
		}

		parent::__construct($config);
		
		// $this->names = self::getScheduleTeamNames();
		
		// set maximum execution time limit
		set_time_limit(90);

    }

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
			  ->from($db->quoteName('#__hb_team'));

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('team LIKE ' . $like);
		}


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', '`order`');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';die();
		return $query;
	}

	protected function getOwnTeamNames()
    {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT '.$db->qn('shortName'));
		$query->from('#__hb_team');
		$db->setQuery($query);
		$result = $db->loadColumn();
		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		return $result;
    }

	function updateTeamData($teamkey) 
	{
		$team = self::getTeam($teamkey);
		$team->url = HbmanagerHelper::get_hvw_json_url($team->leagueIdHvw);
		
		$response = array( 	"teamkey" => $teamkey, 
							"date" => JHTML::_('date', $team->update, 'D, d.m.Y - H:i:s', $this->tz),
							"link" => $team->url,
							"success" => false);

		if (self::updateTeamDB($team)) {
			$response['success'] = true;
		}
		return $response;
	}

	protected function getTeam($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__hb_team');
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		$db->setQuery($query);
		$team = $db->loadObject();
		return $team;
	}

	protected function updateTeamDB ($team) 
	{
		$hvwData = self::getHvwTeamData($team->url);

		$flags['schedule'] = self::updateDB_game($team, $hvwData['schedule']);
		// $flags['standings'] = updateStandings();
		// $flags['detailedStandings'] = updateDetailedStandings();

	}

	protected function getHvwTeamData($url) 
	{
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($url); echo'</pre>';
		$json = file_get_contents($url);
		$obj = json_decode($json, true);
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($obj); echo'</pre>';
		
		// Title
		$hvwData['headline'] 	= $obj[0]['head']['headline2'];
		$hvwData['name'] 		= $obj[0]['head']['name'];
		$hvwData['leagueKey'] 	= $obj[0]['head']['sname'];
		
		// Standings
		$hvwData['standings'] 	= $obj[0]['content']['score'];
		
		// Schedule
		$hvwData['schedule'] 	= $obj[0]['content']['futureGames']['games'];
		
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($hvwData); echo'</pre>';
		return $hvwData;
	}

	protected function deleteCurrentData ($table, $teamkey)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn($table));
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey), 'AND'); 
		$query->where($db->qn('season').' = '.$db->q($this->season)); 
		$db->setQuery($query);
		
		$db->setQuery($query);
		$result = $db->execute();

		return $result;
    }

    protected function updateDB_game ($team, $hvwData)
    {
		self::deleteCurrentData ('#__hb_game', $team->teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$columns = array('season', 'teamkey', 'leagueKey', 'gameIdHvw', 'gymId', 'dateTime', 'home', 'away', 'goalsHome', 'goalsAway', 'goalsHome1', 'goalsAway1', 'comment', 'pointsHome', 'pointsAway', 'ownClub', 'reportHvwId');

		foreach($hvwData as $row) {
			$values[] = implode(', ', 
			self::formatValues_game($row, $team));
		}

		// Prepare the insert query.
		$query 	->insert($db->qn('#__hb_game')) 
				->columns($db->qn($columns))
				->values($values);
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;    
    }

    protected function formatValues_game ($hvwData, $team)
    {
    	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($hvwData);echo'</pre>';
		$value['season'] 	= "'".$this->season."'";
		$value['teamkey'] 	= "'".$team->teamkey."'";
		$value['leagueKey'] = "'".$team->leagueKey."'";
		$value['gameIdHvw'] = self::formatValue4DB($hvwData['gNo']);		
		// Gym Id
		$value['gymId'] 	= self::formatValue4DB($hvwData['gGymnasiumNo']);
		// $value['gymHvwId'] = self::format_DB_value($hvwData['gGymnasiumID']);
		// Date & time
		$value['dateTime'] 	= "'".self::getDateTime($hvwData['gDate'], $hvwData['gTime'])."'";
		// Teams
		$value['home'] 		= self::formatValue4DB($hvwData['gHomeTeam'], false);
		$value['away'] 		= self::formatValue4DB($hvwData['gGuestTeam'], false);
		// Goals
		$value['goalsHome'] = self::formatValue4DB($hvwData['gHomeGoals']);
		$value['goalsAway'] = self::formatValue4DB($hvwData['gGuestGoals']);
		// Halftime
		$value['goalsHome1'] = self::formatValue4DB($hvwData['gHomeGoals_1']);
		$value['goalsAway1'] = self::formatValue4DB($hvwData['gGuestGoals_1']);
		// Comment
		$value['comment'] 	= self::formatValue4DB($hvwData['gComment'], false);
		// Points
		$value['pointsHome'] = self::formatValue4DB($hvwData['gHomePoints']);
		$value['pointsAway'] = self::formatValue4DB($hvwData['gGuestPoints']);
		// Team of own club
		$value['ownClub'] = self::formatValue4DB(self::isOwnClub($hvwData['gHomeTeam'], $hvwData['gGuestTeam']));
		// Game Report Id
		$value['reportHvwId'] 	= self::formatValue4DB($hvwData['sGID']);

		// not used yet
		// "gID": "2367089"
		// "live": true
		// "gReferee": " "

		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';die;
		return $value;
    }

    protected function formatValue4DB ($value, $int = true)
    {
    	$value = trim($value);
    	if ($value !== '') 
    	{
    		if ($int == true) 
    		{
    			$value = (int) $value;
    			return $value;
    		} else {
    			$value = "'".stripcslashes($value)."'";
    		}
    		return $value; 
    	}
    	return "NULL";
    }

    protected function getDateTime ($date, $time)
    {
    	$dateStr = $date.'-'.$time;
		$pattern = '/(?P<d>\d{2})\.(?P<m>\d{2})\.(?P<y>\d{2})-(?P<h>\d{2}):(?P<i>\d{2})/';

		if (preg_match($pattern, $dateStr, $match)) 
		{	
			$m = (object) $match;
			// echo __FILE__.' - '.__LINE__.'<pre>';print_r($m); echo'</pre>';
			$dateTime = '20'.$m->y.'-'.$m->m.'-'.$m->d.' '.$m->h.':'.$m->i.':00';
			$sqlDateTime = JFactory::getDate($dateTime, 'Europe/Berlin' )->toSql();
			// echo __FILE__.' - '.__LINE__.'<p>HVW: <b>'.$dateStr.'</b> -> in DB: <b>'.$sqlDateTime.'</b> -> display: <b>'.JHTML::_('date', $sqlDateTime , 'D, d.m.Y - H:i:s', $this->tz).'</p>';

			return $sqlDateTime;
		}
		return "NULL";
    }

    protected function isOwnClub($home, $away)
	{
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r(array($home, $away)); echo'</pre>';
		if (in_array($home, $this->ownTeamNames) || in_array($away, $this->ownTeamNames))
		{
			return 1;
		}
		return 0;
	}
}
