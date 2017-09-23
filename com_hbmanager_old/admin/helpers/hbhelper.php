<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HB Manager component helper.
 */

abstract class HbHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_TEAMS_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showTeams',
				$submenu == 'hbteams');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_DATA_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showData',
				$submenu == 'hbdata');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_PREVGAMES_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showPrevGames',
				$submenu == 'hbprevgames');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_NEXTGAMES_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showNextGames',
				$submenu == 'hbnextgames');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_JOURNAL_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showJournal',
				$submenu == 'hbjournal');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_PICTURES_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showPictures',
				$submenu == 'hbpictures');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_OVERVIEW_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showOverview',
				$submenu == 'hboverview');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_GOALSINPUT_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showGoalsInput',
				$submenu == 'hbgoalsinput');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_REPORTINPUT_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showReportInput',
				$submenu == 'hbreportinput');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_TEAMMENUS_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showTeamMenus',
				$submenu == 'hbteammenus');
	}
	
	public static function formatInput($input, $i)
	{
		$formatedInput = preg_replace('/name="([\S]*?)\[/',
					"name=\"$1[".$i."][", $input);
		$formatedInput = preg_replace('/id="([\S]*?)_/',
					"id=\"$1_".$i."_", $formatedInput);
		return $formatedInput;
	}
}