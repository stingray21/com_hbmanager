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
class HBmanagerModelGamesPrev extends HBmanagerModelGames
{
	
	function __construct() 
	{
		parent::__construct();
	}

	function updateReportsInDB($games = array())
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		if (empty($games)) return;
		
		$db = $this->getDbo();
		foreach ($games as $game)
		{
			$valid = self::checkIfValidReport($game);
			if ($valid)
			{
				$values = [];
				$values[] = $db->q($game['reportID']);
				$values[] = $db->q($game['gameIdHvw']);
				$values[] = $db->q($game['season']);
				$values[] = (empty($game['report'])) 		? 'NULL' : $db->q($game['report']);
				$values[] = (empty($game['playerlist'])) 	? 'NULL' : $db->q($game['playerlist']);
				$values[] = (empty($game['extra'])) 		? 'NULL' : $db->q($game['extra']);
				// $values[] = (empty($game['trend']))		? $db->q($game['trend']) : 'NULL';
				// $values[] = (empty($game['halftime'])) 	? $db->q($game['halftime']) : 'NULL';

				$values = implode(', ', $values);

				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn($this->tables->gamereport)."(".
						$db->qn('reportID').", ".$db->qn('gameIdHvw').", ".$db->qn('season')
						.", ".$db->qn('report').", ".$db->qn('playerList').", ".$db->qn('extra')
						// .", ".$db->qn('trend').", ".$db->qn('halftime')
						.") ".
						"VALUES (".$values.");";

				// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';
				$db->setQuery($query);
				$result = $db->query();
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
			} 
			elseif (!$valid && !empty($game['reportID'])) 
			{
				$query = $db->getQuery(true);
				$query->delete($db->qn($this->tables->gamereport));
				$query->where($db->qn('reportID').'='.$game['reportID']);
				// $query->where($db->qn('gameIdHvw').'='.$db->q($game['gameIdHvw']));
				// $query->where($db->qn('season').'='.$db->q($game['season']));
				// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';
				$db->setQuery($query);
				$result = $db->query();
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
			}
		}
	}

	protected function checkIfValidReport($game) 
	{
		if (!empty(trim($game['report']))) return true;
		if (!empty(trim($game['playerlist']))) return true;
		if (!empty(trim($game['extra']))) return true;
		// if (!empty(trim($game['trend']))) return true;
		// if (!empty(trim($game['halftime']))) return true;
		return false;
	}
	
	
	function writeNews($includedGames)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($includedGames);echo'</pre>';
		$games = self::getPrevGames(false);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		
		if (!empty($games))
		{
			$alias = JHTML::_('date', time() , 'Ymd-His', $this->timezone)
				.'-news-gamereport';
			$title = self::getReportTitle();
			$content = self::getReportContent($games, $includedGames);
			HBarticle::writeArticle($this, $alias, $title, $content);
		}
	}

	protected function getReportTitle()
	{
		$titleDate = self::getTitleDate($this->dates->prevStart->format('Y-m-d'), 
				$this->dates->prevEnd->format('Y-m-d')	);
		
		//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
		$title = 'Ergebnisse vom '.$titleDate;
		return $title;
	}
	
	protected function getReportContent($games, $includedGames = null)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($includedGames);echo'</pre>';
		$prevTeam = null;
		$content = null;
		$content .= '<div class="news-gamereport">'."\n";
		foreach ($games as $game)
		{	
			if (!$includedGames[$game->gameIdHvw]) continue; 

			if ($prevTeam !== $game->team)
			{
				$content .= '<h4>'.
						'<a href="'.JURI::Root().'index.php/';
				$content .= ($game->youth === 'aktiv') ? 'aktive' : 'jugend';
				$content .= '/'.strtolower($game->teamkey).'">'.
						$game->team.' <span class="league">'.$game->league
						.' ('.$game->leagueKey.')</span></a>'.
						'</h4>'."\n";
			}
			$prevTeam = $game->team;

			$content .= '<div>';
			
			$ind = ($game->goalsHome !== null) ? ' indicator '.$game->indicator : '';
			$ownHome = ($game->ownTeam === 1) ? ' own' : '';
			$ownAway = ($game->ownTeam === 2) ? ' own' : '';
			
			$content .= '<div class="gameInfo'.$ind.'">'."\n";
			$content .= '<span class="team">'.
					'<span class="home'.$ownHome.'">'.$game->home.'</span> '.
					'<span class="dash">-</span> <span class="away'.$ownAway.'">'.$game->away.'</span>'.
				'</span>'.
				'<span class="gameResult">';
			if ($game->goalsHome !== null)
			{
					$content .= ' <span class="'.$ownHome.'">'.$game->goalsHome.'</span>'.
					'<span class="dash">:</span> <span class="'.$ownAway.'">'.$game->goalsAway.'</span>'.
					'<span class="indicator "></span>';
			}
			$content .= '</span>'.
				'</div>'."\n";
				
			if (!empty($game->report))
				$content .= '<p class="gamereport">'.$game->report.'</p>';
			if (!empty($game->playerList))
				$content .= '<p class="playerlist">'.'<span>Es spielten:</span><br />'.$game->playerList.'</p>';
			if (!empty($game->extra))
				$content .= '<p class="extra">'.$game->extra.'</p>';
			
			$content .= '</div>'."\n\n";
		}
		$content .= '</div>';
		return $content;
	}
	
}


