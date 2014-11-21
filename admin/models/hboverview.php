<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbOverview extends JModelLegacy
{	
	
	function __construct() 
	{
		parent::__construct();
		
		
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
	
	function getSchedule($team)
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%m').') AS '.$db->qn('zeit').', '.
				'IF('.$db->qn('heim').' IN '.self::getTeamNames().',1,2) '.
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
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$names = $db->loadColumn();
		foreach ($names as $key => $name){
			$names[$key] = $db->q($name);
		}
		$result = '('.implode(' , ', $names).')';
		return $result;
	}
	
	function getHomeGames()
	{
		// getting schedule of the team from the DB
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%m').') AS '.$db->qn('zeit'));
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('Heim').' IN '.self::getTeamNames());
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
	
}