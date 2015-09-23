<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
$test = JTable::addIncludePath(JPATH_COMPONENT . '/tables');
//echo __FILE__.'<pre>';print_r( $test); echo'</pre>';
		

class HbmanagerModelHbgoalsinput extends JModelLegacy
{	
	
	private $gameId = null;
	private $season = null;
	private $teamkey = null;
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
		$goalsCsv = self::removeHeader(trim($input['goalsCsv']));
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
		$this->importedGameId = $input['gameId'];
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
	
	private function removeHeader($goalsCsv) {
		//echo __FUNCTION__.' with Header <pre>';print_r($goalsCsv); echo'</pre>';
		$headerString = "Nr.,Name,Jahrgang,M,R,Tore\r\n(ges),7m/\r\nTore,Verw.,Hinausstellungen,,,Disq.,Ber.,Team-\r\nZstr.\r\n,,,,,,,,1.,2.,3.,,,\r\n";
		$goalsCsv = str_replace($headerString, '', $goalsCsv);
		//echo __FUNCTION__.' without Header <pre>';print_r($goalsCsv); echo'</pre>';
		return $goalsCsv;
	}
	
	private function getAlias($name) {	
		// TODO: Look-up in DB instead
		//echo __FUNCTION__.' <pre>';print_r($name); echo'</pre>';
//		$encoding = mb_detect_encoding($name);
//		echo __FUNCTION__.' <pre>';print_r($encoding); echo'</pre>';
		
		$search	 = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", " ");
		$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "-");
//		$cleanedName = str_replace($search, $replace, $name);
//		echo __FUNCTION__.' <pre>';print_r($cleanedName); echo'</pre>';
//		$lowercaseName = strtolower($cleanedName);
//		echo __FUNCTION__.' <pre>';print_r($lowercaseName); echo'</pre>';
		//$name = utf8_decode($name);
		
		$alias = strtolower(str_replace($search, $replace, $name));
		//echo __FUNCTION__.' <pre>';print_r($alias); echo'</pre>';
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
		//echo __FUNCTION__.'<pre>';print_r($row); echo'</pre>';
		$value = array();
		$value['spielIdHvw'] = $this->gameId;
		$alias = self::getAlias($row[1]);
		$value['alias'] = $alias;
		//$birthday = $row[2];
		$value['saison'] = $this->season;
		$value['kuerzel'] = $this->teamkey;
		$value['trikotNr'] = $row[0];
		$value['tw'] = self::getGoalkeeper($alias);
		$value['tore'] = ($row[5] != '') ? $row[5] : 0;
		$value['7m'] = self::get7m($row[6]);
		$value['tore7m'] = self::get7mGoals($row[6]);
		$value['gelb'] = $row[7];
		$value['2min1'] = $row[8];
		$value['2min2'] = $row[9];
		$value['2min3'] = $row[10];
		$value['rot'] = $row[11];
		$value['bemerkung'] = $row[12];
		$value['teamZstr'] = $row[13];
		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
	}
	
	public function getConfirmation() {	
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.
				$db->qn('alias').')');
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->order('('.$db->qn('trikotNr').'*1 = 0) ,'.$db->qn('trikotNr').'*1, '.$db->qn('trikotNr') );
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$info = $db->loadAssocList();
		//echo __FUNCTION__.'<pre>';print_r($info); echo'</pre>';
		return $info;
	}
}