<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHbprevnext extends JModelLegacy
{	
	protected $prevGames = array();
	protected $nextGames = array();
	
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
		//echo $this->dates->today = "2014-10-16";
		
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
	
	function setPrevGamesDates()
	{
		// earlist game of the previous week
		$date = self::getEarliestGameDate_LastWeek();
		// if no game date from previous week, get the most recent game date
		if (empty($date)) {
			$date = self::getGameDateBeforeLastWeek();
		}
		if (empty($date)) {
			$date = strftime("%Y-%m-%d",strtotime('last Monday', 
					strtotime('last saturday', strtotime($this->dates->today))));
		}
		$this->dates->prevStart = $date;
		$this->dates->prevEnd = self::getMostRecentGameDate();
	}
		
	function getEarliestGameDate_LastWeek()
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dates->today);echo'</pre>';
		$db = $this->getDbo();
		
		// earlist game of the previous week
		$query = $db->getQuery(true);
		$query->select('DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q(strftime("%Y-%m-%d", strtotime('last Monday', 
					strtotime('last saturday', strtotime($this->dates->today))))).
				' AND ' . $db->q($this->dates->today) );
		$query->order($db->qn('datumZeit').' ASC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}

	
	function getGameDateBeforeLastWeek()
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dateToday);echo'</pre>';
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') < '.
					$db->q(strftime("%Y-%m-%d", strtotime('last Monday', 
						strtotime('last saturday', strtotime($this->dates->today))
					))) 
				);
		$query->order($db->qn('datumZeit').' DESC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	
	function getMostRecentGameDate()
	{
		$db = $this->getDbo();
		
		// earlist game of the previous week
		$query = $db->getQuery(true);
		$query->select('DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->prevStart).' AND '.
				$db->q(strftime("%Y-%m-%d", strtotime('next Tuesday', 
					strtotime('last Saturday', 
						strtotime($this->dates->today))
					)))
				);
		$query->order($db->qn('datumZeit').' DESC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	
	function setNextGamesDates()
	{
		// earlist game of the previous week
		$date = self::getEarliestGameDate_ThisWeek();
		// if no game date from previous week, get the most recent game date
		if (empty($date)) {
			$date = self::getNextGameDate();
		}
		if (empty($date)) {
			$date = strftime("%Y-%m-%d",strtotime('next Monday', 
					strtotime('last friday', strtotime($this->dates->today))));
		}
		$this->dates->nextStart = $date;
		$this->dates->nextEnd = self::getNextGameEndDate();
	}
	
	function getEarliestGameDate_ThisWeek()
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dateToday);echo'</pre>';
		$db = $this->getDbo();
		
		// earlist game of the this week
		$query = $db->getQuery(true);
		$query->select('DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->today).' AND ' .  
				$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
					strtotime('next friday', strtotime($this->dates->today)))))
					);
		$query->order($db->qn('datumZeit').' ASC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	function getNextGameDate($offset = null)
	{
		if ($offset === null) {
			$offset = $this->dates->today;
		}
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($offset);echo'</pre>';
		$db = $this->getDbo();
		
		// earlist game of the this week
		$query = $db->getQuery(true);
		$query->select('DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where('DATE('.$db->qn('datumZeit').') > '.
				$db->q($offset));
		$query->order($db->qn('datumZeit').' ASC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}
	
	function getNextGameEndDate()
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dateToday);echo'</pre>';
		$db = $this->getDbo();
		
		// get last game date after next game (1 week time frame)
		$query = $db->getQuery(true);
		$query->select('DISTINCT DATE('.$db->qn('datumZeit').') AS '.
				$db->qn('datum'));
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->nextStart).' AND ' .  
				$db->q(strftime("%Y-%m-%d", strtotime('next Sunday',
						strtotime($this->dates->nextStart)
					)))
				);
		$query->order($db->qn('datumZeit').' DESC');
		//$query->setLimit(1);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);		
		//$date = $db->loadColumn();
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
			$select = '*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit'); 
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
		$query->order($db->qn('datum').', '.$db->qn('reihenfolge').' ASC');
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
		$select = '*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum').
				', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit').',
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
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit'));
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
				$db->qn('kuerzel').')');
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
			$arranged[$game->datum][] = $game;
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
			$titledate = JHtml::_('date', $minDate, 'D, j. M.', 'Europe/Berlin');
		}
		// back to back days and weekend
		elseif (strftime("%j", $minDate)+1 == strftime("%j", $maxDate) AND
			(strftime("%w", $minDate) == 6 AND strftime("%w", $maxDate) == 0) )
		{
			// if same month
			if (strftime("%m", $minDate) == strftime("%m", $maxDate))
			{
				$date = JHTML::_('date', $minDate , 'j.', 'Europe/Berlin').
					JHTML::_('date', $maxDate , '/j. M.', 'Europe/Berlin');
			}
			else
			{
				$date = JHTML::_('date', $minDate , 'j. F.', 'Europe/Berlin').
					JHTML::_('date', $maxDate , ' / j. F.', 'Europe/Berlin');
			}
			$titledate = 'Wochenende '.$date;
		}
		else
		{
			$titledate = JHtml::_('date', $minDate, 'j. ', 'Europe/Berlin');
			if (strftime("%m", $minDate) !== strftime("%m", $maxDate)) {
				$titledate .= JHtml::_('date', $minDate, 'F. ', 'Europe/Berlin');
			}
			$titledate .= 'bis ';
			$titledate .= JHtml::_('date', $maxDate, 'j. F.', 
				'Europe/Berlin');
		}
		
		return $titledate;
	}
	
}


