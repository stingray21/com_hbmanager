<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
$test = JTable::addIncludePath(JPATH_COMPONENT . '/tables');
//echo __FILE__.'<pre>';print_r( $test); echo'</pre>';
		

class HbmanagerModelHbgoalsinput extends JModelLegacy
{	
	
	private $gameId = '';
	private $season = '';
	private $teamkey = '';
	private $goalkeeper = array();
	
	
	function __construct() 
	{
		parent::__construct();
	}
	
	private function setGameInfo($gameId)
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('spielIdHvw, kuerzel, saison');
		$query->from('hb_spiel');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($gameId));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$info = $db->loadObject();
		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
		$this->gameId = $info->spielIdHvw;
		$this->teamkey = $info->kuerzel;
		$this->season = $info->saison;
	}
	
	
	function addGoals($input)
	{
		//echo __FUNCTION__.'<pre>';print_r($input); echo'</pre>';
		//$input['datum'] 
		self::setGameInfo($input['gameId']);
		self::setGoalkeeper($this->teamkey);
		//echo __FUNCTION__.'<pre>';print_r($this->goalkeeper); echo'</pre>';
		//parse the rows
		$goalsCsv = trim($input['goalsCsv']);
		if (!empty($goalsCsv)) {
			$values = self::getValues($goalsCsv);
		
			$table = JTable::getInstance('Goals','HbmanagerTable');
//			$tablekey = $table->getKeyName(true);
//			echo __FUNCTION__.__LINE__.'<pre>';print_r($tablekey); echo'</pre>';
			foreach ($values as $value) {
				$table->bind($value);
				$table->store();
			}
		}
		
	}
	
	private function getValues($goalsCsv) {
		$rows = str_getcsv($goalsCsv, "\n"); 
		//parse the items in rows
		foreach($rows as &$row) {
			$row = str_getcsv($row, ",", '"',"\\" );
			
			$value = self::formatValues($row);
			
			if (!empty($value['alias'])) {
				$values[] = $value;
			}
		}
		//echo __FUNCTION__.'<pre>';print_r($rows); echo'</pre>';
		//echo __FUNCTION__.'<pre>';print_r($values); echo'</pre>';
		return $values;
	}
	
	private function getAlias($name) {	
		// TODO: Look-up in DB instead
		
		$lowercaseName = strtolower($name);
		$search	 = array("ä", "ö", "ü", "ß", " ");
		$replace = array("ae", "oe", "ue", "ss", "-");
		
		$alias = str_replace($search, $replace, $lowercaseName);
		return $alias;
	}
			
	private function get7m($input) {	
		$penalties = explode('/', $input);
		if (empty($penalties[0])) {
			return 0;
		} else {
			return $penalties[0];
		}
	}
	
	private function get7mGoals($input) {	
		$penalties = explode('/', $input);
		if (count($penalties) == 1) {
			return 0;
		} else {
			return $penalties[1];
		}
	}
	
	private function getGoalkeeper($alias) {	
		if (array_key_exists($alias , $this->goalkeeper)) {
			$goalie = $this->goalkeeper[$alias];
		} else {
			$goalie = 0;
		}
		return $goalie;
	}
	
	private function setGoalkeeper($teamkey) {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('alias, kuerzel, TW');
		$query->from('hb_mannschaft_spieler');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$info = $db->loadObjectList();
		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
		$goalkeeper = array();
		foreach ($info as $player) {
			$goalkeeper[$player->alias] = $player->TW;
		}
		$this->goalkeeper = $goalkeeper;
	}
	
	protected function formatValues($row) {
		$value = array();
		$value['spielIdHvw'] = $this->gameId;
		$alias = self::getAlias($row[1]);
		$value['alias'] = $alias;
		//$birthday = $row[2];
		$value['saison'] = $this->season;
		$value['kuerzel'] = $this->teamkey;
		$value['trikotNr'] = $row[0];
		$value['tw'] = self::getGoalkeeper($alias);
		$value['tore'] = $row[3];
		$value['7m'] = self::get7m($row[4]);
		$value['tore7m'] = self::get7mGoals($row[4]);
		$value['gelb'] = $row[5];
		$value['2min1'] = $row[6];
		$value['2min2'] = $row[7];
		$value['2min3'] = $row[8];
		$value['rot'] = $row[9];
		$value['bemerkung'] = $row[10];
		$value['teamZstr'] = $row[11];
		
		return $value;
	}
	
}