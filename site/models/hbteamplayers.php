<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class HBteamHomeModelHBteamPlayers extends JModelLegacy
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
			$this->saison = $menuparams->get('saison');
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
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey).
				' AND '.$db->qn('saison').' = '.$db->q($this->saison));
		$query->leftJoin($db->qn('hb_spieler').' USING ('.$db->qn('alias').')');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('#__contact_details').' USING ('.$db->qn('alias').')');

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
			if ($player->trainer == true) 
			{
				$positions[] = 'Trainer';
				$positionskurz[] = 'TR';
			}
			if ($player->TW == true) 
			{
				$positions[] = 'Torwart';
				$positionskurz[] = 'TW';
			}
			if ($player->LA == true) 
			{
				$positions[] = 'Linksaußen';
				$positionskurz[] = 'LA';
			}
			if ($player->RL == true) 
			{
				$positions[] = 'Rückraum-Links';
				$positionskurz[] = 'RL';
			}
			if ($player->RM == true) 
			{
				$positions[] = 'Rückraum-Mitte';
				$positionskurz[] = 'RM';
			}
			if ($player->RR == true) 
			{
				$positions[] = 'Rückraum-Rechts';
				$positionskurz[] = 'RR';
			}
			if ($player->RA == true) 
			{
				$positions[] = 'Rechtsaußen';
				$positionskurz[] = 'RA';
			}
			if ($player->KM == true) 
			{
				$positions[] = 'Kreis';
				$positionskurz[] = 'KM';
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
		$pic->saison = $pictureInfo->saison;
		$pic->comment = $pictureInfo->kommentar;
		$pic->caption = self::buildCaption($pictureInfo);
		return $pic;
	}
	

}