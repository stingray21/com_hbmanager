<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbdata.php';

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/hbarticle.php';

class HBmanagerModelHbdatastandings extends HBmanagerModelhbdata
//class hbmanagerModelHbdatastandings extends HBmanagerModelHbdata
{	
    private $updated = array();
	private $standingsArray = array();
	
    function __construct() 
    {
		parent::__construct();
		
		self::setSeason();
		
		//$this->names = self::getScheduleTeamNames();
		// set maximum execution time limit
		set_time_limit(90);

    }

    public function updateStandingsChart($teamkey = null)
    {
		$teams = self::getTeams($teamkey);
		
		foreach ($teams as &$team) {
			self::getStandingsArrays($team->kuerzel);
			$team->tabellenDaten['standings'] = $this->standingsArray;
			$team->tabellenDaten['info']['home'] = $team->nameKurz;
		}	
		
		self::updateDetailsDB($teams);
    }

	public function getTeams($teamkey = null)
    {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('mannschaft, kuerzel, nameKurz');
		$query->from('hb_mannschaft');
		if ($teamkey !== null) {
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		}
		$query->where($db->qn('jugend').' = '.$db->q('aktiv'));
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
								$db->qn('reihenfolge').' ASC');
		//echo __FILE__.' ('.__LINE.')<pre>'; print_r($query); echo '</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($teams); echo '</pre>';
		return $teams;
    }
	
	protected function getStandingsArrays($teamkey) {
		$dates = self::getDates($teamkey);
		$this->standingsArray = array();
		$i =0;
		//while (strtotime($dates[$i]) < time()) {
		while (isset($dates[$i])) {
			//echo __FILE__.' ('.__LINE__.')<pre>'.$dates[$i].': '.strtotime($dates[$i]).' - '.time().'</pre>';
			$standings = self::getStandings($teamkey, $dates[$i]);

			self::add2Array($dates[$i], $standings);
			//self::add2Array(null, $standings);
			$i++;
		}
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($this->standingsArray); echo '</pre>';
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r(json_encode($this->standingsArray)); echo '</pre>';
	}
	
	protected function getDates($teamkey) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DATE(datumZeit) as day,'.
				' DATE_ADD(DATE(`datumZeit`), INTERVAL (8 - IF(DAYOFWEEK(`datumZeit`)=1, 8, DAYOFWEEK(`datumZeit`))) DAY) as nextSunday');
		$query->from('hb_spiel');
		$query->where('eigenerVerein = 1 and kuerzel = '.$db->q($teamkey));		
		$query->where($db->qn('saison').' = '.$db->q($this->season));			
		$query->where('DATE('.$db->qn('datumZeit').') <= DATE_ADD(DATE(NOW()), INTERVAL (8 - IF(DAYOFWEEK(NOW())=1, 8, DAYOFWEEK(NOW()))) DAY)');
		$query->order($db->qn('day')); 
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		//$dates = $db->loadObjectList();
		$dates = $db->loadColumn(1);
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($dates); echo '</pre>';
		return $dates;
	}
	
	protected function getStandings($teamkey, $date) {
		$standingsData = self::sortDetailedStandings(
				self::getDetailedStandingsData($teamkey, $date),$teamkey, true );
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($standingsData); echo '</pre>';
		return $standingsData;
	}
	
	protected function add2Array($date, $standings) {
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($standings); echo '</pre>';
		foreach ($standings as $team) {
			$i = 0;
			$added = false;
			while ($i < count($this->standingsArray) && !$added) {
				if ($this->standingsArray[$i]->name == $team->mannschaft) {
					$this->standingsArray[$i]->data[] = self::formatElement($date, $team); 
					$added = true;
				} 
				$i++;
			}
			if (!$added) {
				$this->standingsArray[$i] = new stdClass();
				$this->standingsArray[$i]->name = $team->mannschaft;
				$this->standingsArray[$i]->data[] = self::formatElement($date, $team);
			}
		}
	} 
	
	protected function formatElement($date, $team) {
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($team); echo '</pre>';
		$element['date'] = $date;
		$element['rank'] = $team->platz;
		$element['points'] = $team->punkte;		
		$element['negpoints'] = $team->minusPunkte;
		return $element;
	} 
	
	protected function updateDetailsDB($teams = null) 
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		if (empty($teams)) return;
		
		$table = 'hb_mannschaftsdetails';
		$columns = array('kuerzel', 'saison', 'tabellenGraph');
		
		$values = null;
		foreach($teams as $row) {
			$values[] = implode(', ', self::formatTeamValues($row));
		}
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($columns); echo'</pre>';
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($values); echo'</pre>';
		$db = $this->getDbo();
		
		// Prepare the insert query.
		$query = $db->getQuery(true);
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$query .= "\nON DUPLICATE KEY UPDATE \n";
		$dublicates = array();
		foreach ($columns as $field) {
			$dublicates[] = $db->qn($field).' = VALUES('.$db->qn($field).')';
		}
		$query .= implode(",\n", $dublicates);
		
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}
	
	protected function formatTeamValues($data) {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($data); echo'</pre>';
		$db = $this->getDbo();
		 
		$value['kuerzel'] = $db->q($data->kuerzel);
		$value['saison'] = $db->q($this->season);
		$value['tabellenGraph'] = (!empty($data->tabellenDaten)) ? $db->q(json_encode($data->tabellenDaten)) : 'NULL';

		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
    }
}