<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 

JLoader::register('HBmanagerModelGameDetails', JPATH_COMPONENT_ADMINISTRATOR . '/models/gamedetails.php');
// Require helper file
JLoader::register('HbmanagerHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/hbmanager.php');

class HBmanagerModelGameDetailsUpdate extends HBmanagerModelGameDetails
// class HBmanagerModelUpdate extends JModelLegacy
{	

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
    }

	
	public function getGamePdfList()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);

		$query->select('`gameIdHvw`, `teamkey`, `reportHvwId`');

		$query->from($this->table_game.' AS game');
		$query->leftJoin($db->qn($this->table_team).' USING ('.$db->qn('teamkey').')');
		$query->leftJoin($db->qn($this->table_gamedetails).' USING ('.$db->qn('gameIdHvw').', '.$db->qn('season').')');
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->where('('.$db->qn('timeString').' = "" OR '.$db->qn('timeString').' IS NULL)');
		$query->where($db->qn('youth').' = '.$db->q('aktiv'));
		$query->where($db->qn('ownClub').' = 1');
		$query->where('DATE('.$db->qn('dateTime').') < '.$db->q($this->today->format('Y-m-d')));
		$query->order($db->qn('dateTime').' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;
	
		foreach ($games as &$game) {
			$game->link = HbmanagerHelper::get_hvw_report_url($game->reportHvwId);
		}
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;
		
		// http://.../index.php?option=com_hbmanager&task=getGamePdfList&format=raw

		return $games;
	}

	public function getGameImportList()
	{
		$games = self::getGames();
		//  echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';die;
		$imports = [];

		foreach ($games as $game) {
			if (!$game->imported && property_exists($game, 'importFilename')) {
				$import = $game;	
				$import->response = self::insertGameData($game->gameIdHvw, substr($game->dateTime,0,10));
				$imports[] = $import;
			}
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($imports);echo'</pre>';
		if (empty($imports)) return 'no imports';
		return $imports;
	}
}

