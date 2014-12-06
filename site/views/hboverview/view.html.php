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
		
		$model = $this->getModel('HBoverview');
		$model->setDates();
		
		// current games
		$currGames = $model->getCurrGames();
		$this->assignRef('currGames', $currGames);
		//echo '=> view->currGames<br><pre>'; print_r($currGames);echo '</pre>';
		
		// previous games
		$prevGames = $model->getPrevGames();
		$this->assignRef('prevGames', $prevGames);
		//echo '=> view->prevGames<br><pre>'; print_r($prevGames);echo '</pre>';
		
		// next games
		$nextGames = $model->getNextGames();
		$this->assignRef('nextGames', $nextGames);
		//echo '=> view->nextGames<br><pre>'; print_r($nextGames);echo '</pre>';
		
		$teams = $model->getTeamArray();
		$this->assignRef('teams', $teams);
		$homegames = $model->getHomeGames();
		$this->assignRef('homegames', $homegames);
		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
		JHtml::stylesheet('com_hbmanager/site.stylesheet.css', array(), true);
		JHtml::stylesheet('com_hbmanager/hboverview.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}