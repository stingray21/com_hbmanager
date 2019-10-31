<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewPlayers extends JViewLegacy
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


		// $model = $this->getModel('hbteamplayers');
		// //echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		// $this->assignRef('model', $model);
				
		// $team = $model->getTeam();
		// //echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		// $this->assignRef('team', $team);
		
		// if (empty($team)) {
		// 	$noTeamMessage = JText::_('COM_HBTEAM_NOTEAM');
		// 	$this->assignRef('noTeamMessage', $noTeamMessage);
		// } else {
		// 	$players = $model->getPlayers();
		// 	//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
		// 	$this->assignRef('players', $players);

		// 	// TODO backend / dymanic
		// 	$picPath = JURI::Root().'hbdata/images/player/'.strtolower($team->kuerzel).'/';
		// 	$this->assignRef('picPath', $picPath);
		// }
