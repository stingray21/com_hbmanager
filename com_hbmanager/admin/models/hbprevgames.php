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
						$db->qn('spielIdHvw').", ".$db->qn('bericht').", ".
						$db->qn('spielerliste').", ".$db->qn('zusatz').", ".
						$db->qn('spielverlauf').", ".$db->qn('halbzeitstand')
						.")".
						"VALUES (".$db->q($game['spielIdHvw']).", ";
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
		$games = self::getPrevGames(false,true,true);
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
	

	// $titledateKW = 'KW'.JHtml::_('date', $maxDate, 'W', 'Europe/Berlin');
	protected function getTitle()
	{
		// format date
		$titleDate = self::getTitleDate($this->dates->prevStart, 
				$this->dates->prevEnd);
		
		//$title = JText::_('COM_HBMANAGER_PREVGAMES_ARTICLE_TITLE');
		$title = 'Ergebnisse vom '.$titleDate;
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
						'<a href="'.JURI::Root().'index.php/';
				$content .= ($game->jugend === 'aktiv') ? 'aktive' : 'jugend';
				$content .= '/'.strtolower($game->kuerzel).'">'.
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