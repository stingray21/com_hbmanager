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
 * HelloWorld Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
class HbmanagerControllerTeam extends JControllerForm
{	
	public function __construct($config = array())
	{
		parent::__construct($config);

		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($config);echo'</pre>';
		
	}

	public function save($key = null, $urlVar = null){
		if($_POST['jform']){
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($_POST['jform']);echo'</pre>';
			
			// Get the original POST data
			$itemData = JRequest::getVar('jform', array(), 'post', 'array');
			
			$itemData['leagueIdHvw'] = (preg_match('/\d{4,6}/', $itemData['leagueIdHvw'])) ? $itemData['leagueIdHvw'] : null;
			$itemData['update'] = (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemData['update'])) ? $itemData['update'] : null;
				
			// Save it back to the $_POST global variable
			JRequest::setVar('jform', $itemData, 'post');
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($_POST['jform']);echo'</pre>';die;
		}
	
		// Finally, save the processed form data
		return parent::save('id', $urlVar);
	}
	
	
}


