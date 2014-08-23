<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbManagerViewHbOverviewAll extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('HBoverviewAll');
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		JHtml::stylesheet('com_hbmanager/site.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}