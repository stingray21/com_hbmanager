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

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HBmanagerModelGamesHome extends HBmanagerModelGames
{

	function __construct()
	{
		parent::__construct();
	}

	public function getGames()
	{
		$games = self::getAllHomeGames();

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($games);echo'</pre>';

		return $games;
	}
}