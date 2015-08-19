<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_SITE.'/models/hboverview.php';

/**
 * HB Manager Component Controller
 */
class hbmanagerController extends JControllerAdmin
{

	function display($cachable=false, $urlparams = false)
	{
		$model = $this->getModel('hbmanager');
		$view = $this->getView('hbmanager','html');
		$view->setModel($model);
		
		$view->display();
		// Set the submenu
		hbhelper::addSubmenu('');
	}
	
	function showTeams()
	{
		$model = $this->getModel('hbteams');

		$post = JRequest::get('post');
		//echo "=> contoller->post<br><pre>"; print_r($post); echo "</pre>";
		if (isset($post['updateTeams_button'])) {
			$model->updateTeams($post['hbteam']);
		}
		if (isset($post['addTeams_button'])) {
			$model->addNewTeams($post['hbAddTeam']);
		}
		if (isset($post['deleteTeams_button'])) {
			$model->deleteTeams($post['hbDeleteTeam']);
		}
		
		$view = $this->getView('hbteams','html');
 		$view->setModel($model);
		
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	function addTeams()
	{
		$model = $this->getModel('hbteams');
		
		$jinput = JFactory::getApplication()->input;
		$updateHvw = $jinput->get('getHvwData', false);
		if ($updateHvw)
		{
			set_time_limit(90);
			$model->updateLeagues();
		}
		
		$view = $this->getView('hbteams','html');
		$view->setModel($model);
		$view->setLayout('addteams');
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	
	function deleteTeams()
	{
		$model = $this->getModel('hbteams');
	
		$view = $this->getView('hbteams','html');
		$view->setModel($model);	
		$view->setLayout('deleteteams');	
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	
	function showData()
	{
		$jinput = JFactory::getApplication()->input;
		$nojs = $jinput->get('nojs', false);
		
		$model = $this->getModel('hbdata');
		
		$view = $this->getView('hbdata','html');
		$view->setModel($model);
				
		$view->setLayout('default_js');
		if ($nojs == true) {
			$view->setLayout('default'); // without js
		}
		
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdata');
	}
	
	function updateData()
	{
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', 'none');
		
		$model = $this->getModel('hbdata');
		//echo '=> controller <br><pre>'; print_r($teamkey); echo '</pre>';
		$model->updateDb($teamkey);
		
		$view = $this->getView('hbdata','html');
		$view->setModel($model);
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdata');
	}
	
	function showOverview()
	{
		$model = $this->getModel('hboverview');
	
		$view = $this->getView('hboverview','html');
		$view->setModel($model);	
		
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hboverview');
	}
	
	function showAllGames()
	{
		$model = $this->getModel('hboverview');
	
		$view = $this->getView('hboverview','html');
		$view->setModel($model);	
		$view->setLayout('allgames');	
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hboverview');
	}
	
	function showHomeGames()
	{
		$model = $this->getModel('hboverview');
	
		$view = $this->getView('hboverview','html');
		$view->setModel($model);	
		$view->setLayout('homegames');	
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hboverview');
	}
	
	function showPrevGames()
	{
		$model = $this->getModel('hbprevgames');
		
		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		
		$dates = null;
		if (isset($post['hbdates'])) {
			$dates = $post['hbdates'];
		}
		$model->setPrevDates($dates);
		
		if (isset($post['hbprevgames'])) $prevGames = $post['hbprevgames'];
		else $prevGames = null;
		
		if (isset($post['update_button'])) {
			//echo "=> Update button<br>";
			$model->updateDB($prevGames);
		} 
		elseif (isset($post['article_button'])) {
			//echo "=> Article button<br>";
			$model->updateDB($prevGames);
			$model->writeNews();
		} 
		else {
			//no button pressed
		}
		
		$view = $this->getView('hbprevgames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbprevgames');
	}
	
	function showNextGames()
	{
		$model = $this->getModel('hbnextgames');

		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		
		$dates = null;
		if (isset($post['hbdates'])) {
			$dates = $post['hbdates'];
		}
		$model->setNextDates($dates);
		
		if (isset($post['hbnextgames'])) $nextGames = $post['hbnextgames'];
		else $nextGames = null;
		
		if (isset($post['update_button'])) {
			//echo "=> update button<br>";
			$model->updateDB($nextGames);
		}
		elseif (isset($post['article_button'])) {
			//echo "=> article button<br>";
			$model->updateDB($nextGames);
			$model->writeNews();
		}
		else {
			//no button pressed
		}		
		
		$view = $this->getView('hbnextgames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbnextgames');
	}
	
	function showJournal()
	{
		$model = $this->getModel('hbjournal');
		
		$post = JRequest::get('post');
		//echo "=> contoller->post<br><pre>"; print_r($post); echo "</pre>";
		
		$dates = null;
		if (isset($post['hbdates'])) {
			$dates = $post['hbdates'];
		}
		$model->setDates($dates);
		
		$view = $this->getView('hbjournal','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbjournal');
	}

	
	function createDbTables()
	{
		$jinput = JFactory::getApplication()->input;
		$dbOption = $jinput->get('dbOption', '');
	
		$model = $this->getModel('hbdatabase');
	
		$model->createDBtables($dbOption);
	
		$view = $this->getView('hbdatabase','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdatabase');
	}
	
	function showCalendar()
	{
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', 'kein');
	
		$model = $this->getModel('hbcalendar');
		
		$model->updateCal($teamkey);
	
		$view = $this->getView('hbcalendar','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbcalendar');
	}
	
	function showJournalWord()
	{
		$model = $this->getModel('hbjournal');
	
		$view = $this->getView('hbJournalWord','docx');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbjournal');
	}
	
	function showTeamMenus()
	{
		$model = $this->getModel('hbteammenus');
		$view = $this->getView('hbteammenus','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteammenus');
	}
	
	function addTeamMenus()
	{
		$model = $this->getModel('hbteammenus');
		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		$model->addMenuItems($post['hbteammenus']);
		$view = $this->getView('hbteammenus','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteammenus');
	}
	
	function showPictures()
	{
//		$jinput = JFactory::getApplication()->input;
//		$teamkey = $jinput->get('teamkey', 'kein');
	
		$model = $this->getModel('hbpictures');
		
		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		
		if (isset($post['hbpictures'])) $pics = $post['hbpictures'];
		else $pics = null;
		
		if (isset($post['update_button'])) {
			//echo "=> update button<br>";
			$model->updateDB($pics);
		}
		
		$view = $this->getView('hbpictures','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbpictures');
	}
	
	function getajax()
	{
		// Set view
		//JRequest::setVar('view', 'ajax');
		//parent::display();
		//echo __FILE__.'('.__LINE__.'):<pre>test</pre>';
		$view = $this->getView('hbteams','raw');
		$view->setLayout('addRow');
		$view->display();
	}
	
	function updateTeamData()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbdata');
		
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
		
//		try
//		{
//			$anyParam = JFactory::getApplication()->input->get('anyparam');
//
//			$response = array("success" => true);
//
//			echo new JResponseJson($response);
//		}
//		catch(Exception $e)
//		{
//		  echo new JResponseJson($e);
//		}
	}
	
	
	function getHvwTeams()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbdata');
		
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
		
//		try
//		{
//			$anyParam = JFactory::getApplication()->input->get('anyparam');
//
//			$response = array("success" => true);
//
//			echo new JResponseJson($response);
//		}
//		catch(Exception $e)
//		{
//		  echo new JResponseJson($e);
//		}
	}
	
	
} 