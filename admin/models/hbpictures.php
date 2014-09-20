<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


class hbmanagerModelHbpictures extends JModelLegacy
{	
	
	function __construct() 
	{
		parent::__construct();
		
		
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('kuerzel').', '.$db->qn('mannschaftID').', '.
			$db->qn('reihenfolge').', '.$db->qn('mannschaft').', '.
			$db->qn('name').', '.$db->qn('nameKurz').', '.
			$db->qn('ligaKuerzel').', '.$db->qn('liga').', '.
			$db->qn('geschlecht').', '.$db->qn('jugend').', '.
			$db->qn('id').', '.$db->qn('dateiname').', '.$db->qn('saison').
			', '.$db->qn('untertitel_dt1').', '.$db->qn('untertitel_dd1').
			', '.$db->qn('untertitel_dt2').', '.$db->qn('untertitel_dd2').
			', '.$db->qn('untertitel_dt3').', '.$db->qn('untertitel_dd3').
			', '.$db->qn('untertitel_dt4').', '.$db->qn('untertitel_dd4').
			', '.$db->qn('kommentar') );
		$query->from('hb_mannschaft');
		$query->leftJoin($db->qn('hb_mannschaftsfoto').' USING ('.
				$db->qn('kuerzel').')');
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
					$db->qn('reihenfolge').' ASC');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
	}

	
	function updateDB($pics = array())
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($pics);echo'</pre>';
		if (empty($pics)) return;
		
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