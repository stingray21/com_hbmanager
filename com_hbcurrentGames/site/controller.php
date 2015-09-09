<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * HB Hallenverzeichnis Component Controller
 */
class HbcurrentGamesController extends JControllerLegacy
{
	
	function display($cachable=false, $urlparams = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'hbcurrentgames'));

		parent::display($cachable);
	}
	
}