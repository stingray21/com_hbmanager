<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 


class HBmanagerModelHBmanager extends JModelLegacy
{	
	protected $tz = false; //true: user-time, false:server-time
 	protected $season = null;
 	protected $teamkey = null;


 	protected $table_team 				= '#__hb_team';
	protected $table_team_picture 		= '#__hb_team_picture';
	protected $table_training 			= '#__hb_training';
	protected $table_team_coach			= '#__hb_team_coach';
	protected $table_gym 				= '#__hb_gym';
	protected $table_game 				= '#__hb_game';
	protected $table_gamereport			= '#__hb_gamereport';
	protected $table_updatelog			= '#__hb_updatelog';
	protected $table_standings 			= '#__hb_standings';
	protected $table_standings_details 	= '#__hb_standings_details';
	protected $table_team_details 		= '#__hb_team_details';

	public function __construct($config = array())
	{

		//request the selected teamkey
		$menuitemid = JRequest::getInt('Itemid');		
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($menuparams);echo'</pre>';
			$params_teamkey = $menuparams->get('teamkey');
			$params_season = $menuparams->get('season');
		}
		
		$this->teamkey 	= !empty($params_teamkey) ? $params_teamkey : 'dummy';
		$this->season 	= !empty($params_season) ? $params_season : HbmanagerHelper::getCurrentSeason();
		$this->tz 		= HbmanagerHelper::getHbTimezone();
		
		parent::__construct($config);
    }

	public function getTeam($teamkey = null)
	{
		$teamkey = ($teamkey === null) ? $this->teamkey : null;
		
		if (empty($teamkey)) return null;

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->table_team);
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		// $query->where($db->qn('season').' = '.$db->q($this->season));
		// $query->order('ISNULL('.$db->qn('order').'), '.$db->qn('order').' ASC');
		// echo __FILE__.' ('.__LINE__.')<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$team = $db->loadObject();	

		// echo __FILE__.' ('.__LINE__.')<pre>'; print_r($team); echo '</pre>';
		
		return $team;
	}

	public function getTeams($teamkey = null)
	{
		$teams = null;
		
		return $teams;
	}

}

