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
		
		if (self::updateGamesInDB($teamkey, $source['schedule'])
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

    protected function getSourceFromHVW($address)
    {
		// returns sourcecode of a website with the address $address as string
		$sourcecode = file_get_contents($address);
		
        $sourcecode = str_replace('&#160;', ' ' ,$sourcecode);
		
		// shortens strings to relevant part for headline
		preg_match('/<h1>.*\d{4}\/\d{4}<\/h1>/', $sourcecode, $matches);
		//print_r($matches);
		$source['headline'] = $matches[0];
//		echo __FILE__.' ('.__LINE__.')<pre>';print_r($sourcecode);echo'</pre>';
//		die;
//		
		// shortens strings to relevant part for standings
		$start = strpos($sourcecode,">Punkte</th></tr>")+17;
		$end = strpos($sourcecode,"</tr></TABLE></div>",$start);
		$source['standings'] = '';
		if ($start+$end > 17) {
			$source['standings'] = substr($sourcecode,$start,($end-$start));
		}
//		echo __FILE__.' ('.__LINE__.')<pre>'.$start.' -> '.$end.'</pre>';
//		echo __FILE__.' ('.__LINE__.')<pre>';print_r($source['standings']);echo'</pre>';
//		die;
		
		// shortens strings to relevant part for schedule
		$start = strpos($sourcecode,'<th align="center">Bem.</th>')+34;
		$end = strpos($sourcecode,'</table>',$start)-8;
		$source['schedule'] = '';
		if ($start+$end > 26) {
			$source['schedule'] = substr($sourcecode,$start,($end-$start));
		}
//		echo __FILE__.' ('.__LINE__.')<pre>'.$start.' -> '.$end.'</pre>';
		echo __FILE__.' ('.__LINE__.')<pre>';print_r($source['schedule']);echo'</pre>';
		die;
		return $source;
    }

    protected function getScheduleData($source)
    {
		// insert dividers 
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
		$replaceMarker = array('||', '&&', '', '');
		$source = str_replace($searchMarker, $replaceMarker ,$source);
		//echo '=> '.__FUNCTION__.'<br><pre>'; print_r($source); echo '</pre>';
		
		// remove link tag for game report 
		// <a href="/misc/sboPublicReports.php?sGID=54233" target="_blank">PI</a>
		$source = preg_replace('#<a href="(/misc/sboPublicReports\.php\?sGID=\d{4,7})" target="_blank">PI</a>\s?#', '$1', $source);
		
		
		$source = strip_tags($source);

		// split date field
		//$search = array(', ');
		//$replace = array('||');
		//$source = str_replace($search, $replace ,$source);

		//echo '=> '.__FUNCTION__.' - '.__LINE__.'<br><pre>'; print_r($source); echo '</pre>';
		//die;
		$scheduleData = self::explode2D($source);
		return self::formatScheduleData($scheduleData);
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

    protected function formatScheduleData($data)
    {
		//echo '=> '.__FILE__.'('.__LINE__.')<br><pre>'; print_r($data); echo '</pre>';
		foreach ($data as $key => $value) 
		{
			//if ($value[1] ==  73568) {echo '=> '.__FUNCTION__.'<br><pre>'; print_r($value); echo '</pre>';}
			
			//format date time
			$pattern = "/\w{2}, (?P<day>\d{2})\.(?P<month>\d{2})\.(?P<year>\d{2})(, (?P<time>\d{2}:\d{2})h)?/";
			
			//TODO
			// try(maybe in formatGamesValues())
			// $date = "6.1.2009 13:00+01:00";
			// print_r(date_parse_from_format("j.n.Y H:iP", $date));
			
			
			preg_match($pattern, $value[2], $match);
			//echo __FUNCTION__.'<pre>';print_r($match); echo'</pre>';
			$value[2] = '20'.$match['year'].$match['month'].$match['day'];
			if (!isset($match['time'])) $match['time'] = '00:00';
			$value[2] .= ' '.$match['time'].":00";
			
			unset($value[5]);
			unset($value[8]);
			unset($value[11]);
			$judging = self::getJudging($value[10],$value[7],$value[9]);
			$value[13] = $judging['home']; 
			$value[14] = $judging['away'];
			
			// add game report link
			if (preg_match('#(/misc/sboPublicReports\.php\?sGID=(\d{4,7}))#', $value[10], $matches) ) {
				//print_r($matches);
				$value[10] = '';
				//$value[15] = $matches[0]; // complete link
				$value[15] = $matches[2]; // just ID
			} else {
				$value[15] = null;
			}
			
			$value = array_values($value);
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';
			$data[$key] = $value;
		}
		//echo '=> '.__FUNCTION__.'<br><pre>'; print_r($data); echo '</pre>';
		//exit();
		return $data;
    }
	
	protected function getJudging($comment, $scoreHome, $scoreAway)
    {
		switch (trim($comment))
		{
			case '(2:0)':
			case '(0:2)':
				$judging['home'] = (int) preg_replace('/^\((\d):(\d)\)$/',
					'$1',$comment);
				$judging['away'] = (int) preg_replace('/^\((\d):(\d)\)$/',
					'$2',$comment);
				break;
			case 'ausg..':
			case 'n.g.':
				$judging['home'] = '';
				$judging['away'] = '';
			default:
				$judging['home'] = $scoreHome;
				$judging['away'] = $scoreAway;
				break;
		}
		//echo '=> model->$data <br><pre>'; print_r($data); echo '</pre>';
		return $judging;
    }
	
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

    protected function updateGamesInDB($teamkey, $source)
    {
		$scheduleData = self::getScheduleData($source);
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($scheduleData);echo'</pre>'; die;
		self::deleteOldData ('hb_spiel', $teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$columns = array('saison',  'spielIdHvw', 'kuerzel', 
				'ligaKuerzel', 'hallenNr', 'datumZeit', 
				'heim', 'gast', 'toreHeim', 'toreGast', 'bemerkung',
				'wertungHeim', 'wertungGast', 'eigenerVerein', 'berichtLink');

		$saison = self::getSeason();

		foreach($scheduleData as $row) {
			$values[] = implode(', ', 
			self::formatGamesValues($row, $teamkey, $saison));
		}

		// Prepare the insert query.
		$query
				->insert($db->qn('hb_spiel')) 
				->columns($db->qn($columns))
				->values($values);

		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$query .= self::getOnDublicate($columns);
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';die;
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

    protected function formatGamesValues($data, $teamkey)
    {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($data);echo'</pre>';die;
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['spielIdHvw'] = $db->q($data[1]);
		$value['kuerzel'] = $db->q($teamkey);
		$value['ligaKuerzel'] = $db->q($data[0]);
		// HallenNummer
		if (trim($data[3]) != '') $value['hallenNr'] = (int)$data[3];
				else  $value['hallenNr'] = "NULL";
		// Datum & Uhrzeit
		if (trim($data[2]) != '') {	
			$sqlDateTime = JFactory::getDate($data[2], 'Europe/Berlin' )->toSql();
			//echo '<p>HVW: <b>'.$datetime.'</b> -> in DB: <b>'.$sqlDateTime.'</b></p>";
			$value['datumzeit'] = $db->q($sqlDateTime);
		}
		else  $value['datumzeit'] = "NULL";

		$value['heim'] = $db->q(addslashes($data[4]));
		$value['gast'] = $db->q(addslashes($data[5]));
		// ToreHeim
		if (trim($data[6]) != '') $value['toreHeim'] = (int)$data[6];
				else  $value['toreHeim'] = "NULL";
		// ToreGast
		if (trim($data[7]) != '') $value['toreGast'] = (int)$data[7];
				else  $value['toreGast'] = "NULL";
		// Bemerkung
		if (trim($data[8]) != '') $value['bemerkung'] = $db->q($data[8]);
				else  $value['bemerkung'] = "NULL";
		// WertungHeim
		if (trim($data[9]) != '') $value['wertungHeim'] = (int)$data[9];
				else  $value['wertungHeim'] = "NULL";
		// WertungGast
		if (trim($data[10]) != '') $value['wertungGast'] = (int)$data[10];
				else  $value['wertungGast'] = "NULL";
		
		// Team of own club
		$ownTeam = self::checkIfOwnTeamIsPlaying($data[4], $data[5]);
		if ($ownTeam) $value['eigenerVerein'] = $ownTeam;
				else  $value['eigenerVerein'] = "NULL";		
				
		// BerichtLink
		if (trim($data[10]) != '') $value['berichtLink'] = $db->q($data[11]);
				else  $value['berichtLink'] = "NULL";

		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';
		return $value;
    }

	protected function checkIfOwnTeamIsPlaying($home, $away)
	{
		if (in_array($home, $this->names)) {
			return true;
		}
		if (in_array($away, $this->names)) {
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
    
    protected function getStandingsData($source)
    {
//        echo __FILE__.' ('.__LINE__.')<pre>';print_r($source);echo'</pre>';
//		die;
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
        $replaceMarker = array('||', '&&', '', '');
        $source = str_replace($searchMarker, $replaceMarker ,$source);

        $source = strip_tags($source);
		
        $search = array('|| ||', '||||', '||:||', '||&&');
        $replace = array('||', '||', '||', '&&');
        $source = str_replace($search, $replace ,$source);

        //echo '=> '.__FUNCTION__.' - '.__LINE__.'<br><pre>'; print_r($source); echo '</pre>';
		//die;

        return $standingsData = self::explode2D($source);
    }
	
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

		//echo '=> model->$query <br><pre>'.$query.'</pre>';
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
		$standingsData = self::getStandingsData($source);
		//echo '=> model<br><pre>'; print_r($standingsData);echo '</pre>';
		
		$prevRank = 1; // in case no rank in HVW --> same as previous team
		$values = null;
		foreach($standingsData as $row) {
			if (trim($row[0]) == '') {
				$row[0] = $prevRank;
			}
			$values[] = implode(', ', 
					self::formatStandingsValues($row, $teamkey));
			$prevRank = $row[0];
		}
		
		$result = self::updateStandingsInDb($teamkey, $table, $columns, $values);
		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }
	
	
	protected function formatStandingsValues($data, $teamkey)
    {
		//echo '=> model<br><pre>'; print_r($data);echo '</pre>';
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['kuerzel'] = $db->q($teamkey);
		
		// Platz
		$value['platz'] = $data[0];
		// Verein
		$value['mannschaft'] = $db->q($data[1]);
		
		$value['spiele'] = $data[2];
		$value['s'] = $data[3];
		$value['u'] = $data[4];
		$value['n'] = $data[5];
		$value['tore'] = $data[6];
		$value['gegenTore'] = $data[7];
		$value['torDiff'] = $data[6]-$data[7];
		$value['punkte'] = $data[8];
		$value['minusPunkte'] = $data[9];
		
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