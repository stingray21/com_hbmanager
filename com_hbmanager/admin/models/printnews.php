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
JLoader::register('HBarticle', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/hbarticle.php');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelPrintNews extends HBmanagerModelGames
{
	
	function __construct() 
	{
		parent::__construct();
	}


	function getReports()
	{
		$games = self::getPrevGames(false);
		$reports = [];
		foreach ($games as $game) {
			if (!empty($game->report) || !empty($game->playerList) || !empty($game->extra) )
			$reports[] = $game;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($reports);echo'</pre>';
		return $reports;
	}

	// function getPrevGames($byDate = true)
	// {
	// 	$start 	= $this->dates->prevStart->format('Y-m-d');
	// 	$end 	= $this->dates->prevEnd->format('Y-m-d');

	// 	$games = self::getGamesfromDB($start, $end, $this->tables->gamereport);
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	$arrange = true;
	// 	if ($arrange) 
	// 	{
	// 		$games = self::groupEF($games);
	// 		$games = self::addCssInfo($games);
	// 		$games = self::sortByOrder($games);
	// 		if ($byDate) $games = self::groupByDay($games);
	// 	}
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	return $this->prevGames = $games;
	// }

	// function getNextGames()
	// {
	// 	$start 	= $this->dates->nextStart->format('Y-m-d');
	// 	$end 	= $this->dates->nextEnd->format('Y-m-d');

	// 	$games = self::getGamesfromDB($start, $end, $this->tables->pregame);
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	$arrange = true;
	// 	if ($arrange) 
	// 	{
	// 		$games = self::groupEF($games);
	// 		$games = self::addCssInfo($games);
	// 		$games = self::groupByDay($games);
	// 		$games = self::sortByTime($games);
	// 	}
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	return $this->prevGames = $games;
	// }


	// protected function getPregameTitle()
	// {
	// 	$titleDate = self::getTitleDate($this->dates->nextStart->format('Y-m-d'), 
	// 			$this->dates->nextEnd->format('Y-m-d')	);
		
	// 	//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
	// 	$title = 'Vorschau für '.$titleDate;
	// 	return $title;
	// }
	
	
}


