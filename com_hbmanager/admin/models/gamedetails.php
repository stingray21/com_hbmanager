<?php
/**
 * @package	 Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelGamedetails extends JModelAdmin
{
	// protected $timezone = 'Europe/Berlin';
	private $timezone = 'UTC';
	
	protected $today = null;
	protected $season = null;

	protected $table_team = '#__hb_team';
	protected $table_game = '#__hb_game';
	protected $table_gamereport = '#__hb_gamereport';
	protected $table_gamedetails = '#__hb_gamereport_details';
	protected $table_teamplayer = '#__hb_team_player';
	protected $table_gameplayer = '#__hb_game_player';
	protected $table_contact = '#__contact_details';

	protected $tz = null;
	protected $importFilePath = null;
	protected $fileGameIds = null;


	private $filenamePattern = '<date>_<ID>.txt';

	private $gameId = null;
	private $teamkey = null;
	private $homeGame = null;
	private $playerAliases = [];
	private $goalkeeper = [];

	// TODO use CONVERT_TZ in MySQL for date
	
	function __construct() 
	{
		parent::__construct();

		$this->tz = new DateTimeZone($this->timezone);

		$this->season = HbmanagerHelper::getCurrentSeason();		
		$this->today = date_create('now', $this->tz);

		$this->importFilePath = JPATH_ROOT.'/hbdata/reports/'.$this->season.'/';
		if (!is_dir($this->importFilePath)) {
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->importFilePath);echo'</pre>';
			mkdir($this->importFilePath, 0755);
		}
		$this->fileGameIds = self::getAvailableImportFiles();

	}

	// public function getTable($type = 'Team', $prefix = 'HbmanagerTable', $config = array())
	// {
	// 	return JTable::getInstance($type, $prefix, $config);
	// }

	public function getForm($data = array(), $loadData = true)
	{
		// // Get the form.
		// $form = $this->loadForm(
		// 	'com_hbmanager.team',
		// 	'team',
		// 	array(
		// 		'control' => 'jform',
		// 		'load_data' => $loadData
		// 	)
		// );

		// if (empty($form))
		// {
		// 	return false;
		// }

		// return $form;
	}

	// protected function loadFormData()
	// {
	// 	// Check the session for previously entered form data.
	// 	$data = JFactory::getApplication()->getUserState(
	// 		'com_hbmanager.edit.team.data',
	// 		array()
	// 	);

	// 	if (empty($data))
	// 	{
	// 		$data = $this->getItem();
	// 	}
	// 	// echo __FILE__.'('.__LINE__.'):<pre>';print_r($data);echo'</pre>';
	// 	return $data;
	// }
	

	public function getGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);

		$query->select('`season`, `gameIdHvw`, `teamkey`, game.`leagueKey` AS `leagueKey`, `leagueIdHvw`, `dateTime`, `home`, `away`, `goalsHome`, `goalsAway`, `reportHvwId`, `order`, `team`, `name`, `shortName`, `league`, `youth`, `update`, IF(`timeString` = \'00:00\', 1, 0) AS `imported` ');

		$query->from($this->table_game.' AS game');
		$query->leftJoin($db->qn($this->table_team).' USING ('.$db->qn('teamkey').')');
		// $query->leftJoin($db->qn($this->table_gamereport).' USING ('.$db->qn('gameIdHvw').', '.$db->qn('season').')');
		$query->leftJoin($db->qn($this->table_gamedetails).' USING ('.$db->qn('gameIdHvw').', '.$db->qn('season').')');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where('('.$db->qn('timeString').' = '.$db->q('00:00').' OR '.$db->qn('timeString').' = "" OR '.$db->qn('timeString').' IS NULL)');
		$query->where($db->qn('youth').' = '.$db->q('aktiv'));
		$query->where($db->qn('ownClub').' = 1');
		$query->where($db->qn('dateTime').' <= '.$db->q($this->today->format('Y-m-d H:i:s')));
		// $query->where($db->qn('goalsHome').' IS NOT NULL');
		$query->order($db->qn('dateTime').' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';

		$games = self::addFileInfo($games);

		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		return $games;
	}

	private function addFileInfo($games)
	{
		
		foreach ($games as &$game) {
			if (in_array($game->gameIdHvw, $this->fileGameIds)) {
				$game->importFilename = self::getFileName($game->gameIdHvw, substr($game->dateTime,0,10));
			}
		}		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		return $games;
	}

	// ------------------------------------------------------------------------
	
	public function previewGameData($gameId, $date)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gameId);echo'</pre>';
		$response = self::importGameData($gameId, $date);

		return $response;
	}	


	public function insertGameData($gameId, $date)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gameData);echo'</pre>';

		$gameData = self::importGameData($gameId, $date);

		// Goals
		$response['players'] = self::savePlayers($gameData->players);
		// Actions
		$response['actions'] = self::saveActions($gameData->actions);

		return $response;
	}

	public function importGameData($gameId, $date)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gameId);echo'</pre>';
		$response = false;

		self::setGameInfo($gameId);

		$inputData = self::importData($gameId, $date);

		$gameData = new stdClass();
		$gameData->gameId = $gameId;
		// Game Info
		$gameData->gameInfo = self::processGame($inputData->gameInfo);
		// Goals
		$playersInput = ($this->homeGame) ? $inputData->playersHome : $inputData->playersAway;
		$gameData->players = self::processPlayers($playersInput);
		// Actions
		$gameData->actions = self::processActions($inputData->action);

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($inputData);echo'</pre>';

		return $gameData;
	}	

	private function getFileName($id, $date)
	{
		$filename = str_replace('<ID>', $id, $this->filenamePattern);
		$filename = str_replace('<date>', $date, $filename);
		
		return $filename;
	}

	public function setGameInfo($gameId)
	{
		$this->gameId = $gameId;

		$db = $this->getDbo();
		
		$query = $db->getQuery(true);

		$query->select('gameIdHvw, teamkey, season, shortName, home, away');

		$query->from($db->qn($this->table_game));
		$query->leftJoin($db->qn($this->table_team).
				' USING ('.$db->qn('teamkey').')');
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		//  echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$info = $db->loadObject();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($info);echo'</pre>';die;
		$this->teamkey = $info->teamkey;
		$this->homeGame = (strcmp($info->home, $info->shortName) === 0) ? true : false; 
		$this->goalkeeper = self::setGoalkeeper($info->teamkey);
	}

	
	private function setGoalkeeper($teamkey) 
	{	
		// $goalkeeper
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('alias');
		$query->from($db->qn($this->table_teamplayer));
		// $query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('TW').' = 1');
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$goalkeeper = $db->loadColumn();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($goalkeeper);echo'</pre>';
		return $goalkeeper;
	}


	private function getAvailableImportFiles()
	{
		$ids = [];
		$files = array_filter(scandir($this->importFilePath), function($file) { 
			return strpos($file, '.txt') !== false; 
			});
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($files);echo '</pre>';
		$pattern = '/'.self::getFileName('(\d{5,6})', '(\d{4}-\d{2}-\d{2})').'/';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($pattern);echo'</pre>';
		
		foreach ($files as $file) {
			preg_match($pattern, $file, $match);
			if (count($match) > 1) { 
				$ids[] = $match[2];
				} 
		}
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($ids);echo '</pre>';
		return $ids;
	}

	public function importData($gameId, $date)
	{
		$rawText = file_get_contents($this->importFilePath.self::getFileName($gameId, $date));
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($rawText);echo '</pre';
		$pattern = '/(\r?\n){1,2}\/\/-----\w{2,10}----------(\r?\n)/';
		$divider = '<&&&>';
		$rawText = preg_replace($pattern, $divider, $rawText);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($rawText);echo'</pre>';
		$parts = explode($divider, $rawText);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($parts);echo'</pre>';

		$sections = new stdClass();
		$sections->gameInfo 	= $parts[1];
		$sections->playersHome 	= $parts[3];
		$sections->playersAway 	= $parts[4];
		$sections->action 		= $parts[5];

		return $sections;
	}

	// -----------------------------------------------------------------------

	private function getAlias($name) {	
		// TODO: Look-up in DB instead
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($name);echo '</pre>';
		
		$replace = array(
			'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
			'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
			'Þ'=>'B',
			'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
			'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
			'Ğ'=>'G',
			'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
			'Ł'=>'L',
			'Ñ'=>'N', 'Ń'=>'N',
			'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
			'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
			'Ț'=>'T',
			'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
			'Ý'=>'Y',
			'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
			'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
			'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
			'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
			'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
			'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
			'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
			'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
			'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
			'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
			'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
			'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
			'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
			'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
			'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
			'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
			'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
			'ק'=>'q',
			'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
			'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
			'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
			'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
			'в'=>'v', 'ו'=>'v', 'В'=>'v',
			'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
			'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
			'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
		);
		$alias = strtr($name, $replace);

		$search	 = array(" ");
		$replace = array("-");

		$alias = strtolower(str_replace($search, $replace, $alias));
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($alias);echo '</pre>';
		return $alias;
	}

	// Game -----------------------------------------------------------------

	private function processGame($gameString) 
	{
		$gameInfo = self::convertGameStringToArray($gameString);
		return $gameInfo;
	}

	private function convertGameStringToArray($string)
	{
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
		$game['dateUni'] = preg_replace('/(\d{2}).(\d{2}).(\d{2})/', '20$3-$2-$1',$game['date']);

		preg_match("|(.*) \((.*)\)|", $rows[2][1],$match);
		$game['gym'] = $match[1];
		$game['gymId'] = $match[2];

		preg_match("|(.*) - (.*)|", $rows[3][1],$match);
		$game['teamHome'] = $match[1];
		$game['teamAway'] = $match[2];
		
		preg_match("|(\d{1,3}:\d{1,3}) (\((\d{1,3}:\d{1,3})\))?|", $rows[4][1],$match);
		$game['result'] = $match[1];
		$game['halftime'] = (!empty($match[3])) ? $match[3] : '';

		$game['homeGame'] = $this->homeGame;

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
		return $game;
	}


	// Goals -----------------------------------------------------------------

	private function processPlayers($playersString) 
	{
		$players = self::convertPlayerStringToArray($playersString);
		$players = self::formatPlayersArray($players);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($players);echo'</pre>';
		return $players;
	}

	private function savePlayers($players) 
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($players);echo'</pre>';
		self::addPlayersInfoToDB($players);
		self::addMissingPlayers($players);
		self::setHomeAliases($players);

		return true; //TODO doublecheck if it acutally worked
	}


	private function convertPlayerStringToArray($string)
	{
		$string = str_replace('"', '', trim($string));
		//remove header
		
		$pattern = "/(Nr.,Name,Jahrgang,M,R,Tore\r?\n?\(ges\),7m\/\r?\n?Tore,Verw.,Hinausstellungen,,?,?Disq.,Ber.,(Team-\r?\n?Zstr\.\r?\n?|zus\.\r?\n?Strafe),?,?\r?\n?,,,,,,,,1.,2.,3.,,,\r?\n?)/";
		$string = preg_replace($pattern, '', $string);

		if (!empty($string)) {
			$rows = str_getcsv($string, "\n"); 
			foreach($rows as &$row) {
				$row = str_getcsv($row, ",", '"',"\\" );
			}
		}
		// filter out empty elements
		$rows = array_filter($rows, function($v) {
				return !empty($v[1]);
			});
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($rows);echo'</pre>';
		return $rows;
	}


	private function formatPlayersArray($players) {
		//parse the items in players
		$playerArray = [];

		foreach($players as $row) {
			
			$value = [];
			$value['gameIdHvw'] = $this->gameId;
			$value['playerName'] = $row[1];
			$alias = self::getAlias($row[1]);
			$value['alias'] = $alias;
			//$birthday = $row[2];
			$value['season'] = $this->season;
			$value['teamkey'] = $this->teamkey;
			$value['number'] = $row[0];
			$value['goalie'] = self::getGoalkeeper($alias);
			$value['goals'] = ($row[5] != '') ? $row[5] : 0;
			$penalty = self::getPenaltyInfo($row[6]);
			$value['penalty'] = $penalty->num;
			$value['penaltyGoals'] = $penalty->goals;
			$value['yellow'] = $row[7];
			$value['suspension1'] = $row[8];
			$value['suspension2'] = $row[9];
			$value['suspension3'] = $row[10];
			$value['red'] = $row[11];
			$value['comment'] = $row[12];
			$value['suspensionTeam'] = $row[13];

			$playerArray[$alias] = $value;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($playerArray);echo'</pre>';
		return $playerArray;
	}

	private function getPenaltyInfo($input) {	
		$input = explode('/', $input);
		$penalty = new stdClass();
		$penalty->num = (empty($input[0])) ? '' : $input[0];
		$penalty->goals = (empty($input[1])) ? '' : $input[1];
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($penalty);echo'</pre>';
		return $penalty;
	}
	
	private function getGoalkeeper($alias) {	
		if (in_array($alias , $this->goalkeeper)) return 1;
		else return 0;
	}


	private function addPlayersInfoToDB ($players)
	{
		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($players);echo'</pre>';
		
		self::cleanPlayersInfoInDB();
		self::addPlayersInfo($players);
		
		return true;
	}
	
	public function cleanPlayersInfoInDB() 
	{	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn($this->table_gameplayer));
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));

		$db->setQuery($query);
		$result = $db->execute();
	}
	
	private function addPlayersInfo($players)
	{
		if (!empty($players)) {
			// echo __FILE__.' ('.__LINE__.')<pre>';print_r($players);echo'</pre>';
			$table = JTable::getInstance('Goals','HbmanagerTable');

			foreach ($players as $value) {
				$table->bind($value);
				$table->store();
			}
		}
	}

	private function addMissingPlayers($data) {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT alias');
		$query->from($db->qn($this->table_gameplayer));
		$query->leftJoin($db->qn($this->table_contact).' USING ('.$db->qn('alias').')');
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('name').' IS NULL');
		// $query->where('('.$db->qn('name').' IS NULL OR '.$db->qn('name').' = '.$db->q('').')');
		$query->order('('.$db->qn('number').'*1 = 0) ,'.$db->qn('number').'*1, '.$db->qn('number') );
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$missing = $db->loadColumn();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($missing);echo'</pre>';
		
		if (count($missing) > 0)  {
			self::addPlayersToDB($missing, $data);
		}
	}
	
	private function addPlayersToDB($missing, $data)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($data);echo'</pre>';die;
		$values = [];
		foreach ($missing as $key => $value) {
			$values[$key]['id'] = ''; // needed, otherwise only 1 entry gets stored in 
			$values[$key]['alias'] = $data[$value]['alias'];
			$values[$key]['name'] = $data[$value]['playerName'];
		}
		// echo __FILE__.' ('.__LINE__.')<pre>';print_r($values);echo'</pre>';
		$table = JTable::getInstance('Contacts','HbmanagerTable');
		// $tablekey = $table->getKeyName(true);
		// echo __FUNCTION__.__LINE__.'<pre>';print_r($tablekey); echo'</pre>';die;
		foreach ($values as $key => $value) {
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($value);echo'</pre>';
			//$table->reset();
			$table->bind($value);
			$table->store();
		}
	}
	
	private function setHomeAliases($players)
	{
		foreach ($players as $value) {
			$aliases[] = $value['alias'];
		}
		return $aliases;
	}


	// Actions --------------------------------------------------------------

	private function processActions($actionString) 
	{
		$actions = self::convertActionStringToArray($actionString);
		$actions = self::formatActionsArray($actions);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($actions);echo'</pre>';
		return $actions;
	}	

	private function saveActions($actions) 
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($actions);echo'</pre>';
		self::addActionsToDB($actions);

		return true; //TODO doublecheck if it acutally worked
	}

	private function convertActionStringToArray($string)
	{
		$string = str_replace('"', '', $string);
		$string = str_replace('7m,', '7m -', $string);
		//remove header
		$pattern = "|(Zeit,Spielzeit,Spielstand,Aktion\r?\n?)|";
		$string = preg_replace($pattern, '', $string);

		if (!empty($string)) {
			$rows = str_getcsv($string, "\n"); 
			//parse the items in rows
			foreach($rows as &$row) {
				$row = str_getcsv($row, "," );
			}
		}

		// add first element
		$gameStartTime = '';
		// TODO: $gameStartTime = self::calcGameStartTime($rows[0][0], $rows[0][0]);
		$gameStartElement[0] = $gameStartTime;
		$gameStartElement[1] = '00:00';
		$gameStartElement[2] = '0:0';
		$gameStartElement[3] = 'Spielbeginn';
		array_unshift($rows, $gameStartElement);

		// add last element
		$lastKey = count($rows);
		$gameEndTime = '';
		// TODO: $gameEndTime = self::calcGameEndTime($rows[$lastKey][0], $rows[$lastKey][0]);
		$gameEndElement[0] = $gameEndTime;
		$gameEndElement[1] = '60:00';
		$gameEndElement[2] = '';
		$gameEndElement[3] = 'Spielende';
		array_push($rows, $gameEndElement);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($rows);echo'</pre>';
		return $rows;
	}


	private function formatActionsArray($actions) {
		
		$actionArray = [];

		$score = [0,0];
		foreach($actions as $i => $row) {
			
			$value = array();
			$value['season'] = $this->season;
			$value['spielIdHvw'] = $this->gameId;
			$value['actionIndex'] = $i;
			//$value['clockTime'] = $row[0];
			$value['timeString'] = $row[1];
			$value['time'] = self::parseTime($row[1]);

			$score = (!empty($row[2])) ? explode(":", $row[2]) : $score;
			$value['scoreHome'] = (count($score) > 1) ? $score[0] : null;
			$value['scoreAway'] = (count($score) > 1) ? $score[1] : null;
			$value['scoreDiff'] = (count($score) > 1) ? $score[1]-$score[0] : null; 

			$value['scoreChange'] = self::getScoreChange($row[3]);

			$currAction = self::getActionType($row[3]); 
			$value['text'] = $currAction->text;
			$value['number'] = $currAction->nr;
			$value['playerName'] = $currAction->playerName;
			$value['alias'] = $currAction->alias;
			$value['teamFlag'] = $currAction->teamFlag;
			$value['category'] = $currAction->category;

			$actionArray[] = $value;
		}

		$actionArray = self::addActionValues($actionArray);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($actionArray);echo'</pre>';die;
		return $actionArray;
	}

	private function parseTime($string) {

		$timeComponents = explode(':', $string);
		$minutes = (int) $timeComponents[0];
		$seconds = (int) $timeComponents[1];
		return $minutes * 60 + $seconds;
	}

	// Examples:
	//"2-min Strafe für Lucas Herre (10)"
	//"Tor durch Andreas Hipp (4)
	//"7m-Tor durch Phillip Koch (2)"
	//"Auszeit HSG Fridingen/Mühlheim 2"
	//"7m - KEIN Tor durch Andreas Hipp (4)"

	private function getScoreChange($input) {
		if (strpos($input, "Tor") === 0) {
			return 1;
		} elseif (strpos($input, "Tor") === 3) {
			return 1;
		}
		return 0;
	}

	private function getActionType($text) {
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($text);echo '</pre>';
		$action = new stdClass();
		$action->text = $text;

		$action->playerName = null;
		$pattern = '/.*(durch|für) (.* .*) \(\d{1,2}\)$/';
		if (preg_match($pattern, $text)) {
			$action->playerName = preg_replace($pattern, '$2', $text);
		}

		$action->nr = null;
	   	$pattern = '/.* \((\d{1,2})\)$/';
		if (preg_match($pattern, $text)) {
			$action->nr = preg_replace($pattern, '$1', $text);
		}
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($action->playerName);echo '</pre>';
		$action->alias = self::getAlias($action->playerName);
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($action->alias);echo '</pre>';
		$action->teamFlag = self::getTeamFlag($action->alias);
		$action->category = self::getCategory($text);

		return $action;
	}

	private function getTeamFlag($alias) {
		
		if ($alias == null) {
			return 0;
		}
		if (in_array($alias, $this->playerAliases)) {
			return -1;
		}
		return 1;
	}

	private function getCategory($input) {
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

	private function addActionValues($rows) {
		//parse the items in rows
		$values = [];
		for($i = 0; $i < count($rows); $i++) {
			$values[$i] = $rows[$i];
			$values[$i]['stats_goals'] = self::getStatisticsGoal($values, $rows[$i]['alias']);
			$values[$i]['stats_yellow'] = self::getStatistics($values, $rows[$i]['alias'], 'yellow');
			$values[$i]['stats_suspension'] = self::getStatistics($values, $rows[$i]['alias'], 'suspension');
			$values[$i]['stats_red'] = self::getStatistics($values, $rows[$i]['alias'], 'red');
		}
		return $values;
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


	private function addActionsToDB ($input){

		self::cleanActionsInDB();
		self::addActions($input);
		
		return true;
	}

	public function cleanActionsInDB() {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn($this->table_gamedetails));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->gameId));
		// echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->execute();
		// echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
	}

	private function addActions($inputData)
	{
		if (!empty($inputData)) {
			//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($inputData);echo '</pre>';

			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$columns = array('season', 'gameIdHvw', 'actionIndex', 'timeString', 'time', 'scoreHome', 'scoreAway', 'scoreDiff', 'scoreChange', 'text', 'number', 'playerName', 'alias', 'teamFlag', 'category', 'stats_goals', 'stats_yellow', 'stats_suspension', 'stats_red');

			foreach($inputData as $row) {
				$values[] = implode(', ', self::formatGoalValueForDB($row));
			}

			// Prepare the insert query.
			$query
					->insert($db->qn($this->table_gamedetails)) 
					->columns($db->qn($columns))
					->values($values);

			// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->execute();
			return $result;
		}
		return false;
	}

	private function formatGoalValueForDB($row) {
		//echo __FUNCTION__.'<pre>';print_r($row); echo'</pre>';
		$value = array();
		$value['season'] 		= (empty($row['season'])) ? 'NULL' : "'".$row['season']."'";
		$value['spielIdHvw'] 	= (empty($row['spielIdHvw'])) ? 'NULL' : $row['spielIdHvw'];
		$value['actionIndex'] 	= (empty($row['actionIndex'])) ? 'NULL' : $row['actionIndex'];
		$value['timeString'] 	= (empty($row['timeString'])) ? 'NULL' : "'".$row['timeString']."'";
		$value['time'] 			= ($row['time'] === '') ? 'NULL' : $row['time'];
		$value['scoreHome'] 	= ($row['scoreHome'] === '') ? 'NULL' : $row['scoreHome'];
		$value['scoreAway'] 	= ($row['scoreAway'] === '') ? 'NULL' : $row['scoreAway'];
		$value['scoreDiff'] 	= ($row['scoreDiff'] === '') ? 'NULL' : $row['scoreDiff'];
		$value['scoreChange'] 	= (empty($row['scoreChange'])) ? 'NULL' : $row['scoreChange'];
        $value['text'] 			= (empty($row['text'])) ? 'NULL' : "'".$row['text']."'";
        $value['number'] 		= (empty($row['number'])) ? 'NULL' : $row['number'];
        $value['playerName'] 			= (empty($row['playerName'])) ? 'NULL' : "'".$row['playerName']."'";
        $value['alias'] 		= (empty($row['alias'])) ? 'NULL' : "'".$row['alias']."'";
        $value['teamFlag'] 			= (empty($row['teamFlag'])) ? 'NULL' : $row['teamFlag'];
        $value['category'] 		= (empty($row['category'])) ? 'NULL' : "'".$row['category']."'";
        $value['stats_goals'] 	= (empty($row['stats_goals'])) ? 'NULL' : $row['stats_goals'];
		$value['stats_yellow'] 	= (empty($row['stats_yellow'])) ? 'NULL' : $row['stats_yellow'];
		$value['stats_suspension'] 	= (empty($row['stats_suspension'])) ? 'NULL' : $row['stats_suspension'];
		$value['stats_red'] 	= (empty($row['stats_red'])) ? 'NULL' : $row['stats_red'];
		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
	}

}


