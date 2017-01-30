<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB HVW Manager Component
 */
class hbteamViewHbTeamGoals extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$jinput = JFactory::getApplication()->input;
		$gameId = $jinput->get('gameId');
		$this->assignRef('gameId', $gameId);
		$teamkey = $jinput->get('teamkey');
		$season = $jinput->get('season');
		$season = $jinput->get('season');
		echo __FILE__.' ('.__LINE__.')<pre>'; print_r($teamkey); echo '</pre>';
		
		$model = $this->getModel('hbteamgoals');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
				
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);

		$games = $model->getGames();
		//echo '=> view->games<br><pre>'; print_r($games); echo '</pre>';
		$this->assignRef('games', $games);
		
		$players = $model->getPlayers($gameId, $teamkey, $season);
		//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
		$this->assignRef('players', $players);
		
		parent::display($tpl);
	}
}