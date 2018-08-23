<?php
 
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

		$query->select("`teamkey` AS `key`, `team` AS `value`");
		$query->from("`#__hb_team`");
		$query->order("ISNULL(`order`), `order`");
		// $query->where("`leagueIdHvw` IS NOT NULL ");


		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();
		//echo "<pre>"; print_r($items); echo "</pre>";

		// Build the field options.
		if (!empty($items))
		{
			if ($translate == true)
			{
				$options[] = JHtml::_('select.option', '', JText::_('COM_HBMANAGER_TEAM_OPTIONS_TEAMKEY_NO_TEAM'));
			}
			else
			{
				$options[] = JHtml::_('select.option', '', JText::_('COM_HBMANAGER_TEAM_OPTIONS_TEAMKEY_NO_TEAM'));
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