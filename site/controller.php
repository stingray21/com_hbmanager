<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * HB Team Overview Component Controller
 */
class HBteamHomeController extends JControllerLegacy
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
		$teamkey = 'm1';
		$season = $jinput->get('season');
		$season = 2013;
		
		$model = $this->getModel('hbteamgoals');
		
		$data = $model->getChartData($teamkey);
		echo json_encode($data);
	}
}