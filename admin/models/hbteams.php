<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbteams extends JModelLegacy
{	
	
	private $keys = array();
	
	function __construct() 
	{
		parent::__construct();
		
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
					$db->qn('reihenfolge').' ASC');
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		if (empty($teams)) {
			$teams = self::getEmptyTeam();
		}
		//echo __FUNCTION__.'<pre>';print_r($teams); echo'</pre>';
		return $teams;
	}
	
	protected function getEmptyTeam()
	{
		$teams = array();
		$teams[0] = new stdClass();
		$teams[0]->kuerzel = '';
		$teams[0]->reihenfolge = '';
		$teams[0]->mannschaft = '';
		$teams[0]->name = '';
		$teams[0]->nameKurz = '';
		$teams[0]->ligaKuerzel = '';
		$teams[0]->liga = '';
		$teams[0]->geschlecht = '';
		$teams[0]->jugend = '';
		$teams[0]->hvwLink = '';
		$teams[0]->update = '';
		return $teams;
	}
	
	function getLeagues()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_staffel');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$leagues = $db->loadObjectList();
		//echo __FUNCTION__.'<pre>';print_r($leagues); echo'</pre>';
		$leagues = self::formatLeagues($leagues);
		return $leagues;
	}
	
	protected function formatLeagues($leagues)
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$clubindicator = $params->get( 'clubindicator' );
		//echo __FILE__.'<pre>';print_r($params); echo'</pre>';
		
		foreach ($leagues as $key => $league)
		{
			//echo __FUNCTION__.'<pre>';print_r($league); echo'</pre>';
			foreach ($league as $field => $value)
			{
				//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
				if (strpos($field, 'mannschaften') !== FALSE)
				{
					$leagues[$key]->{$field} = unserialize($value);
					$leagues[$key]->select[$field] = false;
					foreach ($leagues[$key]->{$field} as $select => $name)
					{
						if (strpos($name, $clubindicator) !== FALSE) {
							$leagues[$key]->select[$field] = $select;
						}							
					}
				}
			}
		}
		//echo __FUNCTION__.'<pre>';print_r($leagues); echo'</pre>';
		return $leagues;
	}
	
	
	function updateLeagues()
	{
		$time_pre = microtime(true);
		
		//self::getAddress();
		$leagueSource = self::getSource(self::getAddress());
		//echo __FUNCTION__.'<pre>';print_r($leagueSource); echo'</pre>';
		$leagues = self::formatSource($leagueSource);
		//self::storeVar($leagues);
		//$leagues = self::retrieveVar();
		//echo __FUNCTION__.'<pre>';print_r($leagues); echo'</pre>';
		
		$time_post = microtime(true);
		$exec_time = $time_post - $time_pre;
		//echo __FUNCTION__.'<pre>time: ';print_r($exec_time); echo'</pre>';
		
		self::updateLeaguesInDb($leagues);
		
		return $result = false;
	}
	
	protected function storeVar($leagues) 
	{
		$var_str = var_export($leagues, true);
		$var = "<?php\n\n\$leagues = $var_str;\n\n?>";
		file_put_contents('C:\xampp\htdocs\test\leagues.php', $var);
	}
	
	protected function retrieveVar() 
	{
		include 'C:\xampp\htdocs\test\leagues.php';
		return $leagues;
	}
		
	protected function getAddress() 
	{
//		$year = strftime('%Y');
//		if (strftime('%m') < 8) {
//			$year = $year-1;
//		}
//		$address = 'http://www.hvw-online.org/index.php'.
//				'?id=39&orgID=11&A=g_org&nm=0&do='.
//				$year.'-10-01';
		
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$url = $params->get( 'urlhvw' );
		$urlstartdate = $params->get( 'urlstartdate' ); 
		$urlyear = $params->get( 'urlyear' );
		//echo __FILE__.'<pre>';print_r($params); echo'</pre>';
		
		$address = 'http://www.hvw-online.org/index.php'.
				'?id=39&orgID=11&A=g_org&nm=0&do='.
				$urlstartdate;
		
		
		// local, for testing
		//$address = 'http://localhost/test/hvw-zollern.htm';
		return $address;
	}

	protected function getSource($address)
	{
		// returns sourcecode of a website with the address $address as string
		$source = file_get_contents($address);
		$leagueSource = null;
		// shortens strings to relevant part
		$start = strpos($source,'<th align="center">Bem.</th>')+28;
		$end = strpos($source,'</table>',$start);
		$source = substr($source,$start,($end-$start));
		//echo __FILE__.'<pre>';print_r($source); echo'</pre>';
		
		$source = str_replace('<td class="gal"><a href="', '&&',$source);
		//echo __FILE__.'<pre>';print_r($source); echo'</pre>';
		// TODO check out regex possessive quantifier (-> performance)
		$pattern = '|&&+'.
				'(?P<url>\?A=g_class&id=\d{1,2}&orgID=\d{1,2}&score=\d{4,6})">'.
				'(?P<league>[\w\d\-\/\+]{3,10})<\/a>|';
		preg_match_all($pattern, $source, $leagueSource, PREG_SET_ORDER);
		//echo __FILE__.'<pre>';print_r($leagueSource); echo'</pre>';

		return $leagueSource;
	}
	
	protected function formatSource($source)
	{
		$leagues = array();
		foreach ($source as $key => $value) {
			//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
			
			$league = $value['league'];
			$url = 'http://www.hvw-online.org/'.$value['url'].'&all=1';
			$gender = self::getGender($league);
			$age = self::getAge($league);
			$cup = self::checkIfCup($league);
			if (!$cup) {
				$data = self::getTeamData($url);
			
				$leagues[] = array_merge( array('league' => $league, 
									'url' => $url, 'gender' => $gender, 
									'age' => $age), $data);
				//return $leagues; // for single test, only first league
			}
		}
		//echo __FUNCTION__.'<pre>';print_r($leagues); echo'</pre>';
		return $leagues;
	}
	
	protected function getGender($league) {
		//echo __FUNCTION__.'<pre>';print_r($league); echo'</pre>';
		preg_match("/^(M|F|w|m|g)/", $league, $match);
		switch ($match[0]) {
			case 'M':
				$gender = 'm';
				break;
			case 'F':
				$gender = 'w';
				break;
			default:
				$gender = $match[0];
				break;
		}
		//echo __FUNCTION__.'<pre>';print_r($gender); echo'</pre>';
		return $gender;
	}
	
	
	protected function getAge($league) {
		//echo __FUNCTION__.'<pre>';print_r($league); echo'</pre>';
		preg_match("/^(w|m|g)J([A-E])/", $league, $match);
		if (isset($match[2])) {
			$age = $match[2];
		}
		else {
			$age = 'aktiv';
		}
		//echo __FUNCTION__.'<pre>';print_r($age); echo'</pre>';
		return $age;
	}
	
	protected function checkIfCup($league) {
		//echo __FUNCTION__.'<pre>';print_r($league); echo'</pre>';
		if (preg_match("/Pok/", $league)) {
			return true;
		}
		return false;
	}
	
	protected function getTeamData($url) {
		$source = self::getTeamSource($url);
		//echo __FUNCTION__.'<pre>';print_r($source); echo'</pre>';
		
		// Name and Saison
		$pattern = "/(?P<name>.*) - Hallenrunde ". 
					"(?P<season>20\d{2}\/20\d{2})<\/h1>/";
		preg_match($pattern, $source['title'], $match);
		//echo __FUNCTION__.'<pre>';print_r($match); echo'</pre>';
		$data['name'] = $match['name'];
		$data['season'] = $match['season'];
		
		// Standings team names
		$pattern = "/(<td class=\"gac\">)+(<b>\d{1,2}<\/b>)?".
					"<\/td>\s+".
					"<td>.+<\/td>\s+".
					"<td>(.+)<\/td>/";
		preg_match_all($pattern, $source['standings'], $standings, 
				PREG_SET_ORDER);
		unset($source['standings']);
		foreach ($standings as $key => $value) {
			$standings[$key] = $value[3];
		}
		sort($standings);
		//echo __FUNCTION__.'<pre>';print_r($standings); echo'</pre>';
		$data['standings'] = $standings;
		
		// Schedule team names
		$pattern = "/<\/td><td class=\"gal\">(.+)<\/td><td>-<\/td>/";
		preg_match_all($pattern, $source['schedule'], $schedule, 
				PREG_SET_ORDER);
		unset($source);
		foreach ($schedule as $key => $value) {
			$schedule[$key] = $value[1];
		}
		$schedule = array_unique($schedule);
		sort($schedule);
		//echo __FUNCTION__.'<pre>';print_r($schedule); echo'</pre>';
		$data['schedule'] = $schedule;
		
		//echo __FUNCTION__.'<pre>';print_r($data); echo'</pre>';
		return $data;
	}
	
	protected function getTeamSource($url) {
		$source = file_get_contents($url);
		//echo __FUNCTION__.'<pre>';print_r($source); echo'</pre>';
		
		// Title
		$start1 = strpos($source,'<h1>Neckar-Zollern<br>')+22;
		$end1 = strpos($source,'<!-- * ScoreTable() * Start ****',$start1);
		$title = substr($source,$start1,($end1-$start1));
		//echo __FUNCTION__.'<pre>';print_r($title); echo'</pre>';
		
		// Standings
		$start2 = strpos($source,'<td class="gac"><b>1</b></td>');
		$end2 = stripos($source,'</tr></TABLE>',$start2);
		$standings = substr($source,$start2,($end2-$start2));
		//echo __FUNCTION__.'<pre>';print_r($standings); echo'</pre>';

		// Schedule
		$start3 = strpos($source,'<th align="center">Bem.</th>')+28;
		$end3 = strpos($source,'</table>',$start3);
		$schedule = substr($source,$start3,($end3-$start3));
		//echo __FUNCTION__.'<pre>';print_r($schedule); echo'</pre>';
		
		return array('title' => $title, 'standings' => $standings,
			'schedule' => $schedule);
	}
	
	protected function updateLeaguesInDb($leagues)
    {
		$table = 'hb_staffel';
		$columns = array('staffel', 'staffelName', 'url', 
						'geschlecht', 'jugend', 'saison', 
						'mannschaftenTabelle', 'mannschaftenSpielplan');
		
		$values = null;
		foreach($leagues as $row) {
			$values[] = implode(', ', 
					self::formatLeagueValues($row));
		}
		
		$result = self::updateDb($table, $columns, $values);
		//echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
		return $result;
    }
	
	
	protected function formatLeagueValues($data)
    {
		//echo __FUNCTION__.'<pre>';print_r($data); echo'</pre>';
		$db = $this->getDbo();
		
		$value['staffel'] = $db->q($data['league']);
		$value['staffelName'] = $db->q($data['name']);
		$value['url'] = $db->q($data['url']);
		$value['geschlecht'] = $db->q($data['gender']);
		$value['jugend'] = $db->q($data['age']);
		$value['saison'] = $db->q($data['season']);
		$value['mannschaftenTabelle'] = $db->q(serialize($data['standings']));
		$value['mannschaftenSpielplan'] = $db->q(serialize($data['schedule']));
	
		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
    }
	
	
	protected function updateDb($table, $columns, $values)
	{
//		echo __FUNCTION__.'<pre>';print_r($columns); echo'</pre>';
//		echo __FUNCTION__.'<pre>';print_r($values); echo'</pre>';
		$db = $this->getDbo();
		
		// delete existing data
		$db->truncateTable($table);
		
		// Prepare the insert query.
		$query = $db->getQuery(true);
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);
		//echo __FUNCTION__.'<pre>';print_r($query); echo'</pre>';
		$db->setQuery($query);
		$result = $db->execute();
		//echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
		return $result;
    }
	
	function getOptions($input, $teams, $select)
	{
		$options = '';
		foreach ($teams as $key => $name){
			$options .= '<option ';
			if ($key == $select) {
				$options .= 'selected="selected" ';
			}
			$options .= 'value="'.$name.'">'.$name.'</option>'."\n";
		}
		$input = str_replace('<option value="leer">auswaehlen</option>',
					$options, $input);
		return $input;
	}
	
	//  UPDATE TEAMS
	function updateTeams($teams) 
	{
		$table = 'hb_mannschaft';
		$columns = array('kuerzel', 'reihenfolge', 'mannschaft', 'name', 
					'nameKurz', 'ligaKuerzel', 'liga', 'geschlecht', 
					'jugend', 'hvwLink');
		
		$values = null;
		foreach($teams as $row) {
			$values[] = implode(', ', 
					self::formatTeamValues($row));
		}
		//echo __FUNCTION__.'<pre>';print_r($columns); echo'</pre>';
		//echo __FUNCTION__.'<pre>';print_r($values); echo'</pre>';
		$db = $this->getDbo();
		
		// Prepare the insert query.
		$query = $db->getQuery(true);
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$query .= "\nON DUPLICATE KEY UPDATE \n";
		$dublicates = array();
		foreach ($columns as $field) {
			$dublicates[] = $db->qn($field).' = VALUES('.$db->qn($field).')';
		}
		$query .= implode(",\n", $dublicates);
		
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}
	
	protected function formatTeamValues($data)
    {
		//echo __FUNCTION__.'<pre>';print_r($data); echo'</pre>';
		$db = $this->getDbo();
		
		$value['kuerzel'] = $db->q($data['kuerzel']);
		$value['reihenfolge'] = $db->q($data['reihenfolge']);
		$value['mannschaft'] = $db->q($data['mannschaft']);
		$value['name'] = $db->q($data['name']);
		$value['nameKurz'] = $db->q($data['nameKurz']);
		$value['ligaKuerzel'] = $db->q($data['ligaKuerzel']);
		$value['liga'] = $db->q($data['liga']);
		$value['geschlecht'] = $db->q($data['geschlecht']);
		$value['jugend'] = $db->q($data['jugend']);
		if (!empty($data['hvwLink'])) {
			$value['hvwLink'] = $db->q('http://www.hvw-online.org/'.
					$data['hvwLink']);
		}
		else {
			$value['hvwLink'] = 'NULL';
		}

		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
    }
	
	
	// DELETE TEAMS
	
	function deleteTeams($teams)
	{
		//echo __FUNCTION__.'<pre>';print_r($teams); echo'</pre>';
		
		$db = $this->getDbo();
		
		foreach ($teams as $team) {
			if (isset($team['deleteTeam'])) {
				$where[] = $db->q($team['kuerzel']);
			}	
		}
		$where = implode(', ', $where);
		$where = $db->qn('kuerzel') . ' IN (' . $where . ')';
		
		$query = $db->getQuery(true);
		$query->delete('hb_mannschaft');
		$query->where($where);
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
			$result = false;
		}
		
		return $result;
	}
	
	// ADD NEW TEAMS
	function addNewTeams($newTeams) {
		//echo __FUNCTION__.'<pre>';print_r($newTeams); echo'</pre>';
		$db = $this->getDbo();
		$this->keys = self::getTeamkeys();
		foreach ($newTeams as $data)
		{
			if (isset($data['includeTeam'])) {
				$teamkey = self::getNewTeamKey($data['staffel']);
				$age = self::getNewAge($data['staffelName']);
				$gender = self::getNewGender($data['staffelName']);
				$number = self::getNewNumber($data['mannschaftenTabelle']);
				$league = self::getNewLeague($data['staffelName']);
				$teamName = self::getNewTeamName($age, $gender, $number);
				//echo __FUNCTION__.'<pre>';print_r($teamName); echo'</pre>';
				$value['kuerzel'] = $db->q($teamkey);
				$value['reihenfolge'] = 'null';
				$value['mannschaft'] = $db->q($teamName);
				$value['name'] = $db->q($data['mannschaftenTabelle']);
				$value['nameKurz'] = $db->q($data['mannschaftenSpielplan']);
				$value['ligaKuerzel'] = $db->q($data['staffel']);
				$value['liga'] = $db->q($league);
				$value['geschlecht'] = $db->q($gender);
				$value['jugend'] = $db->q($age);
				$value['hvwLink'] = $db->q($data['url']);
				$values[] = implode(', ', $value);
			}
		}
		$table = 'hb_mannschaft';
		$columns = array('kuerzel', 'reihenfolge', 'mannschaft', 'name', 
					'nameKurz', 'ligaKuerzel', 'liga', 'geschlecht', 
					'jugend', 'hvwLink');
		
		// Prepare the insert query.
		$query = $db->getQuery(true);
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}
	
	protected function getTeamkeys() 
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('hb_mannschaft');
		$query->order('kuerzel ASC');
		$db->setQuery($query);
		$keys = $db->loadColumn();
		//echo __FUNCTION__.'<pre>';print_r($keys); echo'</pre>';
		return $keys;
	}
	
	
	protected function getNewTeamkey($data) 
	{
		//echo __FUNCTION__.'<pre>';print_r($data); echo'</pre>';
		// kuerzel
		$pattern =  "/^(?P<key>((m|w)J[A-D])|(M|F)(\d\d)?|(gJ(E|F)))(-[A-Z]{2,3}|[\d\+\/]{1,5})?/";
		preg_match($pattern, $data, $match);
		//echo __FUNCTION__.'<pre>';print_r($match); echo'</pre>';
		$team = $match['key'];
		
		$i = 1;		
		do {
			$teamkey = $team.'-'.$i++;
		} while (in_array($teamkey, $this->keys));
		$this->keys[] = $teamkey;
		//echo __FUNCTION__.'<pre>';print_r($teamkey); echo'</pre>';
		return $teamkey;
	}
	
	protected function getNewGender($data) 
	{
		if ( strpos($data,'männl') !== false || 
			strpos($data,'Männer') !== false ) {
			$gender = 'm';
		}
		elseif ( strpos($data,'weibl') !== false || 
			strpos($data,'Frauen') !== false ) {
			$gender = 'w';
		}
		elseif ( strpos($data,'gemischt') !== false ) {
			$gender = 'g';
		}
		else {
			$gender = '';
		}
		//echo __FUNCTION__.'<pre>';print_r($gender); echo'</pre>';
		return $gender;
	}
	
	protected function getNewAge($data) 
	{
		$age = preg_replace('/.*Jugend ([A-F]).*/',
				"$1", $data);
		if ($age === $data) {
			$age = 'aktiv';
		}
		//echo __FUNCTION__.'<pre>';print_r($age); echo'</pre>';
		return $age;
	}
	
	protected function getNewLeague($data) 
	{
		//echo __FUNCTION__.'<pre>';print_r($data); echo'</pre>';
		$pattern = "/(Jugend [A-F]|Männer|Frauen) (?P<league>[\w\d\+ ]*)/";
		preg_match($pattern, $data, $match);
		//echo __FUNCTION__.'<pre>';print_r($match); echo'</pre>';
		$league = $match['league'];
		//echo __FUNCTION__.'<pre>';print_r($league); echo'</pre>';
		return $league;
	}
	
	protected function getNewNumber($data) 
	{
		$number = null;
		if (preg_match('/.*( [1-9])$/', $data, $matches)) {
			$number = $matches[1];
		}
		//echo __FUNCTION__.'<pre>';print_r($number); echo'</pre>';
		return $number;
	}
	
	protected function getNewTeamName($age, $gender, $number) {
		if ($age === 'aktiv') {
			if ($gender === 'm') {
				$name = 'Männer';
			}
			elseif ($gender === 'w') {
				$name = 'Frauen';
			}
		}
		else {
			$name = $age.'-Jugend';
			if ($gender === 'm') {
				$name .= ' männlich';
			}
			elseif ($gender === 'w') {
				$name .= ' weiblich';
			}
			elseif ($gender === 'g') {
				$name .= ' gemischt';
			}
		}
		if ($number) {
			$name .= $number;
		}
		return $name;
	}
}