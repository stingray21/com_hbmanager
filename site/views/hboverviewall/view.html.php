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
		$document = JFactory::getDocument();
		
		$model = $this->getModel('HBoverview');
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		//JToolBarHelper::title(JText::_('COM_HBMANAGER_DATA_TITLE'),'hblogo');
		
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'media/com_hbmanager/css/site.stylesheet.css');
		
		
		// Display the view
		parent::display($tpl);
	}
}