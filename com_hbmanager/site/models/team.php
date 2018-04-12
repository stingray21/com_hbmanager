<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelTeam extends HBmanagerModelHBmanager
{	
	private $show_params = [];
	private $contact_global = [];
	private $domain = null;
	private $team = null;


	public function __construct($config = array())
	{		
		self::setShowParams();
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter
		$this->domain = $params->get('emaildomain');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->show_params);echo'</pre>';
		parent::__construct($config);
		self::setTeam();
	}

	public function getShowParams () 
	{
		return $this->show_params;
	}

	private function setShowParams () 
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter

		$menuitemid = JRequest::getInt('Itemid');		
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($menuparams);echo'</pre>';
		}
		$fields = ['team', 'picture', 'training', 'email', 'schedule', 'standings', 'standings_type', 'hvwLink'];
		foreach ($fields as $field) 
		{
			if (!is_null($menuparams->get('show_'.$field))) {
				$this->show_params[$field] = $menuparams->get('show_'.$field);
			} else {
				$this->show_params[$field] = $params->get('show_'.$field);
			}
		}

		$this->show_params['schedule_params']['reports'] = 1;
		$this->show_params['schedule_params']['indicator'] = 1;

		$this->show_params['hvwLink'] = 1;

		self::setGlobalContactParams();
	}

	private function setGlobalContactParams()
	{
		
		$globalParams = JComponentHelper::getParams('com_contact'); // global config parameter

		$items = array('email','mobile','telephone');
		$global_show = null;
		foreach ($items as $value){
			$this->contact_global[$value] = $globalParams->get('show_'.$value);
		}
	}

	private function setTeam($teamkey = null)
	{
		$teamkey = ($teamkey === null) ? $this->teamkey : null;
		
		if (empty($teamkey)) return null;
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->table_team);
		$query->leftJoin($db->qn($this->table_team_picture).' USING ('.$db->qn('teamkey').')');
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		// $query->where($db->qn('season').' = '.$db->q($this->season));
		// echo __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$team = $db->loadObject();	

		// echo __FILE__.' ('.__LINE__.')<pre>'; print_r($team); echo '</pre>';
		if (!empty($team)) {
			$team = self::addPictureData($team);
			$team->emailAlias = self::getEmailAlias($team->email);
			$team->updateSchedule = self::getUpdate('schedule');
			$team->updateStandings = self::getUpdate('standings');
			$team->updateStandingsDetails = self::getUpdate('standingsDetails');

			$team->hvwLinkUrl = HbmanagerHelper::get_hvw_page_url($team->leagueIdHvw);
			$team->reportMenuLink = self::getReportMenuLink();
		}
		$this->team = $team;
	}

	public function getTeam($teamkey = null)
	{
		return $this->team;
	}

	public function getUpdate($type)
	{
		// getting training information
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('dateTime');
		$query->from($db->qn($this->table_updatelog));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn($type).' = 1');
		$query->order("dateTime DESC");

		$db->setQuery($query);
		$date = $db->loadResult();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($date);echo'</pre>';
		return $date;
	}

	private function addPictureData($team) 
	{
		$paths = [];
		$resolutions = [200, 500, 800, 1200];
		foreach ($resolutions as $res) {
			$path = './images/handball/teams/'.$this->season.'/'.$res.'px/team_'.$this->teamkey.'_'.$this->season.'_'.$res.'px.png';
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($path);echo'</pre>';	
			if (file_exists($path)) {
				$paths[$res.'px'] = $path;
			}
		}

		if (count($paths) < 1) {
			$this->show_params['picture'] = 0;
		}
		$team->paths = $paths;
		$team->caption = json_decode($team->caption);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($paths);echo'</pre>';
		return $team;
	}

	// ============ Training ===========================================================

	
	public function getTraining ()
	{
		// getting training information
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*, DATE_FORMAT(start, \'%H:%i\') as start,'.
			'DATE_FORMAT(end, \'%H:%i\') as end');
		$query->from($db->qn($this->table_training));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->leftJoin($this->table_gym.' USING (gymId)');
		$query->order("FIELD(`day`, 'MO', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So')");

		$db->setQuery($query);
		$trainings = $db->loadObjectList ();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($trainings);echo'</pre>';
		return $trainings;
	}
	
	
	public function getCoaches()
	{
		// getting trainer information
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('`alias`, `coachID`, `teamkey`, `rank`, `id`, `name`, `telephone`, `image`, `email_to`, `mobile`, `params`');
		$query->from($db->qn($this->table_team_coach));
		$query->where('teamkey = '.$db->q($this->teamkey));
		$query->where('season = '.$db->q($this->season));
		$query->leftJoin('#__contact_details USING (alias)');
		$query->order('IF(ISNULL(`rank`),1,0),`rank` DESC');
		$db->setQuery($query);
		$coaches = $db->loadObjectList ();
		$coaches = self::addContact($coaches);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($coaches);echo'</pre>';
		return $coaches;
	}

	private function addContact($coaches) 
	{
		foreach ($coaches as &$coach)
		{
			$params = new JRegistry();
			if ($coach && isset($coach->params)) $params->loadString($coach->params);	
			$contact_show['telephone'] = !is_null($params->get('show_telephone')) ? $params->get('show_telephone') : $this->contact_global['telephone'];
			$contact_show['mobile'] = !is_null($params->get('show_mobile')) ? $params->get('show_mobile') : $this->contact_global['mobile'];
			$contact_show['email_to'] = !is_null($params->get('show_email_to')) ? $params->get('show_email_to') : $this->contact_global['email'];

			$items = array('email_to','mobile','telephone');
			foreach ($items as $value)
			{
				if (!$contact_show[$value]) $coach->{$value} = null;
			}
			if ($this->show_params['email'] === 'alias') $coach->email_to = null;
		}
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($coaches);echo'</pre>';
		return $coaches;
	}
	
	private function getEmailAlias($name) {
		$emailAlias = null;
		if (!empty($name) && !empty($this->domain) && $this->show_params['email'] === 'alias'){
			$emailAlias = $name.'@'.$this->domain;
		}					
		return $emailAlias;
	}


	// ============ Schedule ===========================================================



	public function getSchedule()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*, '.
				'IF('.$db->qn('home').' = '.$db->q($this->team->shortName).',1,0) as homegame, '.
				'(CASE '.
				'WHEN '.$db->qn('pointsHome').' > '.$db->qn('pointsAway').' THEN 1 '.
				'WHEN '.$db->qn('pointsHome').' < '.$db->qn('pointsAway').' THEN 2 '.
				'WHEN ('.$db->qn('pointsHome').' = '.$db->qn('pointsAway').') AND'.
					$db->qn('pointsHome').' IS NOT NULL THEN 0 '.
				'ELSE NULL '.
				'END ) AS `result`,
				homenames.teamname_long AS home_long, homenames.teamname_short AS home_short, homenames.teamname_abbr AS home_abbr,
				awaynames.teamname_long AS away_long, awaynames.teamname_short AS away_short, awaynames.teamname_abbr AS away_abbr '
				);
		$query->from($db->qn($this->table_game));
		$query->leftJoin($db->qn($this->table_gamereport).' USING ('.$db->qn('gameIdHvw').', '.$db->qn('season').')');
		$query->leftJoin($db->qn($this->table_gym).' USING ('.$db->qn('gymId').')');
		$query->leftJoin($db->qn('#__hb_clubteams').' AS homenames ON '.$db->qn('home').'='.$db->qn('homenames').'.'.$db->qn('teamname_short'));
		$query->leftJoin($db->qn('#__hb_clubteams').' AS awaynames ON '.$db->qn('away').'='.$db->qn('awaynames').'.'.$db->qn('teamname_short'));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where('('.$db->qn('home').' = '.$db->q($this->team->shortName).' OR '.
					$db->qn('away').' = '.$db->q($this->team->shortName).')');		
		$query->order($db->qn('dateTime'));
		// echo __FILE__.' ('.__LINE__.'):<pre>'.str_replace('#_', 'hkog', $query).'</pre>';die;
		
		$db->setQuery($query);
		$schedule = $db->loadObjectList();
		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($schedule);echo'</pre>';die;
		

		if (is_null($posts=$db->loadRowList())) 
		{
				$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
				return;
		}
		// $schedule = self::addBackground($schedule);
		// $schedule = self::addResult($schedule);
		// $schedule = self::addHighlightNextGame($schedule);
		return $schedule;
	}
	
	// public static function getReportNr($team)
	// {
	// 	$db = JFactory::getDBO();
	// 	$query = $db->getQuery(true);
	// 	$query->select('COUNT(*)');
	// 	$query->from($db->qn('hb_spielbericht'));
	// 	$query->innerJoin($db->qn('hb_spiel').' USING ('.$db->qn('spielIdHvw').')');
	// 	$query->where($db->qn('Kuerzel').' = '.$db->q($team->kuerzel));
	// 	$db->setQuery($query);
	// 	$recaps = $db->loadResult();
	// 	//echo "<p>recaps</p><pre>"; print_r($recaps); echo "</pre>";
	// }
	
	
	// protected static function addBackground ($schedule)
	// {
	// 	$background = false;
	// 	foreach ($schedule as $row)
	// 	{
	// 		// switch color of background
	// 		$background = !$background;
	// 		// check value of background
	// 		switch ($background) 
	// 		{
	// 			case true: 
	// 				$row->background = 'odd'; 
	// 				break;
	// 			case false: 
	// 				$row->background = 'even'; 
	// 				break;
	// 		}
	// 	}
	// 	return $schedule;
	// }
	
	// protected static function addHighlightNextGame ($schedule)
	// {
	// 	$highlighted = false;
	// 	foreach ($schedule as $row)
	// 	{
	// 		if (time() <= strtotime($row->datumZeit) && !$highlighted) {
	// 			$row->highlight = true; 
	// 			$highlighted = true;
	// 		} else {
	// 			$row->highlight = false; 
	// 		}
	// 	}
	// 	return $schedule;
	// }
	
	// protected static function addResult ($schedule)
	// {
	// 	foreach ($schedule as $row)
	// {
	// 		if (($row->heimspiel && $row->ergebnis == 1) ||
	// 				(!$row->heimspiel && $row->ergebnis == 2)) {
	// 			$row->ampel = " win";
	// 		}
	// 		elseif (($row->heimspiel && $row->ergebnis == 2)||
	// 				(!$row->heimspiel && $row->ergebnis == 1)) {
	// 			$row->ampel = " loss";
	// 		}
	// 		elseif ($row->ergebnis == 0) {
	// 			$row->ampel = " tie";
	// 		}
	// 		else {
	// 			$row->ampel = "";
	// 		}
	// 	}
	// 	return $schedule;
	// }



	// ============ Standings ===========================================================



	public function getStandings()
	{
		if ($this->show_params['standings_type'] === 'details' ) {
			$standings = self::getStandingsDetails();
		}
		else {
			$standings = self::getStandingsStandard();
		}

		return $standings;
	}

	public function getStandingsStandard()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn($this->table_standings));
		$query->leftJoin($db->qn('#__hb_clubteams').' ON '.$db->qn('team').'='.$db->qn('teamname_long'));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('season').' = '.$db->q($this->season));		
		$query->order($db->qn('rank'));
		$db->setQuery($query);
		$standings = $db->loadObjectList();
		
		if (is_null($posts=$db->loadRowList())) 
		{
				$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
				return;
		}

		return $standings;
	}

	public function getStandingsDetails()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn($this->table_standings_details));
		$query->leftJoin($db->qn('#__hb_clubteams').' ON '.$db->qn('team').'='.$db->qn('teamname_long'));
		$query->where($db->qn('teamkey').' = '.$db->q($this->teamkey));
		$query->where($db->qn('season').' = '.$db->q($this->season));		
		$query->order($db->qn('rank'));
		$db->setQuery($query);
		$standings = $db->loadObjectList();
		
		if (is_null($posts=$db->loadRowList())) 
		{
				$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
				return;
		}

		return $standings;
	}

	private function getReportMenuLink()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// $query->select(`alias`, `link`, `path`, `title`);
		$query->select('`path`');
		$query->from($db->qn('#__menu'));
		$query->where($db->qn('link').' = '.$db->q('index.php?option=com_hbmanager&view=gamereports'));
		$query->where($db->qn('params').' REGEXP \'{"teamkey":[^}]*"'.$this->teamkey.'"[^}]*}\'');
		// $query->where(' JSON_EXTRACT(`params`, "$.teamkey") = \''.$db->q($this->teamkey).'\''); // MySQL 5.7 or MariaDB 10.2.3
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		// $link = $db->loadObject();
		$link = $db->loadResult();

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($link);echo'</pre>';
		return $link;
	}
}

