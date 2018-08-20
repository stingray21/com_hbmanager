<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


JLoader::register('HBmanagerModelGames', JPATH_COMPONENT_ADMINISTRATOR . '/models/games.php');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelGamesHome extends HBmanagerModelGames
{
	
	function __construct() 
	{
		parent::__construct();
	}

	public function getGames()
	{
		$games = self::getAllHomeGames();
		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';

		return $games;
	}

	
	public function getAllHomeGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);

		$query->select('*');

		$query->from($this->tables->game);
		$query->leftJoin($db->qn($this->tables->team).
			' USING ('.$db->qn('teamkey').')');
		$query->leftJoin($db->qn($this->tables->gym).
				' USING ('.$db->qn('gymId').')');
		
		$query->where($db->qn('ownClub').' = '.$db->q(1));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->order($db->qn('dateTime').' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		$games = self::organizeHomeGames($games);
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;
		return $games;
	}

	private function organizeHomeGames($games)
	{
		$games = self::groupByDay($games);
		$games = self::sortByTime($games);
		
		$gyms = HbmanagerHelper::getHomeGyms();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gyms);echo'</pre>';
		$homegames = [];

		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;

		foreach ($games as $date => $days) {
			foreach ($days as $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game->gymId);echo'</pre>';
				if (in_array($game->gymId, $gyms)) {
					$homegames[$date][$game->gymId][] = $game;
				}
			}
		}
		return $homegames;
	}	
}


