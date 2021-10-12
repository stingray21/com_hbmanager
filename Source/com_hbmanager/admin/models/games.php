<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelGames extends JModelAdmin
{
	protected $prevGames = array();
	protected $nextGames = array();
	// protected $timezone = 'Europe/Berlin';
	protected $timezone = 'UTC';

	protected $dates = null;
	protected $tables = null;
	protected $tz = null;

	protected $season;

	// TODO use CONVERT_TZ in MySQL for date

	function __construct()
	{
		parent::__construct();

		$this->tables = new stdClass();
		$this->tables->team = '#__hb_team';
		$this->tables->game = '#__hb_game';
		$this->tables->gamereport = '#__hb_gamereport';
		$this->tables->pregame = '#__hb_pregame';
		$this->tables->gym = '#__hb_gym';

		$this->season = HbmanagerHelper::getCurrentSeason();

		$this->tz = new DateTimeZone($this->timezone);

		$dates = new stdClass();
		$dates->prevStart 	= null;
		$dates->prevEnd 	= null;
		$dates->nextStart 	= null;
		$dates->nextEnd 	= null;

		$get = JRequest::get('get');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($get);echo'</pre>';		
		if (isset($get['prevStart'])) 	$dates->prevStart = $get['prevStart'];
		if (isset($get['prevEnd'])) 	$dates->prevEnd 	= $get['prevEnd'];
		if (isset($get['nextStart'])) 	$dates->nextStart = $get['nextStart'];
		if (isset($get['nextEnd'])) 	$dates->nextEnd 	= $get['nextEnd'];

		$post = JRequest::get('post');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		if (isset($post['gameDates']['prevStart'])) $dates->prevStart 	= $post['gameDates']['prevStart'];
		if (isset($post['gameDates']['prevEnd'])) 	$dates->prevEnd 	= $post['gameDates']['prevEnd'];
		if (isset($post['gameDates']['nextStart'])) $dates->nextStart 	= $post['gameDates']['nextStart'];
		if (isset($post['gameDates']['nextEnd'])) 	$dates->nextEnd 	= $post['gameDates']['nextEnd'];

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';

		$this->dates = new stdClass();
		$this->dates->today = date_create('now', $this->tz);
		$this->setDates($dates);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}

	// public function getTable($type = 'Team', $prefix = 'HbmanagerTable', $config = array())
	// {
	// 	return JTable::getInstance($type, $prefix, $config);
	// }

	public function getForm($data = array(), $loadData = true)
	{
		// TODO: implement this method

		// // Get the form.
		// $form = $this->loadForm(
		// 	'com_hbmanager.team',
		// 	'team',
		// 	array(
		// 		'control' => 'jform',
		// 		'load_data' => $loadData
		// 	)
		// );

		// if (empty($form))
		// {
		// 	return false;
		// }

		// return $form;
	}

	// protected function loadFormData()
	// {
	// 	// Check the session for previously entered form data.
	// 	$data = JFactory::getApplication()->getUserState(
	// 		'com_hbmanager.edit.team.data',
	// 		array()
	// 	);

	// 	if (empty($data))
	// 	{
	// 		$data = $this->getItem();
	// 	}
	// 	// echo __FILE__.'('.__LINE__.'):<pre>';print_r($data);echo'</pre>';
	// 	return $data;
	// }

	public function getTeams($order = true)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->tables->team);
		if ($order) {
			$query->order($db->qn('order'));
		}

		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
	}

	public function getDates()
	{
		if (empty($this->dates->nextStart) && empty($this->dates->prevStart)) {
			//echo 'no dates';
			self::setDates();
		}

		$dates['prevStart'] = $this->dates->prevStart->format('Y-m-d');
		$dates['prevEnd'] = $this->dates->prevEnd->format('Y-m-d');

		$dates['nextStart'] = $this->dates->nextStart->format('Y-m-d');
		$dates['nextEnd'] = $this->dates->nextEnd->format('Y-m-d');

		return $dates;
	}

	public function setDates($dates = null)
	{
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';

		// previous games dates
		self::setPrevDates($dates);

		// upcoming games dates
		self::setNextDates($dates);

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}

	public function setPrevDates($dates = null)
	{
		$dates = (object) $dates;
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';

		$startDate 	= (empty($dates->prevStart)) 	? null : date_create($dates->prevStart, $this->tz);
		$endDate 	= (empty($dates->prevEnd)) 	? null : date_create($dates->prevEnd, $this->tz);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($endDate);echo'</pre>';
		// previous games end date
		if (is_null($endDate)) {
			$endDate = self::getPrevGamesEnd();
		}

		// previous games start date
		if (is_null($startDate) || (date_diff($startDate, $endDate)->format('%R') === '-')) {
			$startDate = self::getPrevGamesStart($endDate);
		}

		$this->dates->prevStart = $startDate;
		$this->dates->prevEnd 	= $endDate;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}

	protected function getPrevGamesEnd()
	{
		// TODO: double check time/timezone for offset
		$timestamp = strtotime($this->dates->today->format('Y-m-d'));
		$offset = date_create(date('Y-m-d H:i:s', strtotime('next Monday', strtotime('last Friday', $timestamp))), $this->tz);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($offset);echo'</pre>';

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('MAX(DATE(' . $db->qn('dateTime') . ')) AS '
			. $db->qn('date'));
		$query->from($this->tables->game);
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where('DATE(' . $db->qn('dateTime') . ') <= ' .
			$db->q($offset->format('Y-m-d')));
		$query->where('DATE(' . $db->qn('dateTime') . ') <= ' .
			$db->q($this->dates->today->format('Y-m-d')));
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($date);echo'</pre>';

		return date_create($date, $this->tz);
	}

	protected function getPrevGamesStart($offset)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('MIN(DATE(' . $db->qn('dateTime') . ')) AS '
			. $db->qn('date'));
		$query->from($this->tables->game);
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where(
			'DATE(' . $db->qn('dateTime') . ') BETWEEN ' .
				$db->q(strftime("%Y-%m-%d", strtotime('last Monday', strtotime($offset->format('Y-m-d'))))) .
				' AND ' . $db->q($offset->format('Y-m-d'))
		);
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';

		return date_create($date, $this->tz);
	}


	function setNextDates($dates = null)
	{
		$dates = (object) $dates;
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';

		$startDate 	= (empty($dates->nextStart)) 	? null : date_create($dates->nextStart, $this->tz);
		$endDate 	= (empty($dates->nextEnd)) 	? null : date_create($dates->nextEnd, $this->tz);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($startDate);echo'</pre>';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($endDate);echo'</pre>';

		// next games start date
		if (is_null($startDate)) {
			$startDate = self::getNextGamesStart();
		}

		// next games end date
		if (is_null($endDate) || (date_diff($startDate, $endDate)->format('%R') === '-')) {
			$endDate = self::getNextGamesEnd($startDate);
		}

		$this->dates->nextStart = $startDate;
		$this->dates->nextEnd 	= $endDate;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
	}

	protected function getNextGamesStart()
	{
		// TODO: double check time/timezone for offset
		$timestamp = strtotime($this->dates->today->format('Y-m-d'));
		$offset = date_create(date('Y-m-d H:i:s', strtotime('next Monday', strtotime('last Friday', $timestamp))), $this->tz);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($offset);echo'</pre>';

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('MIN(DATE(' . $db->qn('dateTime') . ')) AS '
			. $db->qn('date'));
		$query->from($this->tables->game);
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where(
			'DATE(' . $db->qn('dateTime') . ') > ' . $db->q($offset->format('Y-m-d'))
		);
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($date);echo'</pre>';

		return date_create($date, $this->tz);
	}

	protected function getNextGamesEnd($offset)
	{

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('MAX(DATE(' . $db->qn('dateTime') . ')) AS '
			. $db->qn('date'));
		$query->from($this->tables->game);
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where(
			'DATE(' . $db->qn('dateTime') . ') BETWEEN ' .
				$db->q($offset->format('Y-m-d')) . ' AND ' .
				$db->q(strftime("%Y-%m-%d", strtotime('next Sunday', strtotime('last Monday', strtotime($offset->format('Y-m-d'))))))
		);
		//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$date = $db->loadResult();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($date);echo'</pre>';

		return date_create($date, $this->tz);
	}

	function getPrevGames($byDate = true)
	{
		$start 	= $this->dates->prevStart->format('Y-m-d');
		$end 	= $this->dates->prevEnd->format('Y-m-d');

		$games = self::getGamesfromDB($start, $end, $this->tables->gamereport);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		if (empty($games)) return [];
		$arrange = true;
		if ($arrange) {
			$games = self::groupEF($games);
			$games = self::abbreviateGames($games);
			$games = self::addCssInfo($games);
			$games = self::sortByOrder($games);
			if ($byDate) $games = self::groupByDay($games);
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		return $this->prevGames = $games;
	}

	// different sorting order
	// function getPrevGames($byDate = true)
	// {
	// 	$start 	= $this->dates->prevStart->format('Y-m-d');
	// 	$end 	= $this->dates->prevEnd->format('Y-m-d');

	// 	$games = self::getGamesfromDB($start, $end);
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	$arrange = true;
	// 	if ($arrange) 
	// 	{
	// 		$games = self::groupEF($games);
	// 		$games = self::addCssInfo($games);
	// 		if ($byDate) 
	// 		{
	// 			$games = self::groupByDay($games);
	// 			foreach ($games as &$day) 
	// 			{
	// 				$games = self::sortByOrder($day);
	// 			}
	// 		} else {
	// 			$games = self::sortByOrder($games);
	// 		}
	// 	}
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
	// 	return $this->prevGames = $games;
	// }

	function getNextGames()
	{
		$start 	= $this->dates->nextStart->format('Y-m-d');
		$end 	= $this->dates->nextEnd->format('Y-m-d');

		$games = self::getGamesfromDB($start, $end, $this->tables->pregame);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		if (empty($games)) return [];
		$arrange = true;
		if ($arrange) {
			$games = self::groupEF($games);
			$games = self::abbreviateGames($games);
			$games = self::addCssInfo($games);
			$games = self::groupByDay($games);
			$games = self::sortByTime($games);
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';
		return $this->prevGames = $games;
	}

	protected function getGamesfromDB($start, $end, $table)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->select('*');

		$query->from($this->tables->game);
		$query->leftJoin($db->qn($this->tables->team) .
			' USING (' . $db->qn('teamkey') . ')');
		$query->leftJoin($db->qn($table) .
			' USING (' . $db->qn('gameIdHvw') . ', ' . $db->qn('season') . ')');
		$query->leftJoin($db->qn($this->tables->gym) .
			' USING (' . $db->qn('gymId') . ')');

		$query->where('(DATE(' . $db->qn('dateTime') . ') BETWEEN '
			. $db->q($start) . ' AND ' . $db->q($end) . ')');
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where('(' . $this->tables->game . '.' . $db->qn('comment') . ' != ' . $db->q('abgesetzt') . ' OR ' . $this->tables->game . '.' . $db->qn('comment') . ' IS NULL)');
		// $query->where($db->qn('goalsHome').' IS NOT NULL');
		$query->order($db->qn('dateTime') . ' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';

		$games = self::addTeamShort($games);

		return $games;
	}

	protected function groupEF($games)
	{
		$tempGames = array();
		foreach ($games as $game) {
			if (preg_match('/^(g|w|m)J(E|F)-/', $game->teamkey)) {
				$date = date_create($game->dateTime, $this->tz)->format('Y-m-d');
				$key = $game->teamkey . '_' . $date . '_' . preg_replace('/[^a-zA-Z0-9_]/', '', $game->home . '_' . $game->away);

				$gameDetails = new stdClass();
				$gameDetails->gameIdHvw = $game->gameIdHvw;
				$gameDetails->dateTime = $game->dateTime;
				$gameDetails->goalsHome = $game->goalsHome;
				$gameDetails->goalsAway = $game->goalsAway;
				$gameDetails->goalsHome1 = $game->goalsHome1;
				$gameDetails->goalsAway1 = $game->goalsAway1;
				$gameDetails->comment = $game->comment;
				$gameDetails->pointsHome = $game->pointsHome;
				$gameDetails->pointsAway = $game->pointsAway;
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gameDetails);echo'</pre>';

				if (!isset($tempGames[$key])) {
					$game->gameIdHvw = null;
					$game->dateTime = null;
					$game->goalsHome = null;
					$game->goalsAway = null;
					// $game->goalsHome1 = null;
					// $game->goalsAway1 = null;
					$game->comment = '';
					$game->pointsHome = null;
					$game->pointsAway = null;
					$tempGames[$key] = $game;
				}

				if (!is_null($gameDetails->goalsHome)) $tempGames[$key]->goalsHome += $gameDetails->goalsHome;
				if (!is_null($gameDetails->goalsAway)) $tempGames[$key]->goalsAway += $gameDetails->goalsAway;
				if (!is_null($gameDetails->pointsHome)) $tempGames[$key]->pointsHome += $gameDetails->pointsHome;
				if (!is_null($gameDetails->pointsAway)) $tempGames[$key]->pointsAway += $gameDetails->pointsAway;
				$tempGames[$key]->details[] = $gameDetails;
			} else {
				$tempGames[] = $game;
			}
		}

		foreach ($tempGames as $game) {
			if (isset($game->details)) {
				usort($game->details, function ($a, $b) {
					$retval = (strtotime($a->dateTime) < strtotime($b->dateTime)) ? -1 : 1;
					return $retval;
				});
				$game->dateTime = $game->details[0]->dateTime;
				$game->gameIdHvw = $game->details[0]->gameIdHvw;
			}
			$grouped[] = $game;
		}
		//echo __FUNCTION__.':<pre>';print_r($arranged);echo'</pre>';
		return $grouped;
	}

	protected function abbreviateGames($games)
	{	
		// TODO: able to change in backend
		$pattern = '/^HK Ostd\/Geisl( (\d))?$/'; 
		$abbreviation = 'HKOG';
		
		foreach ($games as &$game) {
			// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $game ,1).'</pre>';
			
			$game->homeAbbr = preg_replace($pattern, $abbreviation.'$2',$game->home);
			$game->awayAbbr = preg_replace($pattern, $abbreviation.'$2',$game->away);
			// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $game->home ,1).'</pre>';
		}
		
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $games ,1).'</pre>';
		return $games;
	}

	protected function groupByDay($games)
	{
		// arrange games by date
		$arranged = array();
		foreach ($games as $game) {
			$date = date_create($game->dateTime, $this->tz)->format('Y-m-d');
			$arranged[$date][] = $game;
		}

		// echo __FUNCTION__.':<pre>';print_r($arranged);echo'</pre>';
		ksort($arranged);
		// echo __FUNCTION__.':<pre>';print_r($arranged);echo'</pre>';
		return $arranged;
	}

	protected function sortByOrder($games)
	{
		usort($games, function ($a, $b) {
			// $retval = $a->order <=> $b->order;
			// if ($retval == 0) {
			//     $retval = $a->dateTime <=> $b->dateTime;
			// }

			$retval = ($a->order > $b->order) ? -1 : 1;
			if ($a->order == $b->order) {
				$retval = (strtotime($a->dateTime) < strtotime($b->dateTime)) ? -1 : 1;
			}
			return $retval;
		});
		return $games;
	}

	protected function sortByTime($games)
	{
		foreach ($games as &$day) {
			usort($day, function ($a, $b) {
				$retval = (strtotime($a->dateTime) < strtotime($b->dateTime)) ? -1 : 1;
				return $retval;
			});
		}
		return $games;
	}

	protected function addCssInfo($games)
	{
		foreach ($games as &$game) {
			//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
			$game->winnerTeam = self::getWinnerTeam($game);
			$game->ownTeam = self::getOwnTeam($game);
			$game->indicator = self::getIndicator($game);
		}
		return $games;
	}

	protected function addTeamShort($games)
	{
		foreach ($games as &$game) {
			//echo __FUNCTION__."<pre>"; print_r($game); echo "</pre>";
			$search = ['Jugend', 'weiblich', 'männlich', 'gemischt', 'Geislingen', 'Ostdorf'];
			$replace = ['Jgd.', 'weibl.', 'männl.', 'gem.', 'G.', 'O.'];
			// TODO: move $search and $replace in Admin Options
			$game->teamShort = str_replace($search, $replace, $game->team);
		}
		return $games;
	}

	protected function getWinnerTeam($game)
	{
		if ($game->pointsHome > $game->pointsAway) return 1;
		elseif ($game->pointsHome < $game->pointsAway) return 2;
		elseif ($game->pointsHome == $game->pointsAway && $game->pointsHome !== null) return 0;
		return null;
	}

	protected function getOwnTeam($game)
	{
		if ($game->home == $game->shortName) return 1;
		elseif ($game->away == $game->shortName) return 2;
		return null;
	}

	protected function getIndicator($game)
	{
		if ($game->winnerTeam === 0) return 'tied';
		if ($game->winnerTeam === $game->ownTeam && $game->winnerTeam !== null) return 'win';
		if ($game->winnerTeam !== $game->ownTeam && $game->winnerTeam !== null) return 'loss';
		return 'blank';
	}


	protected function getTitleDate($minDate, $maxDate)
	{
		echo __FILE__.' ('.__LINE__.'):<pre>';print_r($minDate);echo'</pre>';
		echo __FILE__.' ('.__LINE__.'):<pre>';print_r($maxDate);echo'</pre>';
		// die();
		if ($minDate === $maxDate) {
			$titledate = JHtml::_('date', $minDate, 'D, j. M.', $this->timezone);
		}
		// back to back days and weekend
		elseif (
			strftime("%j", $minDate) + 1 == strftime("%j", $maxDate) && (strftime("%w", $minDate) == 6 and strftime("%w", $maxDate) == 0)
		) {
			// if same month
			if (strftime("%m", $minDate) == strftime("%m", $maxDate)) {
				$date = JHTML::_('date', $minDate, 'j.', $this->timezone) .
					JHTML::_('date', $maxDate, '/j. M.', $this->timezone);
			} else {
				$date = JHTML::_('date', $minDate, 'j. F', $this->timezone) .
					JHTML::_('date', $maxDate, ' / j. F', $this->timezone);
			}
			$titledate = 'Wochenende ' . $date;
		} else {
			$titledate = JHtml::_('date', $minDate, 'j. ', $this->timezone);
			if (strftime("%m", $minDate) !== strftime("%m", $maxDate)) {
				$titledate .= JHtml::_('date', $minDate, 'F ', $this->timezone);
			}
			$titledate .= 'bis ';
			$titledate .= JHtml::_(
				'date',
				$maxDate,
				'j. F',
				$this->timezone
			);
		}

		return $titledate;
	}


	public function getIncludedGames($games)
	{
		foreach ($games as $key => $value) {
			$included[$value['gameIdHvw']] = (isset($value['includeToNews']));
		}
		return $included;
	}


	public function getHomeGames()
	{
		$games = self::getNextGames();
		$gyms = HbmanagerHelper::getHomeGyms();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gyms);echo'</pre>';
		$homegames = [];
		foreach ($games as $date => $days) {
			foreach ($days as $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game->gymId);echo'</pre>';
				if (in_array($game->gymId, $gyms)) {
					$homegames[$date][$game->gymId][] = $game;
				}
			}
		}
		return $homegames;
	}

	public function getNextAndHomeGames()
	{
		$games = self::getNextGames();
		$gyms = HbmanagerHelper::getHomeGyms();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gyms);echo'</pre>';
		$homegames = [];
		$homeGameIds = [];
		foreach ($games as $date => $days) {
			foreach ($days as $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game->gymId);echo'</pre>';
				if (in_array($game->gymId, $gyms)) {
					$homegames[$date][$game->gymId][] = $game;
				} else {
					$nextgames[$date][] = $game;
				}
			}
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($nextgames);echo'</pre>';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($homegames);echo'</pre>';
		$games = new stdClass();
		$games->nextgames = $nextgames;
		$games->homegames = $homegames;
		return $games;
	}



	public function getAllHomeGames()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->select('*');

		$query->from($this->tables->game);
		$query->leftJoin($db->qn($this->tables->team) .
			' USING (' . $db->qn('teamkey') . ')');
		$query->leftJoin($db->qn($this->tables->gym) .
			' USING (' . $db->qn('gymId') . ')');

		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where($db->qn('season') . ' = ' . $db->q($this->season));
		$query->order($db->qn('dateTime') . ' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		$games = self::organizeHomeGames($games);
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;
		return $games;
	}

	private function organizeHomeGames($games)
	{
		$games = self::groupByDay($games);
		$games = self::sortByTime($games);

		$gyms = HbmanagerHelper::getHomeGyms();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($gyms);echo'</pre>';
		$homegames = [];

		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';die;

		foreach ($games as $date => $days) {
			foreach ($days as $game) {
				// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($game->gymId);echo'</pre>';
				if (in_array($game->gymId, $gyms)) {
					$homegames[$date][$game->gymId][] = $game;
				}
			}
		}
		return $homegames;
	}
}