<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 

// Require helper file
JLoader::register('HbmanagerHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/hbmanager.php');

JLoader::register('HBmanagerModelPrintNews', JPATH_COMPONENT_ADMINISTRATOR . '/models/printnews.php');

class HBmanagerModelReminder extends HBmanagerModelPrintNews
// class HBmanagerModelReminder extends JModelLegacy
{	
	public function __construct($config = array())
	{		
		parent::__construct($config);

		$this->tables->holidays = '#__hb_holidays';

		if (!$this->checkFutureHolidays()) {
			// echo 'No Future Holidays!';
			$this->updateHolidayDB();
		}
		
		// for testing
		// $this->dates->today = date_create('2019-10-28', $this->tz);
		
	}

	public function getReport() 
	{
		$reports = $this->getReports();
		return $reports;
	}

	public function getCurrentHolidays() 
	{
		// $start 	= $this->dates->nextStart->format('Y-m-d');
		$start = $this->dates->today->format('Y-m-d');
		$end 	= $this->dates->nextEnd->format('Y-m-d');

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->tables->holidays);
		$query->where('(DATE(' . $db->qn('date') . ') BETWEEN '
			. $db->q($start) . ' AND ' . $db->q($end) . ')');
		$db->setQuery($query);
		$holidays = $db->loadObjectList();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $holiday ,1).'</pre>';

		return $holidays;
	}
	

	public function getFutureHolidays() 
	{
		// $start 	= $this->dates->nextStart->format('Y-m-d');
		$start = $this->dates->today->format('Y-m-d');

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->tables->holidays);
		$query->where('DATE(' . $db->qn('date') . ') >= ' . $db->q($start) );
		$query->order($db->qn('date') . ' ASC LIMIT 2');
		$db->setQuery($query);
		// echo __FILE__.'('.__LINE__.')<pre>'.$query.'</pre>';
		$holidays = $db->loadObjectList();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $holidays ,1).'</pre>';
		return $holidays;
	}
	
	private function updateHolidayDB() 
	{
		$holidays = $this->getGermanHolidays();

		if (!empty($holidays)) {
			//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($inputData);echo '</pre>';

			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$columns = array('date', 'holiday');

			foreach($holidays as $row) {
				$values[] = $db->q($row[0]).','.$db->q($row[1]);
			}

			// Prepare the insert query.
			$query
					->insert($db->qn($this->tables->holidays)) 
					->columns($db->qn($columns))
					->values($values);

			// echo __FILE__.'('.__LINE__.')<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->execute();
			return $result;
		}
	}
	
	private function getGermanHolidays($year = null) 
	{
		$year = (is_int($year)) ? $year : date('Y') ;

		$base_url = 'https://feiertage-api.de/api/';
		// $url = $base_url.'?jahr=2019&nur_land=BW';
		
		$state = 'BW';

		$result = $this->_file_get_contents_t_curl($base_url.'?jahr='.$year.'&nur_land='.$state);
		$holidays = json_decode($result,true);

		foreach ($holidays as $key => $value) {
			$holiday_array[] = [$value['datum'], $key];
		}

		return $holiday_array;
	}
	
	private function checkFutureHolidays() 
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->tables->holidays);
		$query->where('DATE(' . $db->qn('date') . ') >= ' .
			$db->q($this->dates->today->format('Y-m-d')));
		$db->setQuery($query);
		$holiday = $db->loadResult();
		// $holiday = $db->loadObjectList();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $holiday ,1).'</pre>';

		if (empty($holiday)) return false;
		return true;
	}

	private function _file_get_contents_t_curl($url) {

		// from:
		// https://feiertage-api.de/api/Connector.php.txt

		$ctx = stream_context_create(['http' => ['timeout' => 5]]);
		$file = @file_get_contents($url, false, $ctx);
		if(!empty($file))
			return $file;
		else
		{
			$ch = curl_init();
	
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$data = curl_exec($ch);
			curl_close($ch);
	
			if(empty($data))
			    throw new Exception('Verbindung zu feiertage-api.de war nicht moeglich.');
			else
			    return $data;
		}
		
		throw new Exception('Verbindung zu feiertage-api.de war nicht moeglich.');
	}
}

