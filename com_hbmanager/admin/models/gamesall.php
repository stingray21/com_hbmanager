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


JLoader::register('HBmanagerModelGames', JPATH_COMPONENT_ADMINISTRATOR . '/models/games.php');

// xlsxwriter class ()
require_once('./components/com_hbmanager/helpers/xlsxwriter.class.php');



/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelGamesAll extends HBmanagerModelGames
{

	function __construct()
	{
		parent::__construct();
	}

	public function getGames()
	{
		$teams = self::getTeams();

		foreach ($teams as &$team) {
			$team->games = self::getGamesOfTeam($team->teamkey);
		}

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';

		return $teams;
	}

	protected function getGamesOfTeam($teamkey)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->select('*');

		$query->from($this->tables->game);
		$query->leftJoin($db->qn($this->tables->team) . ' USING (' . $db->qn('teamkey') . ')');
		$query->leftJoin($db->qn($this->tables->gym) . ' USING (' . $db->qn('gymId') . ')');
		$query->where($db->qn('teamkey') . ' = ' . $db->q($teamkey));
		$query->where($db->qn('ownClub') . ' = ' . $db->q(1));
		$query->where($db->qn('season') . ' = ' . $db->q($this->season));
		$query->order($db->qn('dateTime') . ' ASC');
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		// echo __FUNCTION__.':<pre>';print_r($games);echo'</pre>';
		$games = self::addCssInfo($games);

		return $games;
	}

	public function generateExcelFile($games)
	{
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $games ,1).'</pre>';
		$writer = new XLSXWriter();

		$this->writeHomeGamesToExcel($writer);
		$this->writeAllGamesToExcel($writer, $games);

		$base_directory = './';
		if (strcmp(JURI::base(true), '/administrator') === 0) {
			$base_directory = '../';
		}
		$season = HbmanagerHelper::getCurrentSeason();
		$filename = $base_directory . 'hbdata/' . $season . '_hkog_games.xlsx';
		// echo __FILE__ . '(' . __LINE__ . ')<pre>' . $filename . '</pre>';
		if (is_file($filename)) {
			unlink($filename);
		}

		$writer->writeToFile($filename);

		if (file_exists($filename)) {
			return $filename;
		}

		return -1;
	}

	protected function writeHomeGamesToExcel($writer)
	{
		$games = $this->getAllHomeGames();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>' . print_r($games, 1) . '</pre>';

		$sheet = 'Heimspiele';
		$tz = HbmanagerHelper::getHbTimezone();

		$writer->writeSheetHeader(
			$sheet,
			$rowdata = ['' => 'string', '' => 'string', '' => 'string', '' => 'string', '' => 'string', '' => 'string'],
			$col_options = ['widths' => [22, 12, 10, 22, 2, 22]]
		);

		$row = [$sheet, '', '', ''];
		$format = ['font-size' => 12, 'font-style' => 'bold', 'height' => 20];
		$writer->writeSheetRow($sheet, $row, $format);



		foreach ($games as $date => $gyms) {

			$writer->writeSheetRow($sheet, []);
			$writer->writeSheetRow($sheet, []);

			$row = [JHtml::_('date', $date, 'D, d.m.Y', $tz)];
			$format = ['font-style' => 'bold'];
			$writer->writeSheetRow($sheet, $row, $format);

			foreach ($gyms as $gym) {
				foreach ($gym as $key => $game) {
					// echo __FILE__ . '(' . __LINE__ . ')<pre>' . print_r($game, 1) . '</pre>';
					if ($key == 0) {
						$writer->writeSheetRow($sheet, []);
						$row = [$game->gymName . ', ' . $game->town . ' (' . $game->gymId . ')'];
						$format = ['font-style' => 'bold'];
						$writer->writeSheetRow($sheet, $row, $format);
					}
					$row = [
						$game->team,
						$game->leagueKey,
						JHtml::_('date', $game->dateTime, 'H:i', $tz) . ' Uhr',
						$game->home,
						'-',
						$game->away
					];
					$writer->writeSheetRow($sheet, $row);
				}
			}
		}
	}

	protected function writeAllGamesToExcel($writer, $games)
	{
		// echo __FILE__ . '(' . __LINE__ . ')<pre>' . print_r($games, 1) . '</pre>';
		$tz = HbmanagerHelper::getHbTimezone();

		foreach ($games as $team) {

			$link = HbmanagerHelper::get_hvw_page_url($team->leagueIdHvw);

			// format = array('font'=>'Arial','font-size'=>10,'font-style'=>'bold,italic', 'fill'=>'#eee','color'=>'#f00','fill'=>'#ffc', 'border'=>'top,bottom', 'halign'=>'center');
			$sheet = (empty($team->leagueKey)) ? $team->team : $team->leagueKey;

			$writer->writeSheetHeader(
				$sheet,
				$rowdata = ['' => 'string', '' => 'string', '' => 'string', '' => 'string', '' => 'string', '' => 'string'],
				$col_options = ['widths' => [5, 12, 10, 22, 2, 22]]
			);

			// $row = [];
			// $format = [];
			// $writer->writeSheetRow($sheet, $row, $format);

			$row = [$team->team, '', '', ''];
			$format = ['font-size' => 12, 'font-style' => 'bold', 'height' => 20];
			$writer->writeSheetRow($sheet, $row, $format);

			$writer->writeSheetRow($sheet, []);


			if (!empty($team->games)) {
				$row = [$team->league . " (" . $team->leagueKey . ")"];
				$format = ['font-style' => 'bold'];
				$writer->writeSheetRow($sheet, $row, $format);

				$writer->writeSheetRow($sheet, []);

				$row = [$link];
				$format = [];
				$writer->writeSheetRow($sheet, $row, $format);

				$writer->writeSheetRow($sheet, []);

				$row = ['', 'Datum', 'Zeit', 'Heim', '', 'Gast'];
				$format = ['font-style' => 'bold'];
				$writer->writeSheetRow($sheet, $row, $format);


				foreach ($team->games as $game) {
					$row = [
						JHtml::_('date', $game->dateTime, 'D', $tz),
						JHtml::_('date', $game->dateTime, 'd.m.Y', $tz),
						JHtml::_('date', $game->dateTime, 'H:i', $tz) . ' Uhr',
						$game->home,
						'-',
						$game->away
					];

					$writer->writeSheetRow($sheet, $row);
				}
			} else {
				$writer->writeSheetRow($sheet, ['Keine HVW Daten']);
			}
		}
	}
}