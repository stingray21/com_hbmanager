<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hboverview.php';
/**
 * HTML View class for the HB Manager Component
 */
class HbManagerViewHbOverview extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('HBoverview');
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		$homegames = $model->getHomeGames();
		$this->assignRef('homegames', $homegames);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		JHtml::stylesheet('com_hbmanager/site.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}