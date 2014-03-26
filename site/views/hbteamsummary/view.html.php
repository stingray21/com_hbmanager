<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class HBteamHomeViewHBteamSummary extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbteamsummary');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
				
		$teams = $model->getTeams();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('teams', $teams);

		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'media/com_hbteamhome/css/site.stylesheet.css');
		
		// Display the view
		parent::display($tpl);
	}
}