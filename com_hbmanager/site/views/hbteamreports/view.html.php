<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Team Overview Component
 */
class hbteamViewHBteamReports extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$model = $this->getModel('hbteamreports');

		$this->assignRef('gameParts', $model->gameParts);
		// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($model->gameParts);echo '</pre>';

		// // add meta tag
		// $document->setMetaData('og:site_name', "TSV Geislingen - Abt. Handball");
		// $document->setMetaData('og:title', "TSV Geislingen - Handball: Torschützen");
		// $document->setMetaData('og:type', "article");
		// $document->setMetaData('og:image', JURI::Root().'media/com_hbteam/images/goalchart_dummy.png');
		// $document->setMetaData('og:url', JURI::Root().'index.php/aktive/'.$model->teamkey.'/'.$model->teamkey.'-tore');
		// $document->setMetaData('og:description', "Statistik der Torschützen dieser Saison");
			
		JHtml::_('jquery.framework');
		$document->addScriptDeclaration("
			var teamkey = '{$model->teamkey}';
			var season = '{$model->season}';
			var gameId = '{$model->gameId}';
			console.log(teamkey);"
			//."var gameJSON = '{$gameJSON}';"
		);

		
		$document->addScript(JURI::Root().'/media/com_hbteam/js/d3.min.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/gamegraph.js');
				

		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);

		$games = $model->getGamesSelection();
		// echo '=> view->games<br><pre>'; print_r($games); echo '</pre>';
		$this->assignRef('games', $games);

		$gameInfo = $model->getGameInfo();
		//echo '=> view->team<br><pre>'; print_r($gameInfo); echo '</pre>';
		$this->assignRef('gameInfo', $gameInfo);
		
		$report = $model->getReport();
		// echo '=> view->games<br><pre>'; print_r($report); echo '</pre>';
		$this->assignRef('report', $report);
		
		JHtml::stylesheet('com_hbteam/reports.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}