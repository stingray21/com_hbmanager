<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class hbmanagerModelHbdata extends JModelLegacy
{	
	private $updated = array();
	
	function __construct() 
	{
		parent::__construct();
				
		// set maximum execution time limit
		set_time_limit(90);
		
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
	
	function updateDb($key = 'none')
	{
		//$start = time();
		if ($key != 'none')
		{
			$teams = self::getHvwTeams ($key);
			foreach ($teams as $team)
			{
				self::updateTeam($team->kuerzel);
			}
		}
		//echo $duration = (time() - $start). ' sec';	
		return;
	}
	
	protected function getHvwTeams ($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('hvwLink').' IS NOT NULL');
		
		if ($teamkey != 'all') {
			// request only one team of DB
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		}
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		//echo '=> model->$updated <br><pre>'; print_r($teams); echo '</pre>';
		return $teams;
	}
	
	function updateTeam($teamkey) 
	{
		$source = self::getSourceFromHVW( self::getHvwLink($teamkey) );
		$scheduleData = self::getScheduleData($source['schedule']);
		//echo '=> model->$updated <br><pre>'; print_r($scheduleData); echo '</pre>';
		if (self::updateGamesInDB($teamkey, $scheduleData))
		{
			self::updateTimestamp ($teamkey);
			$this->updated[] = $teamkey;
			self::updateLog('schedule', $teamkey);
			return true;
		}
		return false;
	}
	
	protected function getHvwLink ($teamkey)
	{
		//echo '=> model->$updated <br><pre>'; print_r($teamkey); echo '</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('hvwLink'));
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		$db->setQuery($query);
		$result = $db->loadResult();
		//echo '=> model->$query <br><pre>'; echo $query ; echo '</pre>';
		//echo '=> model->$updated <br><pre>'; print_r($result); echo '</pre>';
		return $result;
	}
	
	protected function getSourceFromHVW($address)
	{
		// returns sourcecode of a website with the address $address as string
		$sourcecode = file_get_contents($address);
	
		// shortens strings to relevant part for rankings
		$start = strpos($sourcecode,">Punkte</th></tr>")+17;
		$end = strpos($sourcecode,"</tr></TABLE></div>",$start);
		$source['ranking'] = substr($sourcecode,$start,($end-$start));
		
		// shortens strings to relevant part for schedule
		$start = strpos($sourcecode,'<th align="center">Bem.</th>')+34;
		$end = strpos($sourcecode,'</table>',$start)-8;
		$source['schedule'] = substr($sourcecode,$start,($end-$start));
		
		return $source;
	}
	
	protected function getScheduleData($source)
	{
		$searchMarker = array('</td>', '</tr>',"\n" ,"\t");
		$replaceMarker = array('||', '&&', '', '');
		$source = str_replace($searchMarker, $replaceMarker ,$source);
		
		$source = strip_tags($source);
		
		$search = array('||-||', '||:||', '||&&', ', ');
		$replace = array('||', '||', '&&', '||');
		$source = str_replace($search, $replace ,$source);
		
		//echo $source;
		
		$scheduleData = self::explode2D($source);
		return self::formatScheduleData($scheduleData);
	}
	
	protected function explode2D ($source)
	{
		$data = explode('&&',$source);
		foreach ($data as $key => $value) 
		{
			$data[$key] = explode('||',$value);
		}
		return $data;
	}
	
	protected function formatScheduleData($data)
	{
		foreach ($data as $key => $value) 
		{
			$data[$key][3] = preg_replace('/(\d{2}).(\d{2}).(\d{2})/',
								'20$3-$2-$1', $value[3]);
			$data[$key][4] = str_replace('h', ':00', $value[4]);
		}
		return $data;
	}
	
	function getUpdateStatus()
	{
		$updated = $this->updated;
		//echo '=> model->$updated <br><pre>"; print_r($updated); echo "</pre>';
		return $updated;
	}
	
	protected function deleteOldData ($teamkey)
	{
		//echo '=> model <br><pre>'; print_r($teamkey); echo '</pre>';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn('hb_spiel'));
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey)); 
		$db->setQuery($query);
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->execute();
		//echo '=> model <br><pre>'; print_r($result); echo '</pre>';
		return $result;
	}
	
	protected function updateGamesInDB($teamkey, $scheduleData)
	{
		//echo '=> model<br><pre>'; print_r($scheduleData);echo '</pre>';
		self::deleteOldData ($teamkey);
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$columns = array('saison',  'spielIDhvw', 'kuerzel', 
			'ligaKuerzel', 'hallenNr', 'datumZeit', 
			'heim', 'gast', 'toreHeim', 'toreGast', 'bemerkung');
		
		$saison = self::getSaison();
		
		foreach($scheduleData as $row) {
			$values[] = implode(', ', 
							self::formatValue($row, $teamkey, $saison));
		}

		// Prepare the insert query.
		$query
			->insert($db->qn('hb_spiel')) 
			->columns($db->qn($columns))
			->values($values);
		
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->execute();
		
		//echo '<pre>result';print_r($result);echo '</pre>';
		return $result;
	}
	
	protected function formatValue($data, $teamkey, $season)
	{
		//echo '=> model<br><pre>'; print_r($data);echo '</pre>';
		$db = $this->getDbo();
		
        $value['saison'] = $db->q($season);
		$value['spielIDhvw'] = $db->q($data[1]);
		$value['kuerzel'] = $db->q($teamkey);
		$value['ligaKuerzel'] = $db->q($data[0]);
		// HallenNummer
		if (trim($data[5]) != '') $value['hallenNummer'] = (int)$data[5];
			else  $value['hallenNr'] = "NULL";
		// Datum & Uhrzeit
		if (trim($data[3]) != '' || trim($data[4]) != '') {	
			
			$date = JFactory::getDate($data[3].' '.$data[4], 'Europe/Berlin' )
					->toSql();
			//echo '<p>HVW:'.$data[3].' '.$data[4].' -> in DB: '.$date."</p>";
			$value['datumzeit'] = $db->q($date);
		}
		else  $value['datumzeit'] = "NULL";
		
		$value['heim'] = $db->q(addslashes($data[6]));
		$value['gast'] = $db->q(addslashes($data[7]));
		// ToreHeim
		if (trim($data[8]) != '') $value['toreHeim'] = (int)$data[8];
			else  $value['toreHeim'] = "NULL";
		// ToreGast
		if (trim($data[9]) != '') $value['toreGast'] = (int)$data[9];
			else  $value['toreGast'] = "NULL";
		// Bemerkung
		if (trim($data[10]) != '') $value['bemerkung'] = $db->q($data[10]);
			else  $value['bemerkung'] = "NULL"; 
		
		//echo '=> model<br><pre>'; print_r($value);echo '</pre>';
		return $value;
	}
	
	protected function getSaison()
	{
		// TODO $saison not hardcoded
		$saison = '2014-2015';
		return $saison;
	}
	
	protected function updateTimestamp ($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update('hb_mannschaft');
		$dateUTC = JFactory::getDate( )->toSql();
		//echo '<p>in DB: '.$date."</p>";
		$query->set($db->qn('update').' = '.$db->q($dateUTC));
		$query->where($db->qn('kuerzel').' = '.
					$db->q($teamkey));
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$result = $db->query();
		
		return $result;
	}
	
	protected function updateLog($type, $teamkey)
	{	
		// function to log updates for cronjob
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->insert($db->qn('hb_updatelog'));
		$query->columns($db->qn(array('typ','kuerzel','datum')));
		
		$dateUTC = JFactory::getDate( )->toSql();
		$query->values($db->q($type).', '.$db->q($teamkey).', '.
			$db->q($dateUTC));
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->query();
				
		return $result;
	}
	
	function getUpdateDate($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('kuerzel', 'update')));
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
		//echo '=> model <br><pre>'; print_r($team); echo '</pre>';
		
		$format = 'D, d.m.Y - H:i:s \U\h\r';
		$date = JHtml::_('date', $team->update, $format, false);
		
		return $date;
	}
}