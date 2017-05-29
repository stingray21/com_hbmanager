<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * HB Team Overview Component Controller
 */
class hbteamController extends JControllerLegacy
{
	
	

	// function display($cachable=false, $urlparams = false)
	// {
	// 	$model = $this->getModel('hbmanager');
	// 	$view = $this->getView('hbmanager','html');
	// 	$view->setModel($model);
		
	// 	$view->display();
	// 	// Set the submenu
	// 	hbhelper::addSubmenu('');
	// }

	function getGoals()
	{
		
		$model = $this->getModel('hbteamgoals');
		
		// Set view
		//JRequest::setVar('view', 'ajax');
		//parent::display();
		$view = $this->getView('hbteamgoals','raw');
 		$view->setModel($model);
		$view->setLayout('getGoals');
		$view->display();
	}
	
	function getGoalsJSON()
	{
		
		$model = $this->getModel('hbteamgoals');
		
		$jinput = JFactory::getApplication()->input;
		$gameId = $jinput->get('gameId');
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gameId);echo'</pre>';
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($teamkey);echo'</pre>';
		$season = $jinput->get('season');
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($season);echo'</pre>';
		
		//$players = $model->getPlayersJSON($gameId, $teamkey, $season);
		$players = $model->getPlayers4AllGamesJSON($teamkey, $season);
		
		echo $players;
	}
	
	function getGoals4Chart()
	{
		$jinput = JFactory::getApplication()->input;
		
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$teamkey.'</pre>';
		$season = $jinput->get('season');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$season.'</pre>';
		$futureGames = $jinput->get('futureGames');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$futureGames.'</pre>';
		
		$model = $this->getModel('hbteamgoals');
		$model->setChartData($teamkey, $season, $futureGames);
		
		$data = $model->getChartData($teamkey);
		echo json_encode($data);
	}

	function getGameChartData()
	{
		$jinput = JFactory::getApplication()->input;
		
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$teamkey.'</pre>';
		$season = $jinput->get('season');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$season.'</pre>';
		$gameId = $jinput->get('gameId');

		$model = $this->getModel('hbteamreports');
		$model->setGameData($gameId, $teamkey, $season);
		
		$data = $model->getGameChartData();
		$data = json_encode($data);
		echo $data;
	}
	
	function getStandings4Chart()
	{
		$jinput = JFactory::getApplication()->input;
		
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$teamkey.'</pre>';
		$season = $jinput->get('season');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$season.'</pre>';
		
		$model = $this->getModel('hbteam');
		$model->setTeamkey($teamkey);
		$model->setSeason($season);
		
		$data = $model->getStandingsGraphData();
		echo $data;
	}
}