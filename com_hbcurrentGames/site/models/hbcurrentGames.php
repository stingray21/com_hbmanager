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
	
	function getPrevLimit($today)
	{
		return strftime("%Y-%m-%d", 
			strtotime('last Monday', 
					strtotime('last friday', strtotime($today)))
			);
	}
	
	function getPrevGamesDates()
	{
		$db = $this->getDbo();		
		$query = $db->getQuery(true);
		$todaydate = strftime("%Y-%m-%d", time());
		//echo $todaydate = "2014-09-16";

		$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
			$db->q(self::getPrevLimit($todaydate)).
			" AND " . $db->q($todaydate) .
			" ORDER BY `datum` ASC LIMIT 1";
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->loadResult();
		if (!empty($result)) {
			$dates['startdate'] = $result;
		}
		else {
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` < ".
					$db->q(self::getPrevLimit($todaydate)).
					" ORDER BY `datum` DESC LIMIT 1";
			//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->loadResult();
		}
		if (!empty($result)) {
			$dates['startdate'] = $result;
		}
		else {
			$dates['startdate'] = self::getPrevLimit($todaydate);
		}
		$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
				$dates['startdate']. " AND " . $db->q($todaydate) . 
				" ORDER BY `datum` DESC LIMIT 1";
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$dates['enddate'] = $db->loadResult();
		
		return $dates;
	}
	
	function getNextLimit($today)
	{
		return strftime("%Y-%m-%d", 
			strtotime('next Monday', 
				strtotime('next friday', strtotime($today))
			)
		);
	}
		
	function getNextGamesDates()
	{	
		$db = $this->getDbo();		
		$query = $db->getQuery(true);
		$todaydate = strftime("%Y-%m-%d", time());
		//echo $todaydate = "2013-10-23";

		$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
			$db->q($todaydate). " AND " . 
			$db->q(self::getNextLimit($todaydate)).
			" ORDER BY `datum` ASC LIMIT 1";
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$result = $db->loadResult();
		if (!empty($result)) {
			$dates['startdate'] = $result;
		}
		else {
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` > ".
					$db->q(self::getNextLimit($todaydate)).
					" ORDER BY `datum` ASC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$dates['startdate'] = $db->loadResult();
		}
		$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
				$db->q($dates['startdate']) . " AND " . 
				$db->q(strftime("%Y-%m-%d", strtotime('next friday', 
					strtotime($dates['startdate'])))).
				" ORDER BY `datum` DESC LIMIT 1";
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$dates['enddate'] = $db->loadResult();
		
		return $dates;
	}
	
	function getHomeGamesDates()
	{	
		$db = $this->getDbo();		
		$query = $db->getQuery(true);
		$todaydate = strftime("%Y-%m-%d", time());
		//echo $todaydate = "2013-10-23";
		
		$query->select($db->qn('datum'));
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('datum').' > '.
			$db->q(self::getNextLimit($todaydate)), 'AND' );
		$query->where($db->qn('hallenNummer').' = '.$db->q(7014));
		$query->order($db->qn('datum').' ASC LIMIT 1');

		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$result = $db->loadResult();
		$dates['startdate'] = $result;
		
		$query = $db->getQuery(true);
		$query->select($db->qn('datum'));
		$query->from($db->qn('hb_spiel'));
		$query->where($db->qn('datum').' BETWEEN '.
				$db->q($dates['startdate']) . " AND " . 
				$db->q(strftime("%Y-%m-%d", strtotime('next friday', 
					strtotime($dates['startdate'])))), 'AND' );
		$query->where($db->qn('hallenNummer').' = '.$db->q(7014));
		$query->order($db->qn('datum').' DESC LIMIT 1');
		
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$dates['enddate'] = $db->loadResult();
		
		return $dates;
	}
	
	function getGames($dates, $order, $home = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('kuerzel').', '.
			$db->qn('spielIdHvw').', '.
			$db->qn('datum').', '.
			$db->qn('uhrzeit').', '.  
			$db->qn('heim').', '.  
			$db->qn('gast').', '. 
			$db->qn('toreHeim').', '.
			$db->qn('toreGast').', '.
			$db->qn('bemerkung').', '.
			$db->qn('reihenfolge').', '.
			$db->qn('mannschaft').', '.
			$db->qn('hb_mannschaft').'.'.$db->qn('name').' AS '.$db->qn('name').', '.
			$db->qn('nameKurz').', '.
			$db->qn('ligaKuerzel').', '.
			$db->qn('liga').', '.
			$db->qn('geschlecht').', '.
			$db->qn('jugend').', '.
			$db->qn('hvwLink').', '.
			$db->qn('hb_halle').'.'.$db->qn('name').' AS '.$db->qn('hallenName').', '.
			$db->qn('hallenNummer').', '.
			$db->qn('kurzname').', '.
			$db->qn('land').', '.
			$db->qn('plz').', '.
			$db->qn('stadt').', '.
			$db->qn('strasse').', '.
			$db->qn('telefon').', '.
			$db->qn('bezirkNummer').', '.
			$db->qn('bezirk').', '.
			$db->qn('freigabeVerband').', '.
			$db->qn('freigabeBezirk').', '.
			$db->qn('haftmittel')
			);
		//$query->select('CONCAT('.$db->qn('datum').", ' ', ".$db->qn('zeit').') AS datum');
		$query->from('hb_spiel') ;
		$query->where($db->qn('datum').' BETWEEN '.
			$db->q($dates['startdate']).' AND '.
			$db->q($dates['enddate']));
		if ($home) {
			$query->where($db->qn('hallenNummer').' = '.$db->q(7014));
		}
		$query->join('INNER',$db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->join('LEFT',$db->qn('hb_halle').' USING ('.$db->qn('hallenNummer').')');
		$query->order($db->qn($order));
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		return $games = $db->loadObjectList();
	}

}