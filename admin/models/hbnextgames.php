<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/hbarticle.php';


class hbmanagerModelHbnextgames extends HBmanagerModelHbprevnext
{	
	
	function __construct() 
	{
		parent::__construct();
		
		
	}

	
	function updateDB($nextGames = array())
	{
		if (empty($nextGames)) return;
		
		$db = $this->getDbo();
		
		foreach ($nextGames as $game)
		{
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
				
			foreach ($game as $key => $value)
			{
				$game[$key] = trim($value);
			}
			
			if (count(array_filter($game)) > 1)
			{
				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn('hb_spielvorschau').
					"(".$db->qn('SpielIDhvw').", ".$db->qn('vorschau').", ".
					$db->qn('treffOrt').", ".$db->qn('treffZeit').") ".
					"VALUES (".$db->q($game['spielIdHvw']).', ';
					if (empty($game['vorschau'])) $query .= 'NULL, ';
						else $query .= $db->q($game['vorschau']).', ';
					if (empty($game['treffOrt'])) $query .= 'NULL, ';
						else $query .= $db->q($game['treffOrt']).', ';
					if (empty($game['treffZeit'])) $query .= 'NULL';
						else $query .= $db->q($game['treffZeit']);
					$query .=');';
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($query);echo'</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				}
				catch (Exception $e) {
					// catch any database errors.
				}
			}
		}
	}
	
	protected function getDateFramePreviews()
	{
		$db = $this->getDbo();
		// earliest and latest included date 
		$query = $db->getQuery(true);
		$query->select('MIN(DATE('.$db->qn('datumZeit').')) AS min, MAX(DATE('.
				$db->qn('datumZeit').')) AS max');
		$query->from('hb_spielvorschau');
		$query->leftJoin($db->qn('hb_spiel').
				' USING ('.$db->qn('spielIdHvw').')');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->nextStart).' AND '.
				$db->q($this->dates->nextEnd));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
			$db->setQuery($query);
		$dateframe = $db->loadObject();
		//echo __FUNCTION__'<pre>';print_r($dateframe); echo '</pre>';
		return $dateframe;
	}
	
	function getPreviewGames($arrange = true, $combined = false)
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*, DATE('.$db->qn('datumZeit').') AS '.$db->qn('datum')
				.', TIME_FORMAT('.$db->qn('datumZeit').', '.
					$db->q('%k:%i').') AS '.$db->qn('zeit'));
		$query->from('hb_spielvorschau');
		$query->leftJoin($db->qn('hb_spiel').
				' USING ('.$db->qn('spielIdHvw').')');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
				$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_halle').
				' USING ('.$db->qn('hallenNr').')');
		//$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->nextStart).' AND '.
				$db->q($this->dates->nextEnd));
		if ($combined) {	
			$query->group($db->qn('kuerzel').',DATE('.$db->qn('datumZeit').')'
				.', '.$db->qn('heim').', '.$db->qn('gast') );
		}
		$query->order($db->qn('datumZeit').' ASC');
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		
		if ($arrange){
			return $this->nextGames = self::arrangeGamesByDate($games);
		}
		return $this->nextGames = $games;
	}
	
	// $titledateKW = 'KW'.JHtml::_('date', $maxDate, 'W', 'Europe/Berlin');
	protected function getTitle()
	{
		// format date
		$dateframe = self::getDateFramePreviews();
		$minDate = $dateframe->min;
		$maxDate = $dateframe->max;
		$titleDate = self::getTitleDate($minDate, $maxDate);
		
		//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
		$title = 'Vorschau auf Spiele vom '.$titleDate;
		//echo __FUNCTION__.':<pre>';print_r($title);echo'</pre>';
		return $title;
	}
	
	
	protected function getContent($games)
	{
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		$prevTeam = NULL;
		$content = '<div class="newsspieltag">';
		foreach ($games as $game)
		{

			if ($prevTeam != $game->mannschaft)
			{
				$content .= '<h4>'.
						'<a href="'.JURI::Root().'index.php/'.
						strtolower($game->kuerzel).'-home">'.
						$game->mannschaft.' - '.$game->liga.' ('.
						$game->ligaKuerzel.')</a>'.
						'</h4>'."\n";
			}
			$prevTeam = $game->mannschaft;

			$content .= '<div class="vorberichtspiel">';
			$content .= '<a class="vorberichtspiel">'.$game->heim.' - '.
					$game->gast.'</a>'."\n";
			$content .= '<dl class="vorbericht">'.
					'<dt>Spiel</dt><dd>'.
					JHTML::_('date', strtotime($game->datumZeit),
							'D, j. M. \u\m h:m \U\h\r', 'Europe/Berlin').
					' in '.$game->stadt.'</dd>'."\n";
			if (!empty($game->treffOrt) OR !empty($game->treffZeit))
			{
				$content .= '<dt>Treffpunkt';
				if ($game->hallenNr != '7014') $content.= '/Abfahrt';
				$content .= '</dt>';
				$content .= '<dd>'.$game->treffOrt;
				if (!empty($game->treffZeit)) $content .= ' um '.
						strftime("%H:%M Uhr", strtotime($game->treffZeit));
				$content .= '</dd>'."\n";
			}
			$content .= '</dl>'."\n";
			if (!empty($game->vorschau))
				$content .= '<p class="vorbericht">'.$game->vorschau.'</p>';

			$content .= '</div>'."\n";

		}
		$content .= '</div>'."\n";
		//echo __FUNCTION__.':<pre>';print_r($content);echo'</pre>';
		return $content;
	}
	
	function writeNews()
	{
		// content article
		$games = self::getPreviewGames(false,true,true);
		//echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		
		if (!empty($games))
		{
			$alias = JHTML::_('date', time() , 'Ymd-His', 'Europe/Berlin')
				.'-news-letztespiele';
			$title = self::getTitle();
			$content = self::getContent($games);
			hbarticle::writeArticle($this, $alias, $title, $content);
		}
	}

}