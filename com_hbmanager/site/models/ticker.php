<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelTicker extends HBmanagerModelHBmanager
{	

	public $url = null;
	public $test = true;

	public function __construct($config = array())
	{		
		self::setParams();

		parent::__construct($config);
	}

	private function setParams() 
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($params);echo'</pre>';
			
		$this->url = $params->get('hvwurl-ticker');
		
		$this->test = false;

		$menuitemid = JRequest::getInt('Itemid');		
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($menuparams);echo'</pre>';
			$this->test = $menuparams->get('test');
		}
		
	}

	public function getBaseUrl()
	{
		return $this->url;
	}

	public function getTestMode()
	{
		return $this->test;
	}


	public function getAdditionalGameInfo($gameId)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gameId);echo'</pre>';
		
		if(is_null($gameId) || $gameId === 'null') return null; 
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(' `teamkey`, `order`, `team`, `name`, `shortName`, `league`, t.`leagueKey`, `leagueIdHvw`, `sex`, `youth` ,`dateTime` ');
		$query->from($this->table_game);
		$query->leftJoin($db->qn($this->table_team).' as t USING ('.$db->qn('teamkey').')');
		$query->where($this->table_game.'.'.$db->qn('gameIdHvw').' = '.$db->q($gameId));
		$query->where($this->table_game.'.'.$db->qn('season').' = '.$db->q($this->season));
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$gameInfo = $db->loadObject();

		$gameInfo->gameLength = self::getGameLength ($gameInfo->youth);

		return $gameInfo;
	}


	private function getGameLength ($youth) {
		// https://de.wikipedia.org/wiki/Handball#Spieldauer
		// Aktive und A-Jugend: 	2 × 30 Minuten 
		// C-Jugend und B-Jugend: 	2 × 25 Minuten
		// E-Jugend und D-Jugend: 	2 × 20 Minuten
		// Pause von 10 Minuten
		$length = 60 * 60;
		switch ($youth) {
			case 'aktiv' || 'A':
				$length = 60 * 60;
				break;
			case 'B' || 'C':
				$length = 50 * 60;
				break;
			case 'D' || 'E':
				$length = 40 * 60;
				break;
			
			default:
				$length = 60 * 60;
				break;
		}
		return $length;
	}

}

