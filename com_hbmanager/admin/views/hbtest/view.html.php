<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbManagerViewHbtest extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('HBdataStandings');
		
		//$teams = $model->getTeams();
		//$this->assignRef('teams', $teams);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		$teamkey = null;
		$teamkey = 'F-1';
		
		$model->updateStandingsChart($teamkey);
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_DATA_TITLE'),'hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}