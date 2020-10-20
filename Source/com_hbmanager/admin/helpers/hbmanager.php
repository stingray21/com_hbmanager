<?php

defined('_JEXEC') or die;

class HbmanagerHelper
{
	public static function addSubmenu($submenu) 
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_HBMANAGER'),
			'index.php?option=com_hbmanager',
			$submenu == 'hbmanager'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_TEAMS'),
			'index.php?option=com_hbmanager&view=teams',
			$submenu == 'teams'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_TEAMDATA'),
			'index.php?option=com_hbmanager&view=teamdata',
			$submenu == 'teamdata'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_GAMES_PREV'),
			'index.php?option=com_hbmanager&view=gamesprev',
			$submenu == 'gamesprev'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_GAMES_NEXT'),
			'index.php?option=com_hbmanager&view=gamesnext',
			$submenu == 'gamesnext'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_PRINTNEWS'),
			'index.php?option=com_hbmanager&view=printnews',
			$submenu == 'printnews'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_GAMEDETAILS'),
			'index.php?option=com_hbmanager&view=gamedetails',
			$submenu == 'gamedetails'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_GAMESHOME'),
			'index.php?option=com_hbmanager&view=gameshome',
			$submenu == 'gameshome'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_GAMESALL'),
			'index.php?option=com_hbmanager&view=gamesall',
			$submenu == 'gamesall'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_HBMANAGER_SUBMENU_EMAILS'),
			'index.php?option=com_hbmanager&view=emails',
			$submenu == 'emails'
		);
	}


	public static function get_hvw_json_url($id) 
	{
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$url = $params->get( 'hvwurl-json' );
		
		$pattern = '/__ID__/i';
		$replacement = $id;
		$url = preg_replace($pattern, $replacement, $url);
		
		return $url;
	}

	public static function get_hvw_page_url($id) 
	{
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$url = $params->get( 'hvwurl-page' );
		
		$pattern = '/__ID__/i';
		$replacement = $id;
		$url = preg_replace($pattern, $replacement, $url);
		
		return $url;
	}

	public static function get_hvw_report_url($id) 
	{
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$url = $params->get( 'hvwurl-report' );
		
		$pattern = '/__ID__/i';
		$replacement = $id;
		$url = preg_replace($pattern, $replacement, $url);
		
		return $url;
	}

	public static function getCurrentSeason() 
	{
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$season = $params->get( 'season' );

		if ($season == null || $season == '') 
		{
			// current season
			$year = strftime('%Y');
			if (strftime('%m') < 8) {
				$year = $year-1;
			}
			$season = $year.'-'.($year+1);
		}
		return $season;
	}

	public static function getHbTimezone() 
	{
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$timezone = $params->get( 'timezone' );

		if (empty($timezone)) $timezone = false;

		return (bool) $timezone;
	}	

	public static function getHomeGyms() 
	{
		// TODO: make it setting in options

		// get compontent config parameter
		// $params = JComponentHelper::getParams( 'com_hbmanager' );
		// $timezone = $params->get( 'timezone' );

		// 7003 - Kreissporthalle Balingen
		// 7004 - Längenfeldhalle Balingen
		// 7005 - Sporthalle bei der Realschule Balingen
		// 7014 - Schloßparkhalle Geislingen

		if (empty($timezone)) $gyms = [7003, 7005, 7014];

		return $gyms;
	}

	public static function formatInput($input, $i)
	{
		$formatedInput = preg_replace('/name="([\S]*?)\[/',
					"name=\"$1[".$i."][", $input);
		$formatedInput = preg_replace('/id="([\S]*?)_/',
					"id=\"$1_".$i."_", $formatedInput);
		return $formatedInput;
	}



	public static function getformatedTime($dateTime, $tz, $suffix = '')
	{
		$fakeTime = '03:21';
		if (JHtml::_('date', $dateTime, 'H:i', $tz) !== $fakeTime) {
			return JHtml::_('date', $dateTime, 'H:i', $tz).$suffix;
		}
		
		return '';	
	}

}