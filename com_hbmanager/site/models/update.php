<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 

JLoader::register('HBmanagerModelTeamdata', JPATH_COMPONENT_ADMINISTRATOR . '/models/teamdata.php');
// Require helper file
JLoader::register('HbmanagerHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/hbmanager.php');

class HBmanagerModelUpdate extends HBmanagerModelTeamdata
// class HBmanagerModelUpdate extends JModelLegacy
{	

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
		parent::__construct($config);
    }

    /**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Team', $prefix = 'HbmanagerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the message
	 *
	 * @param   integer  $id  Greeting Id
	 *
	 * @return  string        Fetched String from Table for relevant Id
	 */
	public function getTeams($id = 1)
	{
		if (!is_array($this->messages))
		{
			$this->messages = array();
		}

		if (!isset($this->messages[$id]))
		{
			// Request the selected id
			$jinput = JFactory::getApplication()->input;
			$id     = $jinput->get('id', 1, 'INT');

			// Get a TableHelloWorld instance
			$table = $this->getTable();

			// Load the message
			$table->load($id);

			// Assign the message
			$this->messages[$id] = $table->team;
		}

		return $this->messages;
	}

	function updateMultipleTeams($type = 'cronjob', $onlyOutdated = true) 
	{
		if ($onlyOutdated) $teams = self::getOutdatedTeamList();
		else $teams = self::getTeamList();

		$teams = [$teams[0]];
		foreach ($teams as &$team) {
			$team->response = self::updateTeamData($team->teamkey, $type);
			$flags[] = $team->response['result']['total'];
		}
		$result = new stdClass();;
		$result->success = array_product($flags);
		$result->teams = $teams;

		return $result;
	}

	function getTeamList()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('teamkey').', '.$db->qn('update'));
		$query->from($this->tableTeams);
		$query->where($db->qn('leagueIdHvw').' IS NOT NULL');

		$db->setQuery($query);
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$teams = $db->loadObjectList();

		return $teams;		
	}


	function getOutdatedTeamList()
	{
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT('.$db->qn('teamkey').'), '.$db->qn('update') );
		$query->from($db->qn($this->tableTeams));
		$query->innerJoin($db->qn('#__hb_game').
			' USING ('.$db->qn('teamkey').')' );
		$query->where($db->qn('leagueIdHvw').' IS NOT NULL','AND');
		$query->where('('.$db->qn('pointsHome').' IS NULL'
			. ' AND '.$db->qn('dateTime').' + INTERVAL 2 HOUR < UTC_TIMESTAMP() )'
			. ' OR '
			. '( DAYOFWEEK(NOW()) = 2 '
			. 'AND '.$db->qn('update').'+ INTERVAL 1 Day < NOW() )');

		// echo __FILE__.' ('.__LINE__.'):<pre>'.$query.'</pre>';die;
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		
		return $teams;
	}

	//  /**
	//  * Method to build an SQL query to load the list data.
	//  *
	//  * @return      string  An SQL query
	//  */
	// protected function getListQuery()
	// {
	// 	// Initialize variables.
	// 	$db    = JFactory::getDbo();
	// 	$query = $db->getQuery(true);

	// 	// Create the base select statement.
	// 	$query->select('*')
	// 		  ->from($db->quoteName($this->tableTeams));

	// 	// Filter: like / search
	// 	$search = $this->getState('filter.search');

	// 	if (!empty($search))
	// 	{
	// 		$like = $db->quote('%' . $search . '%');
	// 		$query->where('team LIKE ' . $like);
	// 	}


	// 	// Add the list ordering clause.
	// 	$orderCol	= $this->state->get('list.ordering', '`order`');
	// 	$orderDirn 	= $this->state->get('list.direction', 'asc');

	// 	$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
	// 	// echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';die();
	// 	return $query;
	// }
}

