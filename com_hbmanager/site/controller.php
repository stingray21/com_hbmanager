<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * HB Manager Component Controller
 */
class HBmanagerController extends JControllerLegacy
{
	
	function display($cachable=false, $urlparams = false)
	{
		
		parent::display($cachable);
	}
	
	function showGames()
	{
		$model = $this->getModel('hboverview');
		$model->setDates();
		//echo __FUNCTION__.'<pre>'; print_r($dates); echo '</pre>';
		
		$view = $this->getView('hboverview','html');
		
		$view->setModel($model);	
		//$view->setLayout('homeGames');
		$view->display();
		
		// Set the submenu
		//hbhelper::addSubmenu('hboverview');
	}
	
	function showAllGames()
	{
		$model = $this->getModel('hboverview');
		
		$view = $this->getView('hboverview','html');
		$view->setModel($model);	
		$view->setLayout('allgames');
		$view->display();
		
		// Set the submenu
		//hbhelper::addSubmenu('hboverview');
	}
	
	function showHomeGames4Booklet()
	{
		$model = $this->getModel('hboverview');
	
		$view = $this->getView('hboverview','html');
		$view->setModel($model);	
		$view->setLayout('homegames');
		$view->display();
		
		// Set the submenu
		//hbhelper::addSubmenu('hboverview');
	}
	
	function showHomeGames()
	{
		$model = $this->getModel('hboverview');
	
		$view = $this->getView('hbhomegames','html');
		$view->setModel($model);	
		$view->setLayout('homegames');
		$view->display();
		
		// Set the submenu
		//hbhelper::addSubmenu('hboverview');
	}
	
	function getOutdatedTeams()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbcronjob');
		
		$response = $model->getOutdatedTeams();
		//$response = array("success" => true);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// Output the JSON data.
		echo json_encode($response);
		
	}
	
	
	function updateHvwDataCronJob()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbcronjob');
		
		
		$jinput = JFactory::getApplication()->input;
		$option = $jinput->get('viewoption');
		
		JRequest::setVar('tmpl','component');
		
		$view = $this->getView('hbcronjob','raw');
		$view->setModel($model, true);
		
		if ($option === 'js') {
			$view->javaScript = true;
			$view->setLayout('default_js');
		}
		elseif ($option === 'plain') {
			$view->setLayout('plain');
		}
		
		$view->display();
	}
	
	function updateTeamData()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbcronjob');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey');

		$model->updateTeam($teamkey);

		$response = $model->getUpdateDate($teamkey);
		//$response = array("success" => true);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// Output the JSON data.
		echo json_encode($response);
		
	}
	
	function getHvwTeams()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbcronjob');
		
		$response = $model->getHvwTeamArray();
		//$response = array("success" => true);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// Output the JSON data.
		echo json_encode($response);

	}
} 