<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelHBteamPlayers extends JModelLegacy
{
	/**
	 * @var array messages
	 */
	protected $teamkey;
	
	function __construct() 
	{
		parent::__construct();
		
		//request the selected teamkey
			$menuitemid = JRequest::getInt('Itemid');
			if ($menuitemid)
			{
				$menu = JFactory::getApplication()->getMenu();
				$menuparams = $menu->getParams($menuitemid);
			}
			$this->teamkey = $menuparams->get('teamkey');
			$this->season = $menuparams->get('saison');
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
	
	function getPlayers($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*, hb_mannschaft.name AS mannschaftsname,'.
			' TIMESTAMPDIFF(YEAR, geburtstag, CURDATE()) AS '.$db->qn('alter'));
		$query->from('hb_mannschaft_spieler');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey), 'AND');
		// TODO Season 
		//$query->where($db->qn('saison').' = '.$db->q($this->season));
		$query->leftJoin($db->qn('hb_spieler').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');
		$query->order($db->qn('trikotNr'));
		
		//echo '=> model->$query <br><pre>'; echo $query; echo '</pre>';
		$db->setQuery($query);
		$players = $db->loadObjectList();
		//echo '=> model->players<br><pre>'; print_r($players); echo '</pre>';
		
		$players = self::addPositions($players);
		return $players;
	}
	
	protected function addPositions($players) {
		
		foreach ($players as $player)
		{
			$positions = array();
			$positionskurz = array();
			
			$positionKeys = array('trainer', 'TW', 'LA', 'RL', 'RM', 'RR', 'RA', 'KM');
			$positionNames = array('Trainer', 'Torwart', 'Linksaußen', 'Rückraum-Links',
				'Rückraum-Mitte', 'Rückraum-Rechts', 'Rechtsaußen', 'Kreis');
			$positionAbrv = array('TR', 'TW', 'LA', 'RL', 'RM', 'RR', 'RA', 'KM');
			
			foreach ($positionKeys as $i => $key)
			{
				if ($player->{$key} == true) 
				{
					$positions[] = $positionNames[$i];
					$positionskurz[] = $positionAbrv[$i];
				}
			}
			$player->positions = implode(', ', $positions);
			$player->positionskurz = $positionskurz;
		}
		return $players;
	}
	
	function getPictureInfo()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaftsfoto');
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$pictureInfo = $db->loadObject();
		return $pictureInfo;
	}
	
	function getPicture()
	{
		$pictureInfo = self::getPictureInfo();
		if (empty($pictureInfo)) return null;
		//echo '=> model->pictureInfo<br><pre>'; print_r($pictureInfo); echo '</pre>';
		$pic = new stdClass();
		$pic->filename = $pictureInfo->dateiname;
		$pic->season = $pictureInfo->season;
		$pic->comment = $pictureInfo->kommentar;
		$pic->caption = self::buildCaption($pictureInfo);
		return $pic;
	}
	

}