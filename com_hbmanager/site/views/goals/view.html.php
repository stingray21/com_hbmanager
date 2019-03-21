<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewGoals extends JViewLegacy
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
		$this->tz = $this->get('Timezone');
		$this->team = $this->get('Team');
		$this->season = $this->get('Season');
		$this->selectedGameId = $this->get('selectedGame');
		$this->goalGraph = true;

		$jinput = JFactory::getApplication()->input;
		// Assign config params data to the view
		$menuitemid = $jinput->get('Itemid');	
		//echo '=> model->gameId<br><pre>'; print_r($menuitemid); echo '</pre>';
		// if ($menuitemid)
		// {
		// 	$menu = JFactory::getApplication()->getMenu();
		// 	$menuparams = $menu->getParams($menuitemid);
		// 	$this->chartmodes = $menuparams->get('chartsettings');
		// } else {
			$this->chartmodes = array('goals','goalsSoFar','penaltyGoals', 'averageSoFar','suspensionGame','suspensionSoFar', 'yellowSoFar', 'redSoFar');
			$this->defaultChartMode = 'goalsSoFar';
		// }



		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

		// Display the view
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}


	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		
		// JHtml::_('jquery.framework');
		$document->addScript( 'https://d3js.org/d3.v3.min.js' );
		// $document->addScript( JUri::root() . 'media/com_hbmanager/js/d3.min.js' );

		// $document->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/vue/0.11.5/vue.min.js' );
		$document->addScript( JUri::root() . 'media/com_hbmanager/js/vue.min.js' );
		// $document->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/locale/de.js' );
		$document->addScript( 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js' );
		// $document->addScript( JUri::root() . 'media/com_hbmanager/js/moment.min.js' );
			
		$goalsData = $this->get('GoalsData');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($goalsData);echo'</pre>';
		$document->addScriptDeclaration('
			var teamkey = \''.$goalsData->teamkey.'\';
			var season = \''.$goalsData->season.'\';
			var futureGames = '.$goalsData->futureGames.';
			var gamesJSON = '.$goalsData->gamesJSON.';
			var playersJSON = '.$goalsData->playersJSON.';
			var startGame = '.$goalsData->startGame.';
		');

		$document->addScript( JUri::root() . 'media/com_hbmanager/js/goals.js' );
		$document->addScript( JUri::root() . 'media/com_hbmanager/js/goalchart.js' );
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/site.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_GOALS_TITLE'));
	}

}





// $document = JFactory::getDocument();
// 		$model = $this->getModel('hbteamgoals');
// 		// add meta tag
// 		$document->setMetaData('og:site_name', "TSV Geislingen - Abt. Handball");
// 		$document->setMetaData('og:title', "TSV Geislingen - Handball: Torschützen");
// 		$document->setMetaData('og:type', "article");
// 		$document->setMetaData('og:image', JURI::Root().'media/com_hbteam/images/goalchart_dummy.png');
// 		$document->setMetaData('og:url', JURI::Root().'index.php/aktive/'.$model->teamkey.'/'.$model->teamkey.'-tore');
// 		$document->setMetaData('og:description', "Statistik der Torschützen dieser Saison");
		
		
		
// 		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
// 		$this->assignRef('model', $model);
// 		$this->assignRef('teamkey', $model->teamkey);
// 		$this->assignRef('season', $model->season);
// 		$this->assignRef('gameId', $model->gameId);
// 		$this->assignRef('futureGames', $model->futureGames);
// 		$this->assignRef('defaultChartMode', $model->defaultChartMode);
		
// 		// local jquery
// 		//$document->addScript(JURI::Root().'/media/com_hbmanager/js/jquery-2.0.3.js);		
		
// 		$gamesJSON = $model->getGamesJSON();
// 		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gamesJSON);echo'</pre>';
// 		$playersJSON = $model->getPlayers4AllGamesJSON();
// 		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($gamesJSON);echo'</pre>';
		
		
// 		JHtml::_('jquery.framework');
// 		$document->addScriptDeclaration('
// 			var teamkey = \''.$model->teamkey.'\';
// 			var season = \''.$model->season.'\';
// 			var futureGames = '.$model->futureGames.';
// 			var gamesJSON = '.$gamesJSON.';
// 			var playersJSON = '.$playersJSON.';
// 			var startGame = '.$model->getStartGame($gamesJSON).';
// 			//console.log(teamkey);
// 		');
		
// 		$document->addScript(JURI::Root().'/media/com_hbteam/js/vue.min.js');
// 		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoals.js');
// 		$document->addScript(JURI::Root().'/media/com_hbteam/js/d3.js');
// 		$document->addScript(JURI::Root().'/media/com_hbteam/js/hbgoalsChart.js');
		
// 		$jinput = JFactory::getApplication()->input;
// 		// Assign config params data to the view
// 		$menuitemid = $jinput->get('Itemid');	
// 		//echo '=> model->gameId<br><pre>'; print_r($menuitemid); echo '</pre>';
// 		if ($menuitemid)
// 		{
// 			$menu = JFactory::getApplication()->getMenu();
// 			$menuparams = $menu->getParams($menuitemid);
// 			$chartmodes = $menuparams->get('chartsettings');
// 		} else {
// 			$chartmodes = array('goals','total','penalties','twoMin','twoMinTotal');
// 		}
// 		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($chartmodes); echo '</pre>';
// 		$this->assignRef('chartmodes', $chartmodes);
				
// 		$team = $model->getTeam();
// 		//echo '=> view->team<br><pre>'; print_r($team); echo '</pre>';
// 		$this->assignRef('team', $team);
		
		
// 		$games = $model->getGames();
// 		//echo '=> view->games<br><pre>'; print_r($games); echo '</pre>';
// 		$this->assignRef('games', $games);
		
// 		$players = $model->getPlayers();
// 		//echo '=> view->players<br><pre>'; print_r($players); echo '</pre>';
// 		$this->assignRef('players', $players);
		
// 		JHtml::stylesheet('com_hbteam/goals.stylesheet.css', array(), true);
		
// 		// Display the view
// 		parent::display($tpl);