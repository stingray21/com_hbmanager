<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Hallenverzeichnis Component
 */
class HBcurrentGamesViewHBcurrentGames extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		
		$model = $this->getModel('hbcurrentGames');
		//$this->assignRef('model', $model);

		$prevGames = $model->getPrevGames('');
		$this->assignRef('prevGames', $prevGames);
		//echo "<pre>"; print_r($prevGames); echo "</pre>";
		
		$nextGames = $model->getNextGames('');
		$this->assignRef('nextGames', $nextGames);
		//echo "<pre>"; print_r($nextGames); echo "</pre>";
		
		$homeGames = $model->getHomeGames('');
		$this->assignRef('homeGames', $homeGames);
		//echo "<pre>"; print_r($homeGames); echo "</pre>";
		
		JHtml::stylesheet('com_hbcurrentGames/site.stylesheet.css', array(), true);
		
		
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