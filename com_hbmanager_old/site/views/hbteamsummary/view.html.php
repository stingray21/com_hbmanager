<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class hbteamViewHBteamSummary extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbteamsummary');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
				
		$teams = $model->getTeams();
		//echo '=> view->team<br><pre>'; print_r($teams); echo '</pre>';
		$this->assignRef('teams', $teams);

		$link = $model->link;
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('link', $link);
		
		// TODO dymnamic path
		$picPath = JURI::Root().'hbdata/images/teams/2014-2015/250px/';
		$this->assignRef('picPath', $picPath);
		
		JHtml::stylesheet('com_hbteam/summary.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}