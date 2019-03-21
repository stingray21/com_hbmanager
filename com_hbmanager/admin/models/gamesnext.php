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
class HBmanagerModelGamesNext extends HBmanagerModelGames
{
	
	function __construct() 
	{
		parent::__construct();
	}

	function updatePregamesInDB($games = array())
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
				$values[] = $db->q($game['pregameID']);
				$values[] = $db->q($game['gameIdHvw']);
				$values[] = $db->q($game['season']);
				$values[] = (empty($game['pregame'])) 		? 'NULL' : $db->q($game['pregame']);
				$values[] = (empty($game['meetupLoc'])) 	? 'NULL' : $db->q($game['meetupLoc']);
				$values[] = (empty($game['meetupTime'])) 		? 'NULL' : $db->q($game['meetupTime']);

				$values = implode(', ', $values);

				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn($this->tables->pregame)."(".
						$db->qn('pregameID').", ".
						$db->qn('gameIdHvw').", ".$db->qn('season')
						.", ".$db->qn('pregame').", ".$db->qn('meetupLoc').", ".$db->qn('meetupTime')
						.") ".
						"VALUES (".$values.");";

				// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';
				$db->setQuery($query);
				$result = $db->query();
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
			} 
			elseif (!$valid && !empty($game['pregameID'])) 
			{
				$query = $db->getQuery(true);
				$query->delete($db->qn($this->tables->pregame));
				$query->where($db->qn('pregameID').'='.$game['pregameID']);
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
		if (!empty(trim($game['pregame']))) return true;
		if (!empty(trim($game['meetupLoc']))) return true;
		if (!empty(trim($game['meetupTime']))) return true;
		return false;
	}
	
	
	function writeNews($includedGames)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($includedGames);echo'</pre>';

		$games = self::getNextGames();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		
		if (!empty($games))
		{
			$alias = JHTML::_('date', time() , 'Ymd-His', $this->timezone)
				.'-news-pregame';
			$title = self::getPregameTitle();
			$content = self::getPregameContent($games, $includedGames);
			HBarticle::writeArticle($this, $alias, $title, $content);
		}
	}

	protected function getPregameTitle()
	{
		$titleDate = self::getTitleDate($this->dates->nextStart->format('Y-m-d'), 
				$this->dates->nextEnd->format('Y-m-d')	);
		
		//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
		$title = 'Vorschau für '.$titleDate;
		return $title;
	}
	
	protected function getPregameContent($games, $includedGames = null)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($includedGames);echo'</pre>';
		$prevTeam = null;
		$content = null;
		$content .= '<div class="news-pregame">'."\n";
		foreach ($games as $date => $days)
		{	
			$content .= "\n\n<h3>".JHTML::_('date', $date , 'l, d.m.Y', $this->timezone)."</h3>\n";
			foreach ($days as $game) {

				if (!$includedGames[$game->gameIdHvw]) continue; 

				if ($prevTeam !== $game->team)
				{
					$content .= '<h4>'.
							'<a href="'.JURI::Root().'index.php/';
					$content .= ($game->youth === 'aktiv') ? 'aktive' : 'youth';
					$content .= '/'.strtolower($game->teamkey).'">'.
							$game->team.' <span class="league">'.$game->league
							.' ('.$game->leagueKey.')</span></a>'.
							'</h4>'."\n";
				}
				$prevTeam = $game->team;

				$content .= '<div>';
				
				$ownHome = ($game->ownTeam === 1) ? ' own' : '';
				$ownAway = ($game->ownTeam === 2) ? ' own' : '';
				
				$content .= '<div class="gameInfo">'."\n";
				//$content .= '<span class="time">'.JHtml::_('date', $game->zeit, 'H:i', $this->timezone).' Uhr </span>'.
				$content .= '<span class="team">'.
						'<span class="home'.$ownHome.'">'.$game->home.'</span> '.
						'<span class="dash">-</span> <span class="away'.$ownAway.'">'.$game->away.'</span>'.
					'</span>';
					'</div>'."\n";
					
				if (!empty($game->pregame))
					$content .= '<p class="gamereport">'.$game->pregame.'</p>';
				if (!empty($game->meetupLoc))
					$content .= '<p class="meetupLoc">'.$game->meetupLoc.'</p>';
				if (!empty($game->meetupTime))
					$content .= '<p class="meetupTime">'.$game->meetupTime.'</p>';
				
				$content .= '</div>'."\n\n";
			}
		}
		$content .= '</div>';
		return $content;
	}
	
}


