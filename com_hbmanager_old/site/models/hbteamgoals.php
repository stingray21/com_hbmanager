<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelHBteamGoals extends JModelLegacy
{
	/**
	 * @var array messages
	 */
	public $teamkey;
	public $season;
	public $gameId;
	public $recentGameId;
	public $recentGameDate;
	private $chartGames = array();
	public $futureGames;
	public $defaultChartModel;
	
	function __construct() 
	{
		parent::__construct();
		
		$app = JFactory::getApplication();
		$menuitem   = $app->getMenu()->getActive(); 
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($menuitem); echo '</pre>';
		if (!is_null($menuitem)) {
			$params = $menuitem->params; // get the params
			
			$this->defaultChartMode = $params->get('defaultChartMode', 'goals');
			$this->futureGames = $params->get('futureGames', true);
			//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->futureGames); echo '</pre>';
			$this->teamkey = $params->get('teamkey');
			$this->season = $params->get('season');
			//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->season); echo '</pre>';
		} else {
			
		}
		$game = self::getRecentGame();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($game);echo'</pre>';
		if (!empty($game)) {
			$this->gameId = $game->spielIdHvw;
			$this->recentGameId = $game->spielIdHvw;
			$this->recentGameDate = $game->datum;
		}
			
	}

	
	function setChartData($teamkey = null, $season = null, $futureGames = 1) 
	{
		$this->futureGames = $futureGames;
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->futureGames); echo '</pre>';
		$this->teamkey = $teamkey;
		$this->season = $season;
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->season); echo '</pre>';
		
		$game = self::getRecentGame();
		if (!empty($game)) {
			$this->gameId = $game->spielIdHvw;
			$this->recentGameId = $game->spielIdHvw;
			$this->recentGameDate = $game->datum;
		}
			
	}
	
	function getTeam($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
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
	
	protected function getRecentGame($teamkey = null)
	{
		if ($teamkey === null){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('spielIdHvw, DATE(`datumZeit`) AS `datum`');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
		$query->group($db->qn('spielIdHvw'));
		$query->where('hb_spiel_spieler.'.$db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->where($db->qn('eigenerVerein').' = 1');
		$query->where('DATE('.$db->qn('datumZeit').') < NOW() ');
		$query->order($db->qn('datumZeit').' DESC');
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$game = $db->loadObject();
		
		return $game;
	}
	
	function getGames($teamkey = null)
	{
		if ($teamkey === null){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('spielIdHvw').', '.
			$db->qn('saison').', '.
			$db->qn('ligaKuerzel').', '.
			$db->qn('kuerzel').', '.
			$db->qn('hallenNr').', '.
			//$db->qn('datumZeit').', '.
			$db->qn('heim').', '.
			$db->qn('gast').', '.
			$db->qn('toreHeim').', '.
			$db->qn('toreGast').', '.
			' DATE('.$db->qn('datumZeit').') AS `datum`, '.
		//		$db->qn('toreHeim').' IS NOT NULL AS `show`');
				$db->qn('alias').' IS NOT NULL AS `show`');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_spiel_spieler').' USING ('.$db->qn('spielIdHvw').','.
				$db->qn('saison').','.$db->qn('kuerzel').')');
		$query->where('hb_spiel.'.$db->qn('kuerzel').' = '.$db->q($teamkey));
		//$query->where('hb_spiel.'.$db->qn('toreHeim').' IS NOT NULL');
		$query->where($db->qn('eigenerVerein').' = 1');
		//$query->where('DATE('.$db->qn('datumZeit').') < NOW() ');
		$query->order($db->qn('datumZeit').' ASC');
		$query->group('spielIdHvw');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo '=> model->games<br><pre>'; print_r($games); echo '</pre>';
		
		foreach ($games as $game)
		{
			if (strpos($game->heim, 'Geisl') !== FALSE) {
				$game->gameName = $game->gast.' (H)';
			}
			else {
				$game->gameName = $game->heim.' (A)';
			}
		}
		return $games;
	}
	
	public function getGamesJSON ($teamkey = null) {
		$games = self::getGames($teamkey);
		$gamesJSON = [];
		
		foreach ($games as $key => $game) {
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($game);echo'</pre>';
			$gamesJSON[$key]['id'] = $game->spielIdHvw;
            $gamesJSON[$key]['season'] = $game->saison;
            //$gamesJSON[$key]['league'] = $game->ligaKuerzel;
            $gamesJSON[$key]['teamkey'] = $game->kuerzel;
            //$gamesJSON[$key]['gym'] = $game->hallenNr;
            //$gamesJSON[$key]['home'] = $game->heim;
            //$gamesJSON[$key]['away'] = $game->gast;
            //$gamesJSON[$key]['goalsH'] = $game->toreHeim;
            //$gamesJSON[$key]['goalsA'] = $game->toreGast;
            $gamesJSON[$key]['result'] = ($game->toreHeim !== null) ? $game->toreHeim.':'.$game->toreGast : null;
            //$gamesJSON[$key]['date'] = $game->datum;
			$gamesJSON[$key]['date'] = JHtml::_('date', $game->datum, 'd.m.');
			// TODO timezone $gamesJSON[$key]['date'] = JHtml::_('date', $game->datum, 'd.m.', $timezone);
            $gamesJSON[$key]['show'] = $game->show;
            $gamesJSON[$key]['game'] = $game->gameName;
		}
		
		return json_encode($gamesJSON);
	}
	
	public function getStartGame($gamesJSON) {
		$games = json_decode($gamesJSON);
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($games);echo'</pre>';
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->recentGameId);echo'</pre>';
		foreach($games as $key => $game) {
			if ($game->id == $this->recentGameId) {
				return $key;
			}
		}
		return 0;
	}
	
	public function getPlayers4AllGamesJSON ($teamkey = null, $season = null) {
		
		$games = self::getGames($teamkey);
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($games);echo'</pre>';
		foreach ($games as $key => $game) {
			$data[$key] = new stdClass();
			$data[$key]->game = $game->spielIdHvw;
			$data[$key]->players = json_decode(self::getPlayersJSON($game->spielIdHvw, $teamkey, $season));
		}
		return json_encode($data);
	}
	
	public function getPlayersJSON ($gameId = null, $teamkey = null, $season = null) {
		
		$players = self::getPlayers($gameId, $teamkey, $season);
		$playersJSON = [];
		
		foreach ($players as $key => $player) {
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($player);echo'</pre>';
			$playersJSON[$key]['alias'] = $player->alias;
			$playersJSON[$key]['name'] = $player->name;
			
			$playersJSON[$key]['gk'] = $player->tw;
			$playersJSON[$key]['ga'] = $player->spiele;
			$playersJSON[$key]['g'] = $player->tore;
			$playersJSON[$key]['p'] = $player->tore7m;
			$playersJSON[$key]['t'] = $player->toregesamt;
			$playersJSON[$key]['r'] = $player->quote;
			$playersJSON[$key]['ye'] = $player->gelb;
			$playersJSON[$key]['re'] = $player->rot;
			$playersJSON[$key]['m'] = ($player->zweimin1 != '') ? 1 : 0 ;
			$playersJSON[$key]['m'] += ($player->zweimin2 != '') ? 1 : 0 ;
			$playersJSON[$key]['m'] += ($player->zweimin3 != '') ? 1 : 0 ;
		}
		$playersJSON = json_encode($playersJSON);
		$playersJSON = str_replace ( 'null' , '""' , $playersJSON);
		return $playersJSON;
	}
	
	function getPlayers($gameId = null, $teamkey = null, $season = null)
	{
		if ($teamkey !== null) { $this->teamkey = $teamkey; }
		if ($gameId !== null) { $this->gameId = $gameId; }
		if ($season !== null) {	$this->season = $season; }
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('gesamtTabelle.alias as alias,'.
			' hb_spiel_spieler.spielIdHvw as spielIdHvw,'.
			' hb_spiel_spieler.kuerzel as teamkey,'.
			' hb_spiel_spieler.saison as saison, hb_spiel_spieler.tw as tw,'.
			' hb_mannschaft_spieler.tw as twposition, '.
			'spiele, toregesamt, quote, '.
			'`tore`, `tore7m`, `gelb`, `rot`, `2min1` as `zweimin1`, `2min2` as `zweimin2`,'.
			' `2min3` as `zweimin3`, `groesse`, `geburtstag`, `name`,'.
			' `heim`, `gast`, `toreHeim`, `toreGast`');
		$query->from('('.self::getInnerQuery().') as `spieler`');
		$query->leftJoin($db->qn('hb_spieler').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_mannschaft_spieler').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_spiel_spieler').' ON spieler.alias=hb_spiel_spieler.alias AND spielIdHvw='.$db->q($this->gameId));
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
		$query->leftJoin('( '.self::getTotalGoalsQuery().' ) as `gesamtTabelle` ON spieler.alias=gesamtTabelle.alias');
		//$query->where('hb_spiel_spieler.'.$db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
		//	. $db->q('C').','.$db->q('D').')');
		//$query->order($db->qn('datum').' ASC');
		
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$players = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		
		//$players = self::addPositions($players);
		return $players;
	}
	
	protected function getInnerQuery() {
		$db = $this->getDbo();
		$innerQuery = $db->getQuery(true);
		$innerQuery->select('`alias`');
		$innerQuery->from('hb_spiel_spieler');
		$innerQuery->group($db->qn('alias'));
		$innerQuery->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$innerQuery->where($db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		//echo __FILE__.' ('.__LINE__.')<pre>'.$innerQuery.'</pre>';
		return $innerQuery;
	}
	
	
	protected function getTotalGoalsQuery () {
		$db = $this->getDbo();
		//$season = $season.'/'.(season+1);
		$totalQuery = $db->getQuery(true);
		$totalQuery->select('alias, '
			. 'count(trikotNr NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').') ) AS spiele, '
			. 'sum(tore) AS toregesamt,'
			. ' ROUND(sum(tore) / count(tore), 1) AS quote');
		$totalQuery->from('hb_spiel_spieler');
		$totalQuery->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
		$totalQuery->where('hb_spiel_spieler.'.$db->qn('saison').' = '.$db->q($this->season));
		// TODO where id in details 
		$totalQuery->where('DATE('.$db->qn('datumZeit').') <= '.$db->q($this->recentGameDate));
		$totalQuery->where($db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$totalQuery->group('alias');
		$totalQuery->order($db->qn('datumZeit').' ASC');

		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($totalQuery);echo'</pre>';
		return $totalQuery;
	}
	
	protected function addPositions($players) {
		
		foreach ($players as $player)
		{
			$positions = array();
			$positionskurz = array();
			
			$positionKeys = array('trainer', 'TW', 'LA', 'RL', 'RM', 'RR', 'RA', 'KM');
			$positionNames = array('Trainer', 'Torwart', 'Linksaußen', 'Rückraum-Links',
				'Rückraum-Mitte', 'Rückraum-Rechts', 'Rechtsaußen', 'Kreis');
			$positionAbrv = array('TR', 'TW', 'LA', 'RL', 'RM', 'RR', 'RA', 'KM');
			
			foreach ($positionKeys as $i => $key)
			{
				if ($player->{$key} == true) 
				{
					$positions[] = $positionNames[$i];
					$positionskurz[] = $positionAbrv[$i];
				}
			}
			$player->positions = implode(', ', $positions);
			$player->positionskurz = $positionskurz;
		}
		return $players;
	}
	
	protected function getPlayerOf($games) {
		$db = $this->getDbo();
		
		foreach ($games as $key => $game) {
			$query = $db->getQuery(true);
			$query->select('hb_spiel_spieler.alias as alias, name, tore, tore7m, hb_spiel_spieler.tw as tw');
			$query->from('hb_spiel');
			$query->leftJoin($db->qn('hb_spiel_spieler').' USING ('.$db->qn('spielIdHvw').')');
			$query->leftJoin($db->qn('hb_mannschaft_spieler').' ON ( hb_mannschaft_spieler.kuerzel = hb_spiel.kuerzel'.
				' AND hb_mannschaft_spieler.alias = hb_spiel_spieler.alias)');
			$query->leftJoin('#__contact_details ON (#__contact_details.alias = hb_spiel_spieler.alias)');
			$query->where('hb_spiel.'.$db->qn('spielIdHvw').' = '.$db->q($game->spielIdHvw));
			$query->where($db->qn('tore').' IS NOT NULL');
			$query->where('hb_spiel_spieler.'.$db->qn('trikotNr').' != 0');
			$query->group('hb_spiel_spieler.alias');
			$query->order('name');
			//echo '=> model->$query <br><pre>'.$query.'</pre>';
			$db->setQuery($query);
			$players = $db->loadObjectList();
			echo '=> model->player<br><pre>'; print_r($players); echo '</pre>';
		}
		
	}
	
	function getChartData()
	{
		$data['games'] = self::getChartGames();
		$data['players'] = self::getChartPlayers();
                //echo __FILE__.' - '.__LINE__.'<pre>'; print_r($data); echo '</pre>';
		return $data;
	}
		
	
	protected function getChartGames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$query->select($db->qn(array('spielIdHvw','heim','gast')));
		$query->select($db->qn('spielIdHvw').' AS '.$db->qn('game').', '.
                        $db->qn('heim').', '.$db->qn('gast'));
		$query->from('hb_spiel');
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		if (!$this->futureGames) {
			$query->where('DATE('.$db->qn('datumZeit').') <= '.$db->q($this->recentGameDate));
		}
		$query->where($db->qn('eigenerVerein').' = 1');
		$query->order($db->qn('datumZeit').' ASC');
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		$games = self::addGameName($games);
		//echo __FILE__.' - '.__LINE__.'<pre>'; print_r($games); echo '</pre>';
		$this->chartGames = $games;
		return $games;
	}
	
	protected function addGameName($games) {
		$home = self::getHomeName();
		
		foreach ($games as $game)
		{
			if (strpos($game->heim, $home) !== FALSE) {
				$game->name = $game->gast.' (H)';
			}
			else {
				$game->name = $game->heim.' (A)';
			}
		}
		//echo __FILE__.' - '.__LINE__.'<pre>'; print_r($games); echo '</pre>';
		return $games;
	}
	
	
	protected function getHomeName()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$query->select('name');
		$query->select('nameKurz');
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$name = $db->loadResult();
		//echo __FILE__.' - '.__LINE__.'<pre>'; print_r($name); echo '</pre>';
		return $name;
	}
	
	protected function getChartPlayers()
	{		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select($db->qn(array('alias', 'spielIdHvw', 
			'datumZeit', 
			'trikotNr', 'tw', 'tore', 'tore7m', 
			'gelb', 'rot', 
			'teamZstr', 'id', 'name')));
		$query->select('hb_spiel_spieler.'.$db->qn('kuerzel').' AS '.$db->qn('kuerzel')
			.', hb_spiel_spieler.'.$db->qn('saison').' AS '.$db->qn('saison')
			.', hb_spiel_spieler.'.$db->qn('bemerkung').' AS '.$db->qn('bemerkung')
			.', hb_spiel_spieler.'.$db->qn('bemerkung').' AS '.$db->qn('spielBemerkung')
			.', hb_spiel_spieler.'.$db->qn('7m').' AS '.$db->qn('versuche7m')
			.', hb_spiel_spieler.'.$db->qn('2min1').' AS '.$db->qn('zweiMin1')
			.', hb_spiel_spieler.'.$db->qn('2min2').' AS '.$db->qn('zweiMin2')
			.', hb_spiel_spieler.'.$db->qn('2min3').' AS '.$db->qn('zweiMin3'));
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
		$query->where('hb_spiel_spieler.'.$db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$query->order($db->qn('datumZeit').' ASC');
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$playerdata = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($playerdata); echo '</pre>';
		
		$playerdata = self::groupByPlayer($playerdata);
		$players = self::formatPlayers($playerdata);
                $players = self::addGameNameToPlayers($players);
//		echo __FILE__.' - '.__LINE__.'<pre>'; print_r($players); echo '</pre>';
		return $players;
	}
	
	protected function groupByPlayer($playerdata)
	{		
		$players = array();
		foreach ($playerdata as $value){
			$players[$value->alias][] = $value;
		}
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		return $players;
	}
	
	protected function formatPlayers($playerdata)
	{		
		$players = array();
		$i = 0;
		foreach ($playerdata as $alias => $player) {
			$total = 0;
			$twoMinTotal = 0;
			$data = array();
			foreach ($player as $key => $value) {
				$total += $value->tore;
				$twoMin = ($value->zweiMin1 != '') ? 1 : 0 ;
				$twoMin += ($value->zweiMin2 != '') ? 1 : 0 ;
				$twoMin += ($value->zweiMin3 != '') ? 1 : 0 ;
				$twoMinTotal += $twoMin;
				$data[$key]['game'] = $value->spielIdHvw;
                                $data[$key]['goalie'] = $value->tw;
				$data[$key]['goals'] = intval($value->tore);
				$data[$key]['total'] = $total;
				$data[$key]['penalties'] = intval($value->tore7m);
				$data[$key]['penaltyAttemps'] = intval($value->versuche7m);
				$data[$key]['twoMin'] = $twoMin;
				$data[$key]['twoMinTotal'] = $twoMinTotal;
			}
			
			$players[$i]['name'] = $player[0]->name;
			$players[$i]['alias'] = $player[0]->alias;
			$players[$i]['data'] = $data;
			$i++;
		}
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		return $players;
	}
        
        
        private function addGameNameToPlayers($players) {
            $names = array();
            foreach ($this->chartGames as $game) {
                $names[$game->game] = $game->name;
            }
            //echo __FILE__.' - '.__LINE__.'<pre>'; print_r($names); echo '</pre>';
            
            foreach ($players as &$player) {
                foreach ($player['data'] as &$game) {
                    $game['name'] = $names[$game['game']];
                }
            }
            
            return $players;
        }

	
}