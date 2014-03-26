<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class HBteamHomeViewHBteamGoals extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		// local jquery
		//$document->addScript(JURI::Root().'/media/com_hbmanager/js/jquery-2.0.3.js);
		JHtml::_('jquery.framework');
		$document->addScript(JURI::Root().'/media/com_hbteamhome/js/hbgoals.js');
		$document->addScript(JURI::Root().'/media/com_hbteamhome/js/d3.js');
		$document->addScript(JURI::Root().'/media/com_hbteamhome/js/hbgoalsChart.js');
		
		$model = $this->getModel('hbteamgoals');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
				
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);
		
		$this->assignRef('teamkey', $model->teamkey);
		$this->assignRef('season', $model->season);

		$games = $model->getGames();
		//echo '=> view->games<br><pre>'; print_r($games); echo '</pre>';
		$this->assignRef('games', $games);
		
		$players = $model->getPlayers();
		//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
		$this->assignRef('players', $players);
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'media/com_hbteamhome/css/site.stylesheet.css');
		
		// Display the view
		parent::display($tpl);
	}
}