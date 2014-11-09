<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/hbprevnext.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/hbarticle.php';

class HBmanagerModelHbprevgames extends HBmanagerModelHbprevnext
{	
	
	function __construct() 
	{
		parent::__construct();
		
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}
	

	public function getArrangedGames($combined = false, $reports = false) {
		$games = self::getPrevGames($combined, $reports);
		
		// arrange games by date
		$arranged = array();
		foreach ($games as $game){
			$arranged[$game->datum][] = $game;
		}
		//echo __FUNCTION__.':<pre>';print_r($arranged);echo'</pre>';
		return $this->prevGames = $arranged;
	}
	
	
	
	function updateDB($previousGames = array())
	{
		//echo __FUNCTION__.':<pre>';print_r($previousGames);echo'</pre>';
		if (empty($previousGames)) return;
		
		$db = $this->getDbo();
		foreach ($previousGames as $game)
		{
			//echo __FUNCTION__.':<pre>';print_r($game);echo'</pre>';
			foreach ($game as $key => $value)
			{
				$game[$key] = trim($value);
			}
			
			if (count(array_filter($game)) > 1)
			{
				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn('hb_spielbericht')."(".
						$db->qn('spielIDhvw').", ".$db->qn('bericht').", ".
						$db->qn('spielerliste').", ".$db->qn('zusatz').", ".
						$db->qn('spielverlauf').", ".$db->qn('halbzeitstand')
						.")".
						"VALUES (".$db->q($game['spielIDhvw']).", ";
					if (empty($game['bericht'])) $query .= 'NULL, ';
						else $query .= $db->q($game['bericht']).", ";
					if (empty($game['spielerliste'])) $query .= 'NULL, ';
						else $query .= $db->q($game['spielerliste']).', ';
					if (empty($game['zusatz'])) $query .= 'NULL, ';
						else $query .= $db->q($game['zusatz']).', ';
					if (empty($game['spielverlauf'])) $query .= 'NULL, ';
						else $query .= $db->q($game['spielverlauf']).', ';
					if (empty($game['halbzeitstand'])) $query .= 'NULL ';
						else $query .= $db->q($game['halbzeitstand']);
					$query .= ");";
				//echo __FUNCTION__.':<pre>';print_r($query);echo'</pre>';
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
	
	
	function writeNews()
	{
		// content article
		$games = self::getPrevGames(true,true);
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
	
	protected function getMinMaxDates()
	{
		$db = $this->getDbo();
		// earliest and latest included date 
		$query = $db->getQuery(true);
		$query->select('MIN('.$db->qn('datumZeit').') AS min, MAX('.
				$db->qn('datumZeit').') AS max');
		$query->from('hb_spiel');
		$query->where($db->qn('eigenerVerein').' = '.$db->q(1));
		$query->where('DATE('.$db->qn('datumZeit').') BETWEEN '.
				$db->q($this->dates->prevStart).' AND '.
				$db->q($this->dates->prevEnd));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
			$db->setQuery($query);
		$dateframe = $db->loadObject();
		//echo __FUNCTION__'<pre>';print_r($dateframe); echo '</pre>';
		return $dateframe;
	}

	// $titledateKW = 'KW'.JHtml::_('date', $maxDate, 'W', 'Europe/Berlin');
	protected function getTitle()
	{
		$dateframe = self::getMinMaxDates();

		// format date
		$minDate = strtotime($dateframe->min);
		$maxDate = strtotime($dateframe->max);
		if ($minDate === $maxDate)
		{
			$titledate = JHtml::_('date', $minDate, 'D, j. M.', 'Europe/Berlin');
		}
		// back to back days and weekend
		elseif (strftime("%j", $minDate)+1 == strftime("%j", $maxDate) AND
			(strftime("%w", $minDate) == 6 AND strftime("%w", $maxDate) == 0) )
		{
			// if same month
			if (strftime("%m", $minDate) == strftime("%m", $maxDate))
			{
				$date = JHTML::_('date', $minDate , 'j.', 'Europe/Berlin').
					JHTML::_('date', $maxDate , '/j. M.', 'Europe/Berlin');
			}
			else
			{
				$date = JHTML::_('date', $minDate , 'j. F.', 'Europe/Berlin').
					JHTML::_('date', $maxDate , ' / j. F.', 'Europe/Berlin');
			}
			$titledate = 'Wochenende '.$date;
		}
		else
		{
			$titledate = JHtml::_('date', $minDate, 'j. ', 'Europe/Berlin');
			if (strftime("%m", $minDate) !== strftime("%m", $maxDate)) {
				$titledate .= JHtml::_('date', $minDate, 'F. ', 'Europe/Berlin');
			}
			$titledate .= 'bis ';
			$titledate .= JHtml::_('date', $maxDate, 'j. F.', 
				'Europe/Berlin');
		}
		
		
		//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
		$title = 'Ergebnisse vom '.$titledate;
		//echo __FUNCTION__.':<pre>';print_r($title);echo'</pre>';
		return $title;
	}
	
	protected function getContent($games)
	{
		$prevTeam = NULL;
		$content = null;
		$content .= '<div class="newsspieltag">';
		foreach ($games as $game)
		{	
			if ($prevTeam !== $game->mannschaft)
			{
				$content .= '<h4>'.
						'<a href="'.JURI::Root().'index.php/'.
						strtolower($game->kuerzel).'-home">'.
						$game->mannschaft.' <span class="liga">'.$game->liga
						.' ('.$game->ligaKuerzel.')</span></a>'.
						'</h4>';
			}
			$prevTeam = $game->mannschaft;

			$content .= '<div>';
			$content .= '<table class="ergebnis">'.
							'<tbody>'.
								'<tr>'.
									'<td class="text">'.$game->heim.'</td>'.
									'<td class="symbol">-</td>'.
									'<td class="text">'.$game->gast.'</td>'.
									'<td class="figure">'.$game->toreHeim.'</td>'.
									'<td class="symbol">:</td>'.
									'<td class="figure">'.$game->toreGast.
									'</td>'.
								'</tr>'.
							'</tbody>'.
						'</table>';
			if (!empty($game->bericht))
				$content .= '<p class="spielbericht">'.$game->bericht.'</p>';
			if (!empty($game->bericht))
				$content .= '<p class="spielerliste">'.
					'<span>Es spielten:</span><br />'.
					$game->spielerliste.'</p>';
			if (!empty($game->zusatz))
				$content .= '<p class="zusatz">'.
					$game->zusatz.'</p>';
			$content .= '</div>';
		}
		$content .= '</div>';
		//echo __FUNCTION__.':<pre>';print_r($content);echo'</pre>';
		return $content;
	}
	
}