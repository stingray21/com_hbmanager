<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Hallenverzeichnis Component
 */
class HBOfficialsViewHBOfficials extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$model = $this->getModel('hbofficials');
		//$this->assignRef('model', $model);
		
		
		$officials = $model->getOfficials();
		$this->assignRef('officials', $officials);
		//echo "<pre>"; print_r($officials); echo "</pre>";

		
		//$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		//$this->assignRef('post', $post);
		
		JHtml::stylesheet('com_hbofficials/site.stylesheet.css', array(), true);
		
		
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