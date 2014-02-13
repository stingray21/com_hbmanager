<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbdata extends JModelLegacy
{	
	private $updatedRankings = array();
	private $updatedSchedules = array();
	
	function __construct() 
	{
		parent::__construct();
				
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
	
	function get1Team($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
		return $team;
	}
	
	function getUpdateDate($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel, updateTabelle, updateSpielplan');
		$query->from('hb_mannschaft');
		if ($teamkey != 'all') {
			// request only one team of DB
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		}
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		//echo '=> model->$updated <br><pre>'; print_r($teams); echo '</pre>';
		return $teams;
	}
	
	function getUpdateStatus()
	{
		$updated['rankings'] = $this->updatedRankings;
		$updated['schedules'] = $this->updatedSchedules;
		//echo '=> model->$updated <br><pre>"; print_r($updated); echo "</pre>';
		return $updated;
	}
	
	function updateDb($key = 'none')
	{
		$start = time();
		if ($key != 'none')
		{
			$teams = self::getHvwTeams ($key);
			foreach ($teams as $team)
			{
				self::updateTeam($team);
			}
		}
		echo $duration = (time() - $start). ' sec';	
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
	
	protected function getHvwLink ($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('hvwLink'));
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		$db->setQuery($query);
		$result = $db->loadObject();
		//echo '=> model->$query <br><pre>'; echo $query ; echo '</pre>';
		//echo '=> model->$updated <br><pre>'; print_r($result); echo '</pre>';
		return $result->hvwLink;
	}
	
	function updateTeam($teamkey) 
	{
		self::updateTeamRanking($teamkey);
		self::updateTeamSchedule($teamkey);
	}
	
	// update rankings
	function updateTeamRanking($teamkey) 
	{
		$source = self::getSourceFromHVW( self::getHvwLink($teamkey) );
		$rankingData = self::getRankingData($source['ranking']);
		//echo '=> model->$updated <br><pre>'; print_r($rankingData); echo '</pre>';
		$tableName = 'hbdata_'.$teamkey.'_tabelle';
		self::addDbRankingTable($tableName);
		if (self::updateRankingsInDB($tableName, $rankingData) AND
			self::updateDbTableAllRankings($teamkey)) 
		{
			self::updateRankingTimestamp ($teamkey);
			$this->updatedRankings[] = $teamkey;
		}
	}
	
	function updateTeamSchedule($teamkey) 
	{
		$source = self::getSourceFromHVW( self::getHvwLink($teamkey) );
		$scheduleData = self::getScheduleData($source['schedule']);
		//echo '=> model->$updated <br><pre>'; print_r($scheduleData); echo '</pre>';
		$tableName = 'hbdata_'.$teamkey.'_spielplan';
		self::addDbScheduleTable($tableName);
		if (self::updateSchedulesInDB($tableName, $scheduleData) AND
			self::updateDbTableAllSchedules($teamkey)) 
		{
			self::updateScheduleTimestamp ($teamkey);
			$this->updatedSchedules[] = $teamkey;
		}
	}
		
	protected function getSourceFromHVW($address)
	{
		// returns sourcecode of a website with the address $address as string
		$sourcecode = file_get_contents($address);
	
		// shortens strings to relevant part for rankings
		$start = strpos($sourcecode,">Punkte</th></tr>")+17;
		$end = strpos($sourcecode,"</tr></TABLE></div>",$start);
		$source['ranking'] = substr($sourcecode,$start,($end-$start));
		
		// shortens strings to relevant part for schedule
		$start = strpos($sourcecode,'<th align="center">Bem.</th>')+34;
		$end = strpos($sourcecode,'</table>',$start)-8;
		$source['schedule'] = substr($sourcecode,$start,($end-$start));
		
		return $source;
	}
	
	protected function getRankingData($source)
	{
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
		$replaceMarker = array('||', '&&', '', '');
		$source = str_replace($searchMarker, $replaceMarker ,$source);
		
		$source = strip_tags($source);
		
		$search = array('|| ||', '||||', '||:||', '||&&');
		$replace = array('||', '||', '||', '&&');
		$source = str_replace($search, $replace ,$source);
		
		//echo $source;
		
		return $rankingsData = self::explode2D($source);
	}
	
	protected function getScheduleData($source)
	{
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
		$replaceMarker = array('||', '&&', '', '');
		$source = str_replace($searchMarker, $replaceMarker ,$source);
		
		$source = strip_tags($source);
		
		$search = array('||-||', '||:||', '||&&', ', ');
		$replace = array('||', '||', '&&', '||');
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
			$data[$key][3] = preg_replace('/(\d{2}).(\d{2}).(\d{2})/',
								'20$3-$2-$1', $value[3]);
			$data[$key][4] = str_replace('h', ':00', $value[4]);
		}
		return $data;
	}

	protected function addDbRankingTable($tablename) {
		$db = $this->getDbo();
		
		$query = "CREATE TABLE IF NOT EXISTS ".
			$db->qn($tablename)." (
			`ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
			`Platz` tinyint(2) DEFAULT NULL,
			`Verein` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			`Spiele` tinyint(2) DEFAULT NULL,
			`Siege` tinyint(2) DEFAULT NULL,
			`Unentschieden` tinyint(2) DEFAULT NULL,
			`Niederlagen` tinyint(2) DEFAULT NULL,
			`Plustore` mediumint(4) DEFAULT NULL,
			`Minustore` mediumint(4) DEFAULT NULL,
			`Pluspunkte` tinyint(2) DEFAULT NULL,
			`Minuspunkte` tinyint(2) DEFAULT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB  
		DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		return $result;
	}
	
	protected function addDbScheduleTable($tableName)
	{
		$db = $this->getDbo();
		
		$query = "CREATE TABLE IF NOT EXISTS ".$db->qn($tableName)." (
			  `ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
			  `Klasse` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `SpielNR` mediumint(3) DEFAULT NULL,
			  `Tag` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `Datum` date DEFAULT NULL,
			  `Zeit` time DEFAULT NULL,
			  `Halle` mediumint(4) DEFAULT NULL,
			  `Heim` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `Gast` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `ToreHeim` int(3) DEFAULT NULL,
			  `ToreGast` int(3) DEFAULT NULL,
			  `Bemerkung` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB  
			DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		return $result;
	}
	
	protected function updateRankingsInDB($tableName, $dataArray) 
	{
		$db = $this->getDbo();
		
		// delete existing data
		$db->truncateTable ($tableName);
		
		$query = $db->getQuery(true); 
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Platz`, `Verein`, `Spiele`, `Siege`, `Unentschieden`, ".
				"`Niederlagen`, `Plustore`, `Minustore`, `Pluspunkte`, ".
				"`Minuspunkte`)";
		$query .= " VALUES \n";
		
		foreach ($dataArray as $data)
		{
			$row = '(';
			if (!empty($data[0])) $row .= (int) $data[0].', ';	// Platz
			else $row .= 'NULL, ';
			$row .= "'".$data[1]."', ";			//Verein
			$row .= (int) $data[2].", ";		//Spiele
			$row .= (int) $data[3].", ";		//Siege
			$row .= (int) $data[4].", ";		//Unentschieden
			$row .= (int) $data[5].", ";		//Niederlagen
			$row .= (int) $data[6].", ";		//Plustore
			$row .= (int) $data[7].", ";		//Minustore
			$row .= (int) $data[8].", ";		//Pluspunkte
			$row .= (int) $data[9]."), \n";		//Minuspunkte
			
			$query .= $row;
		}
		$query = rtrim($query, ", \n");
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		try 		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) 
		{
			// catch any database errors.
		}
		if ($result !== FALSE) {
			//return time();
			return true;
		}
		else {
			return FALSE;
		}
	}
	
	protected function updateSchedulesInDB($tableName, $dataArray)
	{
		$db = $this->getDbo();
// delete existing data
		$db->truncateTable ($tableName);
		
		
		$query = $db->getQuery(true);
		$query = "INSERT INTO ".$tableName;
		$query .= " (`Klasse`, `SpielNR`, `Tag`, `Datum`, `Zeit`, `Halle`,
			`Heim`, `Gast`, `ToreHeim`, `ToreGast`, `Bemerkung`)";
		$query .= " VALUES \n";
	
		foreach ($dataArray as $data)
		{
			// Klasse	
			$game = "('".$data[0]."'";
			// SpielNR
			$data[1] = (int) $data[1];
			if ($data[1] != 0) $game .= ", '".$data[1]."'";
			else $game .= ",NULL";
			
			// Tag
			if (preg_match('/(Sa|So|Mo|Di|Mi|Do|Fr)/',$data[2])) {
				$game .= ",'".$data[2]."'";
			}
			else $game .= ",NULL";
			
			// Datum YY-MM-DD
			if (preg_match('/\d{2}-\d{2}-\d{2}/',$data[3])) {
				$game .= ",'".$data[3]."'";
			}
			else $game .= ",NULL";
			
			// Zeit
			if (preg_match('/\d{2}:\d{2}:\d{2}/',$data[4])) {
				$game .= ",'".$data[4]."'";
			}
			else $game .= ",NULL";
			
			// Halle
			if ((int) $data[5] != 0) $game .= ",'".$data[5]."'";
			else $game .= ",NULL";
			
			// Heim
			$game .= ",'".addslashes($data[6])."'";
			
			// Gast
			$game .= ",'".addslashes($data[7])."'";
			
			// ToreHeim
			if ($data[8] != "") $game .= ",".(int)$data[8]."";
			else $game .= ",NULL";
			
			// ToreGast
			if ($data[9] != "") $game .= ", ".(int)$data[9]."";
			else $game .= ",NULL";
			
			// Bemerkung
			$game .= ",'".$data[10]."'), \n";	
				
			$query .= $game;
		}
		$query = rtrim($query, ", \n");
	
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
	
		return $result;
	}
	
	protected function updateRankingTimestamp ($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update('hb_mannschaft');
		$query->set($db->qn('updateTabelle').' = NOW()');
		$query->where($db->qn('kuerzel').' = '.
					$db->q($teamkey));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
			return false;
		}
		return true;
	}
	
		protected function updateScheduleTimestamp ($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update('hb_mannschaft');
		$query->set($db->qn('updateSpielplan').' = NOW()');
		$query->where($db->qn('kuerzel').' = '.
					$db->q($teamkey));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
			return false;
		}
		return true;
	}
	
	protected function updateDbTableAllRankings($teamkey)
	{
		// get the rankings data of team
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hbdata_'.$teamkey.'_tabelle');
		$db->setQuery($query);
		$teamRankings= $db->loadObjectList();
		// echo '=> model->$query <br><pre>'.$query.'</pre>';
		// echo '=> model->$teamRankings <br><pre>'; 
			// print_r($teamRankings);echo "</pre>";

		$query = $db->getQuery(true);
		$query->select('Verein');
		$query->from('hb_tabelle');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		$db->setQuery($query);
		$clubs= $db->loadObjectList();
		// echo '=> model->$query <br><pre>'.$query.'</pre>';
		// echo '=> model->$clubs <br><pre>'; print_r($clubs);echo '</pre>';

		$update = false;
		if (count($teamRankings) == count($clubs)) $update = true;

		if ($update) 
		{
			$query = $db->getQuery(true);
			$query->delete('hb_tabelle');
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
			$db->setQuery($query);
			$db->query();
		}

		foreach ($teamRankings as $row)
		{
			// use ranking from previous row in case of empty ranking
			// (for direct access of row)
			if (!empty($row->Platz)) $curRanking = $row->Platz; 

			$diff = $row->Plustore - $row->Minustore;

			$query = $db->getQuery(true);
			if ($update) $query->update('hb_tabelle');
			else $query->insert('hb_tabelle');

			$query->set(
					$db->qn('kuerzel').' = '.
						$db->q($teamkey).', '.
					$db->qn('platz').' = '.
						$db->q($curRanking).', '.
					$db->qn('verein').' = '.
						$db->q($row->Verein).', '.
					$db->qn('spiele').' = '.
						$db->q($row->Spiele).', '.
					$db->qn('siege').' = '.
						$db->q($row->Siege).', '.
					$db->qn('unentschieden').' = '.
						$db->q($row->Unentschieden).', '.
					$db->qn('niederlagen').' = '.
						$db->q($row->Niederlagen).', '.
					$db->qn('plustore').' = '.
						$db->q($row->Plustore).', '.
					$db->qn('minustore').' = '.
						$db->q($row->Minustore).', '.
					$db->qn('torDifferenz').' = '.
						$db->q($diff).', '.
					$db->qn('pluspunkte').' = '.
						$db->q($row->Pluspunkte).', '.
					$db->qn('minuspunkte').' = '.
						$db->q($row->Minuspunkte));
			if ($update) 
			{
				$query->where(
						$db->qn('kuerzel').' = '.
							$db->q($teamkey).' AND '.
						$db->qn('verein').' = '.
							$db->q($row->Verein));
			}
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result[] = $db->query();
			} catch (Exception $e) {
				// catch any database errors.
			}

			// display and convert to HTML when SQL error
			if ($db->getErrorMsg() != '')
			{
				$jAp->enqueueMessage(nl2br($db->getErrorMsg()."\n\n"),
						'error');
			}
		}

		if (!in_array(false, $result)) {
			//return time();
			return true;
		}
		else {
			return FALSE;
		}
	}
	
	protected function updateDbTableAllSchedules($teamkey)
	{
		$team = self::get1Team($teamkey);
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hbdata_'.$team->kuerzel.'_spielplan');
		$query->where($db->qn('Heim').' = '.$db->q($team->name).' OR '.
				$db->qn('Gast').' = '.$db->q($team->name));
		$db->setQuery($query);
		$games= $db->loadObjectList();

		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		//echo '=> model->$games <br><pre>'; print_r($games);echo '</pre>';

		// VARIANT 1: INSERT ... ON DUPLICATE KEY UPDATE
		foreach ($games as $game)
		{
			$query = 'INSERT INTO hb_spiel (spielIDhvw, kuerzel,'. 
						'hallenNummer, datum, uhrzeit, heim, gast,'. 
						'toreHeim, toreGast, bemerkung) VALUES '."\n";
			$query .= '('.$db->q($game->SpielNR).', '.
					$db->q($team->kuerzel).', '.
					$db->q($game->Halle).', '.
					$db->q($game->Datum).', '.
					$db->q($game->Zeit).', '.
					$db->q($game->Heim).', '.
					$db->q($game->Gast).', ';
			if ($game->ToreHeim !== NULL) $query .= $db->q($game->ToreHeim);
			else $query .= 'NULL';
			$query .= ', ';
			if ($game->ToreGast !== NULL) $query .= $db->q($game->ToreGast);
			else $query .= 'NULL';
			$query .= ', ';
			$query .= $db->q($game->Bemerkung).')';
			$query .= "\n".'ON DUPLICATE KEY UPDATE ';
			$query .= 'spielIDhvw = '.$db->q($game->SpielNR).
					', kuerzel = '.$db->q($team->kuerzel).
					', hallenNummer = '.$db->q($game->Halle).
					', datum = '.$db->q($game->Datum).
					', uhrzeit = '.$db->q($game->Zeit).
					', heim = '.$db->q($game->Heim).
					', gast = '.$db->q($game->Gast);
			$query .= ', toreHeim = ';
			//if (!empty($game->ToreHeim) || '0' == $game->ToreHeim) {
			//	$query .= $db->q($game->ToreHeim);
			//}
			//else $query .= 'NULL';
			if ($game->ToreHeim === NULL) $query .= 'NULL';
			else $query .= $db->q($game->ToreHeim);
			$query .= ', toreGast = ';
			//if (!empty($game->ToreGast) || '0' == $game->ToreGast) {
			//	$query .= $db->q($game->ToreGast);
			//}
			//else $query .= 'NULL';
			if ($game->ToreGast === NULL) $query .= 'NULL';
			else $query .= $db->q($game->ToreGast);
			$query .= ', bemerkung = '.$db->q($game->Bemerkung)."\n";
		
			//echo '=> model->$query <br><pre>'.$query.'</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result[] = $db->query();
			} catch (Exception $e) {
				// catch any database errors.
			}
		}
	
		if (!in_array(false, $result)) {
			//return time();
			return true;
		}
		else {
			return FALSE;
		}
	return ;
	}
}