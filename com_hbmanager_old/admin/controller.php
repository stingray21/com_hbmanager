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
		if (isset($post['update_pic_button'])) {
			//echo "=> update pic button<br>";
			$model->saveImage($pics);
			$model->updateDB(array($pics));
		}
		
		$view = $this->getView('hbpictures','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbpictures');
	}
	
	function addPicture()
	{
		$model = $this->getModel('hbpictures');
		
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', null);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';
		$model->setTeamkey($teamkey);
		
		$view = $this->getView('hbpictures','html');
		$view->setModel($model);
		$view->setLayout('addpicture');
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbpictures');
	}
	
} 