<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
$test = JTable::addIncludePath(JPATH_COMPONENT . '/tables');
//echo __FILE__.'<pre>';print_r( $test); echo'</pre>';
		
// Register all files in the /libraries/mylib folder as classes with a name like:  MyLib<Filename>
JLoader::discover('HBlib', JPATH_LIBRARIES . '/hblib');

class HbmanagerModelHbreportinput extends JModelLegacy
{	
	
	private $gameId = null;
	private $season = null;
	private $teamkey = null;
	private $importFilePath = JPATH_ROOT.'/hbdata/reports/';
	private $goalkeeper = array();
	private $homeAliases = array();
	private $importedData = null;
	
	function __construct() 
	{
		
		parent::__construct();
	}

	public function getGameId () {
		return $this->gameId;
	}

	public function getReportRawText()
	{
		$rawText = file_get_contents($this->importFilePath.$this->gameId.'_all.txt');
		return $rawText;
	}

	public function getImportedDataString() 
	{
		return serialize($this->importedData);
	}

	public function getImportedData()
	{
		return $this->importedData;
	}

	public function getAvailableImportFiles()
	{
		// $files = scandir($this->importFilePath);

		$files = array_filter(scandir($this->importFilePath), function($file) { 
			return strpos($file, '.txt') !== false; 
			});
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($files);echo '</pre>';

		foreach ($files as $file) {
			preg_match('/(\d{5,6})_all.txt/',$file, $match);
			if (count($match) > 1) { 
				$array[] = $match[1];
				} 
		}
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($array);echo '</pre>';
		return $array;
	}

	public function importData()
	{
		$rawText = self::getReportRawText();
		$parts = explode("\n\n//-----", $rawText);
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($parts);echo '</pre';
		foreach ($parts as &$part) {
			$part = explode("----------\n", $part);
		}
		$sections['gameInfo'] = self::getGameArray($parts[1][1]);
		$sections['goalsHome'] = self::getGoalArray($parts[3][1]);
		self::setHomeAliases($sections['goalsHome']);
		$sections['goalsAway'] = self::getGoalArray($parts[4][1]);
		$sections['action'] = self::getActionArray($parts[5][1]);
		return $sections;
	}


	private function getGameArray($string)
	{
		//$score = explode(",", $row[2]);
		if (!empty($string)) {
			$rows = str_getcsv($string, "\n"); 
			//parse the items in rows
			foreach($rows as &$row) {
				$row = str_getcsv($row, "," );
			}
		}
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($rows);echo '</pre>';

		preg_match("|.*NZ: (.*) \((.*)\)|", $rows[0][1],$match);
		$game['league'] = $match[1];
		$game['leagueKey'] = $match[2];

		preg_match("|(\d{4,6}) , am (\d\d?.\d\d?.\d\d) um (\d?\d:\d\d)h|", $rows[1][1],$match);
		$game['gameId'] = $match[1];
		$game['date'] = $match[2];
		$game['time'] = $match[3];

		preg_match("|(.*) \((.*)\)|", $rows[2][1],$match);
		$game['gym'] = $match[1];
		$game['gymId'] = $match[2];

		preg_match("|(.*) - (.*)|", $rows[3][1],$match);
		$game['teamHome'] = $match[1];
		$game['teamAway'] = $match[2];

		preg_match("|(.*) (\((.*)\) )?|", $rows[4][1],$match);
		$game['result'] = $match[1];
		$game['halftime'] = (!empty($match[3])) ? $match[3] : '';

		$game['homeGame'] = $this->homeGame;

		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($game);echo '</pre>';

		return $game;
	}

	public function setGameInfo($gameId)
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('spielIdHvw, kuerzel, saison, heim, gast');
		$query->from('hb_spiel');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($gameId));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$info = $db->loadObject();
		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
		$this->gameId = $info->spielIdHvw;
		$this->teamkey = $info->kuerzel;
		$this->season = $info->saison;
		$this->homeGame = self::getHomeGame($info->heim);

		self::setGoalkeeper($this->teamkey);

		$this->importedData = self::importData();
	}

	private function getHomeGame($home)	
	{
		$indicator = self::getLocalTeamIndicator();

		foreach ($indicator as $test) {
			if (strpos($home, $test) !== false) {
				return 1;
			}
		}
		return 0;
	}

	private function getLocalTeamIndicator() 
	{
		//TODO get from backend settings
		$string = array('Ostd', 'Geisl');
		return $string;
	}

	private function setHomeAliases($array) {
		foreach ($array as $value) {
			$this->homeAliases[] = $value['alias'];
		}
	}

	private function setGoalkeeper($teamkey) {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('alias, kuerzel, TW');
		$query->from('hb_mannschaft_spieler');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$info = $db->loadObjectList();
		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
		$goalkeeper = array();
		foreach ($info as $player) {
			$goalkeeper[$player->alias] = $player->TW;
		}
		$this->goalkeeper = $goalkeeper;
	}

	private function getActionArray($string)
	{
		//parse the rows
		$string = self::removeActionExtraText(trim($string));
		if (!empty($string)) {
			$rows = str_getcsv($string, "\n"); 
			//parse the items in rows
			foreach($rows as &$row) {
				$row = str_getcsv($row, "," );
			}
		}
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($rows);echo '</pre>';
		$rows = self::getActionValues($rows);
		$rows = self::addStartAndEnd($rows);
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($rows);echo '</pre>';
		return $rows;
	}

	private function addStartAndEnd($rows) {
		$start['season'] = $rows[0]['season'];
		$start['spielIdHvw'] = $rows[0]['spielIdHvw'];
		$start['actionIndex'] = 0;
		$start['timeString'] = '00:00';
		$start['time'] = 0;
		$start['scoreHome'] = 0;
		$start['scoreAway'] = 0;
		$start['scoreDiff'] = 0;
		$start['scoreChange'] = 1;
		$start['text'] = 'Spielbeginn';
		$start['number'] = null;
		$start['name'] = null;
		$start['alias'] = null;
		$start['team'] = 0;
		$start['category'] = 'game';
		$start['stats_goals'] = null;
		$start['stats_yellow'] = null;
		$start['stats_suspension'] = null;
		$start['stats_red'] = null;
		array_unshift($rows, $start);

		$end['season'] = $rows[0]['season'];
		$end['spielIdHvw'] = $rows[0]['spielIdHvw'];
		$end['actionIndex'] = $rows[count($rows)-1]['actionIndex']+1;
		$end['timeString'] = '60:00';
		$end['time'] = 3600;
		$end['scoreHome'] = $rows[count($rows)-1]['scoreHome'];
		$end['scoreAway'] = $rows[count($rows)-1]['scoreAway'];
		$end['scoreDiff'] = $rows[count($rows)-1]['scoreDiff'];
		$end['scoreChange'] = 1;
		$end['text'] = 'Spielende';
		$end['number'] = null;
		$end['name'] = null;
		$end['alias'] = null;
		$end['team'] = 0;
		$end['category'] = 'game';
		$end['stats_goals'] = null;
		$end['stats_yellow'] = null;
		$end['stats_suspension'] = null;
		$end['stats_red'] = null;
		array_push($rows, $end);

		return $rows;
	}

	private function removeActionExtraText($string) {
		$string = str_replace('"', '', $string);
		$string = str_replace('7m,', '7m -', $string);
		//remove header
		$pattern = "|(Zeit,Spielzeit,Spielstand,Aktion\r?\n?)|";
		$replacement = '';
		$goalsCsv = preg_replace($pattern, '', $string);

		return $goalsCsv;
	}

	private function getActionValues($rows) {
		//parse the items in rows
		$values = [];
		for($i = 0; $i < count($rows); $i++) {
			$value = self::formatActionValues($rows[$i], $i+1);
			
			$values[$i] = $value;
			$values[$i]['stats_goals'] = self::getStatisticsGoal($values, $value['alias']);
			$values[$i]['stats_yellow'] = self::getStatistics($values, $value['alias'], 'yellow');
			$values[$i]['stats_suspension'] = self::getStatistics($values, $value['alias'], 'suspension');
			$values[$i]['stats_red'] = self::getStatistics($values, $value['alias'], 'red');

		}
		return $values;
	}

	private function formatActionValues($row, $i) {
		//echo __FUNCTION__.'<pre>';print_r($row); echo'</pre>';
		$value = array();
		$value['season'] = $this->season;
		$value['spielIdHvw'] = $this->gameId;
		$value['actionIndex'] = $i;
		//$value['clockTime'] = $row[0];
		$value['timeString'] = $row[1];
		$value['time'] = self::parseTime($row[1]);

		$value['scoreHome'] = null;
		$value['scoreAway'] = null;
		$value['scoreDiff'] = null; 
		$score = explode(":", $row[2]);
		if (count($score) > 1) {
			$value['scoreHome'] = $score[0];
			$value['scoreAway'] = $score[1];
			$value['scoreDiff'] = $score[1]-$score[0]; 
		}
		$value['scoreChange'] = self::getScoreChange($row[3]);

        $currAction = self::getAction($row[3]); 
        $value['text'] = $currAction->text;
        $value['number'] = $currAction->nr;
        $value['name'] = $currAction->name;
        $value['alias'] = $currAction->alias;
        $value['team'] = $currAction->team;
        $value['category'] = $currAction->category;

	    return $value;
	}

	private function getStatisticsGoal($array, $alias) {
		$tally = 0;

	    for ($i = 0; $i < count($array); $i++) {
	        if (strcmp($array[$i]['alias'],$alias) === 0 && $array[$i]['scoreChange'] ) {
	        	$tally++;
	        }
	    }
	    return $tally;
	}

	private function getStatistics($array, $alias, $category) {
		$tally = 0;

	    for ($i = 0; $i < count($array); $i++) {
	        if (strcmp($array[$i]['alias'],$alias) === 0 && strcmp($array[$i]['category'],$category)=== 0) {
	        	$tally++;
	        	//echo __FILE__.' - line '.__LINE__.'<pre>'.$array[$i]['alias'].'-'.$alias.' - '.$array[$i]['category'].'-'.$category.' -> '.$tally.'</pre>';
	        }
	    }
	    return $tally;
	}

	// Parse the time
	private function parseTime($string) {

	    $timeComponents = explode(':', $string);
	    $minutes = (int) $timeComponents[0];
	    $seconds = (int) $timeComponents[1];
	    return $minutes * 60 + $seconds;
	}

	private function getAction($text) {
	    //"2-min Strafe für Lucas Herre (10)"
	    //"Tor durch Andreas Hipp (4)
	    //"7m-Tor durch Phillip Koch (2)"
	    //"Auszeit HSG Fridingen/Mühlheim 2"
	    //"7m - KEIN Tor durch Andreas Hipp (4)"
	    //echo __FILE__.' - line '.__LINE__.'<pre>';print_r($text);echo '</pre>';
	    $action = new stdClass();
	    $action->text = $text;

	    $action->name = null;
	    $pattern = '/.*(durch|für) (.* .*) \(\d{1,2}\)$/';
	    if (preg_match($pattern, $text)) {
	        $action->name = preg_replace($pattern, '$2', $text);
	    }

	    $action->nr = null;
	   	$pattern = '/.* \((\d{1,2})\)$/';
	    if (preg_match($pattern, $text)) {
	        $action->nr = preg_replace($pattern, '$1', $text);
	    }
	    //echo __FILE__.' - line '.__LINE__.'<pre>';print_r($action->name);echo '</pre>';
	    $action->alias = self::getAlias($action->name);
	    //echo __FILE__.' - line '.__LINE__.'<pre>';print_r($action->alias);echo '</pre>';
	    $action->team = self::getTeam($action->alias);
	    $action->category = self::getCategory($text);

	    return $action;
	}


	private function getCategory($input) {
	    //"2-min Strafe für Lucas Herre (10)"
	    //"Tor durch Andreas Hipp (4)
	    //"7m-Tor durch Phillip Koch (2)"
	    //"Auszeit HSG Fridingen/Mühlheim 2"
	    //"7m - KEIN Tor durch Andreas Hipp (4)"
	    $category = '';
	    if (strpos($input, "Verwarnung") === 0) {
	        $category = 'yellow';
	    } elseif (strpos($input, "7m") === 0) {
	        $category = 'penalty';
	    } elseif (strpos($input, "2-min") === 0) {
	        $category = 'suspension';
	    } elseif (strpos($input, "Auszeit") === 0) {
	        $category = 'timeout';
	    } elseif (strpos($input, "Tor") === 0) {
	        $category = 'goal';
	    }
	    return $category;
	}

	private function getScoreChange($input) {
	    //"2-min Strafe für Lucas Herre (10)"
	    //"Tor durch Andreas Hipp (4)
	    //"7m-Tor durch Phillip Koch (2)"
	    //"Auszeit HSG Fridingen/Mühlheim 2"
	    //"7m - KEIN Tor durch Andreas Hipp (4)"
	    $change = 0;
	    if (strpos($input, "Tor") === 0) {
	        $change = 1;
	    } elseif (strpos($input, "Tor") === 3) {
	        $change = 1;
	    }
	    return $change;
	}


	private function getTeam($alias) {
	    
	    if ($alias == null) {
	    	return 0;
	    }
	    if (in_array($alias, $this->homeAliases)) {
	    	return -1;
	    }
	    return 1;
	}

	private function getGoalArray($string)
	{
		//parse the rows
		$string = self::removeGoalExtraText(trim($string));
		if (!empty($string)) {
			$rows = str_getcsv($string, "\n"); 
			//parse the items in rows
			foreach($rows as &$row) {
				$row = str_getcsv($row, ",", '"',"\\" );
			}
		}
		$rows = self::getGoalValues($rows);
		return $rows;
	}

	private function removeGoalExtraText($string) {
		$string = str_replace('"', '', $string);
		//remove header
		$pattern = "|(Nr.,Name,Jahrgang,M,R,Tore\r?\n?\(ges\),7m/\r?\n?Tore,Verw.,Hinausstellungen,,?,?Disq.,Ber.,Team-\r?\n?Zstr.\r?\n?,?,?\r?\n?,,,,,,,,1.,2.,3.,,,\r?\n?)|";
		$replacement = '';
		$goalsCsv = preg_replace($pattern, '', $string);

		//echo __FUNCTION__.' without Header <pre>';print_r($goalsCsv); echo'</pre>';
		return $goalsCsv;
	}

	private function getGoalValues($rows) {
		//parse the items in rows
		foreach($rows as &$row) {
			$value = self::formatGoalValues($row);
			
			if (!empty($value['alias'])) {
				$values[] = $value;
			}
		}
		//echo __FUNCTION__.'<pre>';print_r($rows); echo'</pre>';
		//echo __FUNCTION__.'<pre>';print_r($values); echo'</pre>';
		return $values;
	}

	private function formatGoalValues($row) {
		//echo __FUNCTION__.'<pre>';print_r($row); echo'</pre>';
		$value = array();
		$value['spielIdHvw'] = $this->gameId;
		$value['name'] = $row[1];
		$alias = self::getAlias($row[1]);
		$value['alias'] = $alias;
		//$birthday = $row[2];
		$value['saison'] = $this->season;
		$value['kuerzel'] = $this->teamkey;
		$value['trikotNr'] = $row[0];
		$value['tw'] = self::getGoalkeeper($alias);
		$value['tore'] = ($row[5] != '') ? $row[5] : 0;
		$value['7m'] = self::get7m($row[6]);
		$value['tore7m'] = self::get7mGoals($row[6]);
		$value['gelb'] = $row[7];
		$value['2min1'] = $row[8];
		$value['2min2'] = $row[9];
		$value['2min3'] = $row[10];
		$value['rot'] = $row[11];
		$value['bemerkung'] = $row[12];
		$value['teamZstr'] = $row[13];
		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
	}

	private function getAlias($name) {	
		// TODO: Look-up in DB instead
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($name);echo '</pre>';
		$search	 = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", " ");
		$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "-");
		
		$alias = strtolower(str_replace($search, $replace, $name));
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($alias);echo '</pre>';
		return $alias;
	}
			
	private function get7m($input) {	
		$penalties = explode('/', $input);
		if (empty($penalties[0])) {
			return 0;
		} else {
			return $penalties[0];
		}
	}
	
	private function get7mGoals($input) {	
		$penalties = explode('/', $input);
		if (count($penalties) == 1) {
			return 0;
		} else {
			return $penalties[1];
		}
	}
	
	private function getGoalkeeper($alias) {	
		if (array_key_exists($alias , $this->goalkeeper)) {
			$goalie = $this->goalkeeper[$alias];
		} else {
			$goalie = 0;
		}
		return $goalie;
	}


	public function getMessage()
	{
		return $this->message;
	}

	private function setMessage($info)
	{	
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($info);echo '</pre>';
	
		$this->message = 'Spielbericht vom Spiel '.$info['teamHome'].' - '.$info['teamAway'].' ('.$info['leagueKey'].', '.$info['gameId'].') am '.$info['date'].' importiert';
	
	}

	public function addReportToDB($dataString) 
	{
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($dataString);echo '</pre>';
		$data = unserialize($dataString);
		//secho __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';

		self::setGameInfo($data['gameInfo']['gameId']);

		if ($data['gameInfo']['homeGame']) {
			$goals = self::addGoalsToDB($data['goalsHome']);
		} else {
			$goals = self::addGoalsToDB($data['goalsAway']);
		}

		$actions = self::addActionsToDB($data['action']);

		if ($goals && $actions) {
			self::setMessage($data['gameInfo']);
		}
		return true;
	}

	private function addActionsToDB ($input, $update = true){
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($input);echo'</pre>';
		
		if ($update) {
			self::cleanActionsDB();

			//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($input);echo '</pre>';
			self::addActions($input);
		}
		
		return true;
	}

	public function cleanActionsDB() {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete('hb_spielbericht_details');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		// echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->execute();
		// echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
	}


	private function formatGoalValueForDB($row) {
		//echo __FUNCTION__.'<pre>';print_r($row); echo'</pre>';
		$value = array();
		$value['season'] = (empty($row['season'])) ? 'NULL' : "'".$row['season']."'";
		$value['spielIdHvw'] = (empty($row['spielIdHvw'])) ? 'NULL' : $row['spielIdHvw'];
		$value['actionIndex'] = (empty($row['actionIndex'])) ? 'NULL' : $row['actionIndex'];
		$value['timeString'] = (empty($row['timeString'])) ? 'NULL' : "'".$row['timeString']."'";
		$value['time'] = (empty($row['time']) && $row['time'] !== 0) ? 'NULL' : $row['time'];
		$value['scoreHome'] = (empty($row['scoreHome']) && $row['scoreHome'] !== 0) ? 'NULL' : $row['scoreHome'];
		$value['scoreAway'] = (empty($row['scoreAway']) && $row['scoreAway'] !== 0) ? 'NULL' : $row['scoreAway'];
		$value['scoreDiff'] = (empty($row['scoreDiff']) && $row['scoreDiff'] !== 0) ? 'NULL' : $row['scoreDiff'];
		$value['scoreChange'] = (empty($row['scoreChange'])) ? 'NULL' : $row['scoreChange'];
        $value['text'] = (empty($row['text'])) ? 'NULL' : "'".$row['text']."'";
        $value['number'] = (empty($row['number'])) ? 'NULL' : $row['number'];
        $value['name'] = (empty($row['name'])) ? 'NULL' : "'".$row['name']."'";
        $value['alias'] = (empty($row['alias'])) ? 'NULL' : "'".$row['alias']."'";
        $value['team'] = (empty($row['team'])) ? 'NULL' : $row['team'];
        $value['category'] = (empty($row['category'])) ? 'NULL' : "'".$row['category']."'";
        $value['stats_goals'] = (empty($row['stats_goals'])) ? 'NULL' : $row['stats_goals'];
		$value['stats_yellow'] = (empty($row['stats_yellow'])) ? 'NULL' : $row['stats_yellow'];
		$value['stats_suspension'] = (empty($row['stats_suspension'])) ? 'NULL' : $row['stats_suspension'];
		$value['stats_red'] = (empty($row['stats_red'])) ? 'NULL' : $row['stats_red'];
		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
	}

	private function addActions($inputData)
	{
		if (!empty($inputData)) {
			//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($inputData);echo '</pre>';

			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$columns = array('season', 'spielIdHvw', 'actionIndex', 'timeString', 'time', 'scoreHome', 'scoreAway', 'scoreDiff', 'scoreChange', 'text', 'number', 'name', 'alias', 'team', 'category', 'stats_goals', 'stats_yellow', 'stats_suspension', 'stats_red');

			foreach($inputData as $row) {
				$values[] = implode(', ', self::formatGoalValueForDB($row));
			}

			// Prepare the insert query.
			$query
					->insert($db->qn('hb_spielbericht_details')) 
					->columns($db->qn($columns))
					->values($values);

			//echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->execute();
			return $result;
		}
		return false;
	}

// 	public function checkGame($gameId) {
// 		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gameId);echo'</pre>';
// 		$db = $this->getDbo();
		
// 		$query = $db->getQuery(true);
// 		//$query->select('*');
// 		$query->select('COUNT(spielIdHvw)');
// 		$query->from('hb_spiel_spieler');
// 		$query->where($db->qn('spielIdHvw').' = '.$db->q($gameId));
// 		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
// 		$db->setQuery($query);
// 		$info = $db->loadResult();
// 		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($info);echo'</pre>';
// 		return (boolean) $info;
// 	}
	
	private function addGoalsToDB ($input, $update = true){
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($input);echo'</pre>';
		
		if ($update) {
			self::cleanGoalsDB();
			//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($input);echo '</pre>';
			self::addGoals($input);
		}
		
		return true;
	}
	
	public function cleanGoalsDB() {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete('hb_spiel_spieler');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->execute();
		//echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
	}
	
	private function addGoals($inputData)
	{
		if (!empty($inputData)) {
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->inputData);echo'</pre>';
			$table = JTable::getInstance('Goals','HbmanagerTable');
//			$tablekey = $table->getKeyName(true);
//			echo __FUNCTION__.__LINE__.'<pre>';print_r($tablekey); echo'</pre>';

			foreach ($inputData as $value) {
				$table->bind($value);
				$table->store();
			}
		}
		self::addMissingPlayers($inputData);
	}


	
// 	public function getInputData() {
// 		return $this->inputData;
// 	}
	
	

	
// 	public function getConfirmation() {	
// 		$db = $this->getDbo();
// 		$query = $db->getQuery(true);
// 		$query->select('*');
// 		$query->from('hb_spiel_spieler');
// 		$query->leftJoin($db->qn('#__contact_details').' USING ('.
// 				$db->qn('alias').')');
// 		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
// 		$query->where($db->qn('saison').' = '.$db->q($this->season));
// 		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
// 		$query->order('('.$db->qn('trikotNr').'*1 = 0) ,'.$db->qn('trikotNr').'*1, '.$db->qn('trikotNr') );
// 		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
// 		$db->setQuery($query);
// 		$info = $db->loadAssocList();
// 		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
// 		return $info;
// 	}
	
	public function getLinks() {
		//$games = new HblibGame(''); // TODO what does this?

		$links = self::getGames('all');
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($links);echo '</pre>';
		
		$files = self::getAvailableImportFiles();
		foreach ($links as &$game) {
			$game->file = (in_array($game->spielIdHvw, $files));
		}

		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($links);echo'</pre>';
		return $links;
	}

	private function getGames($option = null)
	{
		if ($option === null) {
			$where = "AND spielIdHvw NOT IN (SELECT DISTINCT spielIdHvw FROM hb_spiel_spieler)";	
		} elseif ($option === "all") {
			$where = ' ';
		}


		$db = $this->_db;
		$query = $db->getQuery(true);
		$query = "SELECT *
				FROM hb_spiel	
				LEFT JOIN hb_mannschaft USING (kuerzel)
				WHERE datumZeit < NOW()
				AND hb_spiel.ligaKuerzel REGEXP '^(M|F)-.*$'
				AND jugend = 'aktiv'
				AND eigenerVerein = 1"
				.$where.
				"ORDER BY datumZeit";
		$db->setQuery($query);
		//$teams = $db->loadColumn();
		$teams = $db->loadObjectList();
//		echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
//		echo __FILE__.' ('.__LINE__.')<pre>';print_r($teams);echo'</pre>';
		return $teams;
	}
	
	private function addMissingPlayers($data) {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT alias');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.
				$db->qn('alias').')');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('name').' IS NULL');
		$query->order('('.$db->qn('trikotNr').'*1 = 0) ,'.$db->qn('trikotNr').'*1, '.$db->qn('trikotNr') );
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$missing = $db->loadColumn();
		//echo __FUNCTION__.'<pre>';print_r($missing); echo'</pre>';
		if (count($missing) > 0)  {
			self::addPlayersToDB($missing, $data);
		}
	}
	
	function addPlayersToDB($missing, $data)
	{
		$values = self::getMissingPlayerValues($missing, $data);
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($values);echo'</pre>';
		$table = JTable::getInstance('Contacts','HbmanagerTable');
//			$tablekey = $table->getKeyName(true);
//			echo __FUNCTION__.__LINE__.'<pre>';print_r($tablekey); echo'</pre>';
			foreach ($values as $key => $value) {
				//echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';
				//$table->reset();
				$table->bind($value);
				$table->store();
			}
	}
	
	private function getMissingPlayerValues($missing, $data) {
		$names = self::getMissingPlayerNames($data);
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($names);echo'</pre>';
		
		foreach ($missing as $key => $player) {
			$values[$key]['id'] = ''; // needed, otherwise only 1 entry gets stored in 
			$values[$key]['alias'] = $player;
			$values[$key]['name'] = $names[$player];
		}
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($values);echo'</pre>';
		return $values;
	}
	
	private function getMissingPlayerNames($data) {
		echo __FILE__.' ('.__LINE__.')<pre>';print_r($data);echo'</pre>';
		foreach ($data as $row) {
			if (!empty($row['alias'])) {
				$players[$row['alias']] = $row['name'];
			}
		}
		echo __FILE__.' ('.__LINE__.')<pre>';print_r($players);echo'</pre>';
		return $players;
	}
}