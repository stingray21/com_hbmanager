<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHbprevnext extends JModelLegacy
{	
	protected $prevGames = array();
	protected $nextGames = array();
	protected $timezone = 'Europe/Berlin';
	
	// TODO use CONVERT_TZ in MySQL for date
	
	// dates
	protected $dates = null;
	
	function __construct() {
		parent::__construct();
		
		$this->dates = new stdClass();
		$this->dates->prevStart = null;
		$this->dates->prevEnd = null;
		$this->dates->nextStart = null;
		$this->dates->nextEnd = null;	
		
		$this->dates->today = strftime("%Y-%m-%d", time());
		
		//$this->dates->today = "2015-10-24"; echo '<div><b>TESTING: date set to '.$this->dates->today.'</b></div>';
//		echo '<pre>'.strftime("%A %w, %Y-%m-%d", strtotime($this->dates->today)).'</pre>';
		//self::setDates();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}
	
	public function getTeams()
	{	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
	}

	public function getDates()
	{
		if (empty($this->dates->nextStart) && empty($this->dates->prevStart)) {
			//echo 'no dates';
			self::setDates();
		}
		
		$dates['nextStart'] = $this->dates->nextStart;
		$dates['nextEnd'] = $this->dates->nextEnd;
		$dates['prevStart'] = $this->dates->prevStart;
		$dates['prevEnd'] = $this->dates->prevEnd;
		
		return $dates;
	}
	
	function setDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		
		// previous games dates
		self::setPrevDates($dates = null);
		
		// upcoming games dates
		self::setNextDates($dates = null);
		//echo __FUNCTION__.':<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function setPrevDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		
		// previous games dates
		if (is_null($dates['prevStart']) || is_null($dates['prevEnd'])) {
			self::setPrevGamesDates();
		}
		else {
			$this->dates->prevStart	= $dates['prevStart'];
			$this->dates->prevEnd	= $dates['prevEnd'];
		}
		//echo __FUNCTION__.':<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function setNextDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		
		// upcoming games dates
		if (is_null($dates['nextStart']) || is_null($dates['nextEnd']))	{
			self::setNextGamesDates();
		}
		else {
			$this->dates->nextStart	= $dates['nextStart'];
			$this->dates->nextEnd	= $dates['nextEnd'];
		}
		//echo __FUNCTION__.':<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function setPrevGamesDates($offset = null)
	{
		$date = self::setPrevEndDate($offset);
		if (empty($date)) {
			$date = strftime("%Y-%m-%d",strtotime('next Monday', 
					strtotime('last Friday', strtotime($this->dates->today))));
		}
		$this->dates->prevEnd = $date;
		$this->dates->prevStart = self::setPrevStartDate();
	}
	
	function setPrevEndDate($offset = null)
	{
		if ($offset === null) {
			$offset = $this->dates->today;
		}
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($offset);echo'</pre>';
		$db = $this->getDbo();  
		
		$query = $db->getQuery(true);
		$query->select('MAX(DATE('.$db->qn('datumZeit').')) AS '
					.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') <= '.
					$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
						strtotime('last Friday', strtotime($offset))
					))) 
				);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	function setPrevStartDate()
	{
		$offset = $this->dates->prevEnd;
		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('MIN(DATE('.$db->qn('datumZeit').')) AS '
					.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
					$db->q(strftime("%Y-%m-%d", strtotime('last Monday', 
					strtotime($offset) ) ) ).' AND '.$db->q($offset)
				);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
		
	
	function setNextGamesDates($offset = null)
	{
		$date = self::setNextStartDate($offset);
		if (empty($date)) {
			$date = strftime("%Y-%m-%d",strtotime('next Tuesday', 
					strtotime('last Friday', strtotime($this->dates->today))));
		}
		$this->dates->nextStart = $date;
		$this->dates->nextEnd = self::setNextEndDate();
	}
	
	function setNextStartDate($offset = null)
	{
		if ($offset === null) {
			$offset = $this->dates->today;
		}
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($offset);echo'</pre>';
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('MIN(DATE('.$db->qn('datumZeit').')) AS '
					.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') >= '.
					$db->q(strftime("%Y-%m-%d", strtotime('next Tuesday', 
						strtotime('last Friday', strtotime($offset))
					))) 
				);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	function setNextEndDate()
	{
		$offset = $this->dates->nextStart;
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(DATE('.$db->qn('datumZeit').')) AS '
					.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.$db->q($offset).
					' AND '.$db->q(strftime("%Y-%m-%d", strtotime('next Sunday', 
							strtotime('last Monday', strtotime($offset) )
					) ) )
				);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	function getPrevGames($arrange = true, $combined = false, $reports = false, $all = false)
	{
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
		if ($reports) {	
			$query->leftJoin($db->qn('hb_spielbericht').
				' USING ('.$db->qn('spielIdHvw').')');
		}
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '
			.$db->q($this->dates->prevStart).' AND '
			.$db->q($this->dates->prevEnd));
		if (!$all) $query->where($db->qn('toreHeim').' IS NOT NULL');
		if ($combined) {	
			$query->group($db->qn('kuerzel').',DATE('.$db->qn('datumZeit').')'
				.', '.$db->qn('heim').', '.$db->qn('gast') );
		}
		$query->order($db->qn('datumZeit').', '.$db->qn('reihenfolge').' ASC');
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		if ($arrange){
			return $this->prevGames = self::arrangeGamesByDate($games);
		}
		
		return $this->prevGames = $games;
	}
	
	function getCombinedSelect()
	{
		$db = $this->getDbo();
		$select = '*,
					CASE 
					WHEN (SUBSTRING('.$db->qn('kuerzel').',1,3) = '
						.$db->q('gJE').') THEN
						SUM( 
							CASE 
							WHEN '.$db->qn('toreHeim').' > '.$db->qn('toreGast')
								.' THEN 2 
							WHEN '.$db->qn('toreHeim').' = '.$db->qn('toreGast')
								.' THEN 1 
							ELSE 0 
						END) 
					ELSE 
						'.$db->qn('toreHeim').'
					END AS '.$db->qn('toreHeim').',
					CASE 
					WHEN (SUBSTRING('.$db->qn('kuerzel').',1,3) = '
						.$db->q('gJE').') THEN
						SUM( 
							CASE 
							WHEN '.$db->qn('toreHeim').' < '.$db->qn('toreGast')
								.' THEN 2 
							WHEN '.$db->qn('toreHeim').' = '.$db->qn('toreGast')
								.' THEN 1 
							ELSE 0 
						END) 
					ELSE 
						'.$db->qn('toreGast').'
					END AS '.$db->qn('toreGast');
		
		return $select;
	}
	
	function getNextGames($arrange = true, $combined = false, $previews = false)
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
				$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_halle').' USING ('.
				$db->qn('hallenNr').')');
		if ($previews) {	
			$query->leftJoin($db->qn('hb_spielvorschau').
				' USING ('.$db->qn('spielIdHvw').')');
		}
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->nextStart).' AND '.
				$db->q($this->dates->nextEnd));
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
			return $this->nextGames = self::arrangeGamesByDate($games);
		}
		return $this->nextGames = $games;
	}
	
	public function arrangeGamesByDate($games) {
		// arrange games by date
		$arranged = array();
		foreach ($games as $game){
			$date = JHtml::_('date', $game->datumZeit , 'Y-m-d', $this->timezone);
			$arranged[$date][] = $game;
		}
		//echo __FUNCTION__.':<pre>';print_r($arranged);echo'</pre>';
		return $arranged;
	}
	
	protected function getTitleDate($minDateStr, $maxDateStr)
	{
		//echo __FUNCTION__.':<pre>'.$minDateStr."\n".$maxDateStr.'</pre>';
		// format date
		$minDate = strtotime($minDateStr);
		$maxDate = strtotime($maxDateStr);
		if ($minDate === $maxDate)
		{
			$titledate = JHtml::_('date', $minDate, 'D, j. M.', $this->timezone);
		}
		// back to back days and weekend
		elseif (strftime("%j", $minDate)+1 == strftime("%j", $maxDate) AND
			(strftime("%w", $minDate) == 6 AND strftime("%w", $maxDate) == 0) )
		{
			// if same month
			if (strftime("%m", $minDate) == strftime("%m", $maxDate))
			{
				$date = JHTML::_('date', $minDate , 'j.', $this->timezone).
					JHTML::_('date', $maxDate , '/j. M.', $this->timezone);
			}
			else
			{
				$date = JHTML::_('date', $minDate , 'j. F.', $this->timezone).
					JHTML::_('date', $maxDate , ' / j. F.', $this->timezone);
			}
			$titledate = 'Wochenende '.$date;
		}
		else
		{
			$titledate = JHtml::_('date', $minDate, 'j. ', $this->timezone);
			if (strftime("%m", $minDate) !== strftime("%m", $maxDate)) {
				$titledate .= JHtml::_('date', $minDate, 'F. ', $this->timezone);
			}
			$titledate .= 'bis ';
			$titledate .= JHtml::_('date', $maxDate, 'j. F.', 
				$this->timezone);
		}
		
		return $titledate;
	}
	
	protected function addCssInfo($gameDays)
	{
		
		if (preg_match('/\d{4}-\d{2}-\d{2}/' , key($gameDays))) {
			//echo "DATES";
			foreach ($gameDays as $date => $games)
			{
				foreach ($games as $game)
				{
					//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
					$game->ergebnis = self::getGameResult($game);
					$game->eigeneMannschaft = self::getOwnTeam($game);
					$game->anzeige = self::getIndicator($game);			
				}
			}
		} else {
			//echo "NO DATES";
			foreach ($gameDays as $game)
			{
				//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
				$game->ergebnis = self::getGameResult($game);
				$game->eigeneMannschaft = self::getOwnTeam($game);
				$game->anzeige = self::getIndicator($game);			
			}
		}
		return $gameDays;
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
}


