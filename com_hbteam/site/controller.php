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
	
	function getGoals2()
	{
		
		$model = $this->getModel('hbteamgoals');
		
		$jinput = JFactory::getApplication()->input;
		$gameId = $jinput->get('gameId');
		$teamkey = $jinput->get('teamkey');
		$season = $jinput->get('season');
		
		$players = $model->getPlayers($gameId, $teamkey, $season);
		
		$data = array('gameId' => $gameId, 'player' =>  $players);
		echo json_encode($data);
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
	
	function testData()
	{
		$jinput = JFactory::getApplication()->input;
		
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$teamkey.'</pre>';
		$season = $jinput->get('season');
		//echo __FILE__.' - '.__LINE__.'<pre>'.$season.'</pre>';
		$model = $this->getModel('hbteamgoals');
		
		$data = $model->getChartData($teamkey);
		
		//echo __FILE__.' - '.__LINE__.'<pre>'; print_r($data); echo '</pre>';
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