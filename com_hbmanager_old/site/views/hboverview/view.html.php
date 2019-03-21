<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
/**
 * HTML View class for the HB Manager Component
 */
class HbManagerViewHbOverview extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		
		$document = JFactory::getDocument();
		// local jquery
		//JHTML::script('jquery-2.0.3.js', 'media/com_hbmanager/js/');
		JHtml::_('jquery.framework');
		$document->addScript(JURI::Root().'/media/com_hbmanager/js/hboverview.js');
		
		$model = $this->getModel('HBoverview');
		$model->setDates();
		
		// current games
		$gameDays = $model->getGameDays();
		$this->assignRef('gameDays', $gameDays);
		//echo '=> view->gameDays<br><pre>'; print_r($gameDays);echo '</pre>';
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		$homegames = $model->getHomeGames();
		$this->assignRef('homegames', $homegames);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		// TODO time zone -> backend option
		$timezone = false; //true: user-time, false:server-time
		$this->assignRef('timezone', $timezone);
		
		// TODO show goal difference -> backend option
		$showDiff = true;
		$this->assignRef('showDiff', $showDiff);
		
		JHtml::stylesheet('com_hbmanager/site.stylesheet.css', array(), true);
		JHtml::stylesheet('com_hbmanager/hboverview.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}