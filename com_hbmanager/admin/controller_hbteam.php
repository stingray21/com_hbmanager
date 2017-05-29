<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * HB Team Component Controller
 */
class hbteamController extends JControllerAdmin
{
	
	
	function display($cachable=false, $urlparams = false)
	{
		// set default view if not set
//		JRequest::setVar('view', JRequest::getCmd('view', 'hbteam'));
//		parent::display($cachable);
		
		$view = $this->getView('hbteam','html');
		$view->display();
	}
//	
//	function showTeams()
//	{
//		$model = $this->getModel('hbteam');
//
//		$post = JRequest::get('post');
//		//echo "=> contoller->post<br><pre>"; print_r($post); echo "</pre>";
//		if (isset($post['updateTeams_button'])) {
//			$model->updateTeams($post['hbteam']);
//		}
//		
//		$view = $this->getView('hbteam','html');
// 		$view->setModel($model);
//		
//		$view->display();
//	}	
	
} 