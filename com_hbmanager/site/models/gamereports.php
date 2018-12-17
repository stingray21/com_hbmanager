<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelGamereports extends HBmanagerModelHBmanager
{	

	private $selectedGameId = null;
	private $games = null;

	public function __construct($config = array())
	{		
		parent::__construct($config);
		
		self::setGames();
		if ($this->games != null) $this->selectedGameId = end($this->games)->gameIdHvw;
		
	}

	public function setGames()
	{
		// Get the database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->table_game);
		$query->leftJoin($db->qn($this->table_gamereport).' USING ('.$db->qn('gameIdHvw').')');
		$query->leftJoin($db->qn($this->table_gym).' USING ('.$db->qn('gymId').')');
		// $query->leftJoin($db->qn('hb_spielbericht_details').' USING ('.$db->qn('spielIdHvw').')');
		// $query->group('hb_spiel.spielIdHvw, hb_spiel.datumZeit,spielberichtId');
		$query->where($this->table_game.'.'.$db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('ownClub').' = 1');
		$query->where($this->table_game.'.'.$db->qn('season').' = '.$db->q($this->season));
		$query->where('DATE('.$db->qn('dateTime').') < NOW() ');
		$query->where($db->qn('pointsHome').' IS NOT NULL ');
		$query->order($db->qn('dateTime').' ASC');
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$games = $db->loadObjectList();
		self::addDateTime($games);
		$this->games = $games;
	}

	private function addDateTime(&$games)
	{
		foreach ($games as &$game) {
			$game->date = JHtml::_('date', $game->dateTime, 'd.m.y', $this->tz);
			$game->time = JHtml::_('date', $game->dateTime, 'H:i', $this->tz);
		}
	}

	public function getGames()
	{
		return $this->games;
	}

	public function getSelectedGame()
	{
		return $this->selectedGameId;
	}





	public function getGameDataAll($teamkey, $season = null) {

		$this->teamkey = $teamkey;
		if ($season !== null) $this->season = $season;

		self::setGames();

		foreach ($this->games as $game) {
			// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($game->gameIdHvw);echo '</pre>';
			$allGames[$game->gameIdHvw] = self::getGameData($teamkey, $game->gameIdHvw);
		}

 
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($allGames);echo '</pre>';die;

		return $allGames;
	}


	public function getGameData($teamkey, $gameId, $season = null) {

		if ($season !== null) $this->season = $season;
		$this->selectedGameId = $gameId;
		$this->teamkey = $teamkey;

		$data['gameinfo'] = self::getGameInfo();

		$data['gamedata'] = self::getGameDetails();

		$data['playerdata'] = self::getChartPlayers();

		$data['picarray'] = self::getPicArray();
 
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';

		return $data;
	}

	public function getGameInfo()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('`gymId`, `season`, `teamkey`, `leagueKey`, `gameIdHvw`, `dateTime`, `home`, `away`, `goalsHome`, `goalsAway`, `goalsHome1`, `goalsAway1`, `comment`, `gymName`, `town`');

		$query->from($this->table_game.' AS game');
		$query->leftJoin($db->qn($this->table_gym).' USING ('.$db->qn('gymId').')');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->selectedGameId));
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre'; 

		$db->setQuery($query);
		$infos = $db->loadObject();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre';die;
		$infos = self::formatGameInfos($infos);

		return $infos;
	}

	private function formatGameInfos($data)
	{		
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';
		$infos = null;
		
		$infos['season'] = $data->season;
		$infos['teamkey'] = $data->teamkey;
		$infos['leagueKey'] = $data->leagueKey;
		$infos['gameIdHvw'] = intval($data->gameIdHvw);
		$infos['dateTime'] = JHtml::_('date', $data->dateTime, 'Y-m-d H:i:s', $this->tz);
		$infos['date'] = JHtml::_('date', $data->dateTime, 'd.m.y', $this->tz);
		$infos['time'] = JHtml::_('date', $data->dateTime, 'H:i', $this->tz);
		$infos['home'] = $data->home;
		$infos['away'] = $data->away;
		$infos['goalsHome'] = intval($data->goalsHome);
		$infos['goalsAway'] = intval($data->goalsAway);
		$infos['goalsHome1'] = intval($data->goalsHome1);
		$infos['goalsAway1'] = intval($data->goalsAway1);
		$infos['comment'] = $data->comment;
		$infos['gymId'] = intval($data->gymId);
		$infos['gymName'] = $data->gymName;
		$infos['town'] = $data->town;

		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre>';
		return $infos;
	}

	protected function getGameDetails()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->table_gamereport_details);
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->selectedGameId));
		$query->order($db->qn('actionIndex').' ASC');
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre';

		$db->setQuery($query);
		$details = $db->loadObjectList();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($details);echo '</pre';
		$details = self::formatDetails($details);

		return $details;
	}

	private function formatDetails($items)
	{		
		foreach ($items as &$item) {
			
			$item->season 			= $item->season;
            $item->gameIdHvw 		= intval($item->gameIdHvw);
            $item->actionIndex 		= intval($item->actionIndex);
            $item->timeString 		= $item->timeString;
            $item->time 			= intval($item->time);
            $item->scoreChange 		= intval($item->scoreChange);
            $item->scoreHome 		= intval($item->scoreHome);
            $item->scoreAway 		= intval($item->scoreAway);
            $item->scoreDiff 		= intval($item->scoreDiff);
            $item->text 			= $item->text;
            $item->number 			= intval($item->number);
            $item->name 			= $item->playerName;
            $item->alias 			= $item->alias;
            $item->team 			= intval($item->teamFlag);
            $item->category 		= $item->category;
            $item->stats_goals 		= intval($item->stats_goals);
            $item->stats_yellow 	= intval($item->stats_yellow);
            $item->stats_suspension = intval($item->stats_suspension);
            $item->stats_red 		= intval($item->stats_red); 
		}
		return $items;
	}

	protected function getChartPlayers()
	{		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		// $query->select('*');
		$query->select('`name`, `alias`, `number`, `goalie`, `goals`, `penalty`, `penaltyGoals`, `yellow`, `suspension1`, `suspension2`, `suspension3`, `red`, `comment`, `suspensionTeam` ');
		$query->from($this->table_game_player);
		$query->leftJoin($db->qn($this->table_contact).' USING ('.$db->qn('alias').')');
		$query->where($db->qn('gameIdHvw').' = '.$db->q($this->selectedGameId));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('number').' NOT IN ('.$db->q('A').','.$db->q('B').','.$db->q('C').','.$db->q('D').')');
		$query->order($db->qn('alias').' ASC');
		// echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$players = $db->loadObjectList();
		$players = self::formatPlayers($players);

		// echo __FILE__.' - '.__LINE__.'<pre>'; print_r($players); echo '</pre>';die;
		return $players;
	}


	private function formatPlayers($players)
	{		
		foreach ($players as &$player) {
			
			// $player->name 		= $player->name;
			// $player->alias 		= $player->alias;
			// $player->number 		= $player->number;
			// $player->goalie 		= $player->goalie;
			$player->goals 			= intval($player->goals);
			$player->penalty 		= intval($player->penalty);
			$player->penaltyGoals 	= intval($player->penaltyGoals);
			$player->yellow 		= ($player->yellow === "") 		? null : 1;
			$player->suspension1 	= ($player->suspension1 === "") ? null : 1;
			$player->suspension2 	= ($player->suspension2 === "") ? null : 1;
			$player->suspension3 	= ($player->suspension3 === "") ? null : 1;
			$player->red 			= ($player->red === "") 		? null : 1;
			// $player->bemerkung 	= $player->bemerkung;
			// $player->teamZstr 	= ($player->teamZstr === "") 		? null : 1;
		}
		return $players;
	}
        
    private function getPicArray()
    {
    	$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select($db->qn('spritesheet'));
		$query->from($this->table_spritesheets);
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		// echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$picArray = $db->loadResult();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($picArray);echo '</pre>';
		
		if (empty($picArray)) $picArray = '["dummy"]';

    	return json_decode($picArray);
    }
}

