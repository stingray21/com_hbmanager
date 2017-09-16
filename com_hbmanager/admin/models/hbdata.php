<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbdatastandings.php';	

class hbmanagerModelHbdata extends JModelLegacy
{	
    private $updated = array();
	protected $season;
	private $names = array();

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
	
    protected function getHvwLink ($teamkey)
    {
		//echo '=> model->$updated <br><pre>'; print_r($teamkey); echo '</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('hvwLink'));
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		$db->setQuery($query);
		$result = $db->loadResult();
		//echo '=> model->$query <br><pre>'; echo $query ; echo '</pre>';
		//echo '=> model->$updated <br><pre>'; print_r($result); echo '</pre>';
		return $result;
    }
	
	protected function getScheduleTeamNames()
    {
		//echo '=> model->$updated <br><pre>'; print_r($teamkey); echo '</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT '.$db->qn('nameKurz'));
		$query->from('hb_mannschaft');
		$db->setQuery($query);
		$result = $db->loadColumn();
		//echo __FILE__.'('.__LINE__.'):<pre>'.$result.'</pre>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		return $result;
    }

    protected function getSourceFromHVW($url) {
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($url); echo'</pre>';
		$json = file_get_contents($url);
		// $json = substr($json, 1, -1);
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($json); echo'</pre>';
		$obj = json_decode($json, true);
		
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($obj); echo'</pre>';
		
		// Title
		$data['headline'] = $obj[0]['head']['headline2'];
		$data['name'] = $obj[0]['head']['name'];
		$data['leagueKey'] = $obj[0]['head']['sname'];
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($data); echo'</pre>';

		// Standings
		$data['standings'] = $obj[0]['content']['score'];
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($data['standings']); echo'</pre>';

		// Schedule
		$data['schedule'] = $obj[0]['content']['futureGames']['games'];
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($data['schedule']); echo'</pre>';
		
		return $data;
	}


    protected function explode2D ($source)
    {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($source);echo'</pre>';
		$data = explode('&&',$source);
		foreach ($data as $key => $value) 
		{
			$data[$key] = explode('||',$value);
		}
		return $data;
	}
	

	// probably ...goals_1 field
	// protected function getJudging($comment, $scoreHome, $scoreAway)
    // {
	// 	switch (trim($comment))
	// 	{
	// 		case '(2:0)':
	// 		case '(0:2)':
	// 			$judging['home'] = (int) preg_replace('/^\((\d):(\d)\)$/',
	// 				'$1',$comment);
	// 			$judging['away'] = (int) preg_replace('/^\((\d):(\d)\)$/',
	// 				'$2',$comment);
	// 			break;
	// 		case 'ausg..':
	// 		case 'n.g.':
	// 			$judging['home'] = '';
	// 			$judging['away'] = '';
	// 		default:
	// 			$judging['home'] = $scoreHome;
	// 			$judging['away'] = $scoreAway;
	// 			break;
	// 	}
	// 	//echo '=> model->$data <br><pre>'; print_r($data); echo '</pre>';
	// 	return $judging;
    // }
	
    function getUpdateStatus()
    {
		$updated = $this->updated;
		//echo '=> model->$updated <br><pre>'; print_r($updated); echo '</pre>';
		return $updated;
    }

    protected function deleteOldData ($table, $teamkey)
    {
		$season = self::getSeason();

		//echo '=> model <br><pre>'; print_r($teamkey); echo '</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn($table));
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey), 'AND'); 
		$query->where($db->qn('saison').' = '.$db->q($season)); 
		$db->setQuery($query);
		//echo '=> model->$query <br><pre>'.$query.'</pre>';

		$db->setQuery($query);
		$result = $db->execute();
		//echo '=> model <br><pre>'; print_r($result); echo '</pre>';
		return $result;
    }

    protected function updateGamesInDB($teamkey, $source, $leagueKey)
    {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($source);echo'</pre>'; die;
		self::deleteOldData ('hb_spiel', $teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$columns = array('saison',  'spielIdHvw', 'kuerzel', 
				'ligaKuerzel', 'hallenNr', 'datumZeit', 
				'heim', 'gast', 'toreHeim', 'toreGast', 'bemerkung',
				'wertungHeim', 'wertungGast', 'eigenerVerein', 'berichtLink');

		$saison = self::getSeason();

		foreach($source as $row) {
			$values[] = implode(', ', 
			self::formatGamesValues($row, $teamkey, $leagueKey));
		}

		// Prepare the insert query.
		$query
				->insert($db->qn('hb_spiel')) 
				->columns($db->qn($columns))
				->values($values);

		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$query .= self::getOnDublicate($columns);
		// echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';die;
		
		$db->setQuery($query);
		$result = $db->execute();
		
		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }
	
	protected function getOnDublicate($columns) {
		$db = $this->getDbo();
		$query = "\nON DUPLICATE KEY UPDATE \n";
		$dublicates = array();
		foreach ($columns as $field) {
			$dublicates[] = $db->qn($field).' = VALUES('.$db->qn($field).')';
		}
		
		return $query .= implode(",\n", $dublicates);
	}

    protected function formatGamesValues($data, $teamkey, $leagueKey)
    {
		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($data);echo'</pre>';
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['spielIdHvw'] = $db->q($data['gNo']);
		$value['kuerzel'] = $db->q($teamkey);
		$value['ligaKuerzel'] = $db->q($leagueKey);
		// HallenNummer
		if (trim($data['gGymnasiumNo']) != '') $value['hallenNr'] = (int) $data['gGymnasiumNo'];
				else  $value['gGymnasiumNo'] = "NULL";
		// if (trim($data['gGymnasiumID']) != '') $value['hallenID'] = (int) $data['gGymnasiumID'];
		// 		else  $value['gGymnasiumID'] = "NULL";

		// Datum & Uhrzeit
		$dateStr = $data['gDate'].'-'.$data['gTime'];
		// echo __FILE__.' - '.__LINE__.'<p>'.$dateStr.'</b></p>';	
		$pattern = '/(?P<day>\d{2})\.(?P<month>\d{2})\.(?P<year>\d{2})-(?P<hour>\d{2}):(?P<min>\d{2})/';
		// $pattern = '/\d{2}\.\d{2}\.\d{2}-\d{2}:\d{2}/';
		if (preg_match($pattern, $dateStr, $matches)) {	
			// echo __FILE__.' - '.__LINE__.'<pre>';print_r($matches); echo'</pre>';
			$dateTime = '20'.$matches['year'].'-'.$matches['month'].'-'.$matches['day'].' '.
				$matches['hour'].':'.$matches['min'].':00';
			// echo __FILE__.' - '.__LINE__.'<p>'.$dateTime.'</b></p>';	
			$sqlDateTime = JFactory::getDate($dateTime, 'Europe/Berlin' )->toSql();
			// echo __FILE__.' - '.__LINE__.'<p>HVW: <b>'.$dateStr.'</b> -> in DB: <b>'.$sqlDateTime.'</b></p>';
			$value['datumzeit'] = $db->q($sqlDateTime);
		}
		else  $value['datumzeit'] = "NULL";

		$value['heim'] = $db->q(addslashes($data['gHomeTeam']));
		$value['gast'] = $db->q(addslashes($data['gGuestTeam']));
		// ToreHeim
		if (trim($data['gHomeGoals']) != '') $value['toreHeim'] = (int) $data['gHomeGoals'];
				else  $value['toreHeim'] = "NULL";
		// ToreGast
		if (trim($data['gGuestGoals']) != '') $value['toreGast'] = (int) $data['gGuestGoals'];
				else  $value['toreGast'] = "NULL";
		// Bemerkung
		if (trim($data['gComment']) != '') $value['bemerkung'] = $db->q($data['gComment']);
				else  $value['bemerkung'] = "NULL";
		// WertungHeim
		if (trim($data['gHomeGoals_1']) != '') $value['wertungHeim'] = (int)$data['gHomeGoals_1'];
				else  $value['wertungHeim'] = "NULL";
		// WertungGast
		if (trim($data['gGuestGoals_1']) != '') $value['wertungGast'] = (int)$data['gGuestGoals_1'];
				else  $value['wertungGast'] = "NULL";


		// not used yet
		// "gID": "2367089",
		// "sGID": 0,
		// "live": true,
		// "gHomePoints": " ",
		// "gGuestPoints": " ",
		// "gComment": " ",
		// "gReferee": " "
		
		// Team of own club
		$ownTeam = self::checkIfOwnTeamIsPlaying($data['gHomeTeam'], $data['gGuestTeam']);
		if ($ownTeam) $value['eigenerVerein'] = $ownTeam;
				else  $value['eigenerVerein'] = "NULL";		
				
		// BerichtLink
		// if (trim($data[10]) != '') $value['berichtLink'] = $db->q($data[11]);
		// 		else  $value['berichtLink'] = "NULL";
		$value['berichtLink'] = $db->q("add in hbdata model");

		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';die;
		return $value;
    }

	protected function checkIfOwnTeamIsPlaying($home, $away)
	{
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($home); echo'</pre>';
		if (in_array(stripcslashes($home), $this->names)) {
			return true;
		}
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($away); echo'</pre>';
		if (in_array(stripcslashes($away), $this->names)) {
			return true;
		}
		return false;
	}
	
    protected function getSeason()
    {
		$saison = $this->season;
		return $saison;
    }
	
	protected function setSeason($string = null)
    {
		//echo __FILE__.' ('.__LINE.')<pre>'; print_r($string); echo '</pre>';
		if ($string !== null) {
			$season = preg_replace('/.*(\d{4})\/(\d{4}).*/', '$1-$2', $string);
			//$season = '2015-2016';
		} else {
			// current season
			$year = strftime('%Y');
			if (strftime('%m') < 8) {
				$year = $year-1;
			}
			$season = $year.'-'.($year+1);
		}
		$this->season = $season;		
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
    
// methods for ranking
    
	
	protected function updateStandingsInDb($teamkey, $table, $columns, $values)
	{
		//echo '=> model<br><pre>'; print_r($standingsData);echo '</pre>';
		self::deleteOldData ($table, $teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		
		//echo '<pre>values';print_r($values);echo '</pre>';
			
		// Prepare the insert query.
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);

		// echo '=> model->$query <br><pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$result = $db->execute();

		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }
	
	protected function updateHvwStandingsInDb($teamkey, $source)
    {
		if (empty($source)) {
			return true; 
			// TODO: more info - notice on screen
		}
		
		$table = 'hb_tabelle';
		$columns = array('saison','kuerzel','platz','mannschaft','spiele',
				's','u','n','tore','gegenTore',
				'torDiff','punkte','minuspunkte');
		//echo '=> model<br><pre>'; print_r($standingsData);echo '</pre>';
		
		$values = null;
		$source = self::addRanking($source);
		foreach($source as $row) {
			$values[] = implode(', ', 
					self::formatStandingsValues($row, $teamkey));
		}
		
		$result = self::updateStandingsInDb($teamkey, $table, $columns, $values);
		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }
	
	protected function addRanking($source)
    {
		// echo '=> model<br><pre>'; print_r($source);echo '</pre>';
		$prevRank = 1; // in case no rank in HVW --> same as previous team
		foreach($source as &$row) {
			if (trim($row['tabScore']) == '') {
				$row['tabScore'] = $prevRank;
			}
			$prevRank = $row['tabScore'];
		}
		// echo '=> model<br><pre>'; print_r($source);echo '</pre>';die;
		return $source;	
	}

	protected function formatStandingsValues($data, $teamkey)
    {
		//echo '=> model<br><pre>'; print_r($data);echo '</pre>';
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['kuerzel'] = $db->q($teamkey);
		// not used yet
		// "tabTeamID": "398157",
		// "liveTeam": true,

		// Platz
		$value['platz'] = $data['tabScore'];
		// Verein
		$value['mannschaft'] = $db->q($data['tabTeamname']);
		
		$value['spiele'] = $data['numPlayedGames'];
		$value['s'] = $data['numWonGames'];
		$value['u'] = $data['numEqualGames'];
		$value['n'] = $data['numLostGames'];
		$value['tore'] = $data['numGoalsShot'];
		$value['gegenTore'] = $data['numGoalsGot'];
		$value['torDiff'] = $data['numGoalsShot'] - $data['numGoalsGot'];
		$value['punkte'] = $data['pointsPlus'];
		$value['minusPunkte'] = $data['pointsMinus'];
		
		//echo '=> model<br><pre>'; print_r($value);echo '</pre>';
		return $value;
    }
	
// DETAILED STANDINGS
	protected function updateDetailedStandingsInDb($teamkey)
    {
		$table = 'hb_tabelle_details';
		$columns = array('saison','kuerzel','platz','mannschaft','spiele',
			's','sH','sA',
			'u','uH','uA',
			'n','nH','nA',
			'tore','toreH','toreA',
			'gegenTore','gegenToreH','gegenToreA',
			'torDiff','torDiffH','torDiffA',
			'punkte','punkteH','punkteA',
			'minusPunkte','minusPunkteH','minusPunkteA');
		//echo '<pre>';print_r($detailedStandingsData);echo'</pre>';
		$standingsData = self::sortDetailedStandings(
				self::getDetailedStandingsData($teamkey),$teamkey, true );
		//echo '=> model<br><pre>'; print_r($standingsData);echo '</pre>';
		$values = null;
		foreach($standingsData as $row) {
			$values[] = implode(', ', 
					self::formatDetailedStandingsValues($row, $teamkey));
		}
		
		$result = self::updateStandingsInDb($teamkey, $table, $columns, $values);
		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }
	
	protected function formatDetailedStandingsValues($data, $teamkey)
    {
		//echo '=> model<br><pre>'; print_r($data);echo '</pre>';
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['kuerzel'] = $db->q($teamkey);
		
		$value['platz'] = $data->platz;
		$value['mannschaft'] = $db->q($data->mannschaft);
		$value['spiele'] = $data->spiele;
		$value['s'] = $data->siege;
		$value['sH'] = $data->siegeH;
		$value['sA'] = $data->siegeA;
		$value['u'] = $data->unentschieden;
		$value['uH'] = $data->unentschiedenH;
		$value['uA'] = $data->unentschiedenA;
		$value['n'] = $data->niederlagen;
		$value['nH'] = $data->niederlagenH;
		$value['nA'] = $data->niederlagenA;
		$value['tore'] = $data->tore;
		$value['toreH'] = $data->toreH;
		$value['toreA'] = $data->toreA;
		$value['gegenTore'] = $data->gegenTore;
		$value['gegenToreH'] = $data->gegenToreH;
		$value['gegenToreA'] = $data->gegenToreA;
		$value['torDiff'] = $data->torDifferenz;
		$value['torDiffH'] = $data->torDifferenzH;
		$value['torDiffA'] = $data->torDifferenzA;
		$value['punkte'] = $data->punkte;
		$value['punkteH'] = $data->punkteH;
		$value['punkteA'] = $data->punkteA;
		$value['minusPunkte'] = $data->minusPunkte;
		$value['minusPunkteH'] = $data->minusPunkteH;
		$value['minusPunkteA'] = $data->minusPunkteA;

		//echo '=> model<br><pre>'; print_r($value);echo '</pre>';
		return $value;
    }
	
	protected function getDetailedStandingsData ($teamkey, $date = null)		
	{
		$db = JFactory::getDBO();
		
		//$noDateOption = ($date === null) ? 1 : 0 ;
		$dateOption = ($date !== null) ? " AND DATE(`datumZeit`) <= ".$db->q($date) : '';
		
		$query = "SELECT 
			mannschaft,
			
			SUM(IF(w='H' OR w='A', 1, 0)) spiele, 
			SUM(IF(w='H', 1, 0)) spieleH, 
			SUM(IF(w='A', 1, 0)) spieleA, 

			SUM( 
			CASE 
			WHEN s.hWertung > s.gWertung THEN 2 
			WHEN s.hWertung = s.gWertung THEN 1 
			ELSE 0 
			END) punkte, 

			SUM( 
			CASE 
			WHEN w = 'H' AND s.hWertung > s.gWertung THEN 2 
			WHEN w = 'H' AND s.hWertung = s.gWertung THEN 1 
			ELSE 0 
			END) punkteH, 

			SUM( 
			CASE 
			WHEN w = 'A' AND s.hWertung > s.gWertung THEN 2 
			WHEN w = 'A' AND s.hWertung = s.gWertung THEN 1 
			ELSE 0 
			END) punkteA, 

			SUM( 
			CASE 
			WHEN s.hWertung < s.gWertung THEN 2 
			WHEN s.hWertung = s.gWertung THEN 1 
			ELSE 0 
			END) minusPunkte, 

			SUM( 
			CASE 
			WHEN w = 'H' AND s.hWertung < s.gWertung THEN 2 
			WHEN w = 'H' AND s.hWertung = s.gWertung THEN 1 
			ELSE 0  
			END) minusPunkteH, 

			SUM( 
			CASE 
			WHEN w = 'A' AND s.hWertung < s.gWertung THEN 2 
			WHEN w = 'A' AND s.hWertung = s.gWertung THEN 1 
			ELSE 0  
			END) minusPunkteA,

			SUM(IF(s.hWertung > s.gWertung, 1, 0)) siege, 
			SUM(IF(w = 'H' AND s.hWertung > s.gWertung, 1, 0)) siegeH, 
			SUM(IF(w = 'A' AND s.hWertung > s.gWertung, 1, 0)) siegeA, 


			SUM(IF(s.hWertung = s.gWertung, 1, 0)) unentschieden, 
			SUM(IF(w = 'H' AND s.hWertung = s.gWertung, 1, 0)) unentschiedenH, 
			SUM(IF(w = 'A' AND s.hWertung = s.gWertung, 1, 0)) unentschiedenA, 


			SUM(IF(s.hWertung < s.gWertung, 1, 0)) niederlagen, 
			SUM(IF(w = 'H' AND s.hWertung < s.gWertung, 1, 0)) niederlagenH, 
			SUM(IF(w = 'A' AND s.hWertung < s.gWertung, 1, 0)) niederlagenA, 


			SUM(IF(s.hTore IS NOT NULL, s.hTore, 0)) AS tore, 
			SUM(IF(w = 'H', s.hTore, 0)) AS toreH, 
			SUM(IF(w = 'A', s.hTore, 0)) AS toreA, 

			SUM(IF(s.hTore IS NOT NULL, s.gTore, 0)) AS gegenTore, 
			SUM(IF(w = 'H', s.gTore, 0)) AS gegenToreH, 
			SUM(IF(w = 'A', s.gTore, 0)) AS gegenToreA,	

			SUM(IF(s.hTore IS NOT NULL, s.hTore-s.gTore, 0)) AS torDifferenz,
			SUM(IF(w = 'H', s.hTore-s.gTore, 0)) AS torDifferenzH,
			SUM(IF(w = 'A', s.hTore-s.gTore, 0)) AS torDifferenzA

			FROM ( 
				SELECT heim as mannschaft, kuerzel 
				FROM hb_spiel
				WHERE kuerzel = ".$db->q($teamkey)."
				AND saison=".$db->q($this->season)." 
				GROUP BY mannschaft
				) AS m
			LEFT JOIN
			(
				SELECT 
				'H' w, 
				s1.datumZeit datumZeit,
				s1.heim mannschaft, 
				s1.gast gegner, 
				s1.toreHeim hTore, 
				s1.toreGast gTore,
				s1.wertungHeim hWertung,	 
				s1.wertungGast gWertung
				FROM hb_spiel s1 
				WHERE s1.wertungHeim IS NOT NULL  
					AND kuerzel = ".$db->q($teamkey)."
					AND saison=".$db->q($this->season)." 
					".$dateOption."
				UNION 

				SELECT 
				'A' w,
				s2.datumZeit datumZeit,
				s2.gast mannschaft, 
				s2.heim gegner, 
				s2.toreGast hTore, 
				s2.toreHeim gTore, 
				s2.wertungGast hWertung, 
				s2.wertungHeim gWertung 
				FROM hb_spiel s2 
				WHERE s2.wertungHeim IS NOT NULL 
					AND kuerzel = ".$db->q($teamkey)."
					AND saison=".$db->q($this->season)." 
					".$dateOption."
			) AS s USING (mannschaft)

			GROUP BY mannschaft 
			ORDER BY punkte DESC, siege DESC, torDifferenz DESC";
		//echo  __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObjectList();
//		echo __FILE__.' ('.__LINE__.')<pre>'; print_r($result); echo '</pre>';
		return $result;
	}

	
	protected function sortDetailedStandings($standings, $teamkey, 
			$directCompare = false)
	{
		//echo '<pre>';print_r($standings);echo'</pre>';
		//echo "<a>teamkey: </a><pre>".$teamkey."</pre>";
		// iteration with adding direct compare flags
		if ($directCompare) {
			$sortedStandings = array();
			foreach ($standings as $team)
			{
				//echo '<br/>'.$team->mannschaft;
				$sortedStandings = self::insertInStandings($sortedStandings, 
						$team, $teamkey, true);
			}
		}
		// iteration to sort a second time with direct compare, or first time 
		// without
		$sortedStandings = array();
		foreach ($standings as $team)
		{
			//echo '<br/>'.$team->mannschaft;
			$sortedStandings = self::insertInStandings($sortedStandings, 
					$team, $teamkey);
		}
		
		//echo '<pre>';print_r($standings);echo'</pre>';
		return $sortedStandings;
	}
	
	protected function insertInStandings ($standings, $team, $teamkey, 
			$directCompare = false)
	{
		$pos = 0;
		$currRank = null;
		$inserted = false;
		$team->platz = 1;
		$direct = 0;
		if (!isset($team->direct)) {
			$team->direct = null;
		}
		foreach ($standings as $row)
		{				
			if (!$inserted) {
				$compare = self::comparePoints ($team, $row);
				//echo '->'.$compare;
				if ($compare === 1) {
					$inserted = true;
					$currRank = $row->platz;
				}
				elseif ($compare === 0) {
					if ($directCompare) {
						$direct = self::compareDirect($team, $row, $teamkey);
						$row->direct = $row->direct + (-1*$direct);
						$team->direct = $team->direct + $direct;
						//echo $row->mannschaft.'->'.$row->direct.'<br/>';
						//echo $team->mannschaft.'->'.$team->direct.'<br/>';
					}
					$pos++;
				}
				else {
					$pos++;
					$team->platz = $pos+1;
				}
			}
			if (!empty($currRank) && $currRank <= $row->platz) {
				$row->platz++;
			}	
		}
		//echo '<pre>';print_r($pos);echo'</pre>';
		array_splice( $standings, $pos, 0, array($team) );
		return $standings;
	}

	
	protected function comparePoints ($team, $row) 
	{
		//echo '<pre>';print_r($team);echo'</pre>';
		//echo '<pre>';print_r($row);echo'</pre>';
		//echo "<a>teamkey: </a><pre>".$teamkey."</pre>";
		if ($team->punkte > $row->punkte) {
			return 1;
		}
		elseif ($team->punkte == $row->punkte) {
			if ($team->minusPunkte < $row->minusPunkte) {
				return 1;
			}
			elseif ($team->minusPunkte == $row->minusPunkte) {
				if ($team->direct > $row->direct) {
					return 1;
				}
				elseif ($team->direct == $row->direct) {
					//echo $team->mannschaft.'->'.$team->direct.'|'.$row->direct.'<br/>';
					return 0;
				}
			}
		}
		return -1;
	}
	
	protected function  compareDirect($team, $opponent, $teamkey)
	{
		$db = JFactory::getDBO();
		$query = "SELECT 
			mannschaft, gegner, 
			SUM(tore - gtore) AS diff, 
			SUM(IF(w = 'A', tore, 0)) - SUM(IF(w = 'H', gtore, 0)) AS ausTorDiff,
			CASE 
				WHEN SUM(tore - gtore) > 0 
					THEN 1
				WHEN SUM(tore - gtore) < 0 
					THEN -1
				WHEN SUM(tore - gtore) = 0 
					THEN CASE
						WHEN SUM(IF(w='A', tore, 0)) - SUM(IF(w='H', gtore, 0)) > 0
							THEN 1
						WHEN SUM(IF(w='A', tore, 0)) - SUM(IF(w='H', gtore, 0)) < 0 
							THEN -1
						WHEN SUM(IF(w='A', tore, 0)) - SUM(IF(w='H', gtore, 0)) = 0 
							THEN 0	
					END
				ELSE 0
			END AS direct
			FROM 
			(SELECT 
			'H' w, 
			DATE(s1.datumZeit) datum,
			s1.heim mannschaft, 
			s1.gast gegner, 
			s1.wertungHeim tore, 
			s1.wertungGast gtore
			FROM hb_spiel s1 
			WHERE heim=".$db->q($team->mannschaft)."
			AND gast=".$db->q($opponent->mannschaft)."
			AND saison=".$db->q($this->season)." 
			AND kuerzel=".$db->q($teamkey)." 

			UNION 

			SELECT 
			'A' w,
			DATE(s2.datumZeit) datum,
			s2.gast mannschaft, 
			s2.heim gegner, 
			s2.wertungGast tore, 
			s2.wertungHeim gTore 
			FROM hb_spiel s2 
			WHERE gast=".$db->q($team->mannschaft)."
			AND heim=".$db->q($opponent->mannschaft)."
			AND saison=".$db->q($this->season)." 
			AND kuerzel=".$db->q($teamkey)." 
			) AS s 

			GROUP BY mannschaft";
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObject();
		//echo '<pre>direct comparison: ';print_r($result);echo'</pre>';
		return (int) $result->direct;
	}
	
}