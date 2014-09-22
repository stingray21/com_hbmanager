<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin'); 
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbdata.php';

class hbmanagerModelHbcronjob extends hbmanagerModelHbdata
{	

	function updateHvwDataCronjob()
	{
		
		$teams = self::getOutdatedTeams();
		//echo '<pre>';print_r($teams); echo '</pre>';
		
		if (is_array($teams)) {
			foreach ($teams as $team)
			{
				self::updateTeam($team->kuerzel);
				$updateDates = self::getUpdateDate($team->kuerzel);
				//echo '<pre>';print_r($updateDates); echo '</pre>';
			}
			$result[] = $updateDates;
		}
		else {
			$result = 'no update'; 	
		}
		return $result;
	}
	
	function getOutdatedTeams()
	{
		$allTeams = self::getHvwTeamArray();
		//echo '<pre>';print_r($allTeams); echo '</pre>';
		$teams = null;
		foreach ($allTeams as $team)
		{
			
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			
			$query->select('DISTINCT('.$db->qn('kuerzel').')' );
			$query->from($db->qn('hb_mannschaft'));
			$query->innerJoin($db->qn('hbdata_'.$team->kuerzel.'_spielplan').
				' ON '.$db->qn('klasse').'='.$db->qn('ligakuerzel') );
			$query->where('( CONCAT('.$db->qn('datum').", ' ', ".$db->qn('zeit').') < NOW()'
				. ' AND ('.$db->qn('updateSpielplan').' < CONCAT('.$db->qn('datum').", ' ', ".$db->qn('zeit').')'
					. ' OR '.$db->qn('updateTabelle').' < CONCAT('.$db->qn('datum').", ' ', ".$db->qn('zeit').'))'
				. ' AND ('.$db->qn('updateSpielplan').' < NOW() - INTERVAL 5 HOUR'
					. ' OR '.$db->qn('updateTabelle').' < NOW() - INTERVAL 5 HOUR) )', 'OR');
			$query->where('( '.$db->qn('kuerzel').' = '.$db->q($team->kuerzel).''
				. ' AND CONCAT('.$db->qn('datum').", ' ', ".$db->qn('zeit').') < NOW() - INTERVAL 2 HOUR'
				. ' AND '.$db->qn('ToreHeim').' = NULL )', 'OR');
			$query->where('( DAYOFWEEK(NOW()) = 1 '
				. 'AND (DAYOFWEEK('.$db->qn('updateSpielplan').') != 1'
					. ' OR DAYOFWEEK('.$db->qn('updateTabelle').') != 1) )');
			
			//echo '=> model->$query <br><pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->loadObject();
			//echo '<pre>';print_r($result); echo '</pre>';
			
			if (!empty($result)) {
				$teams[] = $result;
			}
		}
		
		return $teams;
	}
	
	function getDailyUpdateStatus()
	{
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->q('true').' AS '.$db->qn('outdated'));
		$query->from($db->qn('hb_mannschaft'));
		$query->where('DATE('.$db->qn('updateSpielplan').') < DATE(NOW())'
					.' OR '
					.'DATE('.$db->qn('updateTabelle').') < DATE(NOW())'
					);
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->loadResult();
		//echo '<pre>';print_r($result); echo '</pre>';
				
		return $result;
	}
	
	
	
}