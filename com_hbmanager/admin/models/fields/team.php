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

JFormHelper::loadFieldClass('list');

/**
 * HelloWorld Form Field class for the HelloWorld component
 *
 * @since  0.0.1
 */
class JFormFieldTeam extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'Team';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array  An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__hb_team');
		$db->setQuery((string) $query);
		$teams = $db->loadObjectList();
		$options  = array();

		if ($teams)
		{
			foreach ($teams as $team)
			{
				$options[] = JHtml::_('select.option', $team->teamId, $team->team);
			}
		}

		$options = array_merge(parent::getOptions(), $options);
		echo __FILE__.'('.__LINE__.'):<pre>';print_r($options);echo'</pre>';
		return $options;
	}
}
