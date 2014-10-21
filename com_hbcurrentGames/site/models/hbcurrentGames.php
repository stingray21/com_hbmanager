<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Gyms Model
 */
class HBcurrentGamesModelHBcurrentGames extends JModelLegacy
{
	function getTeams($category = '')
	{
		$db = $this->getDbo();		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		return $teams = $db->loadObjectList();
	}
	
	function getPrevGames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel') ;
		$query->where($db->qn('datum').' < NOW()'.
			' AND '.$db->qn('datum').' > NOW() - INTERVAL 1 WEEK');
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->order($db->qn('reihenfolge'));
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		return $games = $db->loadObjectList();
	}
	
	function getNextGames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel') ;
		$query->where($db->qn('datum').' > NOW()'.
			' AND '.$db->qn('datum').' < NOW() + INTERVAL 1 WEEK');
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->order($db->qn('datum').', '.$db->qn('uhrzeit'));
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		return $games = $db->loadObjectList();
	}
	
	function getHomeGames()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel') ;
		$query->where($db->qn('datum').' > NOW()'.
			' AND '.$db->qn('datum').' < NOW() + INTERVAL 2 WEEK', 'AND');
		$query->where($db->qn('hallenNummer').' = '.$db->q('7014'));
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->order($db->qn('datum').', '.$db->qn('uhrzeit'));
		echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		return $games = $db->loadObjectList();
	}

}