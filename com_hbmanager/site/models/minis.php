<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelMinis extends HBmanagerModelHBmanager
{	
	private $show_params = [];
	private $contact_global = [];
	private $domain = null;
	private $minisTeams = [];


	public function __construct($config = array())
	{		
		self::setShowParams();
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter
		$this->domain = $params->get('emaildomain');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->show_params);echo'</pre>';
		parent::__construct($config);
		self::setMiniTeams();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->miniTeams);echo'</pre>';
		self::setTeams();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->teams);echo'</pre>';
		
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

	private function setMiniTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('teamkey');
		$query->from($this->table_team);
		$query->where($db->qn('youth').' = '.$db->q('minis'));
		// $query->where($db->qn('season').' = '.$db->q($this->season));
		// echo __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$teams = $db->loadColumn();	
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		$this->miniTeams = $teams;
	}

	private function setTeams()
	{
		$teams = [];
		foreach ($this->miniTeams as $key => $teamkey) {
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*, t1.teamkey AS teamkey');
			$query->from($this->table_team.' AS t1');
			$query->leftJoin($db->qn($this->table_team_picture).' AS t2 ON t1.teamkey=t2.teamkey AND '.$db->qn('season').' = '.$db->q($this->season));
			$query->where('t1.teamkey='.$db->q($teamkey));
			// echo __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
			$db->setQuery($query);
			$teams[$key] = $db->loadObject();	
			
			// echo __FILE__.' ('.__LINE__.')<pre>'; print_r($team); echo '</pre>';
			if (!empty($teams[$key])) {
				$teams[$key] = self::addPictureData($teams[$key]);
				$teams[$key]->emailAlias = self::getEmailAlias($teams[$key]->email);
			}
		}
		$this->teams = $teams;
	}

	public function getTeams($teamkey = NULL)
	{
		return $this->teams;
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
		$trainings = [];
		foreach ($this->miniTeams as $key => $teamkey) {
			// getting training information
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*, DATE_FORMAT(start, \'%H:%i\') as start,'.
				'DATE_FORMAT(end, \'%H:%i\') as end');
			$query->from($db->qn($this->table_training));
			$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
			$query->leftJoin($this->table_gym.' USING (gymId)');
			$query->order("FIELD(`day`, 'MO', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So')");

			$db->setQuery($query);
			$trainings[$key] = $db->loadObjectList ();
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($trainings);echo'</pre>';
		}
		return $trainings;
	}
	
	
	public function getCoaches()
	{
		$coachingTeams = [];
		foreach ($this->miniTeams as $key => $teamkey) {
			// getting trainer information
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('`alias`, `coachID`, `teamkey`, `rank`, `id`, `name`, `telephone`, `image`, `email_to`, `mobile`, `params`');
			$query->from($db->qn($this->table_team_coach));
			$query->where('teamkey = '.$db->q($teamkey));
			$query->where('season = '.$db->q($this->season));
			$query->leftJoin('#__contact_details USING (alias)');
			$query->order('IF(ISNULL(`rank`),1,0),`rank` DESC');
			// echo __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
			$db->setQuery($query);
			$coaches = $db->loadObjectList ();
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($coaches);echo'</pre>';
			$coachingTeams[$key] = self::addContact($coaches);
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($coaches);echo'</pre>';
		}
		return $coachingTeams;
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


	
}

