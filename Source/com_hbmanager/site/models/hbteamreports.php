<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelHBteamReports extends JModelLegacy
{
	/**
	 * @var array messages
	 */
	private $params;
	private $games = array();
	public $teamkey;
	public $season;
	public $gameId;
	public $recentGameId;
	public $timezone;

	
	function __construct() 
	{
		parent::__construct();
		$this->params = self::getComponentParams();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($this->params);echo '</pre>';die();
		$this->timezone = is_null($this->params) ? false : (boolean) $this->params->get('timezone', false); //true: user-time, false:server-time
		self::setGameData();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($game);echo'</pre>';			
	}

	public function setGameData($gameId = null, $teamkey = null, $season = null) 
	{
		$this->teamkey = (is_null($teamkey) && !is_null($this->params)) ? $this->params->get('teamkey') : $teamkey;
		$this->season = (is_null($season) && !is_null($this->params)) ? $this->params->get('season') : $season;

		$this->games = self::getGamesList();

		$this->recentGameId = self::getRecentGameId();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($this->recentGameId);echo '</pre>';
		$this->gameId = (is_null($gameId)) ? $this->recentGameId : $gameId;
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($gameId);echo '</pre>';
		$this->gameParts = self::getGameParts();
	}

	private function getComponentParams()
	{
		$app = JFactory::getApplication();
		$menuitem   = $app->getMenu()->getActive(); 
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($menuitem); echo '</pre>';
		
		if (!is_null($menuitem)) {
			$params = $menuitem->params; // get the params
			return $params;
		}
		return null;
	}

	function getTeam($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
		return $team;
	}

	protected function getGamesList()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('spielIdHvw AS gameId, heim, gast, DATE(`datumZeit`) AS `datum`, spielberichtId IS NOT NULL AS report, min(actionIndex) IS NOT NULL AS actions, hb_spiel_spieler.kuerzel IS NOT NULL AS goals');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_spielbericht').' USING ('.$db->qn('spielIdHvw').')');
		$query->leftJoin($db->qn('hb_spielbericht_details').' USING ('.$db->qn('spielIdHvw').')');
		$query->leftJoin($db->qn('hb_spiel_spieler').' USING ('.$db->qn('spielIdHvw').')');
		$query->group('hb_spiel.spielIdHvw, hb_spiel.datumZeit,spielberichtId');
		$query->where('hb_spiel.'.$db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('eigenerVerein').' = 1');
		$query->where('DATE('.$db->qn('datumZeit').') < NOW() ');
		$query->order($db->qn('datumZeit').' DESC');
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games);echo '</pre>';
		return $games;
	}

	public function getGamesSelection()
    {
        $items = $this->games;

        if (!empty($items))
        {
        	foreach ($items as $item)
            {	
            	$game = new stdClass();
                $game->key = $item->gameId;
                $game->value = JHtml::_('date', $item->datum, 'd.m.Y', $this->timezone).' '.$item->heim.' - '.$item->gast.' ('.$item->gameId.')';
				$games[] = $game;
            }
        }
 		$games = array_reverse($games);
        return $games;
    }


	public function getReport()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->rightJoin($db->qn('hb_spielbericht').' USING ('.$db->qn('spielIdHvw').')');
		$query->where('hb_spiel.'.$db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$report = $db->loadObject();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games);echo '</pre>';
		return $report;
	}

	protected function getRecentGameId()
	{
		$games = $this->games;
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games);echo '</pre>';
		
		for ($i = 0; $i < count($games); $i++) {
			// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games[$i]);echo '</pre>';
			if ($games[$i]->actions OR $games[$i]->report OR $i == count($games)-1) {
				return $games[$i]->gameId;
			}
		}
		return null;
	}

	protected function getGameParts()
	{
		$games = $this->games;
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games);echo '</pre>';
		
		for ($i = 0; $i < count($games); $i++) {
			// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($games[$i]);echo '</pre>';
			if ($games[$i]->gameId == $this->gameId) {
				return $games[$i];
			}
		}
		return null;
	}

	function getGameChartData()
	{
		$data['gameinfo'] = self::getGameInfo();

		$data['gamedata'] = self::getGameDetails();

		$data['playerdata'] = self::getChartPlayers();

		$data['picarray'] = self::getPicArray();
 
		//echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';

		return $data;
	}

	public function getGameInfo()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('saison, spielIdHvw, ligaKuerzel, kuerzel, hallenNr, datumZeit, heim, gast, toreHeim,toreGast,bemerkung,berichtLink, hallenName, stadt');
		$query->from('hb_spiel');
		$query->leftJoin('hb_halle USING (hallenNr)');
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre'; 

		$db->setQuery($query);
		$infos = $db->loadObject();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre';
		$infos = self::formatGameInfos($infos);

		return $infos;
	}

	private function formatGameInfos($data)
	{		
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';
		$infos = null;
		$infos['saison'] = $data->saison;
        $infos['spielIdHvw'] = intval($data->spielIdHvw);
        $infos['kuerzel'] = $data->kuerzel;
        $infos['hallenNr'] = intval($data->hallenNr);
        $infos['datumZeit'] = JHtml::_('date', $data->datumZeit, 'Y-m-d H:i:s', $this->timezone);
        $infos['datum'] = JHtml::_('date', $data->datumZeit, 'd.m.Y', $this->timezone);
        $infos['zeit'] = JHtml::_('date', $data->datumZeit, 'G:i', $this->timezone);
        $infos['heim'] = $data->heim;
        $infos['gast'] = $data->gast;
        $infos['toreHeim'] = intval($data->toreHeim);
        $infos['toreGast'] = intval($data->toreGast);
        $infos['bemerkung'] = $data->bemerkung;
        $infos['berichtLink'] = $data->berichtLink;
        $infos['hallenName'] = $data->hallenName;
        $infos['stadt'] = $data->stadt;

		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre>';
		return $infos;
	}

	
	protected function getGameDetails()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spielbericht_details');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where($db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		$query->order($db->qn('actionIndex').' ASC');
		// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre';

		$db->setQuery($query);
		$details = $db->loadObjectList();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($details);echo '</pre';
		$details = self::formatDetails($details);

		return $details;
	}

	private function formatDetails($data)
	{		
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';
		$details = null;
		for ($i = 0; $i < count($data); $i++) {
			
			$details[$i]['season'] = $data[$i]->season;
            $details[$i]['spielIdHvw'] = intval($data[$i]->spielIdHvw);
            $details[$i]['actionIndex'] = intval($data[$i]->actionIndex);
            $details[$i]['timeString'] = $data[$i]->timeString;
            $details[$i]['time'] = intval($data[$i]->time);
            $details[$i]['scoreChange'] = intval($data[$i]->scoreChange);
            $details[$i]['scoreHome'] = intval($data[$i]->scoreHome);
            $details[$i]['scoreAway'] = intval($data[$i]->scoreAway);
            $details[$i]['scoreDiff'] = intval($data[$i]->scoreDiff);
            $details[$i]['text'] = $data[$i]->text;
            $details[$i]['number'] = intval($data[$i]->number);
            $details[$i]['name'] = $data[$i]->name;
            $details[$i]['alias'] = $data[$i]->alias;
            $details[$i]['team'] = intval($data[$i]->team);
            $details[$i]['category'] = $data[$i]->category;
            $details[$i]['stats_goals'] = intval($data[$i]->stats_goals);
            $details[$i]['stats_yellow'] = intval($data[$i]->stats_yellow);
            $details[$i]['stats_suspension'] = intval($data[$i]->stats_suspension);
            $details[$i]['stats_red'] = intval($data[$i]->stats_red); 
		}
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($details);echo '</pre>';
		return $details;
	}


	protected function getChartPlayers()
	{		
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select($db->qn(array('alias', 'spielIdHvw', 
			'trikotNr', 'tw', 'tore', 'tore7m', 
			'gelb', 'rot', 
			'teamZstr', 'id', 'name')));
		$query->select('hb_spiel_spieler.'.$db->qn('kuerzel').' AS '.$db->qn('kuerzel')
			.', hb_spiel_spieler.'.$db->qn('saison').' AS '.$db->qn('saison')
			.', hb_spiel_spieler.'.$db->qn('bemerkung').' AS '.$db->qn('bemerkung')
			.', hb_spiel_spieler.'.$db->qn('7m').' AS '.$db->qn('versuche7m')
			.', hb_spiel_spieler.'.$db->qn('2min1').' AS '.$db->qn('zweiMin1')
			.', hb_spiel_spieler.'.$db->qn('2min2').' AS '.$db->qn('zweiMin2')
			.', hb_spiel_spieler.'.$db->qn('2min3').' AS '.$db->qn('zweiMin3'));
		$query->from('hb_spiel_spieler');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');
		$query->where('hb_spiel_spieler.'.$db->qn('spielIdHvw').' = '.$db->q($this->gameId));
		$query->where($db->qn('trikotNr').' NOT IN ('.$db->q('A').','.$db->q('B').','
			. $db->q('C').','.$db->q('D').')');
		$query->order($db->qn('alias').' ASC');
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$playerdata = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($playerdata);
		$players = self::formatPlayers($playerdata);

		// echo __FILE__.' - '.__LINE__.'<pre>'; print_r($players); echo '</pre>';
		return $players;
	}
	
	private function formatPlayers($playerdata)
	{		
		$players = null;
		for ($i = 0; $i < count($playerdata); $i++) {
			
			$players[$i]['spielIdHvw'] = intval($playerdata[$i]->spielIdHvw);
			$players[$i]['name'] = $playerdata[$i]->name;
			$players[$i]['alias'] = $playerdata[$i]->alias;
			$players[$i]['saison'] = $playerdata[$i]->saison;
			$players[$i]['kuerzel'] = $playerdata[$i]->kuerzel;
			$players[$i]['trikotNr'] = $playerdata[$i]->trikotNr;
			$players[$i]['tw'] = $playerdata[$i]->tw;
			$players[$i]['tore'] = intval($playerdata[$i]->tore);
			$players[$i]['versuche7m'] = intval($playerdata[$i]->versuche7m);
			$players[$i]['tore7m'] = intval($playerdata[$i]->tore7m);
			$players[$i]['gelb'] = ($playerdata[$i]->gelb === "") ? null : 1;
			$players[$i]['zweiMin1'] = ($playerdata[$i]->zweiMin1 === "") ? null : 1;
			$players[$i]['zweiMin2'] = ($playerdata[$i]->zweiMin2 === "") ? null : 1;
			$players[$i]['zweiMin3'] = ($playerdata[$i]->zweiMin3 === "") ? null : 1;
			$players[$i]['rot'] = ($playerdata[$i]->rot === "") ? null : 1;
			// $players[$i]['bemerkung'] = $playerdata[$i]->bemerkung;
			// $players[$i]['teamZstr'] = $playerdata[$i]->teamZstr;
		}
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		return $players;
	}
        
    private function getPicArray()
    {
    	$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select($db->qn('alias'));
		$query->from('hb_spritesheets');
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->order($db->qn('index').' ASC');
		//echo __FILE__.' ('.__LINE__.')<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$picArray = $db->loadColumn();
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($picArray);echo '</pre>';

		    	// TODO move to DB
    	$picArray = array(
        "luis-herre",
        "andreas-haug",
        "dummy",
        "fabian-stegmaier",
        "felix-kohle",
        "florian-struecker",
        "lucas-herre",
        "bernd-schreyeck",
        "lukas-eberhart",
        "marcel-schick",
        "markus-schuler",
        "phillip-koch",
        "steffen-bechtold",
        "thorsten-schlaich"
        );

    	return $picArray;
    }
	
}