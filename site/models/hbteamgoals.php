<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class HBteamHomeModelHBteamGoals extends JModelLegacy
{
	/**
	 * @var array messages
	 */
	public $teamkey;
	public $season;
	public $gameId;
	
	function __construct() 
	{
		parent::__construct();
		
		//request the selected teamkey
			$menuitemid = JRequest::getInt('Itemid');
			if ($menuitemid)
			{
				$menu = JFactory::getApplication()->getMenu();
				$menuparams = $menu->getParams($menuitemid);
			}
			$this->teamkey = $menuparams->get('teamkey');
			$this->season = $menuparams->get('season');
			
			$game = self::getRecentGame();
			$this->gameId = $game->spielIDhvw;
			$this->gameDate = $game->datum;
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
		$query->select('spielIDhvw, datum');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIDhvw').')');
		$query->group($db->qn('spielIDhvw'));
		$query->where('hb_spiel_spieler.'.$db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->where($db->qn('datum').' < NOW() ');
		$query->order($db->qn('datum').' DESC');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$game = $db->loadObject();
		//echo '=> model->gameId<br><pre>'; print_r($game); echo '</pre>';
		return $game;
	}
	
	function getGames($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_spiel_spieler').' USING ('.$db->qn('spielIDhvw').')');
		$query->where('hb_spiel.'.$db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->where('hb_spiel.'.$db->qn('toreHeim').' IS NOT NULL');
		$query->where($db->qn('datum').' < NOW() ');
		$query->order($db->qn('datum').' ASC');
		$query->group('spielIDhvw');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo '=> model->games<br><pre>'; print_r($games); echo '</pre>';
		return $games;
	}
	
	function getPlayers($gameId = null, $teamkey = null, $season = null)
	{
		if ($teamkey === null){
			$teamkey = $this->teamkey;
		}
		if ($gameId === null){
			$gameId = $this->gameId;
		}
		if ($season === null){
			$season = $this->season;
		}
		//echo 'GameID: '.$gameId;
		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('datum');
		$query->from('hb_spiel');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($gameId));
		//echo '=> model->$query <br><pre>'; echo $query; echo '</pre>';
		$db->setQuery($query);
		$this->gameDate = $db->loadResult();
		//echo '=> model->gameDate<br><pre>'; print_r($this->gameDate); echo '</pre>';
		
		$db = $this->getDbo();
		
		$innerQuery = $db->getQuery(true);
		$innerQuery->select('alias, count(tore) AS spiele, sum(tore) AS toregesamt, '.
			'ROUND(sum(tore) / count(tore), 1) AS quote');
		$innerQuery->from('hb_spiel_spieler');
		$innerQuery->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIDhvw').')');
		$innerQuery->where('hb_spiel_spieler.'.$db->qn('saison').' = '.$db->q($season));
		$innerQuery->where($db->qn('datum').' <= '.$db->q($this->gameDate));
		$innerQuery->group('alias');
		$innerQuery->order($db->qn('datum').' ASC');

		//echo '=> model->$innerQuery <br><pre>'; echo $innerQuery; echo '</pre>';
//		$db->setQuery($query);
//		$players = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('`alias`, `spielIdHvw`, hb_spiel_spieler.kuerzel as teamkey,'.
			' hb_spiel_spieler.saison as saison, hb_spiel_spieler.tw as tw,'.
			' hb_mannschaft_spieler.tw as twposition, spiele, toregesamt, quote, '.
			'`tore`, `davon7m`, `gelbeKarte`, `roteKarte`, `2min1`, `2min2`,'.
			' `2min3`, `kommentar`, `groesse`, `geburtstag`, `name`,'.
			' `heim`, `gast`, `toreHeim`, `toreGast`');
		$query->from('hb_spiel_spieler');
		$query->where('spielIdHvw = '.$db->q($gameId).
				' AND hb_spiel_spieler.'.$db->qn('saison').' = '.$db->q($season));
		$query->leftJoin($db->qn('hb_spieler').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_mannschaft_spieler').' USING ('.$db->qn('alias').')');
		//$query->leftJoin($db->qn('hb_spiel').' ON a.spielIdHvw=hb_spiel.spielIDhvw');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIDhvw').')');
		$query->leftJoin('( '.$innerQuery.' ) as `gesamtTabelle` USING ('.$db->qn('alias').')');
		//$query->order($db->qn('datum').' ASC');

		//echo '=> model->$query <br><pre>'; echo $query; echo '</pre>';
		$db->setQuery($query);
		$players = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		
		
		
		//$players = self::addPositions($players);
		return $players;
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
	
	
	
	function getChartGames($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('spielIDhvw','heim','gast')));
		$query->from('hb_spiel');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->where($db->qn('datum').' <= '.$db->q($this->gameDate));
		$query->order($db->qn('datum').' ASC');
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo '=> model->games <br><pre>'; print_r($games); echo '</pre>';
		return $games;
	}
	
	
	function getChartData($teamkey)
	{
		$games = self::getChartGames($teamkey);
		
		$db = $this->getDbo();
		
		foreach ($games as $key => $game) {
			$query = $db->getQuery(true);
			$query->select('hb_spiel_spieler.alias as alias, name, tore, hb_mannschaft_spieler.tw as tw');
			$query->from('hb_spiel');
			$query->leftJoin($db->qn('hb_spiel_spieler').' USING ('.$db->qn('spielIDhvw').')');
			$query->leftJoin($db->qn('hb_mannschaft_spieler').' ON ( hb_mannschaft_spieler.kuerzel = hb_spiel.kuerzel'.
				' AND hb_mannschaft_spieler.alias = hb_spiel_spieler.alias)');
			$query->leftJoin($db->qn('#__contact_details').' ON zjdb_contact_details.alias = hb_spiel_spieler.alias');
			$query->where('hb_spiel.'.$db->qn('spielIDhvw').' = '.$db->q($game->spielIDhvw));
			$query->where($db->qn('tore').' IS NOT NULL');
			$query->group('hb_spiel_spieler.alias');
			//echo '=> model->$query <br><pre>'.$query.'</pre>';
			$db->setQuery($query);
			$players = $db->loadObjectList();
			//echo '=> model->player<br><pre>'; print_r($players); echo '</pre>';
			$gameName = $game->heim.'&'.$game->gast;
			$pattern = '/(.*)&(TSV Geislingen)/';
			$replacement = '${1} (A)';
			$gameName = preg_replace($pattern, $replacement, $gameName);
			$pattern = '/(TSV Geislingen)&(.*)/';
			$replacement = '${2} (H)';
			$gameName = preg_replace($pattern, $replacement, $gameName);
			
			//$gameName = $game->spielIDhvw; 
			$data['game'][]= $gameName; 
			foreach ($players as $player) {
				$player->tore = (int) $player->tore;
				//$data[str_replace('-','_',$player->alias)][] = 
				$goalkeeper = '';
				if ($player->tw == true) $goalkeeper = ' (TW)';
				$data[$player->name.$goalkeeper][] = 
					array('x' => $gameName, 'y' => $player->tore); 
			} 
			foreach ($data as $key => $value) {
				if ($key !== 'game') 
				{
					$prev = 0;
					foreach ($value as $valGame => $valGoal) 
					{
						$data[$key][$valGame]['y2'] = $valGoal['y'] + $prev;
						$prev = $valGoal['y'] + $prev;
					}
				}
			} 
		}
		//echo '=> model->data<br><pre>'; print_r($data); echo '</pre>';
		return $data;
	}
}