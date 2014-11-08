<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbdata extends JModelLegacy
{	
    private $updated = array();
	private $season;
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

    function updateTeam($teamkey) 
    {
		$source = self::getSourceFromHVW( self::getHvwLink($teamkey) );
		self::setSeason($source['headline']);
		
		if (self::updateGamesInDB($teamkey, $source['schedule'])
				&& self::updateHvwStandingsInDB($teamkey, $source['standings'])
				&& self::updateDetailedStandingsInDB($teamkey) )
		{
			self::updateTimestamp ($teamkey);
			$this->updated[] = $teamkey;
			self::updateLog('schedule', $teamkey);
			return true;
		}
		return false;
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
		
		// shortens strings to relevant part for headline
		preg_match('/<h1>.*\d{4}\/\d{4}<\/h1>/', $sourcecode, $matches);
		//print_r($matches);
		$source['headline'] = $matches[0];

		// shortens strings to relevant part for standings
		$start = strpos($sourcecode,">Punkte</th></tr>")+17;
		$end = strpos($sourcecode,"</tr></TABLE></div>",$start);
		$source['standings'] = substr($sourcecode,$start,($end-$start));

		// shortens strings to relevant part for schedule
		$start = strpos($sourcecode,'<th align="center">Bem.</th>')+34;
		$end = strpos($sourcecode,'</table>',$start)-8;
		$source['schedule'] = substr($sourcecode,$start,($end-$start));

		return $source;
    }

    protected function getScheduleData($source)
    {
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
		$replaceMarker = array('||', '&&', '', '');
		$source = str_replace($searchMarker, $replaceMarker ,$source);

		$source = strip_tags($source);

		$search = array(', ');
		$replace = array('||');
		$source = str_replace($search, $replace ,$source);

		//echo $source;

		$scheduleData = self::explode2D($source);
		return self::formatScheduleData($scheduleData);
    }

    protected function explode2D ($source)
    {
		$data = explode('&&',$source);
		foreach ($data as $key => $value) 
		{
			$data[$key] = explode('||',$value);
		}
		return $data;
    }

    protected function formatScheduleData($data)
    {
		foreach ($data as $key => $value) 
		{
			$value[3] = preg_replace('/(\d{2}).(\d{2}).(\d{2})/',
										'20$3-$2-$1', $value[3]);
			$value[4] = str_replace('h', ':00', $value[4]);
			unset($value[7]);
			unset($value[10]);
			unset($value[13]);
			$judging = self::getJudging($value[12],$value[9],$value[11]);
			$value[] = $judging['home']; 
			$value[] = $judging['away'];
			$value = array_values($value);
			$data[$key] = $value;
		}
		//echo '=> model->$data <br><pre>'; print_r($data); echo '</pre>';
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
		//echo '=> model<br><pre>'; print_r($scheduleData);echo '</pre>';
		self::deleteOldData ('hb_spiel', $teamkey);

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$columns = array('saison',  'spielIDhvw', 'kuerzel', 
				'ligaKuerzel', 'hallenNr', 'datumZeit', 
				'heim', 'gast', 'toreHeim', 'toreGast', 'bemerkung',
				'wertungHeim', 'wertungGast', 'eigenerVerein');

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
		$db->setQuery($query);
		$result = $db->execute();

		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
    }

    protected function formatGamesValues($data, $teamkey)
    {
		//echo '=> model<br><pre>'; print_r($data);echo '</pre>';
		$db = $this->getDbo();

		$value['saison'] = $db->q(self::getSeason());
		$value['spielIDhvw'] = $db->q($data[1]);
		$value['kuerzel'] = $db->q($teamkey);
		$value['ligaKuerzel'] = $db->q($data[0]);
		// HallenNummer
		if (trim($data[5]) != '') $value['hallenNummer'] = (int)$data[5];
				else  $value['hallenNr'] = "NULL";
		// Datum & Uhrzeit
		if (trim($data[3]) != '' || trim($data[4]) != '') {	

				$date = JFactory::getDate($data[3].' '.$data[4], 'Europe/Berlin' )
								->toSql();
				//echo '<p>HVW:'.$data[3].' '.$data[4].' -> in DB: '.$date."</p>";
				$value['datumzeit'] = $db->q($date);
		}
		else  $value['datumzeit'] = "NULL";

		$value['heim'] = $db->q(addslashes($data[6]));
		$value['gast'] = $db->q(addslashes($data[7]));
		// ToreHeim
		if (trim($data[8]) != '') $value['toreHeim'] = (int)$data[8];
				else  $value['toreHeim'] = "NULL";
		// ToreGast
		if (trim($data[9]) != '') $value['toreGast'] = (int)$data[9];
				else  $value['toreGast'] = "NULL";
		// Bemerkung
		if (trim($data[10]) != '') $value['bemerkung'] = $db->q($data[10]);
				else  $value['bemerkung'] = "NULL";
		// WertungHeim
		if (trim($data[11]) != '') $value['wertungHeim'] = (int)$data[11];
				else  $value['wertungHeim'] = "NULL";
		// WertungGast
		if (trim($data[12]) != '') $value['wertungGast'] = (int)$data[12];
				else  $value['wertungGast'] = "NULL";
		
		// Team of own club
		$ownTeam = self::checkIfOwnTeamIsPlaying($data[6], $data[7]);
		if ($ownTeam) $value['eigenerVerein'] = $ownTeam;
				else  $value['eigenerVerein'] = "NULL";		

		//echo '=> model<br><pre>'; print_r($value);echo '</pre>';
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
	
	protected function setSeason($string)
    {
		$season = preg_replace('/.*(\d{4})\/(\d{4}).*/', '$1-$2', $string);
		
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
        $searchMarker = array('</td>', '</tr>',"\n" ,"\t");
        $replaceMarker = array('||', '&&', '', '');
        $source = str_replace($searchMarker, $replaceMarker ,$source);

        $source = strip_tags($source);

        $search = array('|| ||', '||||', '||:||', '||&&');
        $replace = array('||', '||', '||', '&&');
        $source = str_replace($search, $replace ,$source);

        //echo $source;

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
	
	protected function getDetailedStandingsData ($teamkey)		
	{
		$db = JFactory::getDBO();
		$query = "SELECT 
			mannschaft,
			
			COUNT(IF(s.hWertung IS NOT NULL, s.hWertung, 0)) spiele, 
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
				WHERE s1.wertungHeim IS NOT NULL && kuerzel = ".
					$db->q($teamkey)."

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
				WHERE s2.wertungHeim IS NOT NULL && kuerzel = ".
					$db->q($teamkey)."
			) AS s USING (mannschaft)

			GROUP BY mannschaft 
			ORDER BY punkte DESC, siege DESC, torDifferenz DESC";
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		//echo '<pre>';print_r($result);echo'</pre>';
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
			s1.toreHeim tore, 
			s1.toreGast gtore
			FROM hb_spiel s1 
			WHERE heim=".$db->q($team->mannschaft)."
			AND gast=".$db->q($opponent->mannschaft)."
			AND kuerzel=".$db->q($teamkey)." 

			UNION 

			SELECT 
			'A' w,
			DATE(s2.datumZeit) datum,
			s2.gast mannschaft, 
			s2.heim gegner, 
			s2.toreGast tore, 
			s2.toreHeim gTore 
			FROM hb_spiel s2 
			WHERE gast=".$db->q($team->mannschaft)."
			AND heim=".$db->q($opponent->mannschaft)."
			AND kuerzel=".$db->q($teamkey)." 
			) AS s 

			GROUP BY mannschaft";
//		echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObject();
		//echo '<pre>direct comparison: ';print_r($result);echo'</pre>';
		return (int) $result->direct;
	}
	
}