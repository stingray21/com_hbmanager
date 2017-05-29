<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class hbteamViewHBtest extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$model = $this->getModel('hbtest');
		
		$players = $model->getPlayers();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($players);echo'</pre>';
		
		JHtml::stylesheet('com_hbteam/goals.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}