<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';

class hbmanagerModelHbOverview extends HBmanagerModelHbprevnext
{	
	protected $currGames = array();
	private $season;
	protected $timezoneMode;
	
	function __construct() 
	{
		parent::__construct();
		
		$this->dates->currStart = null;
		$this->dates->currEnd = null;
		
		// TODO time zone -> backend option
		$this->timezoneMode = false; //true: user-time, false:server-time
		
		self::setSeason();
	}
	
	protected function setSeason()
    {
		$year = strftime('%Y');
		if (strftime('%m') < 8) {
			$year = $year-1;
		}
		
		$season = $year."-".($year+1);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($season);echo'</pre>';
		$this->season = $season;
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
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		return $teams;
	}
	
	function setDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		parent::setdates();
		$dates = self::getCurrentGameDates();
		if (!empty($dates->start) && !empty($dates->ende) )
		{	
			self::setPrevGamesDates( strftime("%Y-%m-%d", 
					strtotime('last Sunday', strtotime($dates->start) ) ) );
			self::setNextGamesDates( strftime("%Y-%m-%d", 
					strtotime('this Sunday', strtotime($dates->ende) ) ) );
			$this->dates->currStart = $dates->start;
			$this->dates->currEnd = $dates->ende;
		}
		
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function getSchedule($team)
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, '
				.'IF('.$db->qn('heim').' IN '.self::getTeamNames().',1,2) '.
					'AS mark');
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where($db->qn('Kuerzel').' = '.$db->q($team->kuerzel));
		$query->where($db->qn('saison').' = '.$db->q($this->season));
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
	
	function getGameDays()
	{
		// current games
		$currGames = self::getCurrGames();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($currGames);echo'</pre>';
		if (!empty($currGames))
		{
			$gameDay = new stdClass();
			$gameDay->games = $currGames;
			$gameDay->shortVar = 'curr';
			$gameDay->languageVar = 'CURRENT';
			$gameDays[] = $gameDay;
		}
		// previous games
		$prevGames = self::getPrevGames();
		//echo __FUNCTION__."<pre>"; print_r($prevGames); echo "</pre>";
		if (!empty($prevGames))
		{
			$gameDay = new stdClass();
			$gameDay->games = $prevGames;
			$gameDay->shortVar = 'prev';
			$gameDay->languageVar = 'RECENT';
			$gameDays[] = $gameDay;
		}
		// next games
		$nextGames = self::getNextGames();
		//echo __FUNCTION__."<pre>"; print_r($nextGames); echo "</pre>";
		if (!empty($nextGames))
		{
			$gameDay = new stdClass();
			$gameDay->games = $nextGames;
			$gameDay->shortVar = 'next';
			$gameDay->languageVar = 'UPCOMING';
			$gameDays[] = $gameDay;
		}
		//echo __FUNCTION__."<pre>"; print_r($gameDays); echo "</pre>";
		return $gameDays;
	}
	
	function getCurrentGameDates()
	{
		//echo __FUNCTION__.':<pre>';print_r($this->dates->today);echo'</pre>';
		$db = $this->getDbo();
		// if not Friday-Sunday abort
		$today = strftime("%w", strtotime($this->dates->today));
		if ($today < 5 && $today != 0) {
			return;
		}
		// earlist game of the this week
		$query = $db->getQuery(true);
		$query->select('MIN(DATE('.$db->qn('datumZeit').')) AS '
				.$db->qn('start').
				', MAX(DATE('.$db->qn('datumZeit').')) AS '
				.$db->qn('ende'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q(strftime("%Y-%m-%d", strtotime('last Thursday +1 day', 
					strtotime($this->dates->today)))).' AND ' .  
				$db->q(strftime("%Y-%m-%d", strtotime('last Thursday +4 day', 
					strtotime($this->dates->today))))
					);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$dates = $db->loadObject();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		return $dates;
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
			$select = '*'; 
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
		$query->order($db->qn('datumZeit').' ASC');
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		if ($arrange){
			$games = self::arrangeGamesByDate($games);
		}
		$games = self::addCssInfo($games);
		$games = self::addStandings($games);
		return $this->currGames = $games;
	}
	
		
	protected function addStandings($gameDays)
	{
		foreach ($gameDays as $date => $games)
		{
			foreach ($games as $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";	
				$game->standings = self::getDetailedStandings($game);
			}
		}
		return $gameDays;
	}
	
	function getHomeGames()
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('hb_spiel'));
		$teamNames = self::getTeamNames();
		//echo __FUNCTION__."<pre>"; print_r($teamNames); echo "</pre>";
		if (!empty($teamNames)) {
			$query->where($db->qn('Heim').' IN '.$teamNames);
		}
		$query->where($db->qn('hallenNr').' IN (7005, 7014, 7003)');
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->join('LEFT',$db->qn('hb_halle').' USING ('.$db->qn('hallenNr').')');
		$query->order($db->qn('datumZeit'));
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$rows = self::addRowColor($rows);
		// echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";die;
		$arangedRows = self::arangeGames($rows);
		// echo __FUNCTION__."<pre>"; print_r($arangedRows); echo "</pre>";die;
		return $arangedRows;
	}
	
	function arangeGames($inputRows)
	{
		// echo __FUNCTION__."<pre>"; print_r($inputRows); echo "</pre>";
		$rows = [];
		foreach ($inputRows as $value)
		{	
			// echo __FUNCTION__."<pre>"; print_r($value); echo "</pre>";
			$date = JHtml::_('date', $value->datumZeit, 'Y-m-d', $this->timezoneMode);
			$rows[$date][$value->hallenNr][] = $value;
			// echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";
		}
		// echo __FUNCTION__."<pre>"; print_r($rows); echo "</pre>";die;
		return $rows;
	}
	
	function getPrevGames($arrange = true, $combined = false, $reports = false, $all = false) 
	{
		$prevGames = parent::getPrevGames(1,0,1,1);
		//echo __FUNCTION__."<pre>"; print_r($prevGames); echo "</pre>";
		$prevGames = self::addCssInfo($prevGames);
		$prevGames = self::addStandings($prevGames);
		//echo __FUNCTION__."<pre>"; print_r($prevGames); echo "</pre>";
		return $this->prevGames = $prevGames;
	}
	
	function getNextGames($arrange = true, $combined = false, $reports = false) 
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
		$nextGames = self::addStandings($nextGames);
		//echo __FUNCTION__."<pre>"; print_r($nextGames); echo "</pre>";
		return $this->nextGames = $nextGames;
	}
	
	protected function addBackground ($standings)
	{
		$background = false;
		foreach ($standings as $row)
		{
			// switch color of background
			if (!empty($row->platz)) $background = !$background;
			// check value of background
			switch ($background) {
				case true: $row->background = 'odd'; break;
				case false: $row->background = 'even'; break;
			}
		}
		return $standings;
	}

	public function getDetailedStandings($team)
	{
		$db = JFactory::getDBO();
		// getting standings of the team from the DB
		$query = $db->getQuery(true);
		$query->select('*, '.
			'IF('.$db->qn('mannschaft').'='.$db->q($team->name).',1,0) AS heimVerein');
		$query->from($db->qn('hb_tabelle_details'));
		$query->where($db->qn('kuerzel').' = '.$db->q($team->kuerzel));
		$query->order($db->qn('platz'));
		//echo nl2br($query);//die; //see resulting query
		$db->setQuery($query);
		$standings = $db->loadObjectList();
		//echo "<pre>"; print_r($standings); echo "</pre>";
		//display and convert to HTML when SQL error
		if (is_null($posts=$db->loadRowList()))
		{
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			return;
		}
		$standings = self::addBackground($standings);
		return $standings;
	}
}