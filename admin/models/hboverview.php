<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbOverview extends JModelLegacy
{	
	private $updatedRankings = array();
	private $updatedSchedules = array();
	
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
		$query->select('*');
		$query->from($db->quoteName('hb_spiel'));
		$query->where($db->quoteName('Kuerzel').' = '.$db->Quote($team->kuerzel));
		$query->order($db->quoteName(array('datum', 'uhrzeit')));
		//echo nl2br($query);//die; //see resulting query
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		//echo "<pre>"; print_r($rows); echo "</pre>";
		
		$rows = self::markHomeInSchedule($rows, $team->nameKurz);
		
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

	function markHomeInSchedule($rows, $hometeam)
	{
		foreach ($rows as $row)
		{
			if (strcmp(trim($row->heim), trim($hometeam)) == 0)	{
				$row->mark = 1;
			}
			elseif (strcmp(trim($row->gast), trim($hometeam)) == 0)	{
				$row->mark = 2;
			}
			else {
				$row->mark = 0;
			}
		}
		return $rows;
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
		$query->select('*');
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('Heim').' IN '.self::getTeamNames());
		$query->join('INNER',$db->qn('hb_halle').' USING ('.$db->qn('hallenNummer').')');
		$query->order($db->qn(array('datum', 'uhrzeit')));
		//echo nl2br($query);//die; //see resulting query
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		//echo "<pre>"; print_r($rows); echo "</pre>";
		$arangedRows = self::arangeGames($rows);
		//echo "<pre>"; print_r($arangedRows); echo "</pre>";
		return $arangedRows;
	}
	
	function arangeGames($inputRows)
	{
		$rows = '';
		foreach ($inputRows as $value)
		{
			$rows[$value->datum][$value->hallenNummer][] = $value;
		}
		return $rows;
	}
	
}