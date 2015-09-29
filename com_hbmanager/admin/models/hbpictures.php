<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


class hbmanagerModelHbpictures extends JModelLegacy
{	
	private $teamkey = null;
	
	
	function __construct() 
	{
		parent::__construct();
		
		
	}
	
	public function setTeamkey($teamkey) {
		$this->teamkey = $teamkey;
	
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('kuerzel').', '.
			$db->qn('reihenfolge').', '.$db->qn('mannschaft').', '.
			$db->qn('name').', '.$db->qn('nameKurz').', '.
			$db->qn('ligaKuerzel').', '.$db->qn('liga').', '.
			$db->qn('geschlecht').', '.$db->qn('jugend').', '.
			$db->qn('dateiname').', '.$db->qn('saison').
			', '.$db->qn('untertitel_dt1').', '.$db->qn('untertitel_dd1').
			', '.$db->qn('untertitel_dt2').', '.$db->qn('untertitel_dd2').
			', '.$db->qn('untertitel_dt3').', '.$db->qn('untertitel_dd3').
			', '.$db->qn('untertitel_dt4').', '.$db->qn('untertitel_dd4').
			', '.$db->qn('kommentar') );
		$query->from('hb_mannschaft');
		$query->leftJoin($db->qn('hb_mannschaftsfoto').' USING ('.
				$db->qn('kuerzel').')');
		if ($this->teamkey !== null) {
			$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		}
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
					$db->qn('reihenfolge').' ASC');
		// Zur Kontrolle
		//echo __FILE__.' ('.__LINE__.'<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		if ($this->teamkey === null) { 
			$teams = $db->loadObjectList();
		} else {
			$teams = $db->loadObject();
		}
		return $teams;
	}
		
	
	function updateDB($teams = null) 
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		if (empty($teams)) return;
		
		$table = 'hb_mannschaftsfoto';
		$columns = array('kuerzel', 'dateiname', 'saison', 'untertitel_dt1', 
			'untertitel_dd1', 'untertitel_dt2', 'untertitel_dd2', 'untertitel_dt3', 
			'untertitel_dd3', 'untertitel_dt4', 'untertitel_dd4', 'kommentar');
		
		$values = null;
		
		foreach($teams as $row) {
			$values[] = implode(', ', self::formatTeamValues($row));
		}
		//echo __FILE__.' ('.__LINE__.'<pre>';print_r($columns); echo'</pre>';
		//echo __FILE__.' ('.__LINE__.'<pre>';print_r($values); echo'</pre>';
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
		
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}

	protected function formatTeamValues($data)
    {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($data); echo'</pre>';
		$db = $this->getDbo();
		
		
		$value['kuerzel'] = $db->q($data['kuerzel']);
		$value['dateiname'] = (!empty($data['dateiname'])) ? $db->q($data['dateiname']) : 'NULL';
		$value['saison'] = (!empty($data['saison'])) ? $db->q($data['saison']) : 'NULL';
		$value['untertitel_dt1'] = (!empty($data['untertitel_dt1'])) ? $db->q($data['untertitel_dt1']) : 'NULL';
		$value['untertitel_dd1'] = (!empty($data['untertitel_dd1'])) ? $db->q($data['untertitel_dd1']) : 'NULL';
		$value['untertitel_dt2'] = (!empty($data['untertitel_dt2'])) ? $db->q($data['untertitel_dt2']) : 'NULL';
		$value['untertitel_dd2'] = (!empty($data['untertitel_dd2'])) ? $db->q($data['untertitel_dd2']) : 'NULL';
		$value['untertitel_dt3'] = (!empty($data['untertitel_dt3'])) ? $db->q($data['untertitel_dt3']) : 'NULL';
		$value['untertitel_dd3'] = (!empty($data['untertitel_dd3'])) ? $db->q($data['untertitel_dd3']) : 'NULL';
		$value['untertitel_dt4'] = (!empty($data['untertitel_dt4'])) ? $db->q($data['untertitel_dt4']) : 'NULL';
		$value['untertitel_dd4'] = (!empty($data['untertitel_dd4'])) ? $db->q($data['untertitel_dd4']) : 'NULL';
		$value['kommentar'] = (!empty($data['kommentar'])) ? $db->q($data['kommentar']) : 'NULL';

		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
    }
	
	function old_updateDB($pics = array())
	{
		
		
		$db = $this->getDbo();
		foreach ($pics as $pic)
		{
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
			foreach ($pic as $value)
			{
				$value = trim($value);
			}
			if (count(array_filter($pic)) > 1)
			{
				$pic = (object) $pic;
				$query = $db->getQuery(true);
				if (empty($pic->id))
				{
					$query = 'INSERT INTO hb_mannschaftsfoto ('.
						$db->qn('kuerzel').', '.
						$db->qn('dateiname').', '.$db->qn('saison').
						', '.$db->qn('untertitel_dt1').', '.$db->qn('untertitel_dd1').
						', '.$db->qn('untertitel_dt2').', '.$db->qn('untertitel_dd2').
						', '.$db->qn('untertitel_dt3').', '.$db->qn('untertitel_dd3').
						', '.$db->qn('untertitel_dt4').', '.$db->qn('untertitel_dd4').
						', '.$db->qn('kommentar') .")\n";
					$query .= ' VALUES ';

					$query .= '('.
							$db->q($pic->kuerzel).', '.
							$db->q($pic->dateiname).', '.
							$db->q($pic->saison).', ';
					if (empty($pic->untertitel_dt1)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dt1);}
					$query .= ', ';
					if (empty($pic->untertitel_dd1)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dd1);}
					$query .= ', ';
					if (empty($pic->untertitel_dt2)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dt2);}
					$query .= ', ';
					if (empty($pic->untertitel_dd2)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dd2);}
					$query .= ', ';
					if (empty($pic->untertitel_dt3)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dt3);}
					$query .= ', ';
					if (empty($pic->untertitel_dd3)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dd3);}
					$query .= ', ';
					if (empty($pic->untertitel_dt4)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dt4);}
					$query .= ', ';
					if (empty($pic->untertitel_dd4)) {$query .= 'NULL';}
					else {$query .= $db->q($pic->untertitel_dd4);}

					$query .= ', '.$db->q($pic->kommentar).')';
				}
				else 
				{
					$query = 'UPDATE hb_mannschaftsfoto SET';
					$query .= $db->qn('kuerzel').' = '.$db->q($pic->kuerzel).
					', '.$db->qn('dateiname').' = '.$db->q($pic->dateiname).
					', '.$db->qn('saison').' = '.$db->q($pic->saison);
				
					$query .= ', '.$db->qn('untertitel_dt1').' = ';
					if (empty($pic->untertitel_dt1)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dt1);
					$query .= ', '.$db->qn('untertitel_dd1').' = ';
					if (empty($pic->untertitel_dd1)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dd1);

					$query .= ', '.$db->qn('untertitel_dt2').' = ';
					if (empty($pic->untertitel_dt2)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dt2);
					$query .= ', '.$db->qn('untertitel_dd2').' = ';
					if (empty($pic->untertitel_dd2)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dd2);

					$query .= ', '.$db->qn('untertitel_dt3').' = ';
					if (empty($pic->untertitel_dt3)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dt3);
					$query .= ', '.$db->qn('untertitel_dd3').' = ';
					if (empty($pic->untertitel_dd3)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dd3);

					$query .= ', '.$db->qn('untertitel_dt4').' = ';
					if (empty($pic->untertitel_dt4)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dt4);
					$query .= ', '.$db->qn('untertitel_dd4').' = ';
					if (empty($pic->untertitel_dd4)) $query .= 'NULL';
					else $query .= $db->q($pic->untertitel_dd4);

					$query .= ', '.$db->qn('kommentar').' = '.$db->q($pic->kommentar);
					$query .= 'WHERE ';
					$query .= $db->qn('id').' = '.$db->q($pic->id)."\n";
				}
				
				//echo '=> model->$query <br><pre>'.$query.'</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				} catch (Exception $e) {
					// catch any database errors.
				}
			
			}
		}
	}
	
}