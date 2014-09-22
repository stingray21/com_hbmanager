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
	
	function showAllGames()
	{
		$model = $this->getModel('hboverviewall');
		
		$view = $this->getView('hboverviewall','html');
		$view->setModel($model);	
	
		$view->display();
		
		// Set the submenu
		//hbhelper::addSubmenu('hboverview');
	}
	
	function showHomeGames()
	{
		$model = $this->getModel('hboverviewhome');
	
		$view = $this->getView('hboverviewhome','html');
		$view->setModel($model);	
	
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
		
//		$teamkey = 'M1';
//		$jinput = JFactory::getApplication()->input;
//		$teamkey = $jinput->get('teamkey');

//		$model->updateTeam($teamkey);
		JRequest::setVar('tmpl','component');
		
		$view = $this->getView('hbcronjob','raw');
		$view->setModel($model, true);
		//$view->setLayout('addRow');
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