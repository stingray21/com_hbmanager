<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
 
defined('JPATH_PLATFORM') or die;
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldTeams extends JFormFieldList
{

	public $type = 'teams';

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;


		// Get the database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("`kuerzel` AS `key`, `mannschaft` AS `value`");
		$query->from("`hb_mannschaft`");
		$query->order("`reihenfolge`");
		$query->where("`hvwLink` IS NOT NULL ");

		//$query = "SELECT `kuerzel` AS `key`, `mannschaft` AS `value` FROM  ORDER BY `reihenfolge`";


		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();
		//echo "<pre>"; print_r($items); echo "</pre>";

		// Build the field options.
		if (!empty($items))
		{
			if ($translate == true)
			{
				$options[] = JHtml::_('select.option', 'allGyms', JText::_('alle'));
			}
			else
			{
				$options[] = JHtml::_('select.option', 'allGyms', 'alle im Bezirk');
			}

			foreach ($items as $item)
			{
				if ($translate == true)
				{
					$options[] = JHtml::_('select.option', $item->key, JText::_($item->value));
				}
				else
				{
					$options[] = JHtml::_('select.option', $item->key, $item->value);
				}
			}
		}
 
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}