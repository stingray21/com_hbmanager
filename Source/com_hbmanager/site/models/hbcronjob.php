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
		
		if (is_array($teams) and !empty($teams)) {
			foreach ($teams as $team)
			{
				self::updateTeam($team->kuerzel);
				$updateDate = self::getUpdateDate($team->kuerzel, false);
				//echo '<pre>';print_r($updateDates); echo '</pre>';
				$team->updated = $updateDate;
			}
			$result = $teams;
		}
		else {
			$result = 'no update'; 	
		}
		//echo '<pre>';print_r($result); echo '</pre>';
		return $result;
	}
	
	function getHvwTeamArray ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('kuerzel');
		$query->from('hb_mannschaft');
		$query->where($db->qn('hvwLink').' IS NOT NULL');
		$db->setQuery($query);
		$teams = $db->loadColumn();
		//echo '=> model->$updated <br><pre>'; echo $query; echo '</pre>';
		//echo '=> model->$updated <br><pre>'; print_r($teams); echo '</pre>';
		return $teams;
	}
	
	function getOutdatedTeams()
	{
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT('.$db->qn('kuerzel').')' );
		$query->from($db->qn('hb_mannschaft'));
		$query->innerJoin($db->qn('hb_spiel').
			' USING ('.$db->qn('kuerzel').')' );
		$query->where($db->qn('hvwLink').' IS NOT NULL','AND');
		$query->where('( ('.$db->qn('ToreHeim').' IS NULL'
			. ' AND '.$db->qn('bemerkung').' IS NULL'
			//. ' AND '.$db->qn('update').' + INTERVAL 2 HOUR < UTC_TIMESTAMP()'
			. ' AND '.$db->qn('datumZeit').' + INTERVAL 2 HOUR < '
			. 'CONVERT_TZ(UTC_TIMESTAMP(),\'UTC\',\'Europe/Berlin\') )'
			. ' OR '
			. '( DAYOFWEEK(NOW()) = 2 '
			. 'AND DAYOFWEEK('.$db->qn('update').') != 2 ) )');

		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		//echo __FUNCTION__.'<pre>';print_r($teams); echo '</pre>';
		
		return $teams;
	}
	
	
}