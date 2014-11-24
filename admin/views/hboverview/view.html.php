<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
require_once JPATH_COMPONENT_SITE.'/models/hboverview.php';
 
/**
 * HTML View class for the HB Manager Component
 */
class HbManagerViewHbOverview extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		
		$model = $this->getModel('HBoverview');
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		$homegames = $model->getHomeGames();
		$this->assignRef('homegames', $homegames);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_DATA_TITLE'),'hblogo');
		
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}