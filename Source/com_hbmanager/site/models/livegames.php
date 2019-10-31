<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelLivegames extends HBmanagerModelHBmanager
{	
	private $params = [];
	private $url = null;
	private $test = false;

	public function __construct($config = array())
	{		
		self::setParams();
		parent::__construct($config);
	}

	private function setParams() 
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($params);echo'</pre>';
			
		$this->url = $params->get('hvwurl-live');
		
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

	public function getGames($test = false) {
		// $url = './ticker/live_overview.json';
		// $url = 'https://spo.handball4all.de/service/if_g_json.php?cmd=po&o=11&og=3';
		
		if ($this->test) {
			$games = self::getTestGames();
			return $games;
		} 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->url);echo'</pre>';
		// TODO: Case of incorrect url and notifaction why no games are shown 
		if (!empty($this->url)) {
			$games = self::getLiveGames($this->url);
		} else {
			$games = null;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		return $games;
	}

	public function getTestGames() {

		$games[0]['gID'] = '2646916';
		$games[0]['sGID'] = '0';
		$games[0]['gNo'] = '70229';
		$games[0]['live'] = 1;
		$games[0]['gToken'] = 'test';
		$games[0]['gAppid'] = '';
		$games[0]['gDate'] = '28.10.18';
		$games[0]['gTime'] = '17:00';
		$games[0]['gGymnasiumID'] = '340';
		$games[0]['gGymnasiumNo'] = '7003';
		$games[0]['gGymnasiumName'] = 'Kreissporthalle';
		$games[0]['gGymnasiumPostal'] = '72336';
		$games[0]['gGymnasiumTown'] = 'Balingen';
		$games[0]['gGymnasiumStreet'] = 'Steinachstraße 19';
		$games[0]['gHomeTeam'] = 'HK Ostd/Geisl';
		$games[0]['gGuestTeam'] = 'HSG Test';
		$games[0]['gHomeGoals'] = ' ';
		$games[0]['gGuestGoals'] = ' ';
		$games[0]['gHomeGoals_1'] = ' ';
		$games[0]['gGuestGoals_1'] = ' ';
		$games[0]['gHomePoints'] = ' ';
		$games[0]['gGuestPoints'] = ' ';
		$games[0]['gComment'] = ' ';
		$games[0]['gReferee'] = 'Grathwohl,Kocbek';
		$games[0]['teamkey'] = 'M-2';
		$games[0]['order'] = '3';
		$games[0]['team'] = 'Männer 1';
		$games[0]['name'] = 'HK Ostdorf/Geislingen';
		$games[0]['shortName'] = 'HK Ostd/Geisl';
		$games[0]['leagueKey'] = 'M-BK';
		$games[0]['league'] = 'Bezirksklasse';
		$games[0]['leagueIdHvw'] = '35341';
		$games[0]['sex'] = 'm';
		$games[0]['youth'] = 'aktiv';
		$games[0]['dateTime'] = '2018-10-28 16:00:00';
		$games[0]['shortGym'] = 'KSH';

		return $games;
	} 

	protected function getLiveGames($url) 
	{
		// Error handler that includes warnings
		set_error_handler(array($this, 'warning_handler'), E_WARNING);
		//echo __FILE__.' - '.__LINE__.'<pre>';print_r($url); echo'</pre>';
		// $json = self::testCase();
		try 
		{
			$json = file_get_contents($url);
		} catch (Exception $e) {	
			// echo 'Exception: ',  $e->getMessage(), "\n";
			$json = null;
			$hvwData['error'] = "no HVW data ($url)";
			$hvwData['message'] = $e->getMessage();
		}	
		restore_error_handler();
		if (empty($json)) return $hvwData;

		$obj = json_decode($json, true);
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($obj); echo'</pre>';

		$hvwData 	= $obj[0]['content']['classes'];

		foreach ($hvwData as $league) {
			foreach ($league['games'] as $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				// if (!empty($game['gToken'])) {
				if (preg_match('/HK Ostd\/Geisl/', $game['gHomeTeam'].$game['gGuestTeam'])) {
					$game = array_merge($game, self::getGameDetails($game['gNo']));
					// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
					$game['shortGym'] = self::getShortGym($game['gGymnasiumName'].', '.$game['gGymnasiumTown']);
					$games[] = $game;
				}
				// if ($game['live']) {
				// 	$games[] = $game;
				// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				// }
			}
		}

		usort($games, 'self::compareGames');
		// foreach ($games as $key => $val) {
		// 	echo "<p>".$val['dateTime']." - ".$val['order']."</p>";
		// }
		$games = self::groupGames($games);
		// echo __FILE__.' - '.__LINE__.'<pre>';print_r($games); echo'</pre>';
		return $games;
	}

	private function getShortGym($gym) {
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gym);echo'</pre>';
		
		switch ($gym) {
			case 'Kreissporthalle, Balingen':
				$short = 'KSH';
				break;
			case 'Schloßparkhalle, Geislingen':
				$short = 'SPH';
				break;
			
			default:
				$short = '';
				break;
		}

		return $short;
	}

	private function groupGames($games) {
		
		foreach ($games as $key => $val) {
			$date = preg_replace('/^(\d{1,2}).(\d{1,2}).(\d{1,2})$/', '20$3-$2-$1', $val['gDate'] );
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $date.' 12:00:00')->getTimestamp();
			$groupedGames[$date][] = $val;
		}

		return $groupedGames;
	}

	// Comparison function
	private function compareGames($a, $b) {
		
		// sort by date
		$aa = DateTime::createFromFormat('Y-m-d H:i:s', $a['dateTime'])->getTimestamp();
		$bb = DateTime::createFromFormat('Y-m-d H:i:s', $b['dateTime'])->getTimestamp();
		
		if ($aa == $bb) {
			if ($a['order'] == $b['order']) {
				return 0;
			}
			return ($a['order'] < $b['order']) ? -1 : 1;
		}
		return ($aa < $bb) ? -1 : 1;

		// sort by order
		// $a = $a['order'];
		// $b = $b['order'];
		
		// if ($a == $b) {
		// 	return 0;
		// }
		// return ($a < $b) ? -1 : 1;
	}


	function warning_handler($errno, $errstr, $errfile, $errline)
	{
		$message = "<br />\n<b>Warning</b>:  $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
		// echo $message;
		throw new Exception($message);
		/* Don't execute PHP internal error handler */
		return false;
	}

	protected function getGameDetails($gameID) {

		// Get the database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(' `teamkey`, `order`, `team`, `name`, `shortName`, `league`, t.`leagueKey`, `leagueIdHvw`, `sex`, `youth` ,`dateTime` ');
		$query->from($this->table_game);
		$query->leftJoin($db->qn($this->table_team).' as t USING ('.$db->qn('teamkey').')');
		$query->where($this->table_game.'.'.$db->qn('gameIdHvw').' = '.$db->q($gameID));
		$query->where($this->table_game.'.'.$db->qn('season').' = '.$db->q($this->season));
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$game = $db->loadAssoc();
		return $game;
	} 
}


