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
 * HbManager Model
 *
 * @since  0.0.1
 */
class HBmanagerModelTeamdata extends JModelList
{
	
    // private $updated = array();
	// protected $season;
	// private $names = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'team',
				'teamkey'
			);
		}

		parent::__construct($config);
		
		// $this->names = self::getScheduleTeamNames();
		
		// set maximum execution time limit
		set_time_limit(90);

    }

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
			  ->from($db->quoteName('#__hb_team'));

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('team LIKE ' . $like);
		}


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', '`order`');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';die();
		return $query;
	}

	function updateTeamData($teamkey) 
	{
		$team = self::getTeam($teamkey);
		$url = HbmanagerHelper::get_hvw_json_url($team->leagueIdHvw);

		$response = array(	"teamkey" => $teamkey, 
							"date" => JHTML::_('date', 'now', 'D, d.m.Y - H:i:s', true),
							"link" => $url);
		return $response;
	}

	protected function getTeam($teamkey)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__hb_team');
		$query->where($db->qn('teamkey').' = '.$db->q($teamkey));
		$db->setQuery($query);
		$team = $db->loadObject();
		return $team;
	}


}
