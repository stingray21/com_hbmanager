<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';

class hbmanagerModelHbOverview extends HBmanagerModelHbprevnext
{	
	protected $currGames = array();
	
	function __construct() 
	{
		parent::__construct();
		
		$this->dates->currStart = null;
		$this->dates->currEnd = null;
		
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
	
	function setDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		parent::setdates();
		$dates = $this->dates;
		if (strtotime($dates->today) >= strtotime($dates->nextStart) &&
				strtotime($dates->today) <= strtotime($dates->nextEnd) )
		{
			//echo 'current games';
			$this->dates->currStart = $dates->nextStart;
			$this->dates->currEnd = $dates->nextEnd;
			$this->dates->nextStart = self::getNextGameDate($dates->nextEnd);
			$this->dates->nextEnd = self::getNextGameEndDate();
		}
		
		//echo __FUNCTION__.':<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function getSchedule($team)
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '
				. $db->q('%k:%i').') AS '.$db->qn('zeit').', '
				.'IF('.$db->qn('heim').' IN '.self::getTeamNames().',1,2) '.
					'AS mark');
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where($db->qn('Kuerzel').' = '.$db->q($team->kuerzel));
		$query->order($db->qn('datumZeit'));
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$rows = self::addRowColor($rows);
		//echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";
		return $rows;
	}

	function addRowColor($rows)
	{
		//echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";
		for ($i = 0; $i < count($rows); $i++)
		{
			$color = ($i % 2 == 1) ? 'even' : 'odd';
			$rows[$i]->background = $color;
		}
		return $rows;
	}
	
	function getTeamArray()
	{
		$teams = self::getTeams();
		foreach ($teams as $team)
		{
			$team->schedule = self::getSchedule($team);
		}
		return $teams;
	}

	
	
	function getTeamNames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT '.$db->qn('nameKurz'));
		$query->from('hb_mannschaft');
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$names = $db->loadColumn();
		foreach ($names as $key => $name){
			$names[$key] = $db->q($name);
		}
		$result = '';
		if (!empty($names)) {
			$result = '('.implode(' , ', $names).')';
		}
		//echo __FUNCTION__."<pre>"; print_r($result); echo "</pre>";
		return $result;
	}
	
	function getCurrGames($arrange = true, $combined = false)
	{
		if ($this->dates->currStart === null) {
			return null;
		}
		
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		if ($combined) {
			$select = self::getCombinedSelect();
		}
		else {
			$select = '*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit'); 
		}
		// %H:%m hour with leading 0
		$query->select($select);
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
			$db->qn('kuerzel').')');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '
			.$db->q($this->dates->currStart).' AND '
			.$db->q($this->dates->currEnd));
		if ($combined) {	
			$query->group($db->qn('kuerzel').',DATE('.$db->qn('datumZeit').')'
				.', '.$db->qn('heim').', '.$db->qn('gast') );
		}
		$query->order($db->qn('datum').', '.$db->qn('zeit').' ASC');
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		if ($arrange){
			return $this->currGames = self::arrangeGamesByDate($games);
		}
		
		return $this->currGames = $games;
	}
	
	function getHomeGames()
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit'));
		$query->from($db->qn('hb_spiel'));
		$teamNames = self::getTeamNames();
		if (!empty($teamNames)) {
			$query->where($db->qn('Heim').' IN '.$teamNames);
		}
		$query->where($db->qn('hallenNr').' IN (7005, 7014, 7003)');
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->join('INNER',$db->qn('hb_halle').' USING ('.$db->qn('hallenNr').')');
		$query->order($db->qn('datumZeit'));
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$rows = self::addRowColor($rows);
		//echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";
		$arangedRows = self::arangeGames($rows);
		//echo __FUNCTION__."<pre>"; print_r($arangedRows); echo "</pre>";
		return $arangedRows;
	}
	
	function arangeGames($inputRows)
	{
		$rows = '';
		foreach ($inputRows as $value)
		{
			$rows[$value->datum][$value->hallenNr][] = $value;
		}
		//echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";
		return $rows;
	}
	
	function getPrevGames($arrange = true, $combined = false, $reports = false, $all = false) 
	{
		$prevGames = parent::getPrevGames(1,0,1,1);
		foreach ($prevGames as $date => $games)
		{
			foreach ($games as $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
				$game->ergebnis = self::getGameResult($game);
				$game->eigeneMannschaft = self::getOwnTeam($game);
				$game->anzeige = self::getIndicator($game);			
			}
		}
		//echo __FUNCTION__."<pre>"; print_r($prevGames); echo "</pre>";
		return $this->prevGames = $prevGames;
	}
	
	protected function getGameResult($game)
	{
		if ($game->wertungHeim > $game->wertungGast) {
			$result = 1;
		} 
		elseif ($game->wertungHeim < $game->wertungGast) {
			$result = 2;
		} 
		elseif ($game->wertungHeim == $game->wertungGast && $game->wertungHeim !== null ) {
			$result = 0;
		}
		else {
			$result = null;
		}
		return $result;
	}	
	
	protected function getOwnTeam($game)
	{
		if ($game->heim == $game->nameKurz) {
			$ownTeam = 1;
		}
		elseif ($game->gast == $game->nameKurz) {
			$ownTeam = 2;
		}
		else {
			$ownTeam = null;
		}
		return $ownTeam;
	}
	
	protected function getIndicator($game) {
		if ($game->ergebnis === $game->eigeneMannschaft && 
				$game->ergebnis !== null) {
			$indicator = 'win';
		}
		elseif ($game->ergebnis !== $game->eigeneMannschaft && 
				$game->ergebnis !== null && $game->ergebnis !== 0) {
			$indicator = 'loss';
		}
		elseif ($game->ergebnis === 0) {
			$indicator = 'tied';
		}
		else {
			$indicator = 'blank';
		}
		return $indicator;
	}
	
	function getNextGames() 
	{
		$nextGames = parent::getNextGames(1,0,1);
		foreach ($nextGames as $date => $games)
		{
			foreach ($games as $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
				$game->eigeneMannschaft = self::getOwnTeam($game);	
			}
		}
		//echo __FUNCTION__."<pre>"; print_r($nextGames); echo "</pre>";
		return $this->nextGames = $nextGames;
	}
}