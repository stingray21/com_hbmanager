<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class HBteamHomeViewHBteamHome extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbteamhome');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
		
		// Assign data to the view
		$this->msg = $this->get('Msg');
		
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);
/*
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}
*/		
		$picture = $model->getPicture();
		//echo '=> view->picture<br><pre>'; print_r($picture); echo '</pre>';
		$this->assignRef('picture', $picture);
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'media/com_hbteamhome/css/site.stylesheet.css');
		
		// Display the view
		parent::display($tpl);
	}
}