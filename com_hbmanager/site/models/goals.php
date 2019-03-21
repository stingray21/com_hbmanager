<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelGoals extends HBmanagerModelHBmanager
{	
	protected $gameId = null;
	protected $team = null;
	protected $players = null;
	protected $playerData = null;
	protected $games = null;
	protected $startGame = null;

	public function __construct($config = array())
	{		
		
		parent::__construct($config);
		
		$app = JFactory::getApplication();
		$menuitem   = $app->getMenu()->getActive(); 
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($menuitem); echo '</pre>';
		if (!is_null($menuitem)) {
			$params = $menuitem->params; // get the params
			
			$this->defaultChartMode = $params->get('defaultChartMode', 'goals');
			$this->futureGames = $params->get('futureGames', true);
			//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->futureGames); echo '</pre>';
		} 
			
		self::setGoalsData();
	}

	public function getGoalsData () 
	{
		$goalsData = new stdClass();
		$goalsData->teamkey 	= null;
		$goalsData->season 	= null;
		$goalsData->futureGames 	= true;
		$goalsData->gamesJSON 	= json_encode($this->games);
		$goalsData->playersJSON = json_encode(self::getPlayerJSON());
		$goalsData->startGame 	= $this->startGame;
		return $goalsData;
	}

	public function setGoalsData ($teamkey = null)
	{
		if ($teamkey === null) $teamkey = $this->teamkey;

		$this->team = self::getTeam();
		$this->players = self::getPlayers();
		$this->playerData = self::getPlayerData();
		$this->games = self::getGames();
	}	

	private function getGames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select("*, DATE(dateTime) AS date, IF(dateTime < NOW(),1,0) AS played, 
			IF(ISNULL(goalsHome), NULL ,CONCAT(goalsHome,':',goalsAway)) AS result,
			IF(home = ".$db->q($this->team->shortName).",1,0) AS homeGame,
			IF(home = ".$db->q($this->team->shortName).", CONCAT(away, ' (H)'), CONCAT(home, ' (A)')) AS game");
		$query->from($this->table_game);
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('ownClub').' = 1');
		$query->order($db->qn('dateTime').' ASC');

		// echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$games = $db->loadObjectList();
		self::addPlayers($games);
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games);echo '</pre>';die;
		return $games;
	}
	
	private function addPlayers(&$games) 
	{		
		foreach ($games as $gameNum => &$game) 
		{
			if ($game->played)
			{	
				if ($game->result !== null) $this->startGame = $gameNum;

				$game->gameIdHvw 	= (int) $game->gameIdHvw;
				$game->goalsAway 	= (int) $game->goalsAway;
				$game->goalsAway1 	= (int) $game->goalsAway1;
				$game->goalsHome 	= (int) $game->goalsHome;
				$game->goalsHome1 	= (int) $game->goalsHome1;
				$game->gymId 		= (int) $game->gymId;
				$game->homeGame 	= (int) $game->homeGame;
				$game->ownClub 		= (int) $game->ownClub;
				$game->played 		= (int) $game->played;
				$game->pointsAway 	= (int) $game->pointsAway;
				$game->pointsHome 	= (int) $game->pointsHome;
				$game->reportHvwId 	= (int) $game->reportHvwId;

				foreach ($this->players as $key => $player) {
					//echo __FILE__.' ('.__LINE__.'):<pre>';print_r($player);echo'</pre>';
					
					$game->players[$key]['played'] = 0;
					if (isset($this->playerData[$player->alias][$game->gameIdHvw])) 
					{
						$game->players[$key] = $this->playerData[$player->alias][$game->gameIdHvw];
					}
					$game->players[$key]['name'] = $player->name;
					$game->players[$key]['games'] = (int) $player->numGames;
					$game->players[$key]['goalsTotal'] = (int) $player->goalsTotal;
					$game->players[$key]['averageTotal'] = round($player->goalsTotal/$player->numGames,1);
					$game->players[$key]['goalie'] = $player->goalie;
					$game->players[$key]['yellowTotal'] = (int) $player->yellowTotal;
					$game->players[$key]['suspensionTotal'] = $player->suspension1Total + $player->suspension2Total + $player->suspension3Total;
					$game->players[$key]['redTotal'] = (int) $player->redTotal;
					$game->players[$key]['penaltyRatioTotal'] = ($player->penaltyTotal > 0) ? $player->penaltyGoalsTotal.'/'.$player->penaltyTotal : null;
					$game->players[$key]['penaltyPercentTotal'] = ($player->penaltyTotal > 0) ? round($player->penaltyGoalsTotal/$player->penaltyTotal*100,0) : null;
				}
			}
		}
	}

	private function getPlayers()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('alias, IFNULL(name, alias) as name, '
			. 'count(number NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').') ) AS numGames, '
			. "IF(goalie = 1, 'TW', '') AS goalie,"
			. 'sum(goals) AS goalsTotal,'
			. "sum(IF(yellow = '', 0, 1)) AS yellowTotal,"
			. "sum(IF(suspension1 = '', 0, 1)) AS suspension1Total,"
			. "sum(IF(suspension2 = '', 0, 1)) AS suspension2Total,"
			. "sum(IF(suspension3 = '', 0, 1)) AS suspension3Total,"
			. "sum(IF(red = '', 0, 1)) AS redTotal,"
			. 'sum(penalty) AS penaltyTotal,'
			. 'sum(penaltyGoals) AS penaltyGoalsTotal,'
			. ' ROUND(sum(goals) / count(goals), 1) AS average');
		$query->from($this->table_game_player);
		$query->leftJoin($db->qn($this->table_contact).' USING ('.$db->qn('alias').')');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('number').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$query->group('alias');
		$query->order($db->qn('alias').' ASC');

		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$players = $db->loadObjectList();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($players);echo '</pre>';die;
		return $players;
	}

	private function getPlayerData()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select("gameIdHvw, alias, number, goalie, goals, penalty, penaltyGoals, 
			IF(yellow = '', 0, 1) AS yellow,
			IF(suspension1 = '', 0, 1) AS suspension1,
			IF(suspension2 = '', 0, 1) AS suspension2,
			IF(suspension3 = '', 0, 1) AS suspension3,
			IF(red = '', 0, 1) AS red,
			DATE(dateTime) AS date");
		$query->from($this->table_game_player);
		$query->leftJoin($db->qn($this->table_game).' USING ('.$db->qn('gameIdHvw').','.$db->qn('season').','.$db->qn('teamkey').')');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('number').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$query->order($db->qn('dateTime').', '.$db->qn('alias').', '.$db->qn('gameIdHvw').' ASC');

		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$playerData = $db->loadObjectList();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($playerData);echo '</pre>';die;
		$players = self::groupPlayers($playerData);
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($players);echo '</pre>';die;
		return $players;
	}

	private function groupPlayers(&$playerItems)
	{
		foreach ($playerItems as $item) {

			if (!isset($temp[$item->alias])) {
				$temp[$item->alias] = array('games' => 0, 'goals' => 0, 'average' => 0, 'penalty' => 0, 'penaltyGoals' => 0, 'yellow' => 0, 'suspension' => 0, 'red' => 0);
			}
			
			$temp[$item->alias]['games'] += 1;
			$temp[$item->alias]['goals'] += $item->goals;
			$temp[$item->alias]['penalty'] += $item->penalty;
			$temp[$item->alias]['penaltyGoals'] += $item->penaltyGoals;
			$temp[$item->alias]['yellow'] += $item->yellow;
			$temp[$item->alias]['suspension'] += ($item->suspension1+$item->suspension2+$item->suspension3);
			$temp[$item->alias]['red'] += $item->red;
			
			$game = array(
						'number' => (int) $item->number, 
						'goalie' => ($item->goalie) ? 'TW' : '', 
						'gamesSoFar' => (int) $temp[$item->alias]['games'], 
						'goals' => (int) $item->goals, 
						'goalsSoFar' =>	$temp[$item->alias]['goals'], 
						'averageSoFar' =>	round($temp[$item->alias]['goals']/$temp[$item->alias]['games'],1), 
						'penalty' => (int) $item->penalty, 
						'penaltyGoals' => (int) $item->penaltyGoals, 
						'penaltyRatio' => ($item->penalty > 0) ? $item->penaltyGoals.'/'.$item->penalty : null, 
						'penaltySoFar' =>	$temp[$item->alias]['penalty'], 
						'penaltyGoalsSoFar' =>	$temp[$item->alias]['penaltyGoals'], 
						'penaltyRatioSoFar' => ($temp[$item->alias]['penalty'] > 0) ? $temp[$item->alias]['penaltyGoals'].'/'.$temp[$item->alias]['penalty'] : null,
						'penaltyPercentSoFar' => ($temp[$item->alias]['penalty'] > 0) ? round($temp[$item->alias]['penaltyGoals']/$temp[$item->alias]['penalty']*100,0) : null,
						'yellow' => (int) $item->yellow, 
						'yellowSoFar' =>	$temp[$item->alias]['yellow'], 
						'suspension1' => (int) $item->suspension1, 
						'suspension2' => (int) $item->suspension2, 
						'suspension3' => (int) $item->suspension3, 
						'suspensionGame' =>	($item->suspension1+$item->suspension2+$item->suspension3), 
						'suspensionSoFar' =>	$temp[$item->alias]['suspension'], 
						'red' => (int) $item->red, 
						'redSoFar' =>	$temp[$item->alias]['red'],
						'played' => true);

			$gameId = 
			$players[$item->alias][$item->gameIdHvw] = $game;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($players);echo'</pre>';die;
		return $players;
	}
	
	private function getPlayerJSON()
	{
		$playerJSON = $this->players;
		foreach ($playerJSON as $key => &$player) {
			foreach ($this->games as $gameKey => $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				
				if (property_exists($game, 'players') && $game->players[$key]['played'] == 1 ) {
					$tempGame = $game->players[$key];
					$tempGame['gameIdHvw'] = $game->gameIdHvw; 
					$tempGame['game'] = $game->game; 
					$tempGame['gameKey'] = $gameKey; 
					$player->games[] = $tempGame;
				}
			}
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($playerJSON);echo'</pre>';
		return $playerJSON;
	}
}

