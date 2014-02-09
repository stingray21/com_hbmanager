<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Hallenverzeichnis Component
 */
class HBGymsViewHBGyms extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
		$document->addScript(JURI::Root().'media/com_hbgyms/js/jquery-2.0.3.js');
		$document->addScript(JURI::Root().'media/com_hbgyms/js/maps_gyms.js');
		
		
		$model = $this->getModel('hbgyms');
		//$this->assignRef('model', $model);
		
		$tems = $model->getTeams();
		$this->assignRef('teams', $teams);
		//echo "<pre>"; print_r($teams); echo "</pre>";

		$gyms = $model->getGyms('all');
		$this->assignRef('gyms', $gyms);
		//echo "<pre>"; print_r($gyms); echo "</pre>";
		
		//$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		//$this->assignRef('post', $post);
		
		JHtml::stylesheet('com_hbgyms/site.stylesheet.css', array(), true);
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}
		
		// Display the view
		parent::display($tpl);
	}
	
}