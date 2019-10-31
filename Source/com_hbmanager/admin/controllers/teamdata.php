<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hbmanager
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorlds Controller
 *
 * @since  0.0.1
 */
class HbmanagerControllerTeamdata extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Teamdata', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		echo "TEAM DATA";
		return $model;
	}

	public function update()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($input);echo'</pre>';
		$pks = $input->post->get('cid', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);

		// Get the model
		$model = $this->getModel();

		$return = $model->test($pks);

		// Redirect to the list screen.
		// $this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=teamdata', false));

	}
	

}
