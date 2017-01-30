<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelHBtest extends JModelLegacy
{
	private $teamkey;
	private $season;
	private $recentGameDate;
	
	function __construct() 
	{
		parent::__construct();
		
		self::setData();			
	}
	
	public function setData() {
		$this->teamkey = 'M-1';
		$this->season = '2015-2016';
		self::setRecentGame();
	}
	
	private function setRecentGame()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('spielIdHvw, datumZeit');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
		$query->group($db->qn('spielIdHvw'));
		$query->where('hb_spiel_spieler.'.$db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('eigenerVerein').' = 1');
		$query->where('DATE('.$db->qn('datumZeit').') < NOW() ');
		$query->order($db->qn('datumZeit').' DESC');
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$game = $db->loadObject();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($game);echo'</pre>';
		$this->recentGameDate = JHtml::_('date', $game->datumZeit, 'Y-m-d');
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->recentGameDate);echo'</pre>';
		$this->recentGameId = $game->spielIdHvw;
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->recentGameId);echo'</pre>';
		return $game;
	}

	public function getPlayers($gameDate = null) {
		if ($gameDate === null) { $gameDate = $this->recentGameDate; }		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('( '.self::getListOfPlayersQuery().' ) AS '.$db->qn('spieler'));
		$query->leftJoin($db->qn('hb_spiel_spieler').' ON spieler.alias=hb_spiel_spieler.alias AND spielIdHvw='.$db->q($this->recentGameId));		
		$query->leftJoin('( '.self::getStatisticalDataQuery($gameDate).' ) AS '.$db->qn('stats').' ON spieler.alias=stats.alias');

		echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$players = $db->loadObjectList();
		//$players = $db->loadColumn();
		echo __FILE__.' ('.__LINE__.')<pre>';print_r($players);echo'</pre>';
		return $players;
	}
	
	protected function getListOfPlayersQuery() {
		$db = $this->getDbo();
		// query for list of all players of a team 
		// from hb_spiel_spieler and hb_mannschaft_spieler
		$query = "SELECT `alias`, `kuerzel`, `saison`, `name`, `trikotNr`, `trainer`, `TW`, `LA`, `RL`, `RM`, `RR`, `RA`, `KM` FROM (
			(SELECT alias, kuerzel, saison
				FROM hb_spiel_spieler
				WHERE kuerzel = ".$db->q($this->teamkey)." AND saison = ".$db->q($this->season)." AND
				`trikotNr` NOT IN ('A','B','C','D') )
				UNION
				(SELECT alias, kuerzel, saison
				FROM hb_mannschaft_spieler
				WHERE kuerzel = ".$db->q($this->teamkey)." AND saison = ".$db->q($this->season)." )
			) AS `spielerliste`
			LEFT JOIN #__contact_details USING (`alias`)
			LEFT JOIN hb_mannschaft_spieler USING (`alias`, `kuerzel`, `saison`)
			ORDER BY `alias` ASC";
		
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		return $query;
	}
	
	protected function getStatisticalDataQuery($gameDate = null) {
		if ($gameDate === null) {
			echo '<p>NO DATE</p>';
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('alias, '
			. 'count(trikotNr NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').') ) AS spiele, '
			. 'sum(tore) AS toregesamt,'
			. ' ROUND(sum(tore) / count(tore), 1) AS quote');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').','.$db->qn('saison').','.$db->qn('kuerzel').')');
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->where('kuerzel = '.$db->q($this->teamkey));
		$query->where($db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$query->where('DATE('.$db->qn('datumZeit').') <= '.$db->q($gameDate));
		$query->group('alias');

		//echo __FILE__.' ('.__LINE__.')<p>'.$query.'</p>';
		return $query;
	}

}