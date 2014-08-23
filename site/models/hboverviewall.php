<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbOverviewAll extends JModelLegacy
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
	
	
	
}