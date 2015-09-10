<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class hbteamViewHBteamGoals extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$model = $this->getModel('hbteamgoals');
		// add meta tag
		$document->setMetaData('og:site_name', "TSV Geislingen - Abt. Handball");
		$document->setMetaData('og:title', "TSV Geislingen - Handball: Torschützen");
		$document->setMetaData('og:type', "article");
		$document->setMetaData('og:image', JURI::Root().'media/com_hbteam/images/goalchart_dummy.png');
		$document->setMetaData('og:url', JURI::Root().'index.php/aktive/'.$model->teamkey.'/'.$model->teamkey.'-tore');
		$document->setMetaData('og:description', "Statistik der Torschützen dieser Saison");
		
		
		
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
		
		// local jquery
		//$document->addScript(JURI::Root().'/media/com_hbmanager/js/jquery-2.0.3.js);
		JHtml::_('jquery.framework');
		$document->addScriptDeclaration('
			var teamkey = \''.$model->teamkey.'\';
			var season = \''.$model->season.'\';
			//console.log(teamkey);
		');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoals.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/d3.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoalsChart.js');
		
		// Assign config params data to the view
		$chartmodes = JComponentHelper::getParams('com_hbteam')->get('chartsettings');
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($chartmodes); echo '</pre>';
		$this->assignRef('chartmodes', $chartmodes);
				
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);
		
		$this->assignRef('teamkey', $model->teamkey);
		$this->assignRef('season', $model->season);
		$this->assignRef('gameId', $model->gameId);

		$games = $model->getGames();
		//echo '=> view->games<br><pre>'; print_r($games); echo '</pre>';
		$this->assignRef('games', $games);
		
		$players = $model->getPlayers();
		//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
		$this->assignRef('players', $players);
		
		JHtml::stylesheet('com_hbteam/goals.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}