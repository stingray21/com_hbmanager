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
class HBmanagerModelGamesAll extends HBmanagerModelGames
{
	
	function __construct() 
	{
		parent::__construct();
	}

	public function getGames()
	{
		$teams = self::getTeams();
		
		foreach ($teams as &$team) {
			$team->games = self::getGamesOfTeam($team->teamkey);
		}
		
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';

		return $teams;
	}

	protected function getGamesOfTeam($teamkey)
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);

		$query->select('*');

		$query->from($this->tables->game);
		// $query->leftJoin($db->qn($this->tables->team).' USING ('.$db->qn('teamkey').')');
		$query->leftJoin($db->qn($this->tables->gym).' USING ('.$db->qn('gymId').')');
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		$query->where($db->qn('ownClub').' = '.$db->q(1));
		$query->where($db->qn('season').' = '.$db->q($this->season));
		$query->order($db->qn('dateTime').' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();		
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		$games = self::addCssInfo($games);

		return $games;
	}
	
}


