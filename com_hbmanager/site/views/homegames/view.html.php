<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewHomegames extends JViewLegacy
{
	/**
	 * Display the HB Manager view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		

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



// $document = JFactory::getDocument();
// //		// local jquery
// 		// JHTML::script('jquery-2.0.3.js', 'media/com_hbmanager/js/');
// 		JHtml::_('jquery.framework');
// 		$document->addScript(JURI::Root().'/media/com_hbmanager/js/hbhomegames.js');
		
// 		$model = $this->getModel('HBHomegames');
// 		$model->setDates();
// //		
// //		// current games
// //		$gameDays = $model->getGameDays();
// //		$this->assignRef('gameDays', $gameDays);
// //		//echo '=> view->gameDays<br><pre>'; print_r($gameDays);echo '</pre>';
// //		
// 		$teams = $model->getTeamArray();
// 		$this->assignRef('teams', $teams);
// 		$homegames = $model->getHomeGames();
// 		$this->assignRef('homegames', $homegames);
// 		$nextGameday = $model->getNextGameday();
// 		$this->assignRef('nextGameday', $nextGameday);
// 		//echo '=> view->$teams <br><pre>'; print_r($teams); echo '</pre>';
		
// 		// TODO time zone -> backend option
// 		$timezone = false; //true: user-time, false:server-time
// 		$this->assignRef('timezone', $timezone);
		
// 		// TODO show goal difference -> backend option
// 		$showDiff = true;
// 		$this->assignRef('showDiff', $showDiff);
		
// 		JHtml::stylesheet('com_hbmanager/site.stylesheet.css', array(), true);
// //		JHtml::stylesheet('com_hbmanager/hboverview.stylesheet.css', array(), true);
		
// 		// Display the view
// 		parent::display($tpl);