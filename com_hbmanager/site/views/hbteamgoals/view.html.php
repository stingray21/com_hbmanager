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
		$this->assignRef('teamkey', $model->teamkey);
		$this->assignRef('season', $model->season);
		$this->assignRef('gameId', $model->gameId);
		$this->assignRef('futureGames', $model->futureGames);
		$this->assignRef('defaultChartMode', $model->defaultChartMode);
		
		// local jquery
		//$document->addScript(JURI::Root().'/media/com_hbmanager/js/jquery-2.0.3.js);		
		
		$gamesJSON = $model->getGamesJSON();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gamesJSON);echo'</pre>';
		$playersJSON = $model->getPlayers4AllGamesJSON();
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gamesJSON);echo'</pre>';
		
		
		JHtml::_('jquery.framework');
		$document->addScriptDeclaration('
			var teamkey = \''.$model->teamkey.'\';
			var season = \''.$model->season.'\';
			var futureGames = '.$model->futureGames.';
			var gamesJSON = '.$gamesJSON.';
			var playersJSON = '.$playersJSON.';
			var startGame = '.$model->getStartGame($gamesJSON).';
			//console.log(teamkey);
		');
		
		$document->addScript(JURI::Root().'/media/com_hbteam/js/vue.min.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoals.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/d3.min.js');
		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoalsChart.js');
		
		$jinput = JFactory::getApplication()->input;
		// Assign config params data to the view
		$menuitemid = $jinput->get('Itemid');	
		//echo '=> model->gameId<br><pre>'; print_r($menuitemid); echo '</pre>';
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$chartmodes = $menuparams->get('chartsettings');
		} else {
			$chartmodes = array('goals','total','penalties','twoMin','twoMinTotal');
		}
		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($chartmodes); echo '</pre>';
		$this->assignRef('chartmodes', $chartmodes);
				
		$team = $model->getTeam();
		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
		$this->assignRef('team', $team);
		
		
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