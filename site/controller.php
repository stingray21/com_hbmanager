<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * HB Hallenverzeichnis Component Controller
 */
class HBOfficialsController extends JControllerLegacy
{
	
	function display($cachable=false, $urlparams = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'hbofficials'));

		parent::display($cachable);
	}
	
}