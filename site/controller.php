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
	
} 