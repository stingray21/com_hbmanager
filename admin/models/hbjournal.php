<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';

class HBmanagerModelHbjournal extends HBmanagerModelHbprevnext
{	
	private $reports = array();
	private $previews = array();
	
	function __construct() {
		parent::__construct();
		
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}
	
	function getGameDates()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT DATE(datumZeit) AS datum');
		$query->from('hb_spiel');
		$query->order($db->qn('datum').' ASC');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->prevStart).' AND '.
				$db->q($this->dates->prevEnd));
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$dates = $db->loadColumn();
		//echo "<pre>"; print_r($dates); echo "</pre>";
		
		return self::formatGameDates($dates);
	}
	
	function formatGameDates($dates) 
	{
		for ($i = 0; $i < count($dates); $i++)
		{
			$currDate = strtotime($dates[$i]);
			$weekend = null;
			if (isset($dates[$i+1])) 
			{
				$weekend = self::formatForWeekend($currDate, 
					strtotime($dates[$i+1]));
			}
			
			if ($weekend !== null) {
				$gameDates[] = $weekend;
				$i++;
			}
			else {
				$gameDates[] = JHtml::_('date', $currDate, 'D, d.m.y', false);
			}
		}
		//echo __FUNCTION__."<pre>"; print_r($gameDates); echo "</pre>";

		return $gameDates;
	}
	
	protected function formatForWeekend($currDate, $nextDate)
	{
		if (strftime("%w", $currDate) == 6 AND 
			strftime("%w", $nextDate) == 0)
		{
			// if same month
			if (strftime("%m", $currDate) == strftime("%m", $nextDate))
			{
				$date = JHTML::_('date', $currDate , 'j.', 'Europe/Berlin').
					JHTML::_('date', $nextDate , '/j. M.', 'Europe/Berlin');
			}
			else
			{
				$date = JHTML::_('date', $currDate , 'j. M.', 'Europe/Berlin').
					JHTML::_('date', $nextDate , ' / j. M.', 'Europe/Berlin');
			}
			return 'Wochenende '.$date;
		}
		return null;
	}

	
	function getSectionRecentGames()
	{
		//echo __FUNCTION__."<pre>"; print_r($this->prevGames); echo "</pre>";
		$data = null;
		if (empty($this->prevGames)) {	
			return $data;
		}	
		$gameDates = self::getGameDates();
		
		if (count($gameDates) == 1) {
			$data['headline'] = 'Alle Spiele des letzten Spieltags';
		}
		else {
			$data['headline'] = 'Alle Spiele der letzten Spieltage';
		}
		$data['games'] = self::getSectionRecentGames_Games();
		$data['games'] = self::formatTeamNames($data['games']);
		//echo __FUNCTION__."<pre>"; print_r($data); echo "</pre>";
		return $data;
	}
	
	protected function formatTeamNames($games) {
		$games = str_replace(array(". Mannschaft","liche"),
				array("", "l."), $games);
		$games = preg_replace('/(männl.|weibl.) ([A-D]-Jugend)/',
				'$2 $1', $games);
		$games = preg_replace('/(gemischte) (E-Jugend)/', '$2', $games);
		return $games;
	}


	protected function getSectionRecentGames_Games() 
	{
		$currTeam = null;
		$i = -1;			
		foreach ($this->prevGames as $date => $games)
		{
			$data['games'][++$i] = JHtml::_('date', $date, 
						'D, d.m.y', false)."\n";
			foreach ($games as $game) 
			{	
				if ($currTeam != $game->mannschaft) {
					$data['games'][$i] .= $game->mannschaft.
						" (".$game->ligaKuerzel.")\n";
					$currTeam = $game->mannschaft;
				}
				$data['games'][$i] .= "&nbsp;&nbsp;";
				$data['games'][$i] .= $game->heim." - ".$game->gast;
				$data['games'][$i] .= "&nbsp;&nbsp;&nbsp;".$game->toreHeim.
					':'.$game->toreGast."\n";
			}
		}
		return $data['games'];
	}
	

	function getSectionUpcomingGames()
	{
		$data = array();
		$currTeam = null;
		//echo __FUNCTION__."<pre>"; print_r($this->nextGames); echo "</pre>";
		if (!empty($this->nextGames))
		{
			$i = -1;			
			$data['headline'] = 'Alle Spiele vom nächsten Spieltag (chronologisch)';
			foreach ($this->nextGames as $date => $games)
			{
				$data['games'][++$i] = JHtml::_('date', $date, 
							'D, d.m.y', false)."\n";
				foreach ($games as $game)
				{
					//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
					
					if ($currTeam != $game->mannschaft) {
						$data['games'][$i] .= $game->mannschaft. " (".
								$game->ligaKuerzel.")\n";
						$currTeam = $game->mannschaft;
					}
					$data['games'][$i] .= "&nbsp;&nbsp;";
					$data['games'][$i] .= $game->zeit." Uhr ".
							"&nbsp;&nbsp;"."\t".$game->heim." - ".$game->gast."\n";
				}
			}
		}
		$data['games'] = self::formatTeamNames($data['games']);
		//echo __FUNCTION__."<pre>"; print_r($data); echo "</pre>";
		return $data;
	}
	
	
	function getReports()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').
				' USING ('.$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_spielbericht').
				' USING ('.$db->qn('spielIDhvw').')');
		$query->where('( DATE('.$db->qn('datumZeit').') BETWEEN '.
					$db->q($this->dates->prevStart).' AND '.
					$db->q($this->dates->prevEnd).')'.
				' AND ('.$db->qn('bericht').' IS NOT NULL OR '.
					$db->qn('spielerliste').' IS NOT NULL)');
		$query->order($db->qn('reihenfolge').' DESC');
		//echo __FUNCTION__.':<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo __FUNCTION__."<pre>"; print_r($dates); echo "</pre>";
		
		$db->setQuery($query);
		$dates = $db->loadColumn();
		
		return $this->reports = $games;
	}
	
	function getSectionReports()
	{
		$data = array();
		
		if (!empty($this->reports))
		{	
			$i = 0;
			foreach ($this->reports as $report)
			{	
				$data[$i]['headline'] = $report->mannschaft.' - '.$report->liga;
				//$data[$i]['headline'] .= '('.$report->ligaKuerzel.')';
				$data[$i]['result'] = "{$report->heim} - {$report->gast}".
						"&nbsp;&nbsp;\t{$report->toreHeim}:{$report->toreGast}";
				$data[$i]['text'] = $report->bericht;
				if (!empty($report->spielerliste)) {
					$data[$i]['lineup'] = $report->spielerliste;
				}
				if (!empty($report->zusatz)) {
					$data[$i]['add'] = $report->zusatz;
				}
				$i++;
			}
			//echo __FUNCTION__."<pre>"; print_r($data); echo "</pre>";
		}
		return $data;
	}
	
	function getPreviews()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').
			' USING ('.$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_spielvorschau').
			' USING ('.$db->qn('spielIDhvw').')');
		$query->where('( DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->nextStart).' AND '.
				$db->q($this->dates->nextEnd).')'
			.' AND ('.$db->qn('vorschau').' IS NOT NULL)');
		$query->order($db->qn('reihenfolge').' DESC');
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		return $this->previews = $games;
	}
	
	function getSectionPreview()
	{
		
		$data = array();
		
		if (!empty($this->previews))
		{
			$i = 0;
			foreach ($this->previews as $preview)
			{
				$data[$i]['headline'] = $preview->mannschaft.' - '.
					$preview->liga.' ('.$preview->ligaKuerzel.')';
				$data[$i]['game'] = substr($preview->uhrzeit,0,5).
					" Uhr \t {$preview->heim} - {$preview->gast}";
				if (!empty($preview->treffOrt) AND !empty($preview->treffZeit))
				{
					$data[$i]['meetup'] = "Treffpunkt: ".$preview->treffOrt.
						" um ".$preview->treffZeit." Uhr";
				}
				$data[$i]['text'] = nl2br($preview->vorschau);
				
			}
		}
		//echo "<pre>"; print_r($data); echo "</pre>";
		return $data;
	}
	
	function getSectionTop($link = true)
	{
		$data['headline'] = 'Abt. Handball';
		
		if ($link)
		{
			$data['link'] = "Aktuellere und ausführlichere Informationen "
					. "auf unserer Homepage: \n";
			$data['link'] .= "www.handball.tsv-geislingen.de";
		}
		
		return $data;
	}
	

}


