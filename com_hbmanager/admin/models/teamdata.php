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
	protected $tz = false; //true: user-time, false:server-time
 	protected $season;
 	protected $ownTeamNames;
 	protected $dateFormat = 'D, d.m.Y - H:i:s';
 	protected $tableTeams = '#__hb_team';

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
		$this->tz = HbmanagerHelper::getHbTimezone();
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
			  ->from($db->quoteName($this->tableTeams));

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


	function updateTeamData($teamkey, $type = 'manual') 
	{
		$team = self::getTeam($teamkey);
		$team->url = HbmanagerHelper::get_hvw_json_url($team->leagueIdHvw);
		
		$response['teamkey'] = $teamkey;
		$response['link'] = $team->url;

		$response['result'] = self::updateTeamDB($team);
		$response['result']['total'] = !in_array(false, $response['result'], true);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($response);echo'</pre>';

		// update dateTime
		if (in_array(true, $response['result'], true))	{
			$team->update = self::updateTimestamp($team->teamkey);
		}

		$response['date'] = JHTML::_('date', $team->update, $this->dateFormat, $this->tz);

		self::updateLog($team, $response['result'], $type);

		return $response;
	}

	function warning_handler($errno, $errstr, $errfile, $errline)
	{
		$message = "<br />\n<b>Warning</b>:  $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
		// echo $message;
		throw new Exception($message);
		/* Don't execute PHP internal error handler */
		return false;
	}

	protected function updateTeamDB ($team) 
	{	
		// Error handler that includes warnings
		set_error_handler(array($this, 'warning_handler'), E_WARNING);

		$hvwData = self::getHvwTeamData($team->url);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($hvwData);echo'</pre>';
		$result['schedule'] = false;
		$result['standings'] = false;
		$result['standingsDetails'] = false;
		$result['error'] = null;

		if (!isset($hvwData['error']))
		{
			// schedule
			try {
				$result['schedule'] = self::updateDB_game($team, $hvwData['schedule']);
			} catch (Exception $e) {	
				//echo 'Exception: ',  $e->getMessage(), "\n";
				$result['error'][] =  $e->getMessage();
			}		

			// standings
			try {
				if (empty($hvwData['standings'])) 
				{
					$result['standings'] = true;
				} else {
					$standingsData = self::addMissingRanking($hvwData['standings']);
					$result['standings'] = self::updateDB_standings($team, $standingsData);
				}
			} catch (Exception $e) {	
				//echo 'Exception: ',  $e->getMessage(), "\n";
				$result['error'][] =  $e->getMessage();
			}		

			// standings_details
			try {
				$standingsData = self::getDetailedStandingsData($team->teamkey);
				$standingsData = self::sortDetailedStandings($standingsData, $team->teamkey, true);
				$result['standingsDetails'] = self::updateDB_standings_details($team, $standingsData);
			} catch (Exception $e) {	
				//echo 'Exception: ',  $e->getMessage(), "\n";
				$result['error'][] =  $e->getMessage();
			}		
		} else {
			$result['error'][] = $hvwData;
		}

		// $result['schedule'] = true; $result['standings'] = (bool)random_int(0, 1) ; $result['standingsDetails'] = true; //testing

		restore_error_handler();
		return $result;
	}

	protected function getHvwTeamData($url) 
	{
		// Error handler that includes warnings
		set_error_handler(array($this, 'warning_handler'), E_WARNING);
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($url); echo'</pre>';
		// $json = self::testCase();
		try 
		{
			$json = file_get_contents($url);
		} catch (Exception $e) {	
			// echo 'Exception: ',  $e->getMessage(), "\n";
			$json = null;
			$hvwData['error'] = "no HVW data ($url)";
			$hvwData['message'] = $e->getMessage();
		}	
		restore_error_handler();
		if (empty($json)) return $hvwData;

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

	protected function getOwnTeamNames()
	{
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT '.$db->qn('shortName'));
		$query->from($this->tableTeams);
		$db->setQuery($query);
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$result = $db->loadColumn();
		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		return $result;
	}

	protected function updateTimestamp ($teamkey)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->update($this->tableTeams);
		
		$dateUTC = JFactory::getDate( )->toSql();
		
		$query->set($db->qn('update').' = '.$db->q($dateUTC));
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		
		$db->setQuery($query);
		$result = $db->query();

		return $dateUTC;
    }

    protected function updateLog($team, $result, $type = 'manual')
    {	
    	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$error = (empty($result['error'])) ? 'NULL' : $db->q(json_encode($result['error']));

		$query->insert($db->qn('#__hb_updatelog'));
		
		$query->columns($db->qn(array('type','teamkey','dateTime', 'schedule', 'standings', 'standingsDetails', 'error')));

		$query->values($db->q($type).', '.$db->q($team->teamkey).', '.$db->q($team->update).', '.
			$db->q($result['schedule']).', '.$db->q($result['standings']).', '.$db->q($result['standingsDetails']).', '.$error);

		$db->setQuery($query);
		$result = $db->query();

		return $result;
    }

	public function getDateFormat()
	{
		return $this->dateFormat;
	}


	protected function getTeam($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->tableTeams);
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		$db->setQuery($query);
		$team = $db->loadObject();
		return $team;
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

	protected function updateDB ($team, $table, $values, $columns)
	{
		self::deleteCurrentData ($table, $team->teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Prepare the insert query.
		$query 	->insert($db->qn($table)) 
				->columns($db->qn($columns))
				->values($values);

		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
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

// ----------------------------------------------------------------------------------
// 	Schedule data for #__hb_game	
// ----------------------------------------------------------------------------------

	protected function updateDB_game ($team, $hvwData)
	{
		$table = '#__hb_game';
		
		$columns = array('season', 'teamkey', 'leagueKey', 'gameIdHvw', 'gymId', 'dateTime', 'home', 'away', 'goalsHome', 'goalsAway', 'goalsHome1', 'goalsAway1', 'comment', 'pointsHome', 'pointsAway', 'ownClub', 'reportHvwId');

		foreach($hvwData as $row) {
			$values[] = implode(', ', 
			self::formatValues_game($row, $team));
		}

		return self::updateDB($team, $table, $values, $columns);
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


// ----------------------------------------------------------------------------------
// 	Standings data for #__hb_standings	
// ----------------------------------------------------------------------------------

	protected function updateDB_standings ($team, $hvwData)
	{
		$table = '#__hb_standings';

		$columns = array('season', 'teamkey', 'rank', 'team', 'games', 'wins', 'ties', 'losses', 'goalsPos', 'goalsNeg', 'goalsDiff', 'pointsPos', 'pointsNeg');

		foreach($hvwData as $row) {
			$values[] = implode(', ', 
			self::formatValues_standings($row, $team));
		}

		return self::updateDB($team, $table, $values, $columns);
	}

	protected function addMissingRanking ($hvwData)
    {
		$prevRank = 1; // in case no rank in HVW --> same as previous team
		foreach($hvwData as &$row) {
			if (trim($row['tabScore']) == '') {
				$row['tabScore'] = $prevRank;
			}
			$prevRank = $row['tabScore'];
		}
		return $hvwData;	
	}

	protected function formatValues_standings ($hvwData, $team)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($hvwData);echo'</pre>';
		$value['season'] 	= "'".$this->season."'";
		$value['teamkey'] 	= "'".$team->teamkey."'";
		$value['rank'] = self::formatValue4DB($hvwData['tabScore']); 
		$value['team'] = self::formatValue4DB($hvwData['tabTeamname'], false);
		$value['games'] = self::formatValue4DB($hvwData['numPlayedGames']);
		$value['wins'] = self::formatValue4DB($hvwData['numWonGames']);
		$value['ties'] = self::formatValue4DB($hvwData['numEqualGames']);
		$value['losses'] = self::formatValue4DB($hvwData['numLostGames']);
		$value['goalsPos'] = self::formatValue4DB($hvwData['numGoalsShot']);
		$value['goalsNeg'] = self::formatValue4DB($hvwData['numGoalsGot']);
		$value['goalsDiff'] = self::formatValue4DB($hvwData['numGoalsShot']-$hvwData['numGoalsGot']);
		$value['pointsPos'] = self::formatValue4DB($hvwData['pointsPlus']);
		$value['pointsNeg'] = self::formatValue4DB($hvwData['pointsMinus']);

		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';die;
		return $value;
	}


// ----------------------------------------------------------------------------------
// 	Calculated detailed standings data for #__hb_standings_details	
// ----------------------------------------------------------------------------------

	protected function updateDB_standings_details ($team, $hvwData)
	{
		$table = '#__hb_standings_details';

		$columns = array('season', 'teamkey', 'rank', 'team', 'games', 'gamesH', 'gamesA', 'wins', 'winsHome', 'winsAway', 'ties', 'tiesHome', 'tiesAway', 'losses', 'lossesHome', 'lossesAway', 'goalsPos', 'goalsPosHome', 'goalsPosAway', 'goalsNeg', 'goalsNegHome', 'goalsNegAway', 'goalsDiff', 'goalsDiffHome', 'goalsDiffAway', 'pointsPos', 'pointsPosHome', 'pointsPosAway', 'pointsNeg', 'pointsNegHome', 'pointsNegAway');

		foreach($hvwData as $row) {
			$values[] = implode(', ', 
			self::formatValues_standings_details($row, $team));
		}

		return self::updateDB($team, $table, $values, $columns);
	}

	protected function formatValues_standings_details ($hvwData, $team)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($hvwData);echo'</pre>';
		$value['season'] 	= "'".$this->season."'";
		$value['teamkey'] 	= "'".$team->teamkey."'"; 
		$value['rank'] 		= self::formatValue4DB($hvwData->rank); 
		$value['team'] 		= self::formatValue4DB($hvwData->team, false); 
		$value['games'] 	= self::formatValue4DB($hvwData->games); 
		$value['gamesH'] 	= self::formatValue4DB($hvwData->gamesH); 
		$value['gamesA'] 	= self::formatValue4DB($hvwData->gamesA); 
		$value['wins'] 		= self::formatValue4DB($hvwData->wins); 
		$value['winsH'] 	= self::formatValue4DB($hvwData->winsH); 
		$value['winsA'] 	= self::formatValue4DB($hvwData->winsA); 
		$value['ties'] 		= self::formatValue4DB($hvwData->ties); 
		$value['tiesH'] 	= self::formatValue4DB($hvwData->tiesH); 
		$value['tiesA'] 	= self::formatValue4DB($hvwData->tiesA); 
		$value['loss'] 		= self::formatValue4DB($hvwData->loss); 
		$value['lossH'] 	= self::formatValue4DB($hvwData->lossH); 
		$value['lossA'] 	= self::formatValue4DB($hvwData->lossA); 
		$value['glsPos'] 	= self::formatValue4DB($hvwData->glsPos); 
		$value['glsPosH'] 	= self::formatValue4DB($hvwData->glsPosH); 
		$value['glsPosA'] 	= self::formatValue4DB($hvwData->glsPosA); 
		$value['glsNeg'] 	= self::formatValue4DB($hvwData->glsNeg); 
		$value['glsNegH'] 	= self::formatValue4DB($hvwData->glsNegH); 
		$value['glsNegA'] 	= self::formatValue4DB($hvwData->glsNegA); 
		$value['glsDiff'] 	= self::formatValue4DB($hvwData->glsDiff); 
		$value['glsDiffH']	= self::formatValue4DB($hvwData->glsDiffH); 
		$value['glsDiffA']	= self::formatValue4DB($hvwData->glsDiffA); 
		$value['ptsPos'] 	= self::formatValue4DB($hvwData->ptsPos); 
		$value['ptsPosH']	= self::formatValue4DB($hvwData->ptsPosH); 
		$value['ptsPosA']	= self::formatValue4DB($hvwData->ptsPosA); 
		$value['ptsNeg'] 	= self::formatValue4DB($hvwData->ptsNeg); 
		$value['ptsNegH']	= self::formatValue4DB($hvwData->ptsNegH); 
		$value['ptsNegA']	= self::formatValue4DB($hvwData->ptsNegA); 

		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';die;
		return $value;
	}

protected function getDetailedStandingsData ($teamkey, $date = null)		
	{
		$db = JFactory::getDBO();
		$table = '#__hb_game';

		//$noDateOption = ($date === null) ? 1 : 0 ;
		$dateOption = ($date !== null) ? " AND DATE(`datumZeit`) <= ".$db->q($date) : '';
		
		// make a second table with the home and away switch, then only take the "new" home teams in account

		$query = "SELECT 
			team,
			
			SUM(IF(loc ='H' OR loc ='A', 1, 0)) AS games, 
			SUM(IF(loc ='H', 1, 0)) AS gamesH, 
			SUM(IF(loc ='A', 1, 0)) AS gamesA, 


			SUM(IF(s.points > s.pointsOpponent, 1, 0)) AS wins, 
			SUM(IF(loc = 'H' AND s.points > s.pointsOpponent, 1, 0)) AS winsH, 
			SUM(IF(loc = 'A' AND s.points > s.pointsOpponent, 1, 0)) AS winsA, 


			SUM(IF(s.points = s.pointsOpponent, 1, 0)) AS ties, 
			SUM(IF(loc = 'H' AND s.points = s.pointsOpponent, 1, 0)) AS tiesH, 
			SUM(IF(loc = 'A' AND s.points = s.pointsOpponent, 1, 0)) AS tiesA, 


			SUM(IF(s.points < s.pointsOpponent, 1, 0)) AS loss, 
			SUM(IF(loc = 'H' AND s.points < s.pointsOpponent, 1, 0)) AS lossH, 
			SUM(IF(loc = 'A' AND s.points < s.pointsOpponent, 1, 0)) AS lossA, 


			SUM(IF(s.points IS NOT NULL, s.points, 0)) AS ptsPos, 
			SUM(IF(loc = 'H', s.points , 0)) AS ptsPosH, 
			SUM(IF(loc = 'A', s.points , 0)) AS ptsPosA, 

			SUM(IF(s.pointsOpponent IS NOT NULL, s.pointsOpponent, 0)) AS ptsNeg, 
			SUM(IF(loc = 'H', s.pointsOpponent , 0)) AS ptsNegH, 
			SUM(IF(loc = 'A', s.pointsOpponent , 0)) AS ptsNegA, 


			SUM(IF(s.goals IS NOT NULL, s.goals, 0)) AS glsPos, 
			SUM(IF(loc = 'H', s.goals, 0)) AS glsPosH, 
			SUM(IF(loc = 'A', s.goals, 0)) AS glsPosA, 

			SUM(IF(s.goalsOpponent IS NOT NULL, s.goalsOpponent, 0)) AS glsNeg, 
			SUM(IF(loc = 'H', s.goalsOpponent, 0)) AS glsNegH, 
			SUM(IF(loc = 'A', s.goalsOpponent, 0)) AS glsNegA,	

			SUM(IF(s.goals IS NOT NULL, s.goals-s.goalsOpponent, 0)) AS glsDiff,
			SUM(IF(loc = 'H', s.goals-s.goalsOpponent, 0)) AS glsDiffH,
			SUM(IF(loc = 'A', s.goals-s.goalsOpponent, 0)) AS glsDiffA

			FROM ( 
				SELECT home as team, teamkey 
				FROM ".$table."
				WHERE teamkey = ".$db->q($teamkey)."
				AND season=".$db->q($this->season)." 
				GROUP BY team
				) AS m
			LEFT JOIN
			(
				SELECT 
				'H' loc, 
				s1.dateTime dateTime,
				s1.home team, 
				s1.away opponent, 
				s1.goalsHome goals, 
				s1.goalsAway goalsOpponent,
				s1.pointsHome points,	 
				s1.pointsAway pointsOpponent
				FROM ".$table." s1 
				WHERE s1.pointsHome IS NOT NULL  
					AND teamkey = ".$db->q($teamkey)."
					AND season = ".$db->q($this->season)." 
					".$dateOption."
				UNION 

				SELECT 
				'A' loc,
				s2.dateTime dateTime,
				s2.away team, 
				s2.home opponent, 
				s2.goalsAway goals, 
				s2.goalsHome goalsOpponent,
				s2.pointsAway points,	 
				s2.pointsHome pointsOpponent
				FROM ".$table." s2 
				WHERE s2.pointsHome IS NOT NULL 
					AND teamkey = ".$db->q($teamkey)."
					AND season = ".$db->q($this->season)." 
					".$dateOption."
			) AS s USING (team)

			GROUP BY team 
			ORDER BY ptsPos DESC, wins DESC, glsDiff DESC";

		// echo  __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		// echo __FILE__.' ('.__LINE__.')<pre>'; print_r($result); echo '</pre>'; die;
		return $result;
	}

	protected function sortDetailedStandings($standings, $teamkey, $compareH2H = false)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($standings);echo'</pre>';
		$sorted = array();
		foreach ($standings as $row)
		{
			// echo __FILE__.' ('.__LINE__.'): '.$row->team.'<br/>';
			$sorted = self::insertInStandings($sorted, $row, $teamkey, $compareH2H);
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($sorted);echo'</pre>';
		return $sorted;
	}

	protected function insertInStandings ($standings, $newRow, $teamkey, $compareH2H = false)
	{
		$pos = 0;
		$currRank = null;
		$inserted = false;
		$newRow->rank = 1;
		$direct = 0;
		if (!isset($newRow->direct)) {
			$newRow->direct = null;
		}
		foreach ($standings as $currRow)
		{				
			if (!$inserted) {
				$compare = self::comparePoints($newRow, $currRow);
				if ($compare === 1) {
					$inserted = true;
					$currRank = $currRow->rank;
				}
				elseif ($compare === 0) {
					if ($compareH2H) {
						$direct = self::compareH2H($newRow, $currRow, $teamkey);
						$currRow->direct = $currRow->direct + (-1*$direct);
						$newRow->direct = $newRow->direct + $direct;
					}
					$pos++;
				}
				else {
					$pos++;
					$newRow->rank = $pos+1;
				}
			}
			if (!empty($currRank) && $currRank <= $currRow->rank) {
				$currRow->rank++;
			}	
		}
		
		array_splice( $standings, $pos, 0, array($newRow) );
		return $standings;
	}

	
	protected function comparePoints ($newRow, $currRow) 
	{
		if ($newRow->ptsPos > $currRow->ptsPos) {
			return 1;
		}
		elseif ($newRow->ptsPos == $currRow->ptsPos) {
			if ($newRow->ptsNeg < $currRow->ptsNeg) {
				return 1;
			}
			elseif ($newRow->ptsNeg == $currRow->ptsNeg) {
				if ($newRow->direct > $currRow->direct) {
					return 1;
				}
				elseif ($newRow->direct == $currRow->direct) {
					return 0;
				}
			}
		}
		return -1;
	}

	protected function  compareH2H($newRow, $currRow, $teamkey)
	{
		// PHP version of head-to-head comparison function with single SQL request
		$db = JFactory::getDBO();

		// home game of $newRow team against $currRow team
		$query = $db->getQuery(true);
		$query->select('goalsHome AS goals, goalsAway AS goalsOpponent');
		$query->from('#__hb_game');
		$query->where(
			'( '.
			$db->qn('home').'='.$db->q($newRow->team).' AND '.$db->qn('away').'='.$db->q($currRow->team).
			' OR '.
			$db->qn('home').'='.$db->q($currRow->team).' AND '.$db->qn('away').'='.$db->q($newRow->team).
			' )');
		$query->where($db->qn('season').'='.$db->q($this->season));
		$query->where($db->qn('teamkey').'='.$db->q($teamkey));
		$query->order('FIELD('.$db->qn('home').','.$db->q($newRow->team).','.$db->q($currRow->team).')');
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$result = $db->loadObjectList(); 
		// if (!empty($result[0]->goals != '')) {echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';}

		$homeGame = $result[0];
		$awayGame = $result[1];

		// compare goals
		$goals = ($homeGame->goals + $awayGame->goals) 
					- ($awayGame->goalsOpponent + $homeGame->goalsOpponent);	
		if ($goals > 0) $direct = 1;
		elseif ($goals < 0) $direct = -1;
		else 
		{
			// take goals shot away in account
			$awayGoals = ($awayGame->goals - $homeGame->goalsOpponent);
			if ($goals > 0) $direct = 1;
			elseif ($goals < 0) $direct = -1;
			else $direct = 0; 
		}
		
		return $direct;
	}

}

	
	