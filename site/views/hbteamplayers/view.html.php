<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class hbteamViewHBteamPlayers extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbteamplayers');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
				
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);
		
		if (empty($team)) {
			$noTeamMessage = JText::_('COM_HBTEAM_NOTEAM');
			$this->assignRef('noTeamMessage', $noTeamMessage);
		} else {
			$players = $model->getPlayers();
			//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
			$this->assignRef('players', $players);

			// TODO backend / dymanic
			$picPath = JURI::Root().'hbdata/images/player/'.strtolower($team->kuerzel).'/';
			$this->assignRef('picPath', $picPath);
		}

		JHtml::stylesheet('com_hbteam/players.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}